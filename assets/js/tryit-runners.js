(function () {
    'use strict';

    function nectraInstallRunners() {
    'use strict';

    function runCFamily(source, mode) {
        const state = { out: '', steps: 0, scopes: [new Map()], funcs: new Map(), mode: mode };
        let tokens = [];
        let pos = 0;

        function fail(message) {
            const token = tokens[pos];
            throw new Error(message + (token ? ' near "' + token.v + '"' : ' at the end of the example'));
        }

        function eof() { return pos >= tokens.length; }
        function isLiteral(token) {
            return !!token && (token.t === 'str' || token.t === 'char' || token.t === 'interp' || token.t === 'num');
        }
        function peekV() {
            const token = tokens[pos];
            if (!token || isLiteral(token)) return '';
            return token.v;
        }
        function peekT() { return tokens[pos] ? tokens[pos].t : ''; }
        function next() { return tokens[pos++] || { t: 'eof', v: '' }; }
        function eat(value) {
            if (peekV() === value) { pos++; return true; }
            return false;
        }
        function expect(value) {
            if (!eat(value)) fail('Expected "' + value + '"');
        }

        function isTypeName(value) {
            if (value === 'int' || value === 'double' || value === 'float' || value === 'char' || value === 'bool' || value === 'long' || value === 'short' || value === 'void') return true;
            if (mode === 'csharp' && (value === 'string' || value === 'var' || value === 'decimal' || value === 'byte' || value === 'sbyte' || value === 'uint' || value === 'long' || value === 'ulong' || value === 'ushort' || value === 'object')) return true;
            return false;
        }

        function startsDeclaration() {
            let index = pos;
            const modifiers = { const: 1, static: 1, unsigned: 1, signed: 1, public: 1, private: 1, protected: 1, internal: 1, readonly: 1, volatile: 1, register: 1, extern: 1 };
            while (tokens[index] && modifiers[tokens[index].v]) index++;
            return !!(tokens[index] && tokens[index].t === 'id' && isTypeName(tokens[index].v));
        }

        function skipModifiers() {
            const modifiers = { const: 1, static: 1, unsigned: 1, signed: 1, public: 1, private: 1, protected: 1, internal: 1, readonly: 1, volatile: 1, register: 1, extern: 1 };
            while (modifiers[peekV()]) pos++;
        }

        function readType() {
            skipModifiers();
            if (!isTypeName(peekV())) fail('Expected a type');
            let name = next().v;
            while (eat('[')) {
                expect(']');
                name += '[]';
            }
            if (eat('?')) name += '?';
            return name;
        }

        function parseParams() {
            const params = [];
            if (eat(')')) return params;
            if (peekV() === 'void' && tokens[pos + 1] && tokens[pos + 1].v === ')') {
                pos += 2;
                return params;
            }
            while (!eof()) {
                skipModifiers();
                if (isTypeName(peekV())) readType();
                while (eat('*')) {}
                if (peekT() === 'id') params.push(next().v);
                if (eat('[')) {
                    if (peekV() !== ']') parseExpr();
                    expect(']');
                }
                if (!eat(',')) break;
            }
            expect(')');
            return params;
        }

        function parseDeclarator() {
            let stars = 0;
            while (eat('*')) stars++;
            if (peekT() !== 'id') fail('Expected a name');
            const name = next().v;
            if (eat('(')) return { name: name, stars: stars, params: parseParams() };
            let bracket = false;
            let length = null;
            if (eat('[')) {
                bracket = true;
                if (peekV() !== ']') length = parseExpr();
                expect(']');
            }
            let init = null;
            if (eat('=')) init = parseInitializer();
            return { name: name, stars: stars, bracket: bracket, length: length, init: init };
        }

        function parseInitializer() {
            if (eat('{')) {
                const items = [];
                while (!eof() && peekV() !== '}') {
                    items.push(parseExpr());
                    if (!eat(',')) break;
                }
                expect('}');
                return { k: 'list', items: items };
            }
            if (eat('new')) {
                if (isTypeName(peekV())) readType();
                while (eat('[')) {
                    if (peekV() !== ']') parseExpr();
                    expect(']');
                }
                if (peekV() === '{') return parseInitializer();
                return { k: 'list', items: [] };
            }
            return parseExpr();
        }

        function parseDeclaration(consumeSemi) {
            const type = readType();
            const first = parseDeclarator();
            if (first.params) {
                if (peekV() === '{') return { k: 'func', name: first.name, type: type, params: first.params, body: parseBlock().body };
                expect(';');
                return { k: 'empty' };
            }
            const decls = [Object.assign({ type: type }, first)];
            while (eat(',')) decls.push(Object.assign({ type: type }, parseDeclarator()));
            if (consumeSemi) expect(';');
            return { k: 'decl', decls: decls };
        }

        function skipBlock() {
            expect('{');
            let depth = 1;
            while (!eof() && depth > 0) {
                if (peekV() === '{') depth++;
                else if (peekV() === '}') depth--;
                pos++;
            }
        }

        function parseBlock() {
            expect('{');
            const body = [];
            while (!eof() && peekV() !== '}') {
                const before = pos;
                body.push(parseStmt());
                if (pos === before) fail('The parser got stuck');
            }
            expect('}');
            return { k: 'block', body: body };
        }

        function parseStmt() {
            if (eof()) fail('Expected a statement');
            if (eat(';')) return { k: 'empty' };
            if (peekV() === '{') return parseBlock();
            if (peekV() === 'if') {
                pos++;
                expect('(');
                const cond = parseExpr();
                expect(')');
                const thenStmt = parseStmt();
                let elseStmt = null;
                if (eat('else')) elseStmt = parseStmt();
                return { k: 'if', cond: cond, then: thenStmt, else: elseStmt };
            }
            if (peekV() === 'for') {
                pos++;
                expect('(');
                let init = null;
                if (peekV() !== ';') init = startsDeclaration() ? parseDeclaration(false) : { k: 'expr', expr: parseExpr() };
                expect(';');
                let cond = null;
                if (peekV() !== ';') cond = parseExpr();
                expect(';');
                let step = null;
                if (peekV() !== ')') step = parseExpr();
                expect(')');
                return { k: 'for', init: init, cond: cond, step: step, body: parseStmt() };
            }
            if (peekV() === 'while') {
                pos++;
                expect('(');
                const cond = parseExpr();
                expect(')');
                return { k: 'while', cond: cond, body: parseStmt() };
            }
            if (peekV() === 'do') {
                pos++;
                const body = parseStmt();
                if (!eat('while')) fail('Expected "while"');
                expect('(');
                const cond = parseExpr();
                expect(')');
                expect(';');
                return { k: 'do', body: body, cond: cond };
            }
            if (peekV() === 'switch') {
                pos++;
                expect('(');
                const disc = parseExpr();
                expect(')');
                expect('{');
                const cases = [];
                while (!eof() && peekV() !== '}') {
                    if (eat('case')) {
                        const value = parseExpr();
                        expect(':');
                        cases.push({ value: value, body: [] });
                    } else if (eat('default')) {
                        expect(':');
                        cases.push({ def: true, body: [] });
                    } else if (!cases.length) {
                        fail('Expected "case"');
                    } else {
                        cases[cases.length - 1].body.push(parseStmt());
                    }
                }
                expect('}');
                return { k: 'switch', disc: disc, cases: cases };
            }
            if (peekV() === 'return') {
                pos++;
                const value = peekV() === ';' ? null : parseExpr();
                expect(';');
                return { k: 'return', value: value };
            }
            if (eat('break')) { expect(';'); return { k: 'break' }; }
            if (eat('continue')) { expect(';'); return { k: 'continue' }; }
            if (peekV() === 'using') {
                while (!eof() && peekV() !== ';') pos++;
                eat(';');
                return { k: 'empty' };
            }
            if (peekV() === 'namespace') {
                pos++;
                if (peekT() === 'id') pos++;
                return parseBlock();
            }
            if (peekV() === 'class' || peekV() === 'struct' || peekV() === 'enum') {
                pos++;
                while (!eof() && peekV() !== '{' && peekV() !== ';') pos++;
                if (peekV() === '{') skipBlock();
                else eat(';');
                return { k: 'empty' };
            }
            if (startsDeclaration()) return parseDeclaration(true);
            const expr = parseExpr();
            expect(';');
            return { k: 'expr', expr: expr };
        }

        function parseExpr() {
            const left = parseTernary();
            if (peekV() === '=' || peekV() === '+=' || peekV() === '-=' || peekV() === '*=' || peekV() === '/=' || peekV() === '%=' || peekV() === '&=' || peekV() === '|=' || peekV() === '^=' || peekV() === '<<=' || peekV() === '>>=') {
                const op = next().v;
                return { k: 'assign', op: op, left: left, right: parseExpr() };
            }
            return left;
        }

        function parseTernary() {
            const cond = parseOr();
            if (!eat('?')) return cond;
            const yes = parseExpr();
            expect(':');
            return { k: 'tern', c: cond, a: yes, b: parseExpr() };
        }

        function parseOr() { return parseBinary(parseAnd, ['||']); }
        function parseAnd() { return parseBinary(parseBitOr, ['&&']); }
        function parseBitOr() { return parseBinary(parseBitXor, ['|']); }
        function parseBitXor() { return parseBinary(parseBitAnd, ['^']); }
        function parseBitAnd() { return parseBinary(parseEq, ['&']); }
        function parseEq() { return parseBinary(parseRel, ['==', '!=']); }
        function parseRel() { return parseBinary(parseShift, ['<', '>', '<=', '>=']); }
        function parseShift() { return parseBinary(parseAdd, ['<<', '>>']); }
        function parseAdd() { return parseBinary(parseMul, ['+', '-']); }
        function parseMul() { return parseBinary(parseUnary, ['*', '/', '%']); }

        function parseBinary(operand, ops) {
            let left = operand();
            while (ops.indexOf(peekV()) !== -1) {
                const op = next().v;
                left = { k: 'bin', op: op, l: left, r: operand() };
            }
            return left;
        }

        function parseUnary() {
            if (eat('++')) return { k: 'inc', op: '++', prefix: true, expr: parseUnary() };
            if (eat('--')) return { k: 'inc', op: '--', prefix: true, expr: parseUnary() };
            if (eat('!')) return { k: 'unary', op: '!', expr: parseUnary() };
            if (eat('-')) return { k: 'unary', op: '-', expr: parseUnary() };
            if (eat('+')) return { k: 'unary', op: '+', expr: parseUnary() };
            if (eat('*')) return { k: 'deref', expr: parseUnary() };
            if (eat('&')) return { k: 'addr', expr: parseUnary() };
            return parsePostfix();
        }

        function parsePostfix() {
            let expr = parsePrimary();
            while (true) {
                if (eat('++')) expr = { k: 'inc', op: '++', prefix: false, expr: expr };
                else if (eat('--')) expr = { k: 'inc', op: '--', prefix: false, expr: expr };
                else if (eat('(')) {
                    const args = [];
                    if (peekV() !== ')') {
                        do { args.push(parseExpr()); } while (eat(','));
                    }
                    expect(')');
                    expr = { k: 'call', callee: expr, args: args };
                } else if (eat('[')) {
                    const index = parseExpr();
                    expect(']');
                    expr = { k: 'index', obj: expr, index: index };
                } else if (eat('.')) {
                    if (peekT() !== 'id') fail('Expected a member name');
                    expr = { k: 'member', obj: expr, name: next().v };
                } else break;
            }
            return expr;
        }

        function parsePrimary() {
            if (eat('(')) {
                if (startsDeclaration() || isTypeName(peekV())) {
                    readType();
                    while (eat('*')) {}
                    expect(')');
                    return parseUnary();
                }
                const inner = parseExpr();
                expect(')');
                return inner;
            }
            if (peekT() === 'num') {
                const token = next();
                return { k: 'lit', v: { k: 'num', v: token.v, float: token.float } };
            }
            if (peekT() === 'str') return { k: 'lit', v: { k: 'str', v: next().v } };
            if (peekT() === 'char') return { k: 'lit', v: { k: 'char', v: next().v } };
            if (peekT() === 'interp') return { k: 'interp', parts: next().parts };
            if (peekV() === 'sizeof') {
                pos++;
                if (eat('(')) {
                    if (isTypeName(peekV())) readType();
                    else parseExpr();
                    expect(')');
                }
                return { k: 'lit', v: { k: 'num', v: 4, float: false } };
            }
            if (peekT() === 'id') return { k: 'var', name: next().v };
            fail('Expected an expression');
        }

        function writeOut(text) {
            if (state.out.length >= 32000) return;
            state.out += String(text).slice(0, 32000 - state.out.length);
        }

        function bump() {
            state.steps++;
            if (state.steps > 200000) throw new Error('This example ran too many steps for the practice runner.');
        }

        function formatValue(value) {
            if (!value || value.k === 'null') return '';
            if (value.k === 'bool') {
                if (mode === 'csharp') return value.v ? 'True' : 'False';
                return value.v ? '1' : '0';
            }
            if (value.k === 'num') return String(value.v);
            if (value.k === 'char') return value.v;
            if (value.k === 'str') return value.v;
            if (value.k === 'arr') return value.v.map(formatValue).join(',');
            return '';
        }

        function asNumber(value) {
            if (!value || value.k === 'null') return 0;
            if (value.k === 'num') return value.v;
            if (value.k === 'bool') return value.v ? 1 : 0;
            if (value.k === 'char') return value.v.charCodeAt(0) || 0;
            if (value.k === 'str') {
                const parsed = Number(value.v);
                return Number.isFinite(parsed) ? parsed : 0;
            }
            return 0;
        }

        function asChar(value) {
            if (!value || value.k === 'null') return '\0';
            if (value.k === 'char') return value.v;
            if (value.k === 'str') return value.v.charAt(0) || '\0';
            if (value.k === 'num') return String.fromCharCode(value.v);
            return '\0';
        }

        function truthy(value) {
            if (!value || value.k === 'null') return false;
            if (value.k === 'bool') return !!value.v;
            if (value.k === 'num') return value.v !== 0;
            if (value.k === 'str') return value.v.length > 0;
            if (value.k === 'char') return value.v !== '\0';
            if (value.k === 'arr') return value.v.length > 0;
            return true;
        }

        function boolResult(flag) {
            if (mode === 'csharp') return { k: 'bool', v: !!flag };
            return { k: 'num', v: flag ? 1 : 0, float: false };
        }

        function baseType(type) {
            return String(type || 'int').replace('[]', '').replace('?', '');
        }

        function defaultValue(type) {
            const base = baseType(type);
            if (base === 'bool') return { k: 'bool', v: false };
            if (base === 'char') return { k: 'char', v: '\0' };
            if (base === 'string' || base === 'object') return { k: 'str', v: '' };
            if (base === 'double' || base === 'float' || base === 'decimal') return { k: 'num', v: 0, float: true };
            return { k: 'num', v: 0, float: false };
        }

        function coerce(type, value) {
            const base = baseType(type);
            if (base === 'var') return value;
            if (base === 'bool') return { k: 'bool', v: truthy(value) };
            if (base === 'char') return { k: 'char', v: asChar(value) };
            if (base === 'string' || base === 'object') {
                if (value && value.k === 'null') return value;
                return { k: 'str', v: value && value.k === 'str' ? value.v : formatValue(value) };
            }
            if (base === 'double' || base === 'float' || base === 'decimal') return { k: 'num', v: asNumber(value), float: true };
            return { k: 'num', v: Math.trunc(asNumber(value)), float: false };
        }

        function copyVal(value) {
            if (!value) return { k: 'num', v: 0, float: false };
            if (value.k === 'arr') return value;
            return { k: value.k, v: value.v, float: value.float };
        }

        function getVar(name) {
            if (name === 'true') return { k: 'bool', v: true };
            if (name === 'false') return { k: 'bool', v: false };
            if (name === 'null' || name === 'NULL' || name === 'nullptr') return { k: 'null', v: null };
            for (let i = state.scopes.length - 1; i >= 0; i--) {
                if (state.scopes[i].has(name)) return state.scopes[i].get(name);
            }
            throw new Error("Unknown name '" + name + "'");
        }

        function setVar(name, value) {
            for (let i = state.scopes.length - 1; i >= 0; i--) {
                if (!state.scopes[i].has(name)) continue;
                const prev = state.scopes[i].get(name);
                state.scopes[i].set(name, matchKind(prev, value));
                return;
            }
            state.scopes[state.scopes.length - 1].set(name, value);
        }

        function declare(name, value) {
            state.scopes[state.scopes.length - 1].set(name, value);
        }

        function matchKind(prev, value) {
            if (!prev) return value;
            if (prev.k === 'num' && prev.float) return { k: 'num', v: asNumber(value), float: true };
            if (prev.k === 'num') return { k: 'num', v: Math.trunc(asNumber(value)), float: false };
            if (prev.k === 'bool') return { k: 'bool', v: truthy(value) };
            if (prev.k === 'char') return { k: 'char', v: asChar(value) };
            if (prev.k === 'str') return { k: 'str', v: value && value.k === 'str' ? value.v : formatValue(value) };
            return value;
        }

        function readLValue(node) {
            if (node.k === 'var') return getVar(node.name);
            if (node.k === 'index') return evalExpr(node);
            if (node.k === 'deref' || node.k === 'addr') return evalExpr(node.expr);
            throw new Error('That expression cannot be assigned to');
        }

        function writeLValue(node, value) {
            if (node.k === 'var') { setVar(node.name, value); return; }
            if (node.k === 'index') {
                const obj = evalExpr(node.obj);
                const index = Math.trunc(asNumber(evalExpr(node.index)));
                if (!obj || obj.k !== 'arr') throw new Error('That value is not an array');
                obj.v[index] = value;
                return;
            }
            if (node.k === 'deref' && node.expr && node.expr.k === 'var') { setVar(node.expr.name, value); return; }
            throw new Error('That expression cannot be assigned to');
        }

        function evalBin(op, a, b) {
            if (op === '+' && mode === 'csharp' && ((a && a.k === 'str') || (b && b.k === 'str'))) {
                return { k: 'str', v: formatValue(a) + formatValue(b) };
            }
            if (op === '+' || op === '-' || op === '*' || op === '/' || op === '%' || op === '&' || op === '|' || op === '^' || op === '<<' || op === '>>') {
                const av = asNumber(a);
                const bv = asNumber(b);
                const isFloat = !!((a && a.float) || (b && b.float));
                let value;
                if (op === '+') value = av + bv;
                else if (op === '-') value = av - bv;
                else if (op === '*') value = av * bv;
                else if (op === '&') value = av & bv;
                else if (op === '|') value = av | bv;
                else if (op === '^') value = av ^ bv;
                else if (op === '<<') value = av << bv;
                else if (op === '>>') value = av >> bv;
                else if (op === '%') {
                    if (bv === 0) throw new Error('Division by zero');
                    value = av % bv;
                } else {
                    if (bv === 0) throw new Error('Division by zero');
                    value = isFloat ? av / bv : Math.trunc(av / bv);
                }
                return { k: 'num', v: value, float: isFloat && op !== '%' };
            }
            if (a && b && a.k === 'null' && b.k === 'null' && (op === '==' || op === '!=')) return boolResult(op === '==');
            let flag;
            if ((a && a.k === 'str' && a.k !== 'num') && (b && b.k === 'str') && op !== '<' && op !== '>' && op !== '<=' && op !== '>=') {
                flag = formatValue(a) === formatValue(b);
                if (op === '!=') flag = !flag;
            } else if ((a && a.k === 'str') || (b && b.k === 'str')) {
                const as = formatValue(a);
                const bs = formatValue(b);
                flag = op === '==' ? as === bs : op === '!=' ? as !== bs : op === '<' ? as < bs : op === '>' ? as > bs : op === '<=' ? as <= bs : as >= bs;
            } else {
                const av = asNumber(a);
                const bv = asNumber(b);
                flag = op === '==' ? av === bv : op === '!=' ? av !== bv : op === '<' ? av < bv : op === '>' ? av > bv : op === '<=' ? av <= bv : av >= bv;
            }
            return boolResult(flag);
        }

        function applyFormat(fmt, args) {
            let index = 0;
            let out = '';
            for (let i = 0; i < fmt.length; i++) {
                if (fmt.charAt(i) !== '%') { out += fmt.charAt(i); continue; }
                if (fmt.charAt(i + 1) === '%') { out += '%'; i++; continue; }
                let cursor = i + 1;
                while (cursor < fmt.length && /[-+0 #]/.test(fmt.charAt(cursor))) cursor++;
                while (cursor < fmt.length && /[0-9]/.test(fmt.charAt(cursor))) cursor++;
                let precision = null;
                if (fmt.charAt(cursor) === '.') {
                    cursor++;
                    let digits = '';
                    while (cursor < fmt.length && /[0-9]/.test(fmt.charAt(cursor))) digits += fmt.charAt(cursor++);
                    precision = digits === '' ? 0 : Number(digits);
                }
                while (cursor < fmt.length && /[hlLzjt]/.test(fmt.charAt(cursor))) cursor++;
                const spec = fmt.charAt(cursor);
                const arg = args[index++] || { k: 'num', v: 0, float: false };
                if (!spec) { out += '%'; break; }
                if (spec === 'd' || spec === 'i' || spec === 'u') out += String(Math.trunc(asNumber(arg)));
                else if (spec === 'x') out += Math.trunc(asNumber(arg)).toString(16);
                else if (spec === 'X') out += Math.trunc(asNumber(arg)).toString(16).toUpperCase();
                else if (spec === 'f' || spec === 'F') out += asNumber(arg).toFixed(precision == null ? 6 : precision);
                else if (spec === 'c') out += arg.k === 'char' ? arg.v : String.fromCharCode(Math.trunc(asNumber(arg)) || 0);
                else if (spec === 's') out += arg.k === 'null' ? '' : formatValue(arg);
                else out += formatValue(arg);
                i = cursor;
            }
            return out;
        }

        function evalSource(source) {
            const savedTokens = tokens;
            const savedPos = pos;
            tokens = tokenize(source);
            pos = 0;
            let value;
            try {
                value = evalExpr(parseExpr());
            } finally {
                tokens = savedTokens;
                pos = savedPos;
            }
            return value;
        }

        function evalExpr(node) {
            if (!node) return { k: 'num', v: 0, float: false };
            if (node.k === 'lit') return node.v;
            if (node.k === 'var') return getVar(node.name);
            if (node.k === 'interp') {
                let text = '';
                node.parts.forEach(function (part) {
                    if (part.text != null) text += part.text;
                    else {
                        let hole = part.expr || '';
                        const formatted = hole.match(/^([A-Za-z_]\w*)\s*:(.+)$/);
                        if (formatted) hole = formatted[1];
                        text += formatValue(evalSource(hole));
                    }
                });
                return { k: 'str', v: text };
            }
            if (node.k === 'unary') {
                const value = evalExpr(node.expr);
                if (node.op === '!') return boolResult(!truthy(value));
                if (node.op === '-') return { k: 'num', v: -asNumber(value), float: !!(value && value.float) };
                return { k: 'num', v: asNumber(value), float: !!(value && value.float) };
            }
            if (node.k === 'inc') {
                const current = readLValue(node.expr);
                const updated = { k: 'num', v: asNumber(current) + (node.op === '++' ? 1 : -1), float: !!(current && current.float) };
                writeLValue(node.expr, updated);
                return node.prefix ? updated : { k: 'num', v: asNumber(current), float: !!(current && current.float) };
            }
            if (node.k === 'bin') {
                if (node.op === '&&') return boolResult(truthy(evalExpr(node.l)) && truthy(evalExpr(node.r)));
                if (node.op === '||') {
                    const left = evalExpr(node.l);
                    if (truthy(left)) return boolResult(true);
                    return boolResult(truthy(evalExpr(node.r)));
                }
                return evalBin(node.op, evalExpr(node.l), evalExpr(node.r));
            }
            if (node.k === 'tern') return truthy(evalExpr(node.c)) ? evalExpr(node.a) : evalExpr(node.b);
            if (node.k === 'assign') {
                if (node.op === '=') {
                    writeLValue(node.left, evalExpr(node.right));
                    return readLValue(node.left);
                }
                const combined = evalBin(node.op.slice(0, -1), readLValue(node.left), evalExpr(node.right));
                writeLValue(node.left, combined);
                return readLValue(node.left);
            }
            if (node.k === 'index') {
                const obj = evalExpr(node.obj);
                const index = Math.trunc(asNumber(evalExpr(node.index)));
                if (obj && obj.k === 'str') return { k: 'char', v: obj.v.charAt(index) || '\0' };
                if (obj && obj.k === 'arr') return obj.v[index] || defaultValue('int');
                throw new Error('That value cannot be indexed');
            }
            if (node.k === 'member') {
                const obj = evalExpr(node.obj);
                if ((node.name === 'Length' || node.name === 'length') && obj) {
                    if (obj.k === 'str') return { k: 'num', v: obj.v.length, float: false };
                    if (obj.k === 'arr') return { k: 'num', v: obj.v.length, float: false };
                }
                return { k: 'member', obj: obj, name: node.name };
            }
            if (node.k === 'deref' || node.k === 'addr') return evalExpr(node.expr);
            if (node.k === 'list') return { k: 'arr', v: node.items.map(evalExpr) };
            if (node.k === 'call') return evalCall(node);
            throw new Error('This expression is not supported by the practice runner');
        }

        function evalCall(node) {
            const callee = node.callee || {};
            if (callee.k === 'member' && (callee.name === 'WriteLine' || callee.name === 'Write')) {
                const text = node.args.length ? formatValue(evalExpr(node.args[0])) : '';
                writeOut(text + (callee.name === 'WriteLine' ? '\n' : ''));
                return { k: 'num', v: 0, float: false };
            }
            const name = callee.k === 'var' ? callee.name : '';
            if (name === 'printf') {
                const fmt = formatValue(evalExpr(node.args[0] || { k: 'lit', v: { k: 'str', v: '' } }));
                writeOut(applyFormat(fmt, node.args.slice(1).map(evalExpr)));
                return { k: 'num', v: 0, float: false };
            }
            if (name === 'puts') {
                writeOut(formatValue(evalExpr(node.args[0] || { k: 'lit', v: { k: 'str', v: '' } })) + '\n');
                return { k: 'num', v: 0, float: false };
            }
            if (name === 'putchar') {
                writeOut(asChar(evalExpr(node.args[0] || { k: 'lit', v: { k: 'num', v: 0 } })));
                return { k: 'num', v: 0, float: false };
            }
            if (name && state.funcs.has(name)) return callFunc(state.funcs.get(name), node.args.map(evalExpr));
            throw new Error("The practice runner does not call '" + (name || callee.name || 'that function') + "'");
        }

        function execDecl(stmt) {
            stmt.decls.forEach(function (decl) {
                const isArray = decl.type.indexOf('[]') !== -1 || decl.bracket;
                let value = decl.init ? evalExpr(decl.init) : null;
                if (isArray) {
                    if (value && value.k === 'str') declare(decl.name, value);
                    else if (value && value.k === 'arr') {
                        const base = baseType(decl.type);
                        value.v = value.v.map(function (item) { return coerce(base, item); });
                        declare(decl.name, value);
                    } else {
                        const length = decl.length ? Math.max(0, Math.trunc(asNumber(evalExpr(decl.length)))) : 0;
                        const items = [];
                        for (let i = 0; i < length; i++) items.push(defaultValue(decl.type));
                        declare(decl.name, { k: 'arr', v: items });
                    }
                    return;
                }
                if (decl.stars && value && value.k === 'str') {
                    declare(decl.name, value);
                    return;
                }
                declare(decl.name, value ? coerce(decl.type, value) : defaultValue(decl.type));
            });
        }

        function callFunc(fn, args) {
            bump();
            state.scopes.push(new Map());
            fn.params.forEach(function (name, index) {
                state.scopes[state.scopes.length - 1].set(name, args[index] ? copyVal(args[index]) : { k: 'num', v: 0, float: false });
            });
            const signal = execBlock(fn.body);
            state.scopes.pop();
            if (signal && signal.sig === 'return') return signal.value || { k: 'num', v: 0, float: false };
            return { k: 'num', v: 0, float: false };
        }

        function sameValue(a, b) {
            if (a && b && a.k === 'str' && b.k === 'str') return a.v === b.v;
            return asNumber(a) === asNumber(b);
        }

        function execSwitch(stmt) {
            const disc = evalExpr(stmt.disc);
            let matched = false;
            for (let i = 0; i < stmt.cases.length; i++) {
                const item = stmt.cases[i];
                if (!matched) {
                    if (item.def || !sameValue(evalExpr(item.value), disc)) continue;
                    matched = true;
                }
                const signal = execBlock(item.body);
                if (signal && signal.sig === 'break') return null;
                if (signal) return signal;
            }
            if (!matched) {
                const start = stmt.cases.findIndex(function (item) { return item.def; });
                if (start >= 0) {
                    for (let i = start; i < stmt.cases.length; i++) {
                        const signal = execBlock(stmt.cases[i].body);
                        if (signal && signal.sig === 'break') return null;
                        if (signal) return signal;
                    }
                }
            }
            return null;
        }

        function exec(stmt) {
            bump();
            if (!stmt || stmt.k === 'empty') return null;
            if (stmt.k === 'block') return execBlock(stmt.body);
            if (stmt.k === 'expr') { evalExpr(stmt.expr); return null; }
            if (stmt.k === 'decl') { execDecl(stmt); return null; }
            if (stmt.k === 'func') { state.funcs.set(stmt.name, stmt); return null; }
            if (stmt.k === 'return') return { sig: 'return', value: stmt.value ? evalExpr(stmt.value) : { k: 'num', v: 0, float: false } };
            if (stmt.k === 'break') return { sig: 'break' };
            if (stmt.k === 'continue') return { sig: 'continue' };
            if (stmt.k === 'if') {
                if (truthy(evalExpr(stmt.cond))) return exec(stmt.then);
                if (stmt.else) return exec(stmt.else);
                return null;
            }
            if (stmt.k === 'for') {
                if (stmt.init) {
                    const signal = exec(stmt.init);
                    if (signal) return signal;
                }
                while (true) {
                    bump();
                    if (stmt.cond && !truthy(evalExpr(stmt.cond))) break;
                    const signal = exec(stmt.body);
                    if (signal && signal.sig === 'break') break;
                    if (signal && signal.sig !== 'continue') return signal;
                    if (stmt.step) evalExpr(stmt.step);
                }
                return null;
            }
            if (stmt.k === 'while') {
                while (truthy(evalExpr(stmt.cond))) {
                    bump();
                    const signal = exec(stmt.body);
                    if (signal && signal.sig === 'break') break;
                    if (signal && signal.sig !== 'continue') return signal;
                }
                return null;
            }
            if (stmt.k === 'do') {
                do {
                    bump();
                    const signal = exec(stmt.body);
                    if (signal && signal.sig === 'break') break;
                    if (signal && signal.sig !== 'continue') return signal;
                } while (truthy(evalExpr(stmt.cond)));
                return null;
            }
            if (stmt.k === 'switch') return execSwitch(stmt);
            return null;
        }

        function execBlock(body) {
            for (let i = 0; i < body.length; i++) {
                const signal = exec(body[i]);
                if (signal) return signal;
            }
            return null;
        }

        function decodeEscape(ch) {
            if (ch === 'n') return '\n';
            if (ch === 't') return '\t';
            if (ch === 'r') return '\r';
            if (ch === '\\' || ch === '"' || ch === "'") return ch;
            if (ch === '0') return '\0';
            return ch || '';
        }

        function expandDefines(text) {
            const defines = [];
            const kept = [];
            String(text || '').replace(/^\uFEFF/, '').split('\n').forEach(function (line) {
                const match = line.match(/^\s*#define\s+([A-Za-z_]\w*)\s+(.*)$/);
                if (match) { defines.push([match[1], match[2].trim()]); return; }
                if (/^\s*#/.test(line)) return;
                kept.push(line);
            });
            let expanded = kept.join('\n');
            defines.forEach(function (pair) {
                expanded = expanded.replace(new RegExp('\\b' + pair[0] + '\\b', 'g'), pair[1]);
            });
            return expanded;
        }

        function tokenize(source) {
            const src = expandDefines(source);
            const list = [];
            let i = 0;
            let lineStart = true;

            function push(token) {
                list.push(token);
                lineStart = false;
            }

            function readQuoted(quote) {
                i++;
                let text = '';
                while (i < src.length && src.charAt(i) !== quote) {
                    if (src.charAt(i) === '\\') {
                        text += decodeEscape(src.charAt(i + 1));
                        i += 2;
                        continue;
                    }
                    text += src.charAt(i++);
                }
                if (src.charAt(i) !== quote) throw new Error('A string is missing its closing quote');
                i++;
                return text;
            }

            while (i < src.length) {
                const c = src.charAt(i);
                if (c === ' ' || c === '\t' || c === '\r') { i++; continue; }
                if (c === '\n') { i++; lineStart = true; continue; }
                if (lineStart && c === '#') {
                    while (i < src.length && src.charAt(i) !== '\n') i++;
                    continue;
                }
                if (c === '/' && src.charAt(i + 1) === '/') {
                    while (i < src.length && src.charAt(i) !== '\n') i++;
                    continue;
                }
                if (c === '/' && src.charAt(i + 1) === '*') {
                    i += 2;
                    while (i < src.length && !(src.charAt(i) === '*' && src.charAt(i + 1) === '/')) i++;
                    i += 2;
                    continue;
                }
                lineStart = false;
                if (c === '$' && src.charAt(i + 1) === '"') {
                    i += 2;
                    const parts = [];
                    let text = '';
                    while (i < src.length && src.charAt(i) !== '"') {
                        if (src.charAt(i) === '\\') { text += decodeEscape(src.charAt(i + 1)); i += 2; continue; }
                        if (src.charAt(i) === '{' && src.charAt(i + 1) === '{') { text += '{'; i += 2; continue; }
                        if (src.charAt(i) === '}' && src.charAt(i + 1) === '}') { text += '}'; i += 2; continue; }
                        if (src.charAt(i) === '{') {
                            if (text) parts.push({ text: text });
                            text = '';
                            i++;
                            const start = i;
                            let depth = 1;
                            let quote = '';
                            while (i < src.length && depth > 0) {
                                const ch = src.charAt(i);
                                if (quote) {
                                    if (ch === '\\') { i += 2; continue; }
                                    if (ch === quote) quote = '';
                                    i++;
                                    continue;
                                }
                                if (ch === '"' || ch === "'") { quote = ch; i++; continue; }
                                if (ch === '{') depth++;
                                else if (ch === '}') depth--;
                                if (depth > 0) i++;
                            }
                            parts.push({ expr: src.slice(start, i) });
                            if (src.charAt(i) === '}') i++;
                            continue;
                        }
                        text += src.charAt(i++);
                    }
                    if (text) parts.push({ text: text });
                    if (src.charAt(i) === '"') i++;
                    push({ t: 'interp', v: 'interpolation', parts: parts });
                    continue;
                }
                if (c === '@' && src.charAt(i + 1) === '"') {
                    i += 2;
                    let text = '';
                    while (i < src.length) {
                        if (src.charAt(i) === '"' && src.charAt(i + 1) === '"') { text += '"'; i += 2; continue; }
                        if (src.charAt(i) === '"') { i++; break; }
                        text += src.charAt(i++);
                    }
                    push({ t: 'str', v: text });
                    continue;
                }
                if (c === '"') { push({ t: 'str', v: readQuoted('"') }); continue; }
                if (c === "'") { push({ t: 'char', v: readQuoted("'") }); continue; }
                if (/[0-9]/.test(c) || (c === '.' && /[0-9]/.test(src.charAt(i + 1)))) {
                    let raw = '';
                    while (i < src.length && /[0-9.]/.test(src.charAt(i))) raw += src.charAt(i++);
                    if (src.charAt(i) === 'e' || src.charAt(i) === 'E') {
                        raw += src.charAt(i++);
                        if (src.charAt(i) === '+' || src.charAt(i) === '-') raw += src.charAt(i++);
                        while (i < src.length && /[0-9]/.test(src.charAt(i))) raw += src.charAt(i++);
                    }
                    while (i < src.length && /[fFlLuU]/.test(src.charAt(i))) i++;
                    push({ t: 'num', v: Number(raw), float: /[.]|[eE]/.test(raw) });
                    continue;
                }
                if (/[A-Za-z_]/.test(c)) {
                    let raw = '';
                    while (i < src.length && /[A-Za-z0-9_]/.test(src.charAt(i))) raw += src.charAt(i++);
                    push({ t: 'id', v: raw });
                    continue;
                }
                const three = src.slice(i, i + 3);
                if (three === '<<=' || three === '>>=') {
                    push({ t: 'op', v: three });
                    i += 3;
                    continue;
                }
                const two = src.slice(i, i + 2);
                if (['==', '!=', '<=', '>=', '&&', '||', '++', '--', '+=', '-=', '*=', '/=', '%=', '<<', '>>', '&=', '|=', '^='].indexOf(two) !== -1) {
                    push({ t: 'op', v: two });
                    i += 2;
                    continue;
                }
                push({ t: '(){}[];,:?.'.indexOf(c) === -1 ? 'op' : 'p', v: c });
                i++;
            }
            return list;
        }

        try {
            tokens = tokenize(source);
            pos = 0;
            const program = [];
            while (!eof()) {
                const before = pos;
                program.push(parseStmt());
                if (pos === before) fail('The parser got stuck');
            }
            execBlock(program);
            if (state.funcs.has('main')) callFunc(state.funcs.get('main'), []);
            return { ok: true, output: state.out };
        } catch (error) {
            const message = error && error.message ? error.message : String(error);
            return { ok: false, output: (state.out || '') + (state.out ? '\n' : '') + message };
        }
    }

    function prepareModule(code) {
        const source = String(code || '');
        if (!/(?:^|\n)\s*(?:import|export)\b|\binterface\s+[A-Za-z_]|\btype\s+[A-Za-z_][\w]*\s*=|:\s*Record<|\bas\s+any\b|:\s*NextRequest\b/.test(source)) return source;
        return source
            .replace(/import\s+type\s+[\s\S]*?;/g, '')
            .replace(/interface\s+[A-Za-z_]\w*\s*\{[\s\S]*?\}\s*/g, '')
            .replace(/type\s+[A-Za-z_]\w*\s*=\s*[\s\S]*?;/g, '')
            .replace(/import\s+\{([^}]+)\}\s+from\s+(['"])(.*?)\2\s*;?/g, function (_, names, _quote, from) {
                return names.split(',').map(function (part) {
                    const bits = part.trim().split(/\s+as\s+/);
                    const imported = bits[0].trim();
                    const local = (bits[1] || bits[0]).trim();
                    if (!local || local === 'type') return '';
                    return 'const ' + local + ' = require(' + JSON.stringify(from) + ').' + imported + ' || require(' + JSON.stringify(from) + ');';
                }).join('\n');
            })
            .replace(/import\s+([A-Za-z_]\w*)\s+from\s+(['"])(.*?)\2\s*;?/g, 'const $1 = require("$3");')
            .replace(/export\s+default\s+/g, 'exports.default = ')
            .replace(/export\s+async\s+function\s+([A-Za-z_]\w*)/g, 'exports.$1 = async function $1')
            .replace(/export\s+function\s+([A-Za-z_]\w*)/g, 'exports.$1 = function $1')
            .replace(/export\s+const\s+/g, 'const ')
            .replace(/:\s*Record<[^;>=]+>/g, '')
            .replace(/:\s*NextRequest\b/g, '')
            .replace(/\s+as\s+any\b/g, '');
    }

    async function executeNodeSample(code) {
        const logs = [];
        const apps = [];
        const pending = [];
        const source = String(code || '');

        function joinPath(base, path) {
            if (!base || base === '/') return path || '/';
            return (String(base).replace(/\/$/, '') + '/' + String(path || '').replace(/^\//, '')).replace(/\/{2,}/g, '/');
        }

        function makeApp() {
            const app = { routes: [], listening: false };
            function register(method, path, fns) {
                app.routes.push({ method: method, path: path, fns: fns });
                return app;
            }
            ['get', 'post', 'put', 'patch', 'delete'].forEach(function (method) {
                app[method] = function (path) {
                    return register(method.toUpperCase(), path, Array.prototype.slice.call(arguments, 1));
                };
            });
            app.use = function (path, handler) {
                if (typeof path !== 'string') { handler = path; path = '/'; }
                if (handler && Array.isArray(handler.routes)) {
                    handler.routes.forEach(function (route) {
                        app.routes.push({ method: route.method, path: joinPath(path, route.path), fns: route.fns });
                    });
                    return app;
                }
                return register('USE', path, [handler]);
            };
            app.listen = function () { app.listening = true; return app; };
            apps.push(app);
            return app;
        }

        function express() { return makeApp(); }
        express.json = function () { return function (req, res, next) { if (typeof next === 'function') next(); }; };
        express.urlencoded = express.json;
        express.Router = function () { return makeApp(); };
        express.static = function () { return function (req, res, next) { if (typeof next === 'function') next(); }; };

        function nextAuth(config) {
            const providers = config && config.providers || [];
            providers.forEach(function (provider) {
                if (provider && typeof provider.authorize === 'function') pending.push(provider.authorize);
            });
            return { handlers: {}, auth: async function () { return null; }, signIn: async function () {}, signOut: async function () {} };
        }

        const NextResponse = {
            redirect: function (url) { return { type: 'redirect', url: String(url && url.href ? url.href : url) }; },
            next: function () { return { type: 'next' }; },
            json: function (data, init) { return { type: 'json', data: data, status: init && init.status || 200 }; }
        };

        function requireModule(name) {
            const key = String(name || '');
            if (key === 'express') return express;
            if (key === 'http' || key === 'https') return { createServer: function () { return { listen: function () {} }; } };
            if (key === 'next-auth' || key === 'next-auth/index') return nextAuth;
            if (key.indexOf('next/server') !== -1) return { NextResponse: NextResponse };
            if (key.indexOf('next-auth/providers/github') !== -1) return function GitHub() { return { id: 'github' }; };
            if (key.indexOf('next-auth/providers/credentials') !== -1) return function Credentials(options) { return Object.assign({ id: 'credentials' }, options || {}); };
            function proxy() { return proxy; }
            return new Proxy(proxy, {
                get: function () { return proxy; },
                apply: function () { return proxy; }
            });
        }

        function show(value) {
            if (typeof value === 'string') return value;
            try { return JSON.stringify(value); } catch (error) { return String(value); }
        }

        const module = { exports: {} };
        const consoleApi = {
            log: function () { logs.push(Array.prototype.map.call(arguments, show).join(' ')); },
            info: function () { logs.push(Array.prototype.map.call(arguments, show).join(' ')); },
            warn: function () { logs.push(Array.prototype.map.call(arguments, show).join(' ')); },
            error: function () { logs.push(Array.prototype.map.call(arguments, show).join(' ')); }
        };
        const env = { NODE_ENV: 'development' };
        source.replace(/process\.env\.([A-Za-z_]\w*)/g, function (_, name) { env[name] = env[name] || 'lesson-value'; return _; });

        try {
            const compiled = new Function('require', 'module', 'exports', 'process', 'console', '__dirname', '__filename', 'Buffer', prepareModule(source));
            const result = compiled(requireModule, module, module.exports, { env: env }, consoleApi, '/lesson', '/lesson/app.js', { from: function (value) { return String(value); } });
            if (result && typeof result.then === 'function') await result;
        } catch (error) {
            return { ok: false, output: (logs.length ? logs.join('\n') + '\n' : '') + (error.name || 'Error') + ': ' + error.message };
        }

        const emailMatch = source.match(/=== ["']([^"']+@[^"']+)["']/);
        const passwordMatch = source.match(/password === ["']([^"']+)["']/);
        for (let i = 0; i < pending.length; i++) {
            try {
                const user = await pending[i]({ email: emailMatch ? emailMatch[1] : '', password: passwordMatch ? passwordMatch[1] : '' });
                logs.push('authorize() returned ' + JSON.stringify(user));
            } catch (error) {
                logs.push('authorize() ' + error.message);
            }
        }

        if (typeof module.exports.middleware === 'function') {
            const samples = [
                ['/admin', {}],
                ['/admin', { nectra_session: 'ok', nectra_role: 'GUEST' }],
                ['/portal', { nectra_session: 'ok', nectra_role: 'ADMIN' }]
            ];
            for (let i = 0; i < samples.length; i++) {
                const pathname = samples[i][0];
                const cookies = samples[i][1];
                const req = {
                    url: 'https://learn.local' + pathname,
                    nextUrl: { pathname: pathname },
                    cookies: { get: function (name) { return cookies[name] == null ? undefined : { value: cookies[name] }; } }
                };
                try {
                    const value = await module.exports.middleware(req);
                    logs.push(pathname + ' -> ' + JSON.stringify(value));
                } catch (error) {
                    logs.push(pathname + ' -> ' + error.message);
                }
            }
        }

        function matchPath(pattern, pathname) {
            const left = String(pattern || '').split('/').filter(Boolean);
            const right = String(pathname || '').split('/').filter(Boolean);
            if (left.length !== right.length) return null;
            const params = {};
            for (let i = 0; i < left.length; i++) {
                if (left[i].charAt(0) === ':') params[left[i].slice(1)] = decodeURIComponent(right[i]);
                else if (left[i] !== right[i]) return null;
            }
            return params;
        }

        const traces = [];
        source.split('\n').forEach(function (line) {
            const match = line.match(/^\s*\/\/\s*trace:\s*([A-Za-z]+)\s+(\S+)(?:\s+(\{.*\}))?\s*$/);
            if (!match) return;
            let body;
            if (match[3]) {
                try { body = JSON.parse(match[3]); } catch (error) { body = {}; }
            }
            traces.push({ method: match[1].toUpperCase(), path: match[2], body: body });
        });

        const app = apps.filter(function (item) { return item.listening; }).pop()
            || apps.filter(function (item) { return item.routes.some(function (route) { return route.method !== 'USE'; }); }).pop()
            || apps[apps.length - 1];

        async function invoke(trace) {
            if (!app) return 'No application was created.';
            const pathname = trace.path.split('?')[0];
            let params = null;
            const route = app.routes.find(function (item) {
                if (item.method !== trace.method) return false;
                params = matchPath(item.path, pathname);
                return !!params;
            });
            if (!route) return 'No ' + trace.method + ' route matches ' + pathname;
            const query = {};
            (trace.path.split('?')[1] || '').split('&').forEach(function (pair) {
                if (!pair) return;
                const bits = pair.split('=');
                query[decodeURIComponent(bits[0])] = decodeURIComponent(bits[1] || '');
            });
            const req = { method: trace.method, path: pathname, url: trace.path, params: params, query: query, body: trace.body || {}, headers: {} };
            let status = 200;
            let payload;
            const res = {
                status: function (code) { status = code; return res; },
                json: function (value) { payload = value; return res; },
                send: function (value) { payload = value; return res; },
                end: function (value) { if (value !== undefined) payload = value; return res; },
                set: function () { return res; },
                type: function () { return res; }
            };
            const chain = app.routes.filter(function (item) {
                if (item.method !== 'USE') return false;
                const prefix = item.path || '/';
                return prefix === '/' || pathname === prefix || pathname.indexOf(prefix.replace(/\/$/, '') + '/') === 0;
            }).concat([route]);
            for (let c = 0; c < chain.length; c++) {
                let index = 0;
                const fns = chain[c].fns.filter(function (fn) { return typeof fn === 'function'; });
                function step(err) {
                    if (err) throw err;
                    const fn = fns[index++];
                    if (!fn) return undefined;
                    return fn(req, res, step);
                }
                const value = step();
                if (value && typeof value.then === 'function') await value;
            }
            const body = payload === undefined ? '' : (typeof payload === 'string' ? payload : JSON.stringify(payload, null, 2));
            return trace.method + ' ' + trace.path + '\n' + status + (body ? '\n\n' + body : '');
        }

        const chunks = [];
        if (logs.length) chunks.push(logs.join('\n'));
        for (let i = 0; i < traces.length; i++) {
            try { chunks.push(await invoke(traces[i])); }
            catch (error) { chunks.push(traces[i].method + ' ' + traces[i].path + '\n' + error.message); }
        }
        if (!chunks.length) {
            const lines = ['The example loaded.'];
            if (app) app.routes.forEach(function (route) { if (route.method !== 'USE') lines.push(route.method + ' ' + route.path); });
            chunks.push(lines.join('\n'));
        }
        return { ok: true, output: chunks.join('\n\n').slice(0, 32000) };
    }

    function executeMongoSample(code) {
        const source = String(code || '').trim();
        if (!source) return { ok: true, output: '' };
        const runnable = /\bdb\s*(?:\.|\[)/.test(source) || /\b(?:ObjectId|ISODate|NumberDecimal)\s*\(/.test(source);
        const lines = source.split('\n').map(function (line) { return line.trim(); }).filter(Boolean);
        const glossary = lines.length > 1 && lines.filter(function (line) { return line.split(/\s+/).length === 1; }).length >= Math.ceil(lines.length * 0.6);
        if (!runnable || glossary) return { ok: true, output: source };

        function clone(value) { return JSON.parse(JSON.stringify(value)); }
        const seed = {
            products: [
                { _id: 'p1', name: 'Studio Laptop', product_name: 'Studio Laptop', category: 'Electronics', price: 450, sku: 'LAP-PRO-16', stock: 4 },
                { _id: 'p2', name: 'SQL Guide', product_name: 'Grandma Cajun', category: 'Books', price: 18, sku: 'BK-1', stock: 20 },
                { _id: 'p3', name: 'Desk Mouse', product_name: 'Desk Mouse', category: 'Electronics', price: 25, sku: 'MS-1', stock: 8 },
                { _id: 'p4', name: 'HDMI Cable', product_name: 'HDMI Cable', category: 'Electronics', price: 12, sku: 'CB-1', stock: 3 }
            ],
            users: [
                { _id: 'u1', name: 'Kabir Mehta', email: 'kabir@nectra.dev', role: 'architect', active: true, skills: ['MongoDB', 'Docker'], city: 'Bengaluru', phone: null, spent: 200, budget: 100 },
                { _id: 'u2', name: 'Asha Rao', email: 'asha@nectra.dev', role: 'member', active: true, skills: ['SQL'], city: 'Pune', phone: '999', spent: 40, budget: 80 },
                { _id: 'u3', name: 'Mia Cole', email: 'mia@nectra.dev', role: 'member', active: false, skills: ['MongoDB'], city: 'London', phone: null, spent: 10, budget: 20 }
            ],
            orders: [
                { _id: 'o1', status: 'paid', total: 450, orderNo: 'ORD-9001', items: [{ sku: 'LAP-PRO-16', qty: 1 }] },
                { _id: 'o2', status: 'shipped', total: 25, orderNo: 'ORD-9002', items: [{ sku: 'MS-1', qty: 1 }] },
                { _id: 'o3', status: 'paid', total: 12, orderNo: 'ORD-9003', items: [] }
            ]
        };
        const collections = {};
        Object.keys(seed).forEach(function (name) { collections[name] = clone(seed[name]); });

        function matches(doc, query) {
            if (!query || typeof query !== 'object' || query instanceof RegExp) return true;
            return Object.keys(query).every(function (key) {
                if (key === '$or') return query.$or.some(function (part) { return matches(doc, part); });
                if (key === '$and') return query.$and.every(function (part) { return matches(doc, part); });
                const actual = doc[key];
                const expected = query[key];
                if (expected instanceof RegExp) return expected.test(String(actual == null ? '' : actual));
                if (expected && typeof expected === 'object' && !Array.isArray(expected)) {
                    return Object.keys(expected).every(function (op) {
                        if (op === '$gt') return actual > expected.$gt;
                        if (op === '$gte') return actual >= expected.$gte;
                        if (op === '$lt') return actual < expected.$lt;
                        if (op === '$lte') return actual <= expected.$lte;
                        if (op === '$eq') return actual === expected.$eq;
                        if (op === '$ne') return actual !== expected.$ne;
                        if (op === '$in') return expected.$in.indexOf(actual) !== -1;
                        if (op === '$nin') return expected.$nin.indexOf(actual) === -1;
                        if (op === '$exists') return (actual !== undefined) === !!expected.$exists;
                        if (op === '$all') return Array.isArray(actual) && expected.$all.every(function (item) { return actual.indexOf(item) !== -1; });
                        if (op === '$size') return Array.isArray(actual) && actual.length === expected.$size;
                        if (op === '$elemMatch') return Array.isArray(actual) && actual.some(function (item) { return matches(typeof item === 'object' ? item : { value: item }, expected.$elemMatch); });
                        return true;
                    });
                }
                return actual === expected;
            });
        }

        function project(doc, spec) {
            if (!spec) return Object.assign({}, doc);
            const include = Object.keys(spec).filter(function (key) { return spec[key] && key !== '_id'; });
            if (include.length) {
                const out = {};
                if (spec._id !== 0) out._id = doc._id;
                include.forEach(function (key) { out[key] = doc[key]; });
                return out;
            }
            const out = Object.assign({}, doc);
            Object.keys(spec).forEach(function (key) { if (!spec[key]) delete out[key]; });
            return out;
        }

        function cursor(docs) {
            return {
                _docs: docs,
                limit: function (count) { return cursor(docs.slice(0, count)); },
                skip: function (count) { return cursor(docs.slice(count)); },
                sort: function (spec) {
                    const keys = Object.keys(spec || {});
                    const copy = docs.slice().sort(function (a, b) {
                        for (let i = 0; i < keys.length; i++) {
                            const key = keys[i];
                            if (a[key] < b[key]) return spec[key] === -1 ? 1 : -1;
                            if (a[key] > b[key]) return spec[key] === -1 ? -1 : 1;
                        }
                        return 0;
                    });
                    return cursor(copy);
                },
                explain: function () { return { executionStats: { nReturned: docs.length, totalDocsExamined: docs.length, stage: 'IXSCAN' } }; },
                toArray: function () { return docs; }
            };
        }

        function fieldValue(doc, expr) {
            if (typeof expr === 'string' && expr.charAt(0) === '$') {
                const parts = expr.slice(1).split('.');
                let value = doc;
                for (let i = 0; i < parts.length && value != null; i++) value = value[parts[i]];
                return value;
            }
            return expr;
        }

        function aggregate(docs, pipeline) {
            let rows = docs.map(function (doc) { return Object.assign({}, doc); });
            for (let s = 0; s < (pipeline || []).length; s++) {
                const stage = pipeline[s] || {};
                const name = Object.keys(stage)[0];
                if (name === '$match') rows = rows.filter(function (doc) { return matches(doc, stage.$match); });
                else if (name === '$limit') rows = rows.slice(0, stage.$limit);
                else if (name === '$skip') rows = rows.slice(stage.$skip);
                else if (name === '$sort') rows = cursor(rows).sort(stage.$sort)._docs;
                else if (name === '$count') rows = [{ [stage.$count]: rows.length }];
                else if (name === '$project') rows = rows.map(function (doc) { return project(doc, stage.$project); });
                else if (name === '$unwind') {
                    const path = typeof stage.$unwind === 'string' ? stage.$unwind : stage.$unwind.path;
                    const next = [];
                    rows.forEach(function (doc) {
                        const items = fieldValue(doc, path);
                        if (!Array.isArray(items) || !items.length) return;
                        items.forEach(function (item) {
                            const copy = Object.assign({}, doc);
                            copy[String(path).replace(/^\$/, '')] = item;
                            next.push(copy);
                        });
                    });
                    rows = next;
                } else if (name === '$group') {
                    const spec = stage.$group;
                    const groups = new Map();
                    rows.forEach(function (doc) {
                        const id = fieldValue(doc, spec._id);
                        const key = JSON.stringify(id);
                        if (!groups.has(key)) groups.set(key, { _id: id, acc: {} });
                        const row = groups.get(key);
                        Object.keys(spec).forEach(function (field) {
                            if (field === '_id') return;
                            const opName = Object.keys(spec[field] || {})[0];
                            const expr = spec[field][opName];
                            if (!row.acc[field]) row.acc[field] = { sum: 0, count: 0, min: null, max: null };
                            const acc = row.acc[field];
                            const numeric = opName === '$sum' && expr === 1 ? 1 : Number(fieldValue(doc, expr) || 0);
                            if (opName === '$sum') acc.sum += numeric;
                            if (opName === '$avg') { acc.sum += Number(fieldValue(doc, expr) || 0); acc.count++; }
                            if (opName === '$min') acc.min = acc.min == null ? numeric : Math.min(acc.min, numeric);
                            if (opName === '$max') acc.max = acc.max == null ? numeric : Math.max(acc.max, numeric);
                        });
                    });
                    rows = Array.from(groups.values()).map(function (row) {
                        const out = { _id: row._id };
                        Object.keys(spec).forEach(function (field) {
                            if (field === '_id') return;
                            const opName = Object.keys(spec[field] || {})[0];
                            const acc = row.acc[field];
                            if (opName === '$sum') out[field] = acc.sum;
                            if (opName === '$avg') out[field] = acc.count ? Math.round((acc.sum / acc.count) * 100) / 100 : null;
                            if (opName === '$min') out[field] = acc.min;
                            if (opName === '$max') out[field] = acc.max;
                        });
                        return out;
                    });
                } else {
                    return { note: 'The ' + name + ' stage needs the full database engine. Match, group, sort, limit, and unwind run here.', rows: rows };
                }
            }
            return rows;
        }

        function collection(name) {
            if (!collections[name]) collections[name] = [];
            const docs = collections[name];
            return {
                find: function (query, projection) { return cursor(docs.filter(function (doc) { return matches(doc, query); }).map(function (doc) { return project(doc, projection); })); },
                findOne: function (query) { return docs.find(function (doc) { return matches(doc, query); }) || null; },
                countDocuments: function (query) { return docs.filter(function (doc) { return matches(doc, query); }).length; },
                insertOne: function (doc) {
                    const row = Object.assign({ _id: name.slice(0, 1) + (docs.length + 10) }, doc || {});
                    docs.push(row);
                    return { acknowledged: true, insertedId: row._id };
                },
                insertMany: function (rows) {
                    const ids = (rows || []).map(function (doc) { return collection(name).insertOne(doc).insertedId; });
                    return { acknowledged: true, insertedIds: ids };
                },
                updateOne: function (filter, update) {
                    const row = docs.find(function (doc) { return matches(doc, filter); });
                    if (!row) return { matchedCount: 0, modifiedCount: 0 };
                    if (update && update.$set) Object.assign(row, update.$set);
                    if (update && update.$inc) Object.keys(update.$inc).forEach(function (key) { row[key] = (row[key] || 0) + update.$inc[key]; });
                    if (update && update.$push) Object.keys(update.$push).forEach(function (key) {
                        if (!Array.isArray(row[key])) row[key] = [];
                        row[key].push(update.$push[key]);
                    });
                    return { matchedCount: 1, modifiedCount: 1 };
                },
                deleteOne: function (filter) {
                    const index = docs.findIndex(function (doc) { return matches(doc, filter); });
                    if (index >= 0) docs.splice(index, 1);
                    return { deletedCount: index >= 0 ? 1 : 0 };
                },
                aggregate: function (pipeline) { return aggregate(docs, pipeline); },
                createIndex: function () { return { ok: 1 }; },
                drop: function () { collections[name] = []; return { ok: 1 }; },
                stats: function () { return { count: docs.length, ok: 1 }; }
            };
        }

        const db = new Proxy({}, {
            get: function (_target, name) {
                if (name === 'stats') return function () { return { collections: Object.keys(collections).length, ok: 1 }; };
                if (name === 'collection') return collection;
                if (typeof name !== 'string' || name === 'then') return undefined;
                return collection(name);
            }
        });

        function ObjectId(id) { return id || 'generated-id'; }
        function ISODate(value) { return value || '2026-11-21T00:00:00.000Z'; }
        function NumberDecimal(value) { return Number(value); }

        function present(value) {
            if (typeof value === 'undefined') return '';
            if (value && Array.isArray(value._docs)) value = value._docs;
            if (typeof value === 'string') return value;
            try { return JSON.stringify(value, null, 2); } catch (error) { return String(value); }
        }

        try {
            const runner = new Function('db', 'ObjectId', 'ISODate', 'NumberDecimal', 'return eval(' + JSON.stringify(source) + ');');
            const value = runner(db, ObjectId, ISODate, NumberDecimal);
            const text = present(value);
            return { ok: true, output: text || 'The command finished.' };
        } catch (error) {
            if (!runnable) return { ok: true, output: source };
            return { ok: false, output: (error && error.message) ? error.message : String(error) };
        }
    }

    function isGlossary(code) {
        const lines = String(code || '').split('\n').map(function (line) { return line.trim(); }).filter(function (line) { return line && line.indexOf('--') !== 0; });
        if (lines.length < 2) return false;
        const shortLines = lines.filter(function (line) { return line.split(/\s+/).length === 1; });
        return shortLines.length >= Math.ceil(lines.length * 0.6);
    }

    function stripSqlComments(sql) {
        let out = '';
        let quote = '';
        for (let i = 0; i < sql.length; i++) {
            const c = sql.charAt(i);
            if (quote) {
                out += c;
                if (c === quote) quote = '';
                continue;
            }
            if (c === "'" || c === '"') { quote = c; out += c; continue; }
            if (c === '-' && sql.charAt(i + 1) === '-') {
                while (i < sql.length && sql.charAt(i) !== '\n') i++;
                out += '\n';
                continue;
            }
            if (c === '/' && sql.charAt(i + 1) === '*') {
                i += 2;
                while (i < sql.length && !(sql.charAt(i) === '*' && sql.charAt(i + 1) === '/')) i++;
                i++;
                continue;
            }
            out += c;
        }
        return out;
    }

    function splitSql(sql) {
        const parts = [];
        let current = '';
        let quote = '';
        for (let i = 0; i < sql.length; i++) {
            const c = sql.charAt(i);
            if (quote) {
                current += c;
                if (c === quote) quote = '';
                continue;
            }
            if (c === "'" || c === '"') { quote = c; current += c; continue; }
            if (c === ';') { if (current.trim()) parts.push(current.trim()); current = ''; continue; }
            current += c;
        }
        if (current.trim()) parts.push(current.trim());
        return parts;
    }

    function isRealSql(sql) {
        const text = sql.trim();
        if (/^SELECT\b/i.test(text) && (/\bFROM\b|\bAS\b|[('"]/i.test(text))) return true;
        return /^(?:INSERT\s+INTO\b|UPDATE\s+[A-Za-z_][\w]*\s+SET\b|DELETE\s+FROM\b|CREATE\s+(?:TABLE|DATABASE|SCHEMA|INDEX|UNIQUE\s+INDEX|OR\s+REPLACE\s+VIEW|VIEW)\b|ALTER\s+TABLE\b|DROP\s+(?:TABLE|DATABASE|SCHEMA|INDEX|VIEW)\b|WITH\b[\s\S]*\bSELECT\b|EXPLAIN\b|SHOW\b|START\s+TRANSACTION\b|BEGIN\b|COMMIT\b|ROLLBACK\b|TRUNCATE\b)/i.test(text);
    }

    function rewriteSqlFunctions(sql) {
        return sql
            .replace(/\bVERSION\s*\(\s*\)/gi, "'8.4.0-practice'")
            .replace(/\bDATABASE\s*\(\s*\)/gi, "'nectra'")
            .replace(/\bSCHEMA\s*\(\s*\)/gi, "'nectra'")
            .replace(/\bNOW\s*\(\s*\)/gi, "'2026-11-21 12:00:00'")
            .replace(/\bSYSDATE\s*\(\s*\)/gi, "'2026-11-21 12:00:00'")
            .replace(/\bCURDATE\s*\(\s*\)/gi, "'2026-11-21'")
            .replace(/\bCURRENT_DATE\s*\(\s*\)/gi, "'2026-11-21'")
            .replace(/\bCURRENT_TIMESTAMP\s*\(\s*\)/gi, "'2026-11-21 12:00:00'")
            .replace(/\bUTC_TIMESTAMP\s*\(\s*\)/gi, "'2026-11-21 12:00:00'")
            .replace(/\bCURRENT_DATABASE\s*\(\s*\)/gi, "'nectra'")
            .replace(/\bDATE_FORMAT\s*\(\s*([^,]+)\s*,\s*'[^']*'\s*\)/gi, '($1)')
            .replace(/\bCREATE\s+OR\s+REPLACE\s+/gi, 'CREATE ')
            .replace(/\bCHAR_LENGTH\s*\(/gi, 'LENGTH(')
            .replace(/\bRIGHT\s*\(\s*([^,]+)\s*,\s*(\d+)\s*\)/gi, 'SUBSTRING($1, LENGTH($1) - $2 + 1, $2)')
            .replace(/\bLEFT\s*\(\s*([^,]+)\s*,\s*(\d+)\s*\)/gi, 'SUBSTRING($1, 1, $2)')
            .replace(/\bCONCAT_WS\s*\(\s*'([^']*)'\s*,\s*([^,]+)\s*,\s*([^)]+)\)/gi, "CONCAT($2, '$1', $3)")
            .replace(/\bDATEDIFF\s*\(\s*'(\d{4}-\d{2}-\d{2})'\s*,\s*'(\d{4}-\d{2}-\d{2})'\s*\)/gi, function (_match, later, earlier) {
                return String(Math.round((Date.parse(later) - Date.parse(earlier)) / 86400000));
            })
            .replace(/\bDATE_ADD\s*\(\s*([^,]+)\s*,\s*\d+\s*\)/gi, '$1')
            .replace(/\bDATEDIFF\s*\([^()]*\)/gi, '0')
            .replace(/\b(?:DENSE_RANK|ROW_NUMBER|RANK)\s*\(\s*\)\s*OVER\s*\([\s\S]*?\)/gi, '1')
            .replace(/\bSUM\s*\(([^()]*)\)\s*OVER\s*\([\s\S]*?\)/gi, '$1');
    }

    function sanitizeSql(sql) {
        let text = sql.replace(/\bILIKE\b/gi, 'LIKE').replace(/::[A-Za-z_][\w.]*/g, '');
        text = text.replace(/DATE_ADD\s*\(\s*([^,]+),\s*INTERVAL\s+(\d+)\s+YEAR\s*\)/gi, 'DATE_ADD($1, $2)');
        text = text.replace(/DATE_ADD\s*\(\s*([^,]+),\s*INTERVAL\s+(\d+)\s+DAY\s*\)/gi, 'DATE_ADD($1, $2)');
        const verb = text.trim().split(/\s+/)[0].toUpperCase();
        if (verb !== 'CREATE' && verb !== 'ALTER') return text;
        text = text
            .replace(/\bUNSIGNED\b/gi, '')
            .replace(/\bAUTO_INCREMENT\b/gi, '')
            .replace(/\bPRIMARY\s+KEY\b/gi, '')
            .replace(/\bNOT\s+NULL\b/gi, '')
            .replace(/\bUNIQUE\b/gi, '')
            .replace(/\bIF\s+NOT\s+EXISTS\b/gi, '')
            .replace(/\bIF\s+EXISTS\b/gi, '')
            .replace(/\bENGINE\s*=\s*\w+/gi, '')
            .replace(/\bDEFAULT\s+CHARSET\s*=\s*\w+/gi, '')
            .replace(/\bCOLLATE\s+\w+/gi, '')
            .replace(/\bCOMMENT\s+'(?:''|[^'])*'/gi, '')
            .replace(/\bCONSTRAINT\s+[A-Za-z_]\w*/gi, '')
            .replace(/\bFOREIGN\s+KEY\s*\([^)]*\)/gi, '')
            .replace(/\bREFERENCES\s+[A-Za-z_]\w*\s*\([^)]*\)(?:\s+ON\s+DELETE\s+\w+)?(?:\s+ON\s+UPDATE\s+\w+)?/gi, '')
            .replace(/\bCHECK\s*\([^)]*\)/gi, '')
            .replace(/\bGENERATED\s+ALWAYS\s+AS\s*\([\s\S]*?\)\s*(?:VIRTUAL|STORED)?/gi, '')
            .replace(/\bSERIAL\b/gi, 'INT')
            .replace(/\bBIGSERIAL\b/gi, 'INT')
            .replace(/\bJSONB?\b/gi, 'STRING')
            .replace(/\bBOOLEAN\b/gi, 'INT')
            .replace(/\bDATETIME\b/gi, 'STRING')
            .replace(/\bTIMESTAMP\b/gi, 'STRING')
            .replace(/\bLONGTEXT\b/gi, 'STRING')
            .replace(/\bMEDIUMTEXT\b/gi, 'STRING')
            .replace(/\bTEXT\b/gi, 'STRING')
            .replace(/\bVARCHAR\s*\(\s*\d+\s*\)/gi, 'STRING')
            .replace(/\bCHAR\s*\(\s*\d+\s*\)/gi, 'STRING')
            .replace(/\bDECIMAL\s*\(\s*\d+\s*,\s*\d+\s*\)/gi, 'FLOAT')
            .replace(/\bNUMERIC\s*\(\s*\d+\s*,\s*\d+\s*\)/gi, 'FLOAT')
            .replace(/\bDOUBLE\b/gi, 'FLOAT')
            .replace(/\bBIGINT\b/gi, 'INT')
            .replace(/\bSMALLINT\b/gi, 'INT')
            .replace(/\bTINYINT(?:\s*\(\s*\d+\s*\))?/gi, 'INT')
            .replace(/\bDATE\b/gi, 'STRING')
            .replace(/\bAFTER\s+[A-Za-z_]\w*/gi, '')
            .replace(/,?\s*ADD\s+INDEX\s+[A-Za-z_]\w*\s*\([^)]*\)/gi, '');
        return text.replace(/,\s*,/g, ',').replace(/,\s*\)/g, ')').replace(/\(\s*,/g, '(').replace(/\s+/g, ' ').trim();
    }

    function sqlSeed() {
        return [
            ['customers', "CREATE TABLE customers (customer_id INT, id INT, customer_name STRING, full_name STRING, first_name STRING, last_name STRING, email STRING, phone STRING, country STRING, city STRING, status STRING, total_spent FLOAT, bio STRING)", "INSERT INTO customers VALUES (1,1,'Asha Rao','Asha Rao','Asha','Rao','asha@nectradigital.com','9876500000','India','Bengaluru','active',1200,'Buys tea'), (2,2,'Kabir Mehta','Kabir Mehta','Kabir','Mehta','kabir@example.com',NULL,'Germany','Berlin','active',6400,'Notes'), (3,3,'Mia Cole','Mia Cole','Mia','Cole','mia@shop.co.uk','1112223333','UK','London','paused',80,''), (4,4,'Lars Berg','Lars Berg','Lars','Berg','lars@example.se',NULL,'Sweden','Stockholm','active',5400,''), (5,5,'Noah Shah','Noah Shah','Noah','Shah','noah@nectradigital.com',NULL,'USA','Austin','active',220,'')"],
            ['products', "CREATE TABLE products (product_id INT, product_name STRING, category STRING, price FLOAT, stock INT, is_discontinued INT, category_id INT, sku STRING, id INT, sku_code STRING)", "INSERT INTO products VALUES (1,'Grandma Cajun','Books',12,20,0,2,'BK-1',1,'BK-1'), (2,'Studio Laptop','Electronics',450,4,0,1,'LAP-PRO-16',2,'LAP-PRO-16'), (3,'Desk Mouse','Electronics',25,8,0,1,'MS-1',3,'MS-1'), (4,'Organic Tea','Books',9,12,0,2,'OR-1004',4,'OR-1004')"],
            ['employees', "CREATE TABLE employees (employee_id INT, first_name STRING, last_name STRING, department STRING, salary FLOAT, hire_date STRING, ssn STRING, work_email STRING, branch_id INT, is_active INT)", "INSERT INTO employees VALUES (1,'Asha','Rao','Engineering',96000,'2022-04-02','XXX-XX-1111','asha@nectradigital.com',1,1), (2,'Kabir','Mehta','Engineering',110000,'2021-01-15','XXX-XX-2222','kabir@nectradigital.com',1,1), (3,'Mia','Cole','Sales',82000,'2023-06-01','XXX-XX-3333','mia@nectradigital.com',2,1), (4,'Lars','Berg','Support',54000,'2024-02-11','XXX-XX-4444','lars@nectradigital.com',2,0)"],
            ['orders', "CREATE TABLE orders (id INT, order_id INT, customer_id INT, order_code STRING, total_amount FLOAT, amount FLOAT, status STRING, order_date STRING, [total] FLOAT)", "INSERT INTO orders (id, order_id, customer_id, order_code, total_amount, amount, status, order_date, [total]) VALUES (1,1,1,'ORD-1001',500,500,'paid','2026-04-02',500), (2,2,2,'ORD-1002',80,80,'shipped','2026-04-03',80), (3,3,1,'ORD-1003',120,120,'paid','2026-04-04',120)"],
            ['users', "CREATE TABLE users (id INT, user_id INT, username STRING, email STRING, role STRING, is_deleted INT, is_active INT)", "INSERT INTO users VALUES (1,1,'asha','asha@nectradigital.com','member',0,1), (42,42,'admin','admin@nectradigital.com','admin',0,1), (7,7,'guest','guest@example.com','banned',0,0)"],
            ['accounts', "CREATE TABLE accounts (id INT, plan_tier STRING, discount_pct FLOAT, updated_at STRING, status STRING)", "INSERT INTO accounts VALUES (1042,'Basic',0,'2026-04-01','active'), (1043,'Pro',5,'2026-03-01','paused')"],
            ['bank_accounts', "CREATE TABLE bank_accounts (account_id INT, balance FLOAT)", "INSERT INTO bank_accounts VALUES (1001,2000), (1002,800), (2002,100)"],
            ['courses', "CREATE TABLE courses (title STRING, slug STRING, instructor STRING, price FLOAT, is_published INT)", "INSERT INTO courses VALUES ('Modern Python 3.12','modern-python','Instructor NectraDigital',49,1)"],
            ['tokens', "CREATE TABLE tokens (expires_at STRING)", "INSERT INTO tokens VALUES ('2020-01-01'), ('2027-01-01')"],
            ['subscriptions', "CREATE TABLE subscriptions (subscription_id INT, user_id INT, start_date STRING, is_active INT)", "INSERT INTO subscriptions VALUES (1,1,'2026-01-15',1), (2,2,'2025-11-21',1)"],
            ['clients', "CREATE TABLE clients (city STRING)", "INSERT INTO clients VALUES ('Bengaluru'), ('Berlin')"],
            ['domestic_clients', "CREATE TABLE domestic_clients (client_id INT, client_name STRING, email STRING, is_active INT)", "INSERT INTO domestic_clients VALUES (1,'Asha','asha@nectradigital.com',1)"],
            ['international_clients', "CREATE TABLE international_clients (client_id INT, client_name STRING, email STRING, is_active INT)", "INSERT INTO international_clients VALUES (2,'Mia','mia@shop.co.uk',1)"],
            ['user_accounts', "CREATE TABLE user_accounts (account_id INT, username STRING)", "INSERT INTO user_accounts VALUES (1,'asha')"],
            ['shipments', "CREATE TABLE shipments (order_id INT, tracking_number STRING)", "INSERT INTO shipments VALUES (1,'A12345678'), (2,'B87654321')"],
            ['students', "CREATE TABLE students (id INT, full_name STRING, status STRING)", "INSERT INTO students VALUES (1,'Asha Rao','graduated'), (2,'Kabir Mehta','active')"],
            ['student_archive', "CREATE TABLE student_archive (student_id INT, student_name STRING, completion_date STRING)", "INSERT INTO student_archive VALUES (9,'Earlier Student','2025-01-01')"],
            ['customer_profiles', "CREATE TABLE customer_profiles (customer_id INT, first_name STRING, last_name STRING, email STRING, phone STRING, bio STRING)", "INSERT INTO customer_profiles VALUES (1,'Asha','Rao','asha@nectradigital.com','9876500000','Buys tea'), (2,'Kabir','Mehta','kabir@example.com',NULL,'Notes')"],
            ['customer_spending', "CREATE TABLE customer_spending (customer_id INT, customer_name STRING, total_spent FLOAT)", "INSERT INTO customer_spending VALUES (2,'Kabir Mehta',6400), (4,'Lars Berg',5400), (1,'Asha Rao',1200), (5,'Noah Shah',220)"],
            ['branch_offices', "CREATE TABLE branch_offices (branch_id INT, branch_name STRING, city STRING)", "INSERT INTO branch_offices VALUES (1,'Indiranagar','Bengaluru'), (2,'Mitte','Berlin')"],
            ['transfer_logs', "CREATE TABLE transfer_logs (from_account INT, to_account INT, amount FLOAT, executed_at STRING)", "INSERT INTO transfer_logs VALUES (1002,1001,10,'2026-04-01 09:00:00')"]
        ];
    }

    function installSqlFunctions(alasql) {
        const fn = alasql.fn;
        fn.VERSION = function () { return '8.4.0-practice'; };
        fn.DATABASE = function () { return 'nectra'; };
        fn.NOW = function () { return '2026-11-21 12:00:00'; };
        fn.CURDATE = function () { return '2026-11-21'; };
        fn.CURRENT_DATE = fn.CURDATE;
        fn.CURRENT_TIMESTAMP = fn.NOW;
        fn.SYSDATE = fn.NOW;
        fn.DATABASE = fn.DATABASE;
        fn.IFNULL = function (a, b) { return a == null ? b : a; };
        fn.IF = function (cond, a, b) { return cond ? a : b; };
        fn.CONCAT = function () { return Array.prototype.slice.call(arguments).join(''); };
        fn.CONCAT_WS = function (sep) { return Array.prototype.slice.call(arguments, 1).filter(function (value) { return value != null; }).join(sep); };
        fn.DATEDIFF = function (a, b) { return Math.round((Date.parse(a) - Date.parse(b)) / 86400000); };
        fn.DATE_ADD = function (value, amount) {
            const text = String(value || '');
            const year = Number(text.slice(0, 4));
            if (!year) return text;
            return String(year + Number(amount || 0)) + text.slice(4);
        };
        fn.DATE_FORMAT = function (value, format) {
            const text = String(value || '');
            return String(format || '')
                .replace(/%Y/g, text.slice(0, 4) || '2026')
                .replace(/%m/g, text.slice(5, 7) || '11')
                .replace(/%d/g, text.slice(8, 10) || '21')
                .replace(/%H/g, '12').replace(/%i/g, '00').replace(/%s/g, '00');
        };
    }

    function asciiTable(rows) {
        if (!rows || !rows.length) return '0 rows returned.';
        const headers = Object.keys(rows[0]);
        const cells = rows.map(function (row) {
            return headers.map(function (header) { return row[header] == null ? 'NULL' : String(row[header]); });
        });
        const widths = headers.map(function (header, index) {
            return Math.max(header.length, cells.reduce(function (max, row) { return Math.max(max, row[index].length); }, 0));
        });
        const line = '+' + widths.map(function (width) { return '-'.repeat(width + 2); }).join('+') + '+';
        function draw(values) {
            return '|' + values.map(function (value, index) { return ' ' + value.padEnd(widths[index]) + ' '; }).join('|') + '|';
        }
        return [line, draw(headers), line].concat(cells.map(draw)).concat([line]).join('\n');
    }

    function runSqlWith(alasql, code) {
        const original = String(code || '').trim();
        if (!original) return { ok: true, output: '' };
        if (isGlossary(original)) return { ok: true, output: original };
        const statements = splitSql(stripSqlComments(original)).map(sanitizeSql).map(rewriteSqlFunctions).filter(isRealSql);
        if (!statements.length) return { ok: true, output: original };
        if (!alasql || typeof alasql !== 'function') return { ok: false, output: 'The SQL practice engine did not load. Retry Run Code.' };
        installSqlFunctions(alasql);
        try { alasql('DROP DATABASE IF EXISTS nectra_practice'); } catch (error) { /* a fresh page has no practice database yet */ }
        try {
            alasql('CREATE DATABASE nectra_practice');
            alasql('USE nectra_practice');
        } catch (error) { /* the engine's default database still runs the lesson */ }
        const created = new Set();
        statements.forEach(function (sql) {
            const match = sql.match(/\bCREATE\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?[`"\[]?([A-Za-z_]\w*)/i);
            if (match) created.add(match[1].toLowerCase());
        });
        try {
            sqlSeed().forEach(function (entry) {
                if (created.has(entry[0])) return;
                alasql(entry[1]);
                alasql(entry[2]);
            });
        } catch (error) {
            return { ok: false, output: 'The practice data could not be prepared.\n' + error.message };
        }
        const chunks = [];
        for (let i = 0; i < statements.length; i++) {
            let sql = statements[i];
            if (/^ALTER\s+TABLE\b/i.test(sql) && /,\s*ADD\b/i.test(sql)) {
                const head = sql.match(/^(ALTER\s+TABLE\s+[A-Za-z_][\w]*)/i);
                const pieces = sql.split(/,\s*(?=ADD\b)/i);
                if (head && pieces.length > 1) {
                    statements.splice(i, 1, pieces[0], ...pieces.slice(1).map(function (piece) { return head[1] + ' ' + piece; }));
                    sql = statements[i];
                }
            }
            if (/^(?:START\s+TRANSACTION|BEGIN|COMMIT|ROLLBACK)\b/i.test(sql)) {
                chunks.push('Transaction step acknowledged.');
                continue;
            }
            try {
                const verb = sql.trim().split(/\s+/)[0].toUpperCase();
                const result = alasql(sql);
                if (verb === 'SELECT' || verb === 'WITH' || verb === 'EXPLAIN' || verb === 'SHOW' || Array.isArray(result)) {
                    chunks.push(asciiTable(Array.isArray(result) ? result : []));
                } else if (typeof result === 'number') {
                    const label = verb === 'INSERT' ? 'inserted' : verb === 'UPDATE' ? 'updated' : verb === 'DELETE' ? 'deleted' : 'affected';
                    chunks.push(result + ' row' + (result === 1 ? '' : 's') + ' ' + label + '.');
                } else {
                    chunks.push('Statement finished.');
                }
            } catch (error) {
                chunks.push(error && error.message ? error.message : String(error));
                return { ok: false, output: chunks.join('\n\n').slice(0, 32000) };
            }
        }
        return { ok: true, output: chunks.join('\n\n').slice(0, 32000) };
    }

    globalThis.NectraRunners = {
        runC: function (source) { return runCFamily(source, 'c'); },
        runCSharp: function (source) { return runCFamily(source, 'csharp'); },
        executeNodeSample: executeNodeSample,
        executeMongoSample: executeMongoSample,
        runSqlWith: runSqlWith
    };
    }

    globalThis.NectraRunnersInstall = nectraInstallRunners;
    nectraInstallRunners();

    if (typeof module !== 'undefined' && require.main === module) {
        const assertOut = function (label, actual, expected) {
            if (actual !== expected) {
                console.error('FAIL ' + label);
                console.error(JSON.stringify(actual));
                console.error(JSON.stringify(expected));
                process.exitCode = 1;
                throw new Error(label);
            }
            console.log('ok ' + label);
        };
        const cHello = '#include <stdio.h>\nint main(void) {\n    printf("hello from C\\n");\n    return 0;\n}';
        const cMilk = '#include <stdio.h>\nint main(void) {\n    printf("milk");\n    printf(" aisle\\n");\n    printf("done\\n");\n    return 0;\n}';
        const cTypes = '#include <stdio.h>\nint main(void) {\n    int price = 40;\n    double half = 3.5;\n    char grade = \'A\';\n    printf("price = %d\\n", price);\n    printf("half = %.1f\\n", half);\n    printf("grade = %c\\n", grade);\n    return 0;\n}';
        const cMath = '#include <stdio.h>\nint main(void) {\n    int price = 40;\n    int count = 2;\n    int total = price * count;\n    int hot = price > 30;\n    printf("total = %d\\n", total);\n    printf("hot = %d\\n", hot);\n    return 0;\n}';
        const cLoop = '#include <stdio.h>\nint main(void) {\n    int sum = 0;\n    for (int i = 1; i <= 3; i++) { sum = sum + i; }\n    if (sum == 6) printf("sum = %d\\n", sum);\n    return 0;\n}';
        assertOut('c hello', globalThis.NectraRunners.runC(cHello).output, 'hello from C\n');
        assertOut('c milk', globalThis.NectraRunners.runC(cMilk).output, 'milk aisle\ndone\n');
        assertOut('c types', globalThis.NectraRunners.runC(cTypes).output, 'price = 40\nhalf = 3.5\ngrade = A\n');
        assertOut('c math', globalThis.NectraRunners.runC(cMath).output, 'total = 80\nhot = 1\n');
        assertOut('c loop', globalThis.NectraRunners.runC(cLoop).output, 'sum = 6\n');
        const cBits = '#include <stdio.h>\nint main(void) {\n    int a = 5;\n    int b = 3;\n    printf("both = %d\\n", a & b);\n    printf("either = %d\\n", a | b);\n    printf("shifted = %d\\n", a << 1);\n    return 0;\n}';
        assertOut('c bits', globalThis.NectraRunners.runC(cBits).output, 'both = 1\neither = 7\nshifted = 10\n');
        assertOut('cs star', globalThis.NectraRunners.runCSharp('string origin = "*";\nConsole.WriteLine(origin);').output, '*\n');
        const csHello = 'Console.WriteLine("hello from C#");';
        const csMilk = 'Console.Write("milk");\nConsole.WriteLine(" aisle");\nConsole.WriteLine("done");';
        const csInterp = 'int price = 40;\ndouble half = 3.5;\nbool hot = true;\nstring note = "milk";\nConsole.WriteLine($"price = {price}");\nConsole.WriteLine($"half = {half}");\nConsole.WriteLine($"hot = {hot}");\nConsole.WriteLine($"note = {note}");';
        assertOut('cs hello', globalThis.NectraRunners.runCSharp(csHello).output, 'hello from C#\n');
        assertOut('cs milk', globalThis.NectraRunners.runCSharp(csMilk).output, 'milk aisle\ndone\n');
        assertOut('cs interp', globalThis.NectraRunners.runCSharp(csInterp).output, 'price = 40\nhalf = 3.5\nhot = True\nnote = milk\n');
        globalThis.NectraRunners.executeNodeSample("const express = require('express');\nconst app = express();\napp.get('/notes', (req, res) => {\n  res.status(200).json({ item: 'milk' });\n});\napp.listen(3000);\n// trace: GET /notes").then(function (result) {
            assertOut('express get', result.output, 'GET /notes\n200\n\n{\n  "item": "milk"\n}');
            return globalThis.NectraRunners.executeNodeSample("const express = require('express');\nconst app = express();\napp.use(express.json());\napp.post('/notes', (req, res) => {\n  res.status(201).json({ item: req.body.title });\n});\napp.listen(3000);\n// trace: POST /notes {\"title\":\"tea\"}");
        }).then(function (result) {
            assertOut('express post', result.output, 'POST /notes\n201\n\n{\n  "item": "tea"\n}');
            return globalThis.NectraRunners.executeNodeSample("const express = require('express');\nconst app = express();\nconst notes = express.Router();\nnotes.get('/:id', (req, res) => {\n  res.json({ id: req.params.id });\n});\napp.use('/api', notes);\napp.listen(3000);\n// trace: GET /api/9");
        }).then(function (result) {
            assertOut('express route', result.output, 'GET /api/9\n200\n\n{\n  "id": "9"\n}');
            const mongo = globalThis.NectraRunners.executeMongoSample("db.products.find({ category: 'Electronics' }).limit(2)");
            if (mongo.output.indexOf('Studio Laptop') === -1 || mongo.output.indexOf('SQL Guide') !== -1) throw new Error('mongo filter');
            console.log('ok mongo');
            const count = globalThis.NectraRunners.executeMongoSample('db.users.countDocuments()');
            assertOut('mongo count', count.output, '3');
            if (process.exitCode) process.exit(process.exitCode);
        }).catch(function (error) {
            console.error(error);
            process.exit(1);
        });
    }
})();
