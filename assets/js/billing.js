(function () {
    var app = document.getElementById('billing-app');
    if (!app) {
        return;
    }
    var boot = JSON.parse(app.getAttribute('data-billing') || '{}');
    var alertBox = document.getElementById('billing-alert');

    function show(message, ok) {
        alertBox.hidden = false;
        alertBox.textContent = message;
        alertBox.className = 'mb-6 rounded-md p-4 border-l-4 ' + (ok ? 'bg-green-50 border-green-500 text-green-700' : 'bg-red-50 border-red-500 text-red-700');
        alertBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function post(fields) {
        var body = new URLSearchParams(fields);
        body.set('csrf_token', boot.csrf);
        return fetch(boot.endpoint, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'Accept': 'application/json' },
            body: body.toString()
        }).then(function (response) {
            return response.json().catch(function () {
                return { status: 'error', message: 'Unexpected response' };
            });
        });
    }

    var checkoutLoader = null;
    function loadCheckout() {
        if (window.Razorpay) {
            return Promise.resolve();
        }
        if (!checkoutLoader) {
            checkoutLoader = new Promise(function (resolve, reject) {
                var script = document.createElement('script');
                script.src = 'https://checkout.razorpay.com/v1/checkout.js';
                script.onload = resolve;
                script.onerror = function () {
                    checkoutLoader = null;
                    reject(new Error('checkout'));
                };
                document.head.appendChild(script);
            });
        }
        return checkoutLoader;
    }

    function buy(button) {
        var card = button.closest('article');
        var select = card ? card.querySelector('[data-course-select]') : null;
        var fields = { action: 'create_order', plan_id: button.getAttribute('data-buy-plan') };
        if (select) {
            if (!select.value) {
                show(boot.text.choose, false);
                select.focus();
                return;
            }
            fields.course_id = select.value;
        }
        button.disabled = true;
        show(boot.text.opening, true);
        Promise.all([post(fields), loadCheckout()]).then(function (results) {
            var order = results[0];
            if (order.status !== 'success') {
                button.disabled = false;
                show(order.message || 'Error', false);
                return;
            }
            var finished = false;
            var poller = null;
            function stopPolling() {
                if (poller) {
                    clearInterval(poller);
                    poller = null;
                }
            }
            // UPI QR payments can succeed without the checkout handler firing, so ask the server too.
            function checkPaid() {
                return post({ action: 'order_status', order_id: order.order_id }).then(function (state) {
                    if (state.status === 'success' && state.paid && !finished) {
                        finished = true;
                        stopPolling();
                        try { checkout.close(); } catch (e) {}
                        show(boot.text.success, true);
                        window.location.reload();
                    }
                    return !!(state && state.paid);
                }).catch(function () {
                    return false;
                });
            }
            var checkout = new window.Razorpay({
                key: order.key,
                order_id: order.order_id,
                amount: order.amount,
                currency: order.currency,
                name: order.name,
                description: order.description,
                prefill: order.prefill,
                theme: { color: '#04AA6D' },
                handler: function (result) {
                    finished = true;
                    stopPolling();
                    show(boot.text.success, true);
                    post({
                        action: 'verify_payment',
                        razorpay_order_id: result.razorpay_order_id,
                        razorpay_payment_id: result.razorpay_payment_id,
                        razorpay_signature: result.razorpay_signature
                    }).then(function (verified) {
                        if (verified.status === 'success') {
                            window.location.reload();
                        } else {
                            button.disabled = false;
                            show(verified.message || 'Error', false);
                        }
                    });
                },
                modal: {
                    ondismiss: function () {
                        stopPolling();
                        if (finished) {
                            return;
                        }
                        checkPaid().then(function (paid) {
                            if (!paid) {
                                button.disabled = false;
                                show(boot.text.cancelled, false);
                            }
                        });
                    }
                }
            });
            checkout.on('payment.failed', function (response) {
                var reason = response && response.error && response.error.description;
                show(boot.text.failed + ' ' + (reason || boot.text.cancelled), false);
            });
            checkout.open();
            var checks = 0;
            poller = setInterval(function () {
                if (++checks > 110) {
                    stopPolling();
                    return;
                }
                checkPaid();
            }, 8000);
        }).catch(function () {
            button.disabled = false;
            show(boot.text.cancelled, false);
        });
    }

    app.addEventListener('click', function (event) {
        var button = event.target.closest('[data-buy-plan]');
        if (button && !button.disabled) {
            buy(button);
        }
    });
})();
