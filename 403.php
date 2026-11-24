<?php
http_response_code(403);
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('X-Robots-Tag: noindex, nofollow');
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>403 — Not Allowed</title>
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{min-height:100vh;display:flex;align-items:center;justify-content:center;background:#0f172a;color:#f8fafc;font-family:system-ui,-apple-system,Segoe UI,Roboto,sans-serif;padding:24px}
        .card{max-width:460px;width:100%;background:#1e293b;border:1px solid #334155;border-radius:16px;padding:36px 32px;text-align:center;box-shadow:0 20px 50px rgba(0,0,0,.45)}
        .badge{display:inline-flex;align-items:center;gap:6px;background:rgba(239,68,68,.12);color:#f87171;border:1px solid rgba(239,68,68,.3);font-size:12px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;padding:5px 12px;border-radius:999px;margin-bottom:16px}
        h1{font-size:28px;font-weight:800;letter-spacing:-.02em;margin-bottom:10px}
        p{color:#94a3b8;font-size:14px;line-height:1.6}
    </style>
</head>
<body>
    <div class="card">
        <div class="badge">403 Forbidden</div>
        <h1>Access Not Allowed</h1>
        <p>You do not have permission to open this resource or directory directly.</p>
    </div>
</body>
</html>
