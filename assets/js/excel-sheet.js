/* Excel course only. Turns each studio grid into a sheet you can edit.
   Formulas calculate here. Nothing is sent to the code runner. */
(function (root) {
    'use strict';

    var MONTHS = { jan: 1, january: 1, feb: 2, february: 2, mar: 3, march: 3, apr: 4, april: 4, may: 5, jun: 6, june: 6, jul: 7, july: 7, aug: 8, august: 8, sep: 9, sept: 9, september: 9, oct: 10, october: 10, nov: 11, november: 11, dec: 12, december: 12 };
    var PRICES = { A1: 'Item', B1: 'Rate', A2: 'Milk', B2: '60', A3: 'Bread', B3: '40', A4: 'Eggs', B4: '90', A5: 'Rice', B5: '180' };

    function trimNum(n) {
        if (!isFinite(n)) return '#NUM!';
        if (Math.abs(n - Math.round(n)) < 1e-9) return String(Math.round(n));
        var s = n.toFixed(4).replace(/0+$/, '').replace(/\.$/, '');
        return s;
    }

    function pad2(n) { return n < 10 ? '0' + n : String(n); }

    function formatDate(val) {
        var names = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        return val.d + ' ' + names[val.m - 1] + ' ' + val.y;
    }

    function parseDateText(s) {
        var m = String(s).trim().match(/^(\d{1,2})\s+([A-Za-z]{3,9})(?:\s+(\d{4}))?$/);
        if (!m) return null;
        var mon = MONTHS[m[2].toLowerCase()];
        if (!mon) return null;
        var day = parseInt(m[1], 10);
        var year = m[3] ? parseInt(m[3], 10) : 2026;
        if (day < 1 || day > 31) return null;
        return { kind: 'date', y: year, m: mon, d: day, v: Date.UTC(year, mon - 1, day) / 86400000 };
    }

    function literal(raw, dateMode) {
        if (raw == null) return { kind: 'blank', v: '' };
        var s = String(raw).trim();
        if (s === '') return { kind: 'blank', v: '' };
        if (s.charAt(0) === "'") return { kind: 'text', v: s.slice(1) };
        if (s.charAt(0) === '#') return { kind: 'err', v: s.split(' ')[0] };
        if (/^true$/i.test(s)) return { kind: 'bool', v: true };
        if (/^false$/i.test(s)) return { kind: 'bool', v: false };
        if (/^-?\d+(\.\d+)?%$/.test(s)) return { kind: 'num', v: parseFloat(s) / 100 };
        if (/^-?\d+(\.\d+)?$/.test(s)) return { kind: 'num', v: parseFloat(s) };
        if (dateMode) {
            var dt = parseDateText(s);
            if (dt) return dt;
        }
        return { kind: 'text', v: s };
    }

    function isErr(v) { return v && v.kind === 'err'; }

    function numOf(v) {
        if (!v || v.kind === 'blank') return { kind: 'num', v: 0 };
        if (v.kind === 'num' || v.kind === 'date') return { kind: 'num', v: v.v };
        if (v.kind === 'bool') return { kind: 'num', v: v.v ? 1 : 0 };
        if (v.kind === 'text' && /^-?\d+(\.\d+)?$/.test(v.v)) return { kind: 'num', v: parseFloat(v.v) };
        if (v.kind === 'err') return v;
        return { kind: 'err', v: '#VALUE!' };
    }

    function showVal(v) {
        if (!v || v.kind === 'blank') return '';
        if (v.kind === 'err') return v.v;
        if (v.kind === 'text') return String(v.v);
        if (v.kind === 'bool') return v.v ? 'TRUE' : 'FALSE';
        if (v.kind === 'date') return formatDate(v);
        if (v.kind === 'num') return trimNum(v.v);
        if (v.kind === 'arr') return showVal(v.rows[0][0]);
        return '';
    }

    function hintOf(expect) {
        var s = String(expect || '').trim();
        if (/^rs\b/i.test(s)) return 'rs';
        if (/%$/.test(s) && !/^#/.test(s)) return 'pct';
        if (s === '###') return 'hash';
        return '';
    }

    function showHint(v, hint, expect) {
        if (hint === 'rs' && v && v.kind === 'num') return 'Rs ' + trimNum(v.v);
        if (hint === 'pct' && v && v.kind === 'num') {
            var face = String(expect || '');
            var places = 0;
            var frac = face.match(/\.(\d+)%$/);
            if (frac) places = frac[1].length;
            else if (!/%$/.test(face)) places = 2;
            return Number((v.v * 100).toFixed(places)) + '%';
        }
        if (hint === 'hash') return '###';
        return showVal(v);
    }

    function colToN(col) {
        var n = 0;
        col = col.toUpperCase();
        for (var i = 0; i < col.length; i++) n = n * 26 + (col.charCodeAt(i) - 64);
        return n;
    }

    function nToCol(n) {
        var s = '';
        while (n > 0) {
            var m = (n - 1) % 26;
            s = String.fromCharCode(65 + m) + s;
            n = Math.floor((n - 1) / 26);
        }
        return s;
    }

    function splitRef(text) {
        var m = String(text).match(/^(?:([A-Za-z_][A-Za-z0-9_]*)!)?(\$?)([A-Za-z]+)(\$?)(\d+)?$/);
        if (!m) return null;
        return { sheet: m[1] || '', colLock: m[2] === '$', col: m[3].toUpperCase(), rowLock: m[4] === '$', row: m[5] ? parseInt(m[5], 10) : 0 };
    }

    function addrOf(ref, fallbackSheet) {
        var p = splitRef(ref);
        if (!p || !p.row) return null;
        return { sheet: p.sheet || fallbackSheet, addr: p.col + p.row, col: p.col, row: p.row };
    }

    function tokenize(formula) {
        var s = String(formula).trim().replace(/^=/, '');
        var out = [];
        var i = 0;
        while (i < s.length) {
            var c = s.charAt(i);
            if (c === ' ' || c === '\n' || c === '\t') { i++; continue; }
            if (c === '"') {
                var j = i + 1;
                var str = '';
                while (j < s.length) {
                    if (s.charAt(j) === '"') {
                        if (s.charAt(j + 1) === '"') { str += '"'; j += 2; continue; }
                        break;
                    }
                    str += s.charAt(j);
                    j++;
                }
                out.push({ t: 'str', v: str });
                i = j + 1;
                continue;
            }
            if (/[0-9]/.test(c) || (c === '.' && /[0-9]/.test(s.charAt(i + 1)))) {
                var k = i;
                while (k < s.length && /[0-9.]/.test(s.charAt(k))) k++;
                var num = parseFloat(s.slice(i, k));
                if (s.charAt(k) === '%') { num = num / 100; k++; }
                out.push({ t: 'num', v: num });
                i = k;
                continue;
            }
            if (/[A-Za-z_$]/.test(c)) {
                var q = i;
                while (q < s.length && /[A-Za-z0-9_$.!]/.test(s.charAt(q))) q++;
                out.push({ t: 'id', v: s.slice(i, q) });
                i = q;
                continue;
            }
            var two = s.slice(i, i + 2);
            if (two === '>=' || two === '<=' || two === '<>') {
                out.push({ t: 'op', v: two });
                i += 2;
                continue;
            }
            if ('(),:'.indexOf(c) >= 0) out.push({ t: c, v: c });
            else out.push({ t: 'op', v: c });
            i++;
        }
        return out;
    }

    function parseFormula(formula) {
        var tokens = tokenize(formula);
        var i = 0;
        function peek() { return tokens[i]; }
        function eat() { return tokens[i++]; }
        function parseExpr() { return parseCat(); }
        function parseCat() {
            var left = parseCmp();
            while (peek() && peek().v === '&') { eat(); left = { k: 'cat', a: left, b: parseCmp() }; }
            return left;
        }
        function parseCmp() {
            var left = parseAdd();
            while (peek() && ['=', '<>', '<', '>', '<=', '>='].indexOf(peek().v) >= 0) {
                var op = eat().v;
                left = { k: 'cmp', op: op, a: left, b: parseAdd() };
            }
            return left;
        }
        function parseAdd() {
            var left = parseMul();
            while (peek() && (peek().v === '+' || peek().v === '-')) {
                var op = eat().v;
                left = { k: 'bin', op: op, a: left, b: parseMul() };
            }
            return left;
        }
        function parseMul() {
            var left = parseUnary();
            while (peek() && (peek().v === '*' || peek().v === '/')) {
                var op = eat().v;
                left = { k: 'bin', op: op, a: left, b: parseUnary() };
            }
            return left;
        }
        function parseUnary() {
            if (peek() && peek().t === 'op' && peek().v === '-') { eat(); return { k: 'neg', a: parseUnary() }; }
            if (peek() && peek().t === 'op' && peek().v === '+') { eat(); return parseUnary(); }
            return parsePrim();
        }
        function parsePrim() {
            var t = peek();
            if (!t) return { k: 'err', v: '#VALUE!' };
            if (t.t === 'num') { eat(); return { k: 'num', v: t.v }; }
            if (t.t === 'str') { eat(); return { k: 'str', v: t.v }; }
            if (t.v === '(') { eat(); var inner = parseExpr(); if (peek() && peek().v === ')') eat(); return inner; }
            if (t.t === 'id') {
                eat();
                if (peek() && peek().v === '(') {
                    eat();
                    var args = [];
                    if (!(peek() && peek().v === ')')) {
                        args.push(parseExpr());
                        while (peek() && peek().v === ',') { eat(); args.push(parseExpr()); }
                    }
                    if (peek() && peek().v === ')') eat();
                    return { k: 'call', name: String(t.v).toUpperCase(), args: args };
                }
                if (peek() && peek().v === ':') {
                    eat();
                    var end = eat();
                    return { k: 'range', a: t.v, b: end ? end.v : t.v };
                }
                return { k: 'ref', v: t.v };
            }
            eat();
            return { k: 'err', v: '#VALUE!' };
        }
        return parseExpr();
    }

    function rangeEndpoints(node, fallback) {
        var a = splitRef(node.a);
        var b = splitRef(node.b);
        if (!a || !b) return null;
        var sheet = a.sheet || b.sheet || fallback;
        if (!a.row) a.row = 1;
        if (!b.row) b.row = 24;
        var c1 = colToN(a.col);
        var c2 = colToN(b.col);
        var r1 = a.row;
        var r2 = b.row;
        return { sheet: sheet, c1: Math.min(c1, c2), c2: Math.max(c1, c2), r1: Math.min(r1, r2), r2: Math.max(r1, r2) };
    }

    function eachRange(node, fallback, fn) {
        var box = rangeEndpoints(node, fallback);
        if (!box) return;
        for (var r = box.r1; r <= box.r2; r++) {
            for (var c = box.c1; c <= box.c2; c++) fn(box.sheet, nToCol(c) + r, r, c);
        }
    }

    function evalAst(node, ctx) {
        if (!node) return { kind: 'err', v: '#VALUE!' };
        if (node.k === 'num') return { kind: 'num', v: node.v };
        if (node.k === 'str') return { kind: 'text', v: node.v };
        if (node.k === 'err') return { kind: 'err', v: node.v };
        if (node.k === 'neg') {
            var n = numOf(evalAst(node.a, ctx));
            if (isErr(n)) return n;
            return { kind: 'num', v: -n.v };
        }
        if (node.k === 'cat') {
            var a = evalAst(node.a, ctx);
            var b = evalAst(node.b, ctx);
            if (isErr(a) || isErr(b)) return isErr(a) ? a : b;
            return { kind: 'text', v: showVal(a) + showVal(b) };
        }
        if (node.k === 'ref') {
            if (!/(\d+)$/.test(node.v) && ctx.scope && Object.prototype.hasOwnProperty.call(ctx.scope, node.v.toLowerCase())) {
                return ctx.scope[node.v.toLowerCase()];
            }
            var loc = addrOf(node.v, ctx.sheet);
            if (!loc) {
                if (/^(true|false)$/i.test(node.v)) return { kind: 'bool', v: /^true$/i.test(node.v) };
                return { kind: 'err', v: '#NAME?' };
            }
            return evalCell(loc.sheet, loc.addr, ctx);
        }
        if (node.k === 'range') return { kind: 'range', node: node };
        if (node.k === 'bin' || node.k === 'cmp') return evalBin(node, ctx);
        if (node.k === 'call') return evalCall(node, ctx);
        return { kind: 'err', v: '#VALUE!' };
    }

    function evalBin(node, ctx) {
        if ((node.a && node.a.k === 'range') || (node.b && node.b.k === 'range')) {
            return { kind: 'mask', node: node };
        }
        var a = evalAst(node.a, ctx);
        var b = evalAst(node.b, ctx);
        if (isErr(a)) return a;
        if (isErr(b)) return b;
        if (node.k === 'cmp') {
            var cmp = compareVals(a, b);
            if (cmp === null) return { kind: 'err', v: '#VALUE!' };
            var ok = false;
            if (node.op === '=') ok = cmp === 0;
            else if (node.op === '<>') ok = cmp !== 0;
            else if (node.op === '<') ok = cmp < 0;
            else if (node.op === '>') ok = cmp > 0;
            else if (node.op === '<=') ok = cmp <= 0;
            else if (node.op === '>=') ok = cmp >= 0;
            return { kind: 'bool', v: ok };
        }
        if ((a.kind === 'date' || b.kind === 'date') && (node.op === '+' || node.op === '-')) {
            if (a.kind === 'date' && b.kind === 'date' && node.op === '-') return { kind: 'num', v: a.v - b.v };
            if (a.kind === 'date' && (b.kind === 'num' || b.kind === 'blank')) {
                var days = b.kind === 'blank' ? 0 : b.v;
                var base = new Date((a.v + (node.op === '+' ? days : -days)) * 86400000);
                return { kind: 'date', y: base.getUTCFullYear(), m: base.getUTCMonth() + 1, d: base.getUTCDate(), v: a.v + (node.op === '+' ? days : -days) };
            }
        }
        var na = numOf(a);
        var nb = numOf(b);
        if (isErr(na) || isErr(nb)) return isErr(na) ? na : nb;
        if (node.op === '+') return { kind: 'num', v: na.v + nb.v };
        if (node.op === '-') return { kind: 'num', v: na.v - nb.v };
        if (node.op === '*') return { kind: 'num', v: na.v * nb.v };
        if (node.op === '/') {
            if (nb.v === 0) return { kind: 'err', v: '#DIV/0!' };
            return { kind: 'num', v: na.v / nb.v };
        }
        return { kind: 'err', v: '#VALUE!' };
    }

    function compareVals(a, b) {
        if ((a.kind === 'num' || a.kind === 'date') && (b.kind === 'num' || b.kind === 'date')) return a.v === b.v ? 0 : (a.v < b.v ? -1 : 1);
        if (a.kind === 'text' || b.kind === 'text' || a.kind === 'blank' || b.kind === 'blank') {
            var as = showVal(a).toLowerCase();
            var bs = showVal(b).toLowerCase();
            if (as === bs) return 0;
            return as < bs ? -1 : 1;
        }
        if (a.kind === 'bool' || b.kind === 'bool') {
            var an = a.kind === 'bool' ? (a.v ? 1 : 0) : numOf(a).v;
            var bn = b.kind === 'bool' ? (b.v ? 1 : 0) : numOf(b).v;
            return an === bn ? 0 : (an < bn ? -1 : 1);
        }
        return null;
    }

    function evalCell(sheet, addr, ctx) {
        var key = sheet + '!' + addr;
        if (ctx.stack.indexOf(key) >= 0) return { kind: 'err', v: '#CIRC!' };
        var book = ctx.book;
        var grid = book[sheet] || {};
        var raw = grid[addr];
        if (raw == null) raw = '';
        if (String(raw).trim().charAt(0) === '=') {
            ctx.stack.push(key);
            var ast;
            try { ast = parseFormula(raw); }
            catch (e) { ctx.stack.pop(); return { kind: 'err', v: '#VALUE!' }; }
            var prev = ctx.sheet;
            ctx.sheet = sheet;
            var val = evalAst(ast, ctx);
            ctx.sheet = prev;
            ctx.stack.pop();
            return val;
        }
        return literal(raw, ctx.dateMode);
    }

    function cellsOf(node, ctx) {
        var list = [];
        if (!node) return list;
        if (node.k === 'call' && (node.name === 'COL' || node.name === 'COLUMN')) {
            var packed = evalCall(node, ctx);
            if (packed && packed.kind === 'list') return packed.items;
            list.push(packed);
            return list;
        }
        if (node.k === 'range') {
            eachRange(node, ctx.sheet, function (sheet, addr) {
                list.push(evalCell(sheet, addr, ctx));
            });
            return list;
        }
        if (node.k === 'ref') {
            var loc = addrOf(node.v, ctx.sheet);
            if (loc) list.push(evalCell(loc.sheet, loc.addr, ctx));
            return list;
        }
        var one = evalAst(node, ctx);
        list.push(one);
        return list;
    }

    function matrixOf(node, ctx) {
        if (!node || node.k !== 'range') {
            var one = evalAst(node, ctx);
            return [[one]];
        }
        var box = rangeEndpoints(node, ctx.sheet);
        if (!box) return [[{ kind: 'err', v: '#REF!' }]];
        var rows = [];
        for (var r = box.r1; r <= box.r2; r++) {
            var row = [];
            for (var c = box.c1; c <= box.c2; c++) row.push(evalCell(box.sheet, nToCol(c) + r, ctx));
            rows.push(row);
        }
        return rows;
    }

    function matchCrit(val, critNode, ctx) {
        var crit = evalAst(critNode, ctx);
        if (isErr(crit)) return false;
        if (crit.kind === 'text') {
            var t = crit.v.trim();
            var op = t.match(/^(>=|<=|<>|>|<|=)\s*(.*)$/);
            if (op) {
                var right = literal(op[2], ctx.dateMode);
                var cmp = compareVals(val.kind === 'blank' ? { kind: 'num', v: 0 } : val, right);
                if (cmp === null) return false;
                if (op[1] === '>') return cmp > 0;
                if (op[1] === '>=') return cmp >= 0;
                if (op[1] === '<') return cmp < 0;
                if (op[1] === '<=') return cmp <= 0;
                if (op[1] === '<>') return cmp !== 0;
                return cmp === 0;
            }
        }
        var cmp2 = compareVals(val.kind === 'blank' ? { kind: 'text', v: '' } : val, crit.kind === 'blank' ? { kind: 'text', v: '' } : crit);
        return cmp2 === 0;
    }

    function maskOf(node, ctx, height) {
        if (node && node.k === 'mask') {
            var rangeNode = node.a && node.a.k === 'range' ? node.a : node.b;
            var other = rangeNode === node.a ? node.b : node.a;
            var flags = [];
            var idx = 0;
            eachRange(rangeNode, ctx.sheet, function (sheet, addr) {
                if (idx >= height) return;
                var left = evalCell(sheet, addr, ctx);
                var right = evalAst(other, ctx);
                var fake = { k: 'cmp', op: node.op, a: { k: 'num', v: 0 }, b: { k: 'num', v: 0 } };
                var cmp = compareVals(left, right);
                var ok = false;
                if (cmp !== null) {
                    if (node.op === '=') ok = cmp === 0;
                    else if (node.op === '<>') ok = cmp !== 0;
                    else if (node.op === '>') ok = node.a === rangeNode ? cmp > 0 : cmp < 0;
                    else if (node.op === '<') ok = node.a === rangeNode ? cmp < 0 : cmp > 0;
                    else if (node.op === '>=') ok = node.a === rangeNode ? cmp >= 0 : cmp <= 0;
                    else if (node.op === '<=') ok = node.a === rangeNode ? cmp <= 0 : cmp >= 0;
                }
                flags.push(ok);
                idx++;
                return fake;
            });
            return flags;
        }
        var v = evalAst(node, ctx);
        if (v && v.kind === 'bool') {
            var all = [];
            for (var i = 0; i < height; i++) all.push(!!v.v);
            return all;
        }
        return [];
    }

    function evalCall(node, ctx) {
        var name = node.name;
        var args = node.args || [];
        if (name === 'LET') {
            var scope = Object.assign({}, ctx.scope || {});
            var prev = ctx.scope;
            for (var p = 0; p + 1 < args.length - 1; p += 2) {
                var nm = args[p];
                if (!nm || nm.k !== 'ref') return { kind: 'err', v: '#NAME?' };
                ctx.scope = scope;
                scope[String(nm.v).toLowerCase()] = evalAst(args[p + 1], ctx);
            }
            ctx.scope = scope;
            var result = evalAst(args[args.length - 1], ctx);
            ctx.scope = prev;
            return result;
        }
        if (name === 'IF') {
            var test = evalAst(args[0], ctx);
            if (isErr(test)) return test;
            var yes = !(test.kind === 'bool' ? !test.v : (test.kind === 'num' ? test.v === 0 : (test.kind === 'text' ? test.v === '' : test.kind === 'blank')));
            return evalAst(yes ? args[1] : args[2], ctx);
        }
        if (name === 'IFERROR' || name === 'IFNA') {
            var first = evalAst(args[0], ctx);
            if (isErr(first) && (name === 'IFERROR' || first.v === '#N/A')) return evalAst(args[1], ctx);
            return first;
        }
        if (name === 'AND' || name === 'OR') {
            var any = false;
            var all = true;
            for (var ai = 0; ai < args.length; ai++) {
                var bit = evalAst(args[ai], ctx);
                if (isErr(bit)) return bit;
                var truth = bit.kind === 'bool' ? bit.v : (bit.kind === 'num' ? bit.v !== 0 : bit.kind === 'text');
                if (truth) any = true;
                else all = false;
            }
            return { kind: 'bool', v: name === 'AND' ? all : any };
        }
        if (name === 'SUM' || name === 'AVERAGE' || name === 'COUNT' || name === 'COUNTA' || name === 'MIN' || name === 'MAX') {
            var seen = [];
            var counted = 0;
            var filled = 0;
            var failed = null;
            args.forEach(function (arg) {
                cellsOf(arg, ctx).forEach(function (cell) {
                    if (!cell || cell.kind === 'blank') return;
                    if (cell.kind === 'err') { failed = cell; return; }
                    filled++;
                    if (cell.kind === 'num' || cell.kind === 'date') {
                        seen.push(cell.v);
                        counted++;
                    }
                });
            });
            if (failed) return failed;
            if (name === 'COUNT') return { kind: 'num', v: counted };
            if (name === 'COUNTA') return { kind: 'num', v: filled };
            if (!seen.length) return name === 'AVERAGE' ? { kind: 'err', v: '#DIV/0!' } : { kind: 'num', v: 0 };
            var acc = seen[0];
            if (name === 'SUM' || name === 'AVERAGE') {
                acc = 0;
                seen.forEach(function (n) { acc += n; });
                if (name === 'AVERAGE') acc = acc / seen.length;
            } else if (name === 'MIN') seen.forEach(function (n) { if (n < acc) acc = n; });
            else seen.forEach(function (n) { if (n > acc) acc = n; });
            return { kind: 'num', v: acc };
        }
        if (name === 'ROUND') {
            var rv = numOf(evalAst(args[0], ctx));
            if (isErr(rv)) return rv;
            var digits = args[1] ? numOf(evalAst(args[1], ctx)) : { kind: 'num', v: 0 };
            if (isErr(digits)) return digits;
            var f = Math.pow(10, digits.v);
            return { kind: 'num', v: Math.round(rv.v * f) / f };
        }
        if (name === 'COL' || name === 'COLUMN') {
            var want = showVal(evalAst(args[0], ctx)).toLowerCase();
            var gridCol = ctx.book[ctx.sheet] || {};
            var col = '';
            for (var hc = 1; hc <= 8; hc++) {
                var header = gridCol[nToCol(hc) + '1'];
                if (header && String(header).trim().toLowerCase() === want) { col = nToCol(hc); break; }
            }
            if (!col) return { kind: 'err', v: '#REF!' };
            var items = [];
            for (var hr = 2; hr <= 30; hr++) {
                var lab = gridCol['A' + hr];
                if (lab && /^(total|average|result|count|max|min|sum)$/i.test(String(lab).trim())) break;
                var here = gridCol[col + hr];
                var emptyLab = lab == null || String(lab).trim() === '';
                var emptyHere = here == null || String(here).trim() === '';
                if (emptyLab && emptyHere) break;
                items.push(evalCell(ctx.sheet, col + hr, ctx));
            }
            return { kind: 'list', items: items };
        }
        if (name === 'ABS') {
            var av = numOf(evalAst(args[0], ctx));
            if (isErr(av)) return av;
            return { kind: 'num', v: Math.abs(av.v) };
        }
        if (name === 'VALUE') {
            var tv = evalAst(args[0], ctx);
            var nv = numOf(tv);
            return nv;
        }
        if (name === 'LEN' || name === 'TRIM' || name === 'LEFT' || name === 'RIGHT' || name === 'MID') {
            var src = evalAst(args[0], ctx);
            if (isErr(src)) return src;
            var text = showVal(src);
            if (name === 'LEN') return { kind: 'num', v: text.length };
            if (name === 'TRIM') return { kind: 'text', v: text.replace(/\s+/g, ' ').trim() };
            var n1 = args[1] ? numOf(evalAst(args[1], ctx)) : { kind: 'num', v: 1 };
            if (isErr(n1)) return n1;
            if (name === 'LEFT') return { kind: 'text', v: text.slice(0, n1.v) };
            if (name === 'RIGHT') return { kind: 'text', v: text.slice(Math.max(0, text.length - n1.v)) };
            var n2 = args[2] ? numOf(evalAst(args[2], ctx)) : { kind: 'num', v: text.length };
            if (isErr(n2)) return n2;
            return { kind: 'text', v: text.substr(Math.max(0, n1.v - 1), n2.v) };
        }
        if (name === 'TEXT') {
            var base = evalAst(args[0], ctx);
            var code = showVal(evalAst(args[1], ctx));
            var dt = base && base.kind === 'date' ? base : null;
            if (!dt && base && base.kind === 'num') {
                var ms = base.v * 86400000;
                var dd = new Date(ms);
                dt = { kind: 'date', y: dd.getUTCFullYear(), m: dd.getUTCMonth() + 1, d: dd.getUTCDate(), v: base.v };
            }
            if (!dt) return { kind: 'text', v: showVal(base) };
            var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            var out = code;
            out = out.replace(/mmmm/g, months[dt.m - 1]).replace(/mmm/g, months[dt.m - 1].slice(0, 3)).replace(/yyyy/g, String(dt.y)).replace(/dd/g, pad2(dt.d)).replace(/d/g, String(dt.d));
            return { kind: 'text', v: out };
        }
        if (name === 'TEXTJOIN') {
            var delim = showVal(evalAst(args[0], ctx));
            var skip = evalAst(args[1], ctx);
            var skipBlank = skip.kind === 'bool' ? skip.v : true;
            var bits = [];
            args.slice(2).forEach(function (arg) {
                cellsOf(arg, ctx).forEach(function (cell) {
                    if (skipBlank && (!cell || cell.kind === 'blank' || showVal(cell) === '')) return;
                    bits.push(showVal(cell));
                });
            });
            return { kind: 'text', v: bits.join(delim) };
        }
        if (name === 'SUMIF' || name === 'COUNTIF' || name === 'AVERAGEIF') {
            var testBox = args[0];
            var crit = args[1];
            var sumBox = args[2] || args[0];
            var tests = cellsOf(testBox, ctx);
            var sums = name === 'COUNTIF' ? tests : cellsOf(sumBox, ctx);
            var total = 0;
            var hits = 0;
            for (var si = 0; si < tests.length; si++) {
                if (!matchCrit(tests[si], crit, ctx)) continue;
                hits++;
                if (name !== 'COUNTIF') {
                    var sv = numOf(sums[si] || { kind: 'blank' });
                    if (!isErr(sv) && sums[si] && sums[si].kind !== 'text') total += sv.v;
                }
            }
            if (name === 'COUNTIF') return { kind: 'num', v: hits };
            if (name === 'AVERAGEIF') return hits ? { kind: 'num', v: total / hits } : { kind: 'err', v: '#DIV/0!' };
            return { kind: 'num', v: total };
        }
        if (name === 'SUMIFS' || name === 'COUNTIFS' || name === 'AVERAGEIFS') {
            var sumArg = name === 'COUNTIFS' ? null : args[0];
            var start = name === 'COUNTIFS' ? 0 : 1;
            var sums2 = sumArg ? cellsOf(sumArg, ctx) : [];
            var len = name === 'COUNTIFS' ? cellsOf(args[0], ctx).length : sums2.length;
            var total2 = 0;
            var hits2 = 0;
            for (var row = 0; row < len; row++) {
                var pass = true;
                for (var pair = start; pair < args.length; pair += 2) {
                    var colCells = cellsOf(args[pair], ctx);
                    if (!matchCrit(colCells[row] || { kind: 'blank' }, args[pair + 1], ctx)) { pass = false; break; }
                }
                if (!pass) continue;
                hits2++;
                if (name !== 'COUNTIFS') {
                    var nv2 = numOf(sums2[row] || { kind: 'blank' });
                    if (!isErr(nv2) && sums2[row] && sums2[row].kind !== 'text') total2 += nv2.v;
                }
            }
            if (name === 'COUNTIFS') return { kind: 'num', v: hits2 };
            if (name === 'AVERAGEIFS') return hits2 ? { kind: 'num', v: total2 / hits2 } : { kind: 'err', v: '#DIV/0!' };
            return { kind: 'num', v: total2 };
        }
        if (name === 'SUMPRODUCT') {
            var left = matrixOf(args[0], ctx);
            var right = matrixOf(args[1], ctx);
            if (left.length !== right.length || (left[0] || []).length !== (right[0] || []).length) return { kind: 'err', v: '#VALUE!' };
            var prod = 0;
            for (var rr = 0; rr < left.length; rr++) {
                for (var cc = 0; cc < left[rr].length; cc++) {
                    var l = numOf(left[rr][cc]);
                    var rt = numOf(right[rr][cc]);
                    if (isErr(l) || isErr(rt)) continue;
                    if (left[rr][cc].kind === 'text' || right[rr][cc].kind === 'text') continue;
                    prod += l.v * rt.v;
                }
            }
            return { kind: 'num', v: prod };
        }
        if (name === 'XLOOKUP') {
            var key = evalAst(args[0], ctx);
            var look = cellsOf(args[1], ctx);
            var back = cellsOf(args[2], ctx);
            for (var xi = 0; xi < look.length; xi++) {
                if (compareVals(look[xi], key) === 0) return back[xi] || { kind: 'blank', v: '' };
            }
            if (args[3]) return evalAst(args[3], ctx);
            return { kind: 'err', v: '#N/A' };
        }
        if (name === 'VLOOKUP') {
            var key2 = evalAst(args[0], ctx);
            var grid = matrixOf(args[1], ctx);
            var idx = numOf(evalAst(args[2], ctx));
            if (isErr(idx)) return idx;
            var exact = true;
            if (args[3]) {
                var mode = evalAst(args[3], ctx);
                if (mode.kind === 'bool') exact = mode.v === false;
                else {
                    var ms = String(showVal(mode)).toUpperCase();
                    exact = ms === 'FALSE' || ms === '0';
                }
            }
            if (exact) {
                for (var vr = 0; vr < grid.length; vr++) {
                    if (compareVals(grid[vr][0], key2) === 0) {
                        return grid[vr][idx.v - 1] || { kind: 'err', v: '#REF!' };
                    }
                }
                return { kind: 'err', v: '#N/A' };
            }
            var last = null;
            for (var ar = 0; ar < grid.length; ar++) {
                var cmpA = compareVals(grid[ar][0], key2);
                if (cmpA === null) continue;
                if (cmpA > 0) break;
                last = grid[ar];
            }
            if (!last) return { kind: 'err', v: '#N/A' };
            return last[idx.v - 1] || { kind: 'err', v: '#REF!' };
        }
        if (name === 'MATCH') {
            var key3 = evalAst(args[0], ctx);
            var look3 = cellsOf(args[1], ctx);
            for (var mi = 0; mi < look3.length; mi++) {
                if (compareVals(look3[mi], key3) === 0) return { kind: 'num', v: mi + 1 };
            }
            return { kind: 'err', v: '#N/A' };
        }
        if (name === 'INDEX') {
            var mat = matrixOf(args[0], ctx);
            var rrI = numOf(evalAst(args[1], ctx));
            if (isErr(rrI)) return rrI;
            var ccI = args[2] ? numOf(evalAst(args[2], ctx)) : { kind: 'num', v: 1 };
            if (isErr(ccI)) return ccI;
            var rowI = mat[rrI.v - 1];
            if (!rowI) return { kind: 'err', v: '#REF!' };
            return rowI[ccI.v - 1] || { kind: 'err', v: '#REF!' };
        }
        if (name === 'FILTER') {
            var body = matrixOf(args[0], ctx);
            var flags = maskOf(args[1], ctx, body.length);
            var kept = [];
            for (var fi = 0; fi < body.length; fi++) if (flags[fi]) kept.push(body[fi]);
            if (!kept.length) return args[2] ? evalAst(args[2], ctx) : { kind: 'err', v: '#N/A' };
            return { kind: 'arr', rows: kept };
        }
        if (name === 'SORT') {
            var sm = matrixOf(args[0], ctx).map(function (row) { return row.slice(); });
            var colIndex = args[1] ? numOf(evalAst(args[1], ctx)).v : 1;
            var order = args[2] ? numOf(evalAst(args[2], ctx)).v : 1;
            sm.sort(function (a, b) {
                var cmp = compareVals(a[colIndex - 1] || { kind: 'blank' }, b[colIndex - 1] || { kind: 'blank' });
                return (order < 0 ? -1 : 1) * (cmp || 0);
            });
            return { kind: 'arr', rows: sm };
        }
        if (name === 'UNIQUE') {
            var um = matrixOf(args[0], ctx);
            var seenU = {};
            var uniq = [];
            um.forEach(function (row) {
                var id = row.map(showVal).join('|').toLowerCase();
                if (seenU[id]) return;
                seenU[id] = true;
                uniq.push(row);
            });
            return { kind: 'arr', rows: uniq };
        }
        return { kind: 'err', v: '#NAME?' };
    }

    function calculate(book, sheet, addr, dateMode) {
        var ctx = { book: book, sheet: sheet, stack: [], scope: {}, dateMode: !!dateMode };
        if (addr) return evalCell(sheet, addr, ctx);
        return { kind: 'blank', v: '' };
    }

    function describeRef(ref) {
        var p = splitRef(ref);
        if (!p) return ref;
        var where = p.sheet ? 'On the ' + p.sheet + ' sheet, ' : '';
        var place = 'column ' + p.col + (p.row ? ', row ' + p.row : ' (the whole column)');
        var lock = '';
        if (p.colLock && p.rowLock) lock = ' Both the column and the row are locked with dollar signs, so a fill stays on this cell.';
        else if (p.colLock) lock = ' The column is locked, so a fill down can change the row and a fill across cannot change the column.';
        else if (p.rowLock) lock = ' The row is locked, so a fill across can change the column and a fill down cannot change the row.';
        else if (p.row) lock = ' There is no dollar sign, so a fill walks this address with the new row or column.';
        return where + ref + ' is ' + place + '.' + lock;
    }

    function explainFormula(formula, book, sheet, dateMode) {
        if (!formula || String(formula).trim().charAt(0) !== '=') {
            return 'This cell stores a value, not a formula. The grid shows that value because nothing is being calculated. Type an equals sign first if you want Excel to work it out from other cells.';
        }
        var ast;
        try { ast = parseFormula(formula); } catch (e) { ast = null; }
        var ctx = { book: book, sheet: sheet, stack: [], scope: {}, dateMode: !!dateMode };
        var lines = [];
        lines.push('The equals sign is what makes this a formula. Without it, Excel stores the characters and does not calculate.');
        var refs = [];
        var refRe = /(?:[A-Za-z_][A-Za-z0-9_]*!)?\$?[A-Z]+\$?\d+/gi;
        var mm;
        var src = formula.replace(/"[^"]*"/g, '');
        while ((mm = refRe.exec(src))) {
            if (refs.indexOf(mm[0]) < 0) refs.push(mm[0]);
        }
        refs.forEach(function (ref) {
            var loc = addrOf(ref, sheet);
            var held = loc ? showVal(evalCell(loc.sheet, loc.addr, ctx)) : '';
            lines.push(describeRef(ref) + ' Right now it holds ' + (held === '' ? 'nothing' : held) + '.');
        });
        if (ast && ast.k === 'call') {
            lines.push(callStory(ast, ctx));
        } else if (ast && (ast.k === 'bin' || ast.k === 'cmp')) {
            lines.push(binStory(ast, ctx));
        }
        var live = evalAst(ast || { k: 'err', v: '#VALUE!' }, ctx);
        if (live && live.kind === 'arr') {
            var flat = live.rows.map(function (row) { return row.map(showVal).join(', '); }).join(' | ');
            lines.push('The result spills across ' + live.rows.length + ' row' + (live.rows.length === 1 ? '' : 's') + ': ' + flat + '. Those cells belong to this formula. Type in one of them and the spill is blocked.');
        } else if (live && live.kind === 'err' && live.v === '#CIRC!') {
            lines.push('This formula includes its own cell, so it has no finished answer. A total has to sit outside the rows it adds. The sheet shows no settled rupee figure until the range stops at the last purchase.');
        } else {
            lines.push('The result is ' + (showVal(live) || 'blank') + '. Change an input and press Enter. This cell follows, because it stores the recipe rather than a typed number.');
        }
        if (formula.indexOf('$') >= 0) {
            lines.push('A dollar sign freezes the part it sits in front of. $D$6 always means D6. D2 without dollars becomes D3 on the next row.');
        }
        return lines.join(' ');
    }

    function binStory(ast, ctx) {
        var a = evalAst(ast.a, ctx);
        var b = evalAst(ast.b, ctx);
        var word = { '+': 'add', '-': 'subtract', '*': 'multiply', '/': 'divide', '=': 'ask whether these are equal', '<>': 'ask whether these differ', '>': 'ask whether the first is greater', '<': 'ask whether the first is smaller', '>=': 'ask whether the first is at least', '<=': 'ask whether the first is at most' };
        var w = word[ast.op] || 'combine';
        return 'Excel will ' + w + ' ' + (showVal(a) || 'a blank') + ' and ' + (showVal(b) || 'a blank') + '. Parentheses, if you add them, run before multiply and divide, and those run before add and subtract.';
    }

    function callStory(ast, ctx) {
        var name = ast.name;
        if (name === 'SUM') return 'SUM adds every number in the range, including both ends. Text and blank cells are left out, so a quantity stored as a word does not join and does not raise a loud error. A real zero does join. The total cell must sit outside the range, or the formula depends on itself.';
        if (name === 'AVERAGE') return 'AVERAGE adds the numbers it can see and divides by how many numbers it found. A blank is left out, which shrinks the count. A zero is kept. If nothing qualifies, there is no average.';
        if (name === 'COUNT') return 'COUNT counts numbers and dates. It does not count words, so a text quantity is invisible to it.';
        if (name === 'COUNTA') return 'COUNTA counts cells that are not empty, including words. A space is an entry. The header should stay outside the range.';
        if (name === 'MIN' || name === 'MAX') return name + ' returns the ' + (name === 'MIN' ? 'smallest' : 'largest') + ' number in the range. It does not return the name beside that number. Leave the grand total outside the range, or the total wins.';
        if (name === 'ROUND') return 'ROUND changes the stored number to the decimals you ask for. A display format only hides extra decimals and can make a column of lines disagree with the total. Money that a person will add is usually rounded to 2.';
        if (name === 'IF') return 'IF asks the first argument as a yes-or-no question. If the answer is yes, the cell becomes the second argument. If the answer is no, it becomes the third. Text answers belong in quotes. Numbers you still want to add do not.';
        if (name === 'AND') return 'AND is true only when every test is true. It belongs inside IF when the sentence says both.';
        if (name === 'OR') return 'OR is true when any one test is true. It belongs inside IF when the sentence says either.';
        if (name === 'IFERROR') return 'IFERROR shows the second argument when the first one fails. Read the error before you hide it. A miss you expected, such as a lookup, can say Missing. A mistyped function should stay visible until you fix it.';
        if (name === 'SUMIF' || name === 'COUNTIF' || name === 'AVERAGEIF') return name + ' keeps the rows whose test cell matches, and ignores the other rows. The ranges must be the same height and start on the same row, so each test sits beside its own amount.';
        if (name === 'SUMIFS' || name === 'COUNTIFS' || name === 'AVERAGEIFS') return name + ' keeps a row only when every test passes on that same row. ' + (name === 'COUNTIFS' ? 'The tests come in pairs and there is no amount to add, because each passing row counts as one.' : 'The amount range is written first, then each test range and its test.');
        if (name === 'SUMPRODUCT') return 'SUMPRODUCT multiplies the pairs that share a position, then adds those products. B2 multiplies with C2, not with C3. The ranges have to be the same shape.';
        if (name === 'XLOOKUP') return 'XLOOKUP looks down the column you point at, finds the key, and returns the cell on that same row from the column you name. It does not count columns, so inserting a notes column does not move the answer. The last argument is what to show when the key is missing. Zero is a poor stand-in for an unknown rate.';
        if (name === 'VLOOKUP') return 'VLOOKUP finds the key in the first column of a block and counts across to the column number you give, starting at 1. FALSE means an exact match, which is what a food name needs. An inserted column changes that count without an error.';
        if (name === 'INDEX') return 'INDEX returns the cell at a row position, and a column position if you give one, inside the range you name.';
        if (name === 'MATCH') return 'MATCH returns the position of the key, not the rate. The 0 asks for an exact match. INDEX then uses that position to read a different column.';
        if (name === 'FILTER') return 'FILTER spills the rows that pass, and leaves the source list where it is. The test has to be one true-or-false for each row. A total of the spill is only the matching rows, so its label should say so.';
        if (name === 'SORT') return 'SORT spills a reordered copy. The source rows stay in the order you entered. The column index counts inside the range you gave it, and -1 means largest first.';
        if (name === 'UNIQUE') return 'UNIQUE spills each distinct key once. The repeated purchases stay on the source list. It does not add the rupees. Pair it with SUMIF or a pivot when you need the money.';
        if (name === 'TEXT') return 'TEXT turns a date or number into words for a label. The result is text, so it will not add or subtract later. Keep the real date in its own cell.';
        if (name === 'TEXTJOIN') return 'TEXTJOIN stitches cells into one line with the separator you choose. TRUE skips blanks. The columns underneath should stay, because the joined cell is text and cannot be the quantity you sum.';
        if (name === 'LET') return 'LET gives a name to a piece of this formula so you can read it. The name lives only inside this formula. A rate the whole sheet shares should still be a cell, and the name should point at that cell.';
        if (name === 'LEFT' || name === 'RIGHT' || name === 'MID') return name + ' cuts characters. Positions start at 1. Use it only when every code has the same shape. If the pieces are real fields, split them into columns instead.';
        if (name === 'TRIM') return 'TRIM removes spaces you did not mean to type, including the invisible one at the end of a name that makes a lookup miss.';
        return name + ' calculates from the arguments in the parentheses. The cells it names are listed above, with the values they hold now.';
    }

    var REF_RE = /((?:[A-Za-z_][A-Za-z0-9_]*!)?)(\$?)([A-Za-z]+)(\$?)(\d+)/g;

    function shiftFormula(formula, dRow, dCol) {
        return String(formula).replace(new RegExp(REF_RE.source, 'g'), function (all, sheet, cLock, col, rLock, row) {
            if (sheet) return all;
            var c = colToN(col);
            var r = parseInt(row, 10);
            if (!cLock) c += dCol;
            if (!rLock) r += dRow;
            if (c < 1 || r < 1) return all;
            return cLock + nToCol(c) + rLock + r;
        });
    }

    function matchesExpect(val, expect) {
        var e = String(expect == null ? '' : expect).trim();
        if (e === '') return !val || val.kind === 'blank' || showVal(val) === '';
        if (showVal(val) === e) return true;
        if (e === '###') return !!(val && val.kind === 'num');
        if (/^rs\b/i.test(e) && val && val.kind === 'num') {
            var n = parseFloat(e.replace(/[^0-9.]/g, ''));
            return isFinite(n) && Math.abs(val.v - n) < 0.05;
        }
        if (/%$/.test(e) && val && val.kind === 'num') {
            var body = e.slice(0, -1);
            var d = (body.split('.')[1] || '').length;
            if ((val.v * 100).toFixed(d) + '%' === e) return true;
            if (String(Math.round(val.v * 100)) + '%' === e) return true;
        }
        if (val && val.kind === 'num' && /^-?\d+(\.\d+)?$/.test(e)) {
            var dec = (e.split('.')[1] || '').length;
            return Math.abs(Number(val.v.toFixed(dec)) - parseFloat(e)) < 1e-6;
        }
        return false;
    }

    function preprocessFormula(formula, addr, headers) {
        var row = '2';
        var rm = String(addr || '').match(/(\d+)$/);
        if (rm) row = rm[1];
        return String(formula).replace(/\[@\s*([^\]]+?)\s*\]/g, function (_, name) {
            var col = headers[String(name).trim().toLowerCase()];
            return col ? col + row : '"' + String(name).replace(/"/g, '') + '"';
        }).replace(/[A-Za-z_][A-Za-z0-9_]*\[\s*([^\]]+?)\s*\]/g, function (_, name) {
            return 'COL("' + String(name).trim().replace(/"/g, '') + '")';
        });
    }

    function eachLocalRef(text, fn) {
        var re = new RegExp(REF_RE.source, 'g');
        var m;
        while ((m = re.exec(String(text)))) {
            if (m[1]) continue;
            fn(m[3].toUpperCase(), parseInt(m[5], 10));
        }
    }

    function rewriteRefs(text, colMap, rowMap) {
        return String(text).replace(new RegExp(REF_RE.source, 'g'), function (all, sheet, cl, col, rl, row) {
            if (sheet) return all;
            var c = col.toUpperCase();
            var r = String(parseInt(row, 10));
            if (colMap[c]) c = colMap[c];
            if (rowMap[r]) r = String(rowMap[r]);
            return cl + c + rl + r;
        });
    }

    function fitFormula(formula, dest, letters, cells, maxRow) {
        var have = {};
        letters.forEach(function (L) { have[L] = true; });
        var mentioned = {};
        eachLocalRef(formula, function (c) { mentioned[c] = true; });
        if (dest) eachLocalRef(dest, function (c) { mentioned[c] = true; });
        var missing = [];
        var used = {};
        Object.keys(mentioned).forEach(function (c) {
            if (have[c]) used[c] = true;
            else missing.push(c);
        });
        missing.sort(function (a, b) { return colToN(a) - colToN(b); });
        var kinds = {};
        letters.forEach(function (L) {
            var nums = 0;
            var texts = 0;
            for (var r = 2; r <= maxRow; r++) {
                var lab = cells['A' + r];
                if (lab && /^(total|average|result|count|max|min|sum)$/i.test(String(lab.raw).trim())) continue;
                var cell = cells[L + r];
                if (!cell) continue;
                var raw = String(cell.raw).trim();
                if (!raw) continue;
                if (/^-?\d/.test(raw) || /%$/.test(raw) || /^rs\b/i.test(raw) || raw === '###') nums++;
                else texts++;
            }
            kinds[L] = nums > 0 && nums >= texts ? 'num' : 'text';
        });
        var unusedNum = [];
        var unusedAny = [];
        letters.forEach(function (L) {
            if (used[L]) return;
            unusedAny.push(L);
            if (kinds[L] === 'num') unusedNum.push(L);
        });
        var colMap = {};
        if (missing.length) {
            var pool = unusedNum.length >= missing.length ? unusedNum.slice() : unusedAny.slice();
            if (pool.length > missing.length && pool[0] === 'A' && kinds.A !== 'num') {
                var trimmed = pool.slice(1);
                if (trimmed.length >= missing.length) pool = trimmed;
            }
            if (pool.length >= missing.length) {
                var slice = pool.slice(pool.length - missing.length);
                missing.forEach(function (c, i) { colMap[c] = slice[i]; });
            }
        }
        var rowMap = {};
        var lastLab = cells['A' + maxRow];
        if (lastLab && /^(total|average|result|count|max|min|sum)$/i.test(String(lastLab.raw).trim())) {
            var seenRows = {};
            eachLocalRef(formula, function (_c, r) { seenRows[r] = true; });
            if (dest) eachLocalRef(dest, function (_c, r) { seenRows[r] = true; });
            Object.keys(seenRows).forEach(function (r) {
                if (parseInt(r, 10) > maxRow) rowMap[r] = maxRow;
            });
        }
        return {
            formula: rewriteRefs(formula, colMap, rowMap),
            dest: dest ? rewriteRefs(dest, colMap, rowMap) : dest,
            colMap: colMap,
            rowMap: rowMap
        };
    }

    function mappingStory(entry, mapped, colMap, rowMap) {
        if (entry === mapped) return '';
        var bits = [];
        Object.keys(colMap).sort(function (a, b) { return colToN(a) - colToN(b); }).forEach(function (from) {
            bits.push('column ' + from + ' in the lesson is column ' + colMap[from] + ' here');
        });
        Object.keys(rowMap).forEach(function (from) {
            bits.push('row ' + from + ' in the lesson is the total row here, row ' + rowMap[from]);
        });
        if (!bits.length) return '';
        return 'The lesson writes ' + entry + '. On the full April file the columns run Item, Qty, Rate, Line. This grid keeps the columns the story needs, so ' + bits.join(', and ') + '. The values below are the cells you can edit. ';
    }

    function enhanceExcelSheets(container) {
        if (!container) return;
        var visuals = container.querySelectorAll('.nd-visual');
        Array.prototype.forEach.call(visuals, enhanceOne);
    }

    function enhanceOne(visual) {
        if (!visual || visual.getAttribute('data-xl-ready') === '1') return;
        if ((visual.textContent || '').indexOf('EXCEL STUDIO') < 0) return;
        var table = visual.querySelector('table');
        if (!table || table.rows.length < 2) return;
        var fxBadge = null;
        var divs = visual.querySelectorAll('div');
        for (var i = 0; i < divs.length; i++) {
            if (divs[i].children.length === 0 && divs[i].textContent.trim() === 'fx') { fxBadge = divs[i]; break; }
        }
        if (!fxBadge || !fxBadge.parentNode) return;
        var barRow = fxBadge.parentNode;
        var nameEl = barRow.children[0];
        var fxEl = barRow.children[2];
        if (!nameEl || !fxEl) return;
        visual.setAttribute('data-xl-ready', '1');

        var main = visual.querySelector('[data-fx]');
        var sheetName = (main && main.getAttribute('data-sheet')) || 'Spend';
        var lessonAddr = ((main && main.getAttribute('data-cell')) || nameEl.textContent || '').trim().toUpperCase();
        var lessonFx = ((main && main.getAttribute('data-fx')) || fxEl.textContent || '').trim();

        var letters = [];
        var head = table.rows[0];
        for (var c = 1; c < head.cells.length; c++) letters.push(head.cells[c].textContent.trim().toUpperCase());

        var cells = {};
        var order = [];
        var maxRow = 1;
        for (var r = 1; r < table.rows.length; r++) {
            var rowNum = parseInt(table.rows[r].cells[0].textContent.trim(), 10);
            if (!rowNum) continue;
            if (rowNum > maxRow) maxRow = rowNum;
            for (var c2 = 1; c2 < table.rows[r].cells.length && c2 - 1 < letters.length; c2++) {
                var addr = letters[c2 - 1] + rowNum;
                var td = table.rows[r].cells[c2];
                var shown = td.textContent.trim();
                cells[addr] = {
                    el: td,
                    raw: shown,
                    expect: shown,
                    hint: hintOf(shown),
                    spill: false,
                    formula: false,
                    entry: shown,
                    showFormula: false,
                    textNum: false,
                    owned: false,
                    colMap: {},
                    rowMap: {}
                };
                order.push(addr);
                td.setAttribute('data-xl', addr);
                td.contentEditable = 'true';
                td.setAttribute('spellcheck', 'false');
                td.style.outline = 'none';
                td.style.minWidth = '36px';
                td.style.cursor = 'text';
            }
        }

        var headers = {};
        letters.forEach(function (L) {
            if (cells[L + '1']) headers[String(cells[L + '1'].raw).trim().toLowerCase()] = L;
        });

        var whatIfValue = '0';
        var primary = null;

        function bookValue(cell) {
            var raw = String(cell.raw == null ? '' : cell.raw);
            if (cell.formula) return raw;
            if (raw.trim().charAt(0) === '=') return "'" + raw.trim();
            return raw;
        }

        function currentBook() {
            var book = {};
            book[sheetName] = {};
            Object.keys(cells).forEach(function (addr) {
                if (cells[addr].spill) return;
                book[sheetName][addr] = bookValue(cells[addr]);
            });
            book.Prices = Object.assign({}, PRICES);
            book.WhatIf = { A1: 'Scratch', B1: 'Extra', A2: 'What-if', B2: whatIfValue };
            var inputs = priceBox ? priceBox.querySelectorAll('[data-paddr]') : [];
            Array.prototype.forEach.call(inputs, function (input) {
                book.Prices[input.getAttribute('data-paddr')] = input.value;
            });
            var winput = whatBox ? whatBox.querySelector('[data-waddr]') : null;
            if (winput) {
                whatIfValue = winput.value;
                book.WhatIf.B2 = whatIfValue;
            }
            return book;
        }

        function bindEntry(addr, entry) {
            if (!cells[addr] || String(entry).trim().charAt(0) !== '=') return null;
            var ready = preprocessFormula(entry, addr, headers);
            var fit = fitFormula(ready, lessonAddr && cells[addr] === cells[primary] ? lessonAddr : addr, letters, cells, maxRow);
            if (addr === primary || !primary) {
                fit = fitFormula(ready, lessonAddr, letters, cells, maxRow);
            }
            var dest = fit.dest && cells[fit.dest] ? fit.dest : addr;
            if (!cells[dest]) return fit;
            cells[dest].formula = true;
            cells[dest].owned = true;
            cells[dest].entry = entry;
            cells[dest].raw = fit.formula;
            cells[dest].colMap = fit.colMap;
            cells[dest].rowMap = fit.rowMap;
            cells[dest].showFormula = String(cells[dest].expect).trim().charAt(0) === '=';
            return { dest: dest, fit: fit };
        }

        if (lessonFx.charAt(0) === '=') {
            var firstReady = preprocessFormula(lessonFx, lessonAddr, headers);
            var firstFit = fitFormula(firstReady, lessonAddr, letters, cells, maxRow);
            var dest0 = firstFit.dest && cells[firstFit.dest] ? firstFit.dest : '';
            if (!dest0) {
                var probeBook = {};
                probeBook[sheetName] = {};
                Object.keys(cells).forEach(function (addr) {
                    var raw = String(cells[addr].raw);
                    probeBook[sheetName][addr] = raw.charAt(0) === '=' ? "'" + raw : raw;
                });
                probeBook.Prices = Object.assign({}, PRICES);
                probeBook.WhatIf = { B2: '0' };
                var probe = calculate(probeBook, sheetName, firstFit.dest || lessonAddr, true);
                if (!probe || probe.kind === 'err') {
                    probe = { kind: 'blank', v: '' };
                    try {
                        var ctxP = { book: probeBook, sheet: sheetName, stack: [], scope: {}, dateMode: true };
                        probe = evalAst(parseFormula(firstFit.formula), ctxP);
                    } catch (err) { probe = { kind: 'err', v: '#VALUE!' }; }
                }
                var found = '';
                var foundRow = -1;
                Object.keys(cells).forEach(function (addr) {
                    if (!matchesExpect(probe, cells[addr].expect)) return;
                    var rw = parseInt(String(addr).replace(/^[A-Z]+/, ''), 10) || 0;
                    if (rw >= foundRow) { found = addr; foundRow = rw; }
                });
                dest0 = found;
            }
            if (dest0 && cells[dest0]) {
                primary = dest0;
                cells[dest0].formula = true;
                cells[dest0].owned = true;
                cells[dest0].entry = lessonFx;
                cells[dest0].raw = firstFit.formula;
                cells[dest0].colMap = firstFit.colMap;
                cells[dest0].rowMap = firstFit.rowMap;
                cells[dest0].showFormula = String(cells[dest0].expect).trim().charAt(0) === '=';
                var accept = cells[dest0].showFormula || matchesExpect(
                    calculate((function () {
                        var b = {};
                        b[sheetName] = {};
                        Object.keys(cells).forEach(function (addr) {
                            var raw = String(cells[addr].raw);
                            b[sheetName][addr] = cells[addr].formula ? raw : (raw.charAt(0) === '=' ? "'" + raw : raw);
                        });
                        b.Prices = Object.assign({}, PRICES);
                        b.WhatIf = { B2: whatIfValue };
                        return b;
                    })(), sheetName, dest0, String(cells[dest0].expect).trim().charAt(0) !== '#'),
                    cells[dest0].expect
                );
                var expect0 = String(cells[dest0].expect).trim();
                var numericFace = /^-?\d/.test(expect0) || /%$/.test(expect0) || /^rs\b/i.test(expect0) || expect0 === '###' || expect0.charAt(0) === '#' || expect0.charAt(0) === '=';
                if (!accept && !numericFace) {
                    cells[dest0].formula = false;
                    cells[dest0].owned = false;
                    cells[dest0].raw = cells[dest0].expect;
                    primary = null;
                } else if (primary && /WhatIf!/i.test(lessonFx)) {
                    var boot = {};
                    boot[sheetName] = {};
                    Object.keys(cells).forEach(function (addr) {
                        var raw = String(cells[addr].raw);
                        boot[sheetName][addr] = cells[addr].formula ? raw : (raw.charAt(0) === '=' ? "'" + raw : raw);
                    });
                    boot.Prices = Object.assign({}, PRICES);
                    boot.WhatIf = { B2: '0' };
                    var gotW = calculate(boot, sheetName, primary, true);
                    var targetW = parseFloat(String(cells[primary].expect).replace(/[^0-9.-]/g, ''));
                    if (gotW && gotW.kind === 'num' && isFinite(targetW)) whatIfValue = trimNum(targetW - gotW.v);
                }
            }
        } else if (lessonAddr && cells[lessonAddr] && lessonFx && lessonFx !== cells[lessonAddr].expect) {
            cells[lessonAddr].raw = lessonFx;
        }

        if (primary && cells[primary]) {
            var bootBook = {};
            bootBook[sheetName] = {};
            Object.keys(cells).forEach(function (addr) {
                var raw = String(cells[addr].raw);
                bootBook[sheetName][addr] = cells[addr].formula ? raw : (raw.charAt(0) === '=' ? "'" + raw : raw);
            });
            bootBook.Prices = Object.assign({}, PRICES);
            bootBook.WhatIf = { B2: whatIfValue };
            var dateBoot = String(cells[primary].expect).charAt(0) !== '#';
            var gotBoot = calculate(bootBook, sheetName, primary, dateBoot);
            if (gotBoot && gotBoot.kind === 'num' && !matchesExpect(gotBoot, cells[primary].expect)) {
                var nums = Object.keys(cells).filter(function (addr) {
                    return addr !== primary && /^-?\d+(\.\d+)?$/.test(String(cells[addr].raw).trim());
                });
                var winners = [];
                nums.forEach(function (addr) {
                    var saved = bootBook[sheetName][addr];
                    bootBook[sheetName][addr] = "'" + saved;
                    var g2 = calculate(bootBook, sheetName, primary, dateBoot);
                    if (matchesExpect(g2, cells[primary].expect)) winners.push(addr);
                    bootBook[sheetName][addr] = saved;
                });
                if (winners.length === 1) {
                    cells[winners[0]].raw = "'" + String(cells[winners[0]].raw).replace(/^'/, '');
                    cells[winners[0]].textNum = true;
                }
            }
        }

        var dateMode = true;
        if (primary && cells[primary] && String(cells[primary].expect).charAt(0) === '#') dateMode = false;
        if (!primary && String(lessonFx).charAt(0) === '=' && cells[lessonAddr] && String(cells[lessonAddr].expect).charAt(0) === '#') dateMode = false;

        var snapshot = null;
        function takeSnapshot() {
            var snap = { dateMode: dateMode, whatIf: whatIfValue, cells: {}, prices: Object.assign({}, PRICES) };
            Object.keys(cells).forEach(function (addr) {
                snap.cells[addr] = {
                    raw: cells[addr].raw,
                    formula: cells[addr].formula,
                    entry: cells[addr].entry,
                    showFormula: cells[addr].showFormula,
                    textNum: cells[addr].textNum,
                    owned: cells[addr].owned
                };
            });
            return JSON.stringify(snap);
        }

        var panel = document.createElement('div');
        panel.style.marginTop = '10px';
        panel.innerHTML = ''
            + '<div class="xl-read" style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:10px 12px;color:#14532d;font-size:13px;line-height:1.55;font-weight:600;"></div>'
            + '<div style="margin-top:6px;font-size:12px;font-weight:700;color:#166534;">Click a cell, type, and press Enter. The formula cell follows.</div>'
            + '<div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:8px;align-items:center;">'
            + '<button type="button" class="xl-check" style="background:#107c41;color:#fff;border:0;border-radius:8px;padding:7px 12px;font-weight:800;font-size:12px;cursor:pointer;">Check the sheet</button>'
            + '<button type="button" class="xl-reset" style="background:#ffffff;color:#14532d;border:1px solid #86efac;border-radius:8px;padding:7px 12px;font-weight:800;font-size:12px;cursor:pointer;">Reset</button>'
            + '<button type="button" class="xl-fill" style="background:#ffffff;color:#14532d;border:1px solid #86efac;border-radius:8px;padding:7px 12px;font-weight:800;font-size:12px;cursor:pointer;">Fill formula down</button>'
            + '<button type="button" class="xl-prices" style="background:#ffffff;color:#14532d;border:1px solid #86efac;border-radius:8px;padding:7px 12px;font-weight:800;font-size:12px;cursor:pointer;">Prices sheet</button>'
            + '<label style="display:flex;align-items:center;gap:6px;font-size:12px;font-weight:700;color:#166534;"><input type="checkbox" class="xl-dates"> Read date-looking cells as real dates</label>'
            + '</div>'
            + '<div class="xl-status" style="margin-top:8px;font-size:13px;font-weight:700;color:#14532d;"></div>'
            + '<div class="xl-pricebox" style="display:none;margin-top:8px;"></div>'
            + '<div class="xl-whatbox" style="display:none;margin-top:8px;"></div>';
        var host = table.parentNode;
        host.insertBefore(panel, table.nextSibling);
        var readEl = panel.querySelector('.xl-read');
        var statusEl = panel.querySelector('.xl-status');
        var dateBox = panel.querySelector('.xl-dates');
        var priceBox = panel.querySelector('.xl-pricebox');
        var whatBox = panel.querySelector('.xl-whatbox');
        dateBox.checked = dateMode;
        var pricesOpen = /Prices!/i.test(lessonFx);
        var whatOpen = /WhatIf!/i.test(lessonFx);
        var selected = primary || (cells[lessonAddr] ? lessonAddr : order[0]);
        var dirty = false;

        function paintPrices() {
            if (!pricesOpen) { priceBox.style.display = 'none'; return; }
            var book = currentBook();
            var html = '<div style="font-size:12px;font-weight:800;color:#166534;margin-bottom:4px;">Prices — the rate card a lookup reads</div><table style="border-collapse:collapse;font-size:13px;background:#fff;">';
            [1, 2, 3, 4, 5].forEach(function (rn) {
                html += '<tr>';
                ['A', 'B'].forEach(function (col) {
                    var addr = col + rn;
                    var val = book.Prices[addr] != null ? book.Prices[addr] : '';
                    html += '<td style="border:1px solid #d1d5db;padding:4px 8px;"><input data-paddr="' + addr + '" value="' + String(val).replace(/"/g, '&quot;') + '" style="width:88px;border:0;font:inherit;font-weight:700;color:#111827;background:#fff;"></td>';
                });
                html += '</tr>';
            });
            html += '</table>';
            priceBox.innerHTML = html;
            priceBox.style.display = 'block';
            Array.prototype.forEach.call(priceBox.querySelectorAll('input'), function (input) {
                input.addEventListener('input', function () { dirty = true; recalc(); });
            });
        }

        function paintWhat() {
            if (!whatOpen) { whatBox.style.display = 'none'; return; }
            whatBox.innerHTML = '<div style="font-size:12px;font-weight:800;color:#166534;margin-bottom:4px;">WhatIf!B2 — the extra cell on the scratch sheet</div>'
                + '<input data-waddr="B2" value="' + String(whatIfValue).replace(/"/g, '&quot;') + '" style="width:120px;border:1px solid #86efac;border-radius:8px;padding:6px 8px;font-weight:800;color:#14532d;">';
            whatBox.style.display = 'block';
            whatBox.querySelector('input').addEventListener('input', function (ev) {
                whatIfValue = ev.target.value;
                dirty = true;
                recalc();
            });
        }

        function storyFor(addr) {
            var cell = cells[addr];
            if (!cell) return '';
            if (cell.textNum && !cell.formula) {
                return addr + ' shows ' + String(cell.raw).replace(/^'/, '') + ', and it is stored as text. SUM and AVERAGE skip text, even when the characters look like a number. Type the number again and press Enter if you want it to join the total.';
            }
            if (!cell.formula) {
                if (String(cell.raw).trim().charAt(0) === '=') {
                    return addr + ' is showing a formula as text. Show Formulas is a way to read the sheet. This cell is not calculating until you are in the formula cell and the recipes are real formulas.';
                }
                return explainFormula(String(cell.raw), currentBook(), sheetName, dateBox.checked);
            }
            var book = currentBook();
            var preface = mappingStory(cell.entry, cell.raw, cell.colMap || {}, cell.rowMap || {});
            var body = explainFormula(cell.raw, book, sheetName, dateBox.checked);
            if (cell.showFormula) {
                preface += 'Show Formulas is on, so this cell displays the recipe instead of the rupees. ';
            }
            var live = calculate(book, sheetName, addr, dateBox.checked);
            if (!dirty && cell.expect && live && live.kind === 'num' && !matchesExpect(live, cell.expect)) {
                body += ' The lesson picture still shows ' + cell.expect + '. The cells as stored calculate to ' + showVal(live) + '. A format can hide a fraction, and a typed total can disagree with the lines. Edit a line and this cell will follow the stored numbers.';
            }
            return preface + body;
        }

        function paintAddr(addr, val) {
            var cell = cells[addr];
            if (!cell || document.activeElement === cell.el) return;
            if (cell.spill && !cell.formula) {
                cell.el.textContent = showVal(val);
                return;
            }
            if (!cell.formula) {
                cell.el.textContent = dirty ? String(cell.raw).replace(/^'/, '') : cell.expect;
                return;
            }
            if (cell.showFormula) {
                cell.el.textContent = cell.entry;
                return;
            }
            if (!dirty && cell.hint === 'hash') {
                cell.el.textContent = '###';
                return;
            }
            if (!dirty && cell.expect) {
                cell.el.textContent = cell.expect;
                return;
            }
            var hint = cell.hint === 'hash' ? '' : cell.hint;
            cell.el.textContent = showHint(val, hint, cell.expect);
        }

        function recalc() {
            Object.keys(cells).forEach(function (addr) { cells[addr].spill = false; });
            var book = currentBook();
            order.forEach(function (addr) {
                if (!cells[addr].formula) return;
                var ctx = { book: currentBook(), sheet: sheetName, stack: [], scope: {}, dateMode: dateBox.checked };
                var val = evalCell(sheetName, addr, ctx);
                if (val && val.kind === 'arr') {
                    var origin = splitRef(addr);
                    var blocked = false;
                    var targets = [];
                    for (var ri = 0; ri < val.rows.length && !blocked; ri++) {
                        for (var ci = 0; ci < val.rows[ri].length; ci++) {
                            var target = nToCol(colToN(origin.col) + ci) + (origin.row + ri);
                            targets.push({ addr: target, piece: val.rows[ri][ci] });
                            if (target !== addr) {
                                var host = cells[target];
                                if (!host) { blocked = true; break; }
                                var raw = String(host.raw).replace(/^'/, '').trim();
                                if (raw && !host.spill && !(matchesExpect(val.rows[ri][ci], raw) || showVal(val.rows[ri][ci]) === raw) && !(host.formula && host.owned && host.entry)) {
                                    if (!(host === cells[addr])) blocked = true;
                                }
                            }
                        }
                    }
                    if (blocked) {
                        if (document.activeElement !== cells[addr].el) cells[addr].el.textContent = '#SPILL!';
                        return;
                    }
                    targets.forEach(function (t) {
                        if (!cells[t.addr]) return;
                        if (t.addr !== addr) cells[t.addr].spill = true;
                        if (document.activeElement !== cells[t.addr].el) {
                            cells[t.addr].el.textContent = (!dirty && cells[t.addr].expect && t.addr === addr) ? cells[t.addr].expect : showVal(t.piece);
                        }
                    });
                    return;
                }
                paintAddr(addr, val);
            });
            order.forEach(function (addr) {
                if (cells[addr].formula || cells[addr].spill) return;
                paintAddr(addr, null);
            });
            if (selected && cells[selected] && document.activeElement !== fxEl) {
                var showBar = cells[selected].formula ? cells[selected].entry : String(cells[selected].raw).replace(/^'/, '');
                fxEl.textContent = showBar;
                nameEl.textContent = selected;
            }
            if (selected && cells[selected]) readEl.textContent = storyFor(selected);
        }

        function selectCell(addr) {
            if (!cells[addr]) return;
            if (selected && cells[selected]) cells[selected].el.style.boxShadow = '';
            selected = addr;
            cells[addr].el.style.boxShadow = 'inset 0 0 0 2px #107c41';
            nameEl.textContent = addr;
            fxEl.textContent = cells[addr].formula ? cells[addr].entry : String(cells[addr].raw).replace(/^'/, '');
            readEl.textContent = storyFor(addr);
        }

        function refitOwned(addr, entry) {
            var ready = preprocessFormula(entry, addr, headers);
            var destForFit = (addr === primary) ? lessonAddr : addr;
            var fit = fitFormula(ready, destForFit, letters, cells, maxRow);
            var dest = fit.dest && cells[fit.dest] ? fit.dest : addr;
            if (addr === primary && fit.dest && cells[fit.dest]) dest = fit.dest;
            if (!cells[dest]) dest = addr;
            if (dest !== addr && cells[addr] && cells[addr].owned && addr !== dest) {
                cells[addr].formula = false;
                cells[addr].owned = false;
                if (cells[addr].raw === cells[addr].entry || String(cells[addr].raw).charAt(0) === '=') cells[addr].raw = cells[addr].expect;
            }
            cells[dest].formula = true;
            cells[dest].owned = true;
            cells[dest].entry = entry;
            cells[dest].raw = fit.formula;
            cells[dest].colMap = fit.colMap;
            cells[dest].rowMap = fit.rowMap;
            if (dest === primary) cells[dest].showFormula = String(cells[dest].expect).trim().charAt(0) === '=' && entry === lessonFx;
            else cells[dest].showFormula = false;
            if (addr === primary || dest === primary) primary = dest;
            return dest;
        }

        function commitEl(el) {
            var addr = el.getAttribute('data-xl');
            if (!addr || !cells[addr]) return;
            var typed = el.textContent.replace(/\u00a0/g, ' ').trim();
            var cell = cells[addr];
            if (cell.textNum && typed === String(cell.raw).replace(/^'/, '')) {
                recalc();
                selectCell(addr);
                return;
            }
            if (cell.formula && typed === cell.entry) {
                recalc();
                selectCell(addr);
                return;
            }
            if (!cell.formula && typed === String(cell.raw).replace(/^'/, '')) {
                recalc();
                selectCell(addr);
                return;
            }
            dirty = true;
            cell.textNum = false;
            if (typed.charAt(0) === '=') {
                var landed = refitOwned(addr, typed);
                recalc();
                selectCell(landed || addr);
                return;
            }
            cell.formula = false;
            cell.owned = false;
            cell.showFormula = false;
            cell.entry = typed;
            cell.raw = typed;
            if (addr === primary) primary = null;
            recalc();
            selectCell(addr);
        }

        order.forEach(function (addr) {
            var el = cells[addr].el;
            el.addEventListener('focus', function () {
                selectCell(addr);
                if (cells[addr].formula) el.textContent = cells[addr].entry;
                else el.textContent = String(cells[addr].raw).replace(/^'/, '');
            });
            el.addEventListener('blur', function () { commitEl(el); });
            el.addEventListener('keydown', function (ev) {
                if (ev.key === 'Enter') { ev.preventDefault(); el.blur(); }
            });
        });

        fxEl.contentEditable = 'true';
        fxEl.setAttribute('spellcheck', 'false');
        function commitBar() {
            if (!selected || !cells[selected]) return;
            var typed = fxEl.textContent.replace(/\u00a0/g, ' ').trim();
            dirty = true;
            if (typed.charAt(0) === '=') {
                var landed = refitOwned(selected, typed);
                recalc();
                selectCell(landed || selected);
                return;
            }
            cells[selected].textNum = false;
            cells[selected].formula = false;
            cells[selected].owned = false;
            cells[selected].showFormula = false;
            cells[selected].entry = typed;
            cells[selected].raw = typed;
            if (selected === primary) primary = null;
            recalc();
            selectCell(selected);
        }
        fxEl.addEventListener('keydown', function (ev) {
            if (ev.key === 'Enter') { ev.preventDefault(); commitBar(); }
        });
        fxEl.addEventListener('blur', function () {
            if (!selected || !cells[selected]) return;
            var typed = fxEl.textContent.replace(/\u00a0/g, ' ').trim();
            var current = cells[selected].formula ? cells[selected].entry : String(cells[selected].raw).replace(/^'/, '');
            if (typed !== current) commitBar();
        });

        panel.querySelector('.xl-check').addEventListener('click', function () {
            recalc();
            var now = takeSnapshot();
            now = JSON.parse(now);
            now.dateMode = dateBox.checked;
            now.whatIf = whatIfValue;
            var start = JSON.parse(snapshot);
            var same = JSON.stringify(now.cells) === JSON.stringify(start.cells) && now.dateMode === start.dateMode && String(now.whatIf) === String(start.whatIf);
            if (same && !dirty) {
                statusEl.textContent = 'Matches the lesson. The cells are still the ones this page started with.';
                statusEl.style.color = '#166534';
                return;
            }
            if (primary && cells[primary] && cells[primary].formula && String(cells[primary].entry).charAt(0) !== '=') {
                statusEl.textContent = 'The lesson cell no longer starts with an equals sign, so it will not calculate. Put the formula back, or press Reset.';
                statusEl.style.color = '#9a3412';
                return;
            }
            var focus = selected && cells[selected] ? selected : primary;
            var shown = focus && cells[focus] ? cells[focus].el.textContent.trim() : '';
            statusEl.textContent = 'Your sheet is calculating. ' + (focus || 'The cell') + ' now shows ' + (shown || 'a blank') + '. Reset returns the lesson figures.';
            statusEl.style.color = '#14532d';
        });

        panel.querySelector('.xl-reset').addEventListener('click', function () {
            var again = JSON.parse(snapshot);
            Object.keys(cells).forEach(function (addr) {
                var saved = again.cells[addr];
                if (!saved) return;
                cells[addr].raw = saved.raw;
                cells[addr].formula = saved.formula;
                cells[addr].entry = saved.entry;
                cells[addr].showFormula = saved.showFormula;
                cells[addr].textNum = saved.textNum;
                cells[addr].owned = saved.owned;
                cells[addr].spill = false;
            });
            whatIfValue = again.whatIf;
            dateBox.checked = again.dateMode;
            dirty = false;
            primary = null;
            Object.keys(cells).forEach(function (addr) { if (cells[addr].owned && cells[addr].entry === lessonFx) primary = addr; });
            pricesOpen = /Prices!/i.test(lessonFx);
            whatOpen = /WhatIf!/i.test(lessonFx);
            paintPrices();
            paintWhat();
            recalc();
            statusEl.textContent = 'Restored the lesson.';
            statusEl.style.color = '#166534';
            var back = primary || (cells[lessonAddr] ? lessonAddr : order[0]);
            if (back && cells[back]) selectCell(back);
        });

        panel.querySelector('.xl-fill').addEventListener('click', function () {
            if (!selected || !cells[selected] || !cells[selected].formula) {
                statusEl.textContent = 'Select a cell whose formula starts with = before you fill it down.';
                statusEl.style.color = '#9a3412';
                return;
            }
            var origin = splitRef(selected);
            if (!origin || !cells[origin.col + (origin.row + 1)]) {
                statusEl.textContent = 'There is no row under this cell to fill.';
                statusEl.style.color = '#9a3412';
                return;
            }
            var guard = 0;
            var row = origin.row + 1;
            var entry = cells[selected].entry;
            while (cells[origin.col + row] && guard < 12) {
                var dest = origin.col + row;
                var shifted = shiftFormula(entry, row - origin.row, 0);
                var fit = fitFormula(preprocessFormula(shifted, dest, headers), dest, letters, cells, maxRow);
                cells[dest].formula = true;
                cells[dest].owned = true;
                cells[dest].entry = shifted;
                cells[dest].raw = fit.formula;
                cells[dest].colMap = fit.colMap;
                cells[dest].rowMap = fit.rowMap;
                cells[dest].showFormula = false;
                cells[dest].textNum = false;
                row++;
                guard++;
            }
            dirty = true;
            recalc();
            statusEl.textContent = 'Filled the formula down. Relative addresses walked. Locked addresses stayed.';
            statusEl.style.color = '#166534';
        });

        panel.querySelector('.xl-prices').addEventListener('click', function () {
            pricesOpen = !pricesOpen;
            paintPrices();
        });
        dateBox.addEventListener('change', function () { dirty = true; recalc(); });

        snapshot = takeSnapshot();
        paintPrices();
        paintWhat();
        recalc();
        if (selected && cells[selected]) selectCell(selected);
    }

    root.enhanceExcelSheets = enhanceExcelSheets;
    root.NectraExcel = {
        calculate: calculate,
        explainFormula: explainFormula,
        parseFormula: parseFormula,
        fitFormula: fitFormula,
        matchesExpect: matchesExpect,
        shiftFormula: shiftFormula
    };

    if (typeof document !== 'undefined') {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function () {
                var area = document.getElementById('dynamic-area');
                if (area) enhanceExcelSheets(area);
            });
        }
    }

    if (typeof process !== 'undefined' && process.argv && process.argv[1] && /excel-sheet\.js$/.test(String(process.argv[1]).replace(/\\/g, '/'))) {
        var fails = 0;
        function check(name, cond) {
            if (!cond) { console.log('FAIL ' + name); fails++; }
            else console.log('ok ' + name);
        }
        var prices = { A1: 'Item', B1: 'Rate', A2: 'Milk', B2: '60', A3: 'Bread', B3: '40', A4: 'Eggs', B4: '90', A5: 'Rice', B5: '180' };
        var book = { Spend: { A2: 'Milk', B2: '12', C2: '60', D2: '=B2*C2' }, Prices: prices };
        var mul = calculate(book, 'Spend', 'D2', false);
        check('mul', mul.kind === 'num' && mul.v === 720);
        book.Spend = { B2: '12', B3: '8', B4: 'four', B5: '2', B6: '=SUM(B2:B5)' };
        var sumText = calculate(book, 'Spend', 'B6', false);
        check('sum skips four', sumText.kind === 'num' && sumText.v === 22);
        book.Spend.B5 = "'2";
        var sumAp = calculate(book, 'Spend', 'B6', false);
        check('apostrophe text', sumAp.kind === 'num' && sumAp.v === 20);
        book.Spend = { A2: 'Milk', D2: '720', E2: '=IF(D2>500,"High","Ordinary")' };
        check('if', showVal(calculate(book, 'Spend', 'E2', false)) === 'High');
        book.Spend = { D2: '720', D6: '1760', E2: '=D2/$D$6' };
        var share = calculate(book, 'Spend', 'E2', false);
        check('lock', share.kind === 'num' && Math.abs(share.v - 720 / 1760) < 1e-9);
        book.Spend = { A2: 'Milk', C2: '=XLOOKUP(A2,Prices!A:A,Prices!B:B,"Missing")' };
        check('xlookup', showVal(calculate(book, 'Spend', 'C2', false)) === '60');
        book.Spend = { A2: 'Oil', C2: '=IFERROR(VLOOKUP(A2,Prices!A:B,2,FALSE),"Missing")' };
        check('vlookup miss', showVal(calculate(book, 'Spend', 'C2', false)) === 'Missing');
        book.Spend = { B2: '2 Apr', B3: '18 Apr', C3: '=B3-B2' };
        check('dates', showVal(calculate(book, 'Spend', 'C3', true)) === '16');
        check('text dates', showVal(calculate(book, 'Spend', 'C3', false)) === '#VALUE!');
        book.Spend = { D2: '720', D3: '320', D4: '360', D5: '360', D6: '=SUM(D2:D6)' };
        check('circ', showVal(calculate(book, 'Spend', 'D6', false)) === '#CIRC!');
        var fakeCells = {};
        [['A2', 'Milk'], ['A3', 'Bread'], ['A4', 'Total'], ['B2', '720'], ['B3', '320'], ['B4', '1760'], ['C2', '41%'], ['C3', '18%'], ['C4', '100%']].forEach(function (pair) {
            fakeCells[pair[0]] = { raw: pair[1] };
        });
        var fitted = fitFormula('=D2/$D$6', 'E2', ['A', 'B', 'C'], fakeCells, 4);
        check('fit share', fitted.formula === '=B2/$B$4' && fitted.dest === 'C2');
        var fittedSum = fitFormula('=SUM(D2:D5)', 'D6', ['A', 'B'], { A2: { raw: 'Milk' }, A6: { raw: 'Total' }, B2: { raw: '720' }, B6: { raw: '1760' } }, 6);
        check('fit sum', fittedSum.formula === '=SUM(B2:B5)' && fittedSum.dest === 'B6');
        check('pct', matchesExpect({ kind: 'num', v: 720 / 1760 }, '41%'));
        book.Spend = { B2: '2 Apr 2026', C2: '=B2+1' };
        check('next day', showVal(calculate(book, 'Spend', 'C2', true)) === '3 Apr 2026');
        book.Spend = { A2: 'Milk', B2: '12', C2: '60', D2: '=[@Qty]*[@Rate]' };
        var headers = { qty: 'B', rate: 'C' };
        book.Spend.D2 = preprocessFormula('=[@Qty]*[@Rate]', 'D2', headers);
        check('structured', showVal(calculate(book, 'Spend', 'D2', false)) === '720');
        if (fails) process.exit(1);
        console.log('all checks passed');
    }
})(typeof window !== 'undefined' ? window : globalThis);
