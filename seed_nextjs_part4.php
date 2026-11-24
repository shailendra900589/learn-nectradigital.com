<?php
// Next.js Tutorial - Part 4 (Chapters 36 to 47)
// Author: Instructor NectraDigital

function getNextJsPart4(): array {
    return [
        // ==========================================
        // CHAPTER 36: Authentication in Next.js (Auth.js v5)
        // ==========================================
        [
            'order' => 36,
            'section' => 'Authentication & Security',
            'name' => 'Authentication in Next.js: Session Cookies, JWTs, NextAuth.js / Auth.js v5',
            'keywords' => 'next.js authentication, auth.js v5, nextauth next.js 14, session cookies, jwt nextjs, server session, next.js login auth',
            'quiz' => [
                'question' => 'Where should sensitive session token verification happen in Next.js App Router for maximum security?',
                'options' => [
                    'Exclusively on the client side inside a React useEffect hook',
                    'On the server inside Server Components, Route Handlers, or Middleware via secure HTTP-only cookies',
                    'Stored unencrypted in window.localStorage',
                    'Inside the HTML head tag as a public meta element'
                ],
                'correct' => 1,
                'explanation' => 'Sensitive auth tokens must be kept in HTTP-only, Secure, SameSite cookies and verified on the server in Server Components or Middleware. Client-side storage like localStorage exposes tokens to XSS attacks.'
            ],
            'content' => <<<'HTML'
<div class="content-body space-y-6">
    <div class="bg-gradient-to-r from-slate-900 to-indigo-950 p-6 rounded-2xl border border-indigo-900/50 shadow-xl text-white">
        <span class="px-3 py-1 bg-indigo-500/20 text-indigo-300 rounded-full text-xs font-mono font-bold uppercase tracking-wider border border-indigo-500/30">Module 8: Authentication &amp; Security</span>
        <h1 class="text-3xl font-black mt-3 mb-2">Authentication in Next.js: Session Cookies, JWTs &amp; Auth.js v5</h1>
        <p class="text-slate-300 text-sm leading-relaxed">Master enterprise authentication patterns in Next.js App Router. Learn how HTTP-only session cookies prevent XSS theft, how JWTs provide stateless validation, and how to integrate Auth.js (NextAuth v5) across Server and Client Components.</p>
        <div class="mt-4 flex items-center gap-3 text-xs text-indigo-400">
            <span>Author: <strong>Instructor NectraDigital</strong></span>
            <span>•</span>
            <span>Production Grade Next.js 14/15</span>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <h2 class="text-xl font-black text-slate-800">1. Client Storage vs. HTTP-Only Cookies Architecture</h2>
        <p class="text-slate-600 text-sm leading-relaxed">In modern web applications, storing authentication tokens (JWTs) in <code>localStorage</code> or <code>sessionStorage</code> is an insecure anti-pattern because any malicious script injected via an XSS flaw can access <code>window.localStorage</code> and exfiltrate user credentials. Next.js App Router enforces the golden standard: storing sessions in <strong>HTTP-Only, Secure, SameSite=Lax/Strict</strong> cookies.</p>

        <div class="overflow-x-auto my-3">
            <table class="w-full text-xs text-left border border-slate-200 rounded-xl overflow-hidden">
                <thead class="bg-slate-100 text-slate-700 font-bold uppercase">
                    <tr>
                        <th class="p-3 border-b">Feature</th>
                        <th class="p-3 border-b">localStorage (Insecure)</th>
                        <th class="p-3 border-b">HTTP-Only Cookie (Recommended)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <tr>
                        <td class="p-3 font-semibold text-slate-800">XSS Resilience</td>
                        <td class="p-3 text-rose-600 font-bold">Vulnerable (JS can read `localStorage`)</td>
                        <td class="p-3 text-emerald-600 font-bold">Protected (Browser blocks JS read access)</td>
                    </tr>
                    <tr>
                        <td class="p-3 font-semibold text-slate-800">Server Component Access</td>
                        <td class="p-3 text-rose-600">Impossible (RSC has no browser DOM access)</td>
                        <td class="p-3 text-emerald-600 font-bold">Native (`cookies().get('session')`)</td>
                    </tr>
                    <tr>
                        <td class="p-3 font-semibold text-slate-800">Automatic Propagation</td>
                        <td class="p-3">Requires manual `Authorization: Bearer` headers</td>
                        <td class="p-3 text-emerald-600">Sent automatically by browser with requests</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <h2 class="text-xl font-black text-slate-800">2. Auth.js (NextAuth v5) Configuration: `auth.ts`</h2>
        <p class="text-slate-600 text-sm leading-relaxed">Auth.js v5 provides unified helper functions across Next.js: a single <code>auth()</code> function that works identically in Server Components, Route Handlers, Server Actions, and Edge Middleware.</p>

        <div class="w3-example-box">
            <div class="w3-example-title flex items-center justify-between">
                <span>auth.ts - Enterprise Auth.js v5 Setup with OAuth &amp; Credentials</span>
                <span class="text-xs bg-slate-800 text-slate-300 px-2 py-0.5 rounded font-mono">TypeScript</span>
            </div>
            <pre class="w3-code notranslate"><code class="language-typescript">import NextAuth from "next-auth";
import GitHub from "next-auth/providers/github";
import Credentials from "next-auth/providers/credentials";

export const { handlers, auth, signIn, signOut } = NextAuth({
  providers: [
    GitHub({
      clientId: process.env.GITHUB_ID,
      clientSecret: process.env.GITHUB_SECRET,
    }),
    Credentials({
      name: "Corporate Credentials",
      credentials: {
        email: { label: "Email", type: "email" },
        password: { label: "Password", type: "password" }
      },
      async authorize(credentials) {
        if (!credentials?.email || !credentials?.password) return null;
        // Verify credentials securely against database
        if (credentials.email === "admin@nectradigital.com" && credentials.password === "NectraPass2026!") {
          return {
            id: "usr_9921",
            name: "Instructor NectraDigital",
            email: "admin@nectradigital.com",
            role: "ADMIN"
          };
        }
        return null;
      }
    })
  ],
  callbacks: {
    jwt({ token, user }) {
      if (user) {
        token.role = (user as any).role || "USER";
        token.userId = user.id;
      }
      return token;
    },
    session({ session, token }) {
      if (session.user) {
        (session.user as any).role = token.role;
        (session.user as any).id = token.userId;
      }
      return session;
    }
  },
  session: { strategy: "jwt", maxAge: 30 * 24 * 60 * 60 }
});</code></pre>
            <div class="try-act-row">
                <button type="button" class="w3-btn-try">Try it Yourself &raquo;</button>
                <button type="button" class="w3-btn-copy">Copy Code</button>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <h2 class="text-xl font-black text-slate-800">3. Interactive Session Demo Component</h2>
        <p class="text-slate-600 text-sm leading-relaxed">Test how authentication state changes reactively in an App Router environment with simulated login, token decoding, and role detection.</p>

        <div class="w3-example-box">
            <div class="w3-example-title flex items-center justify-between">
                <span>SessionViewer.jsx - Live Interactive Auth Simulation</span>
                <span class="text-xs bg-slate-800 text-slate-300 px-2 py-0.5 rounded font-mono">React / Next.js</span>
            </div>
            <pre class="w3-code notranslate"><code class="language-jsx">function SessionViewer() {
  const [session, setSession] = React.useState({
    user: {
      name: "Instructor NectraDigital",
      email: "instructor@nectradigital.com",
      role: "ADMIN"
    },
    expires: new Date(Date.now() + 86400000).toISOString()
  });

  const [loading, setLoading] = React.useState(false);

  const toggleAuth = () => {
    setLoading(true);
    setTimeout(() => {
      if (session) {
        setSession(null);
      } else {
        setSession({
          user: {
            name: "Instructor NectraDigital",
            email: "instructor@nectradigital.com",
            role: "ADMIN"
          },
          expires: new Date(Date.now() + 86400000).toISOString()
        });
      }
      setLoading(false);
    }, 400);
  };

  return (
    <div className="p-6 max-w-lg mx-auto bg-white rounded-2xl border border-slate-200 shadow-lg font-sans">
      <div className="flex items-center justify-between border-b pb-3 mb-4">
        <h3 className="text-lg font-black text-slate-900">Auth.js v5 Session Inspector</h3>
        <span className={`px-2.5 py-0.5 rounded-full text-xs font-bold ${session ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600'}`}>
          {session ? 'Authenticated' : 'Guest'}
        </span>
      </div>

      {session ? (
        <div className="space-y-3">
          <div className="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-1.5">
            <div className="text-xs text-slate-500 font-semibold">User Identity:</div>
            <div className="font-bold text-slate-900 text-sm">{session.user.name}</div>
            <div className="text-xs text-slate-600 font-mono">{session.user.email}</div>
            <div className="mt-2 inline-block px-2 py-0.5 bg-indigo-100 text-indigo-800 text-xs font-bold rounded">
              Role: {session.user.role}
            </div>
          </div>
          <button 
            onClick={toggleAuth} 
            disabled={loading}
            className="w-full py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-lg text-sm transition"
          >
            {loading ? "Signing Out..." : "Sign Out (Clear Session Cookie)"}
          </button>
        </div>
      ) : (
        <div className="text-center py-6 space-y-4">
          <p className="text-slate-500 text-sm">No active session found in HTTP-Only cookies.</p>
          <button 
            onClick={toggleAuth} 
            disabled={loading}
            className="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg text-sm transition"
          >
            {loading ? "Authenticating..." : "Sign In with Auth.js (Simulate Login)"}
          </button>
        </div>
      )}
    </div>
  );
}</code></pre>
            <div class="try-act-row">
                <button type="button" class="w3-btn-try">Try it Yourself &raquo;</button>
                <button type="button" class="w3-btn-copy">Copy Code</button>
            </div>
        </div>
    </div>
</div>
HTML
        ],

        // ==========================================
        // CHAPTER 37: Role-Based Route Protection & Redirection
        // ==========================================
        [
            'order' => 37,
            'section' => 'Authentication & Security',
            'name' => 'Role-Based Route Protection & Redirection in Middleware and Server Components',
            'keywords' => 'next.js rbac, role-based protection, nextjs middleware auth, protect route next.js, server component redirect, nextjs authorization',
            'quiz' => [
                'question' => 'Why should route protection be enforced at both the Edge Middleware AND Server Component levels in production?',
                'options' => [
                    'Because Middleware only runs for CSS and image files',
                    'Defense-in-depth: Middleware rejects unauthorized requests at the edge before rendering, while RSCs guard direct internal data queries and sensitive business logic',
                    'Middleware can never read cookies in Next.js',
                    'Server Components cannot call redirect()'
                ],
                'correct' => 1,
                'explanation' => 'Defense-in-depth guarantees safety: Middleware filters unwanted network traffic at the edge before running expensive server work, while Server Components verify permissions right before querying private databases.'
            ],
            'content' => <<<'HTML'
<div class="content-body space-y-6">
    <div class="bg-gradient-to-r from-slate-900 to-indigo-950 p-6 rounded-2xl border border-indigo-900/50 shadow-xl text-white">
        <span class="px-3 py-1 bg-indigo-500/20 text-indigo-300 rounded-full text-xs font-mono font-bold uppercase tracking-wider border border-indigo-500/30">Module 8: Authentication &amp; Security</span>
        <h1 class="text-3xl font-black mt-3 mb-2">Role-Based Route Protection &amp; Redirection</h1>
        <p class="text-slate-300 text-sm leading-relaxed">Implement multi-tier Role-Based Access Control (RBAC) across Next.js. Master early Edge Middleware interception, programmatic redirects, and granular Server Component permission guards.</p>
        <div class="mt-4 flex items-center gap-3 text-xs text-indigo-400">
            <span>Author: <strong>Instructor NectraDigital</strong></span>
            <span>•</span>
            <span>Multi-Tier Defense-in-Depth</span>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <h2 class="text-xl font-black text-slate-800">1. Two-Tier Authorization Architecture</h2>
        <div class="p-4 bg-slate-900 text-indigo-300 rounded-xl font-mono text-xs overflow-x-auto leading-relaxed">
            [Incoming HTTP Request: /admin/billing]<br/>
            &nbsp;&nbsp;│<br/>
            &nbsp;&nbsp;▼<br/>
            [Tier 1: Edge Middleware (middleware.ts)]<br/>
            &nbsp;&nbsp;├── Reads token from request.cookies<br/>
            &nbsp;&nbsp;├── Token missing? ───► NextResponse.redirect('/login?callbackUrl=...')<br/>
            &nbsp;&nbsp;└── Role !== 'ADMIN'? ──► NextResponse.redirect('/unauthorized')<br/>
            &nbsp;&nbsp;│<br/>
            &nbsp;&nbsp;▼ (Authorized Pass-Through)<br/>
            [Tier 2: Server Component (app/admin/billing/page.tsx)]<br/>
            &nbsp;&nbsp;├── Re-verifies user session against live DB / auth()<br/>
            &nbsp;&nbsp;├── Permission: "billing:write" check<br/>
            &nbsp;&nbsp;└── Renders secure financial telemetry
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <h2 class="text-xl font-black text-slate-800">2. Production Edge Middleware Implementation</h2>
        <div class="w3-example-box">
            <div class="w3-example-title flex items-center justify-between">
                <span>middleware.ts - Multi-Role Route Guard</span>
                <span class="text-xs bg-slate-800 text-slate-300 px-2 py-0.5 rounded font-mono">TypeScript</span>
            </div>
            <pre class="w3-code notranslate"><code class="language-typescript">import { NextResponse } from "next/server";
import type { NextRequest } from "next/server";

// Role-to-route protection mapping
const ROLE_PERMISSIONS: Record<string, string[]> = {
  "/admin": ["ADMIN", "SUPERADMIN"],
  "/analytics": ["ADMIN", "ANALYST"],
  "/portal": ["ADMIN", "MEMBER", "ANALYST"],
};

export async function middleware(req: NextRequest) {
  const { pathname } = req.nextUrl;
  const sessionToken = req.cookies.get("nectra_session")?.value;

  // 1. Check if route requires authentication
  const matchedRoute = Object.keys(ROLE_PERMISSIONS).find(prefix => pathname.startsWith(prefix));

  if (matchedRoute) {
    if (!sessionToken) {
      const loginUrl = new URL("/login", req.url);
      loginUrl.searchParams.set("callbackUrl", pathname);
      return NextResponse.redirect(loginUrl);
    }

    // 2. Decode user role (e.g. from cryptographically verified JWT)
    const userRole = req.cookies.get("nectra_role")?.value || "GUEST";
    const allowedRoles = ROLE_PERMISSIONS[matchedRoute];

    if (!allowedRoles.includes(userRole)) {
      return NextResponse.redirect(new URL("/unauthorized", req.url));
    }
  }

  return NextResponse.next();
}

export const config = {
  matcher: ["/admin/:path*", "/analytics/:path*", "/portal/:path*"]
};</code></pre>
            <div class="try-act-row">
                <button type="button" class="w3-btn-try">Try it Yourself &raquo;</button>
                <button type="button" class="w3-btn-copy">Copy Code</button>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <h2 class="text-xl font-black text-slate-800">3. Interactive RBAC Simulator</h2>
        <p class="text-slate-600 text-sm leading-relaxed">Test how varying role levels unlock or lock enterprise routes dynamically.</p>

        <div class="w3-example-box">
            <div class="w3-example-title flex items-center justify-between">
                <span>RbacGate.jsx - Interactive Role-Based Gate</span>
                <span class="text-xs bg-slate-800 text-slate-300 px-2 py-0.5 rounded font-mono">React / Next.js</span>
            </div>
            <pre class="w3-code notranslate"><code class="language-jsx">function RbacGate() {
  const [currentRole, setCurrentRole] = React.useState('GUEST');
  const [targetRoute, setTargetRoute] = React.useState('/admin/audit');

  const routes = [
    { path: '/portal/dashboard', minRole: ['MEMBER', 'ANALYST', 'ADMIN'], label: 'Member Dashboard' },
    { path: '/analytics/revenue', minRole: ['ANALYST', 'ADMIN'], label: 'Revenue Telemetry' },
    { path: '/admin/audit', minRole: ['ADMIN'], label: 'Enterprise Audit Log' },
  ];

  const currentRouteDef = routes.find(r => r.path === targetRoute);
  const isAllowed = currentRouteDef && currentRouteDef.minRole.includes(currentRole);

  return (
    <div className="p-6 max-w-lg mx-auto bg-white rounded-2xl border border-slate-200 shadow-md font-sans">
      <h3 className="text-lg font-black text-slate-900 mb-4">RBAC Edge Gate Simulation</h3>

      <div className="space-y-4">
        <div>
          <label className="block text-xs font-bold text-slate-600 mb-1">Select Current User Role:</label>
          <div className="grid grid-cols-4 gap-2">
            {['GUEST', 'MEMBER', 'ANALYST', 'ADMIN'].map(role => (
              <button
                key={role}
                onClick={() => setCurrentRole(role)}
                className={`py-1.5 text-xs font-bold rounded-lg border transition ${currentRole === role ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-slate-50 text-slate-700 hover:bg-slate-100'}`}
              >
                {role}
              </button>
            ))}
          </div>
        </div>

        <div>
          <label className="block text-xs font-bold text-slate-600 mb-1">Target Route Request:</label>
          <select 
            value={targetRoute} 
            onChange={(e) => setTargetRoute(e.target.value)}
            className="w-full border border-slate-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none"
          >
            {routes.map(r => (
              <option key={r.path} value={r.path}>{r.label} ({r.path})</option>
            ))}
          </select>
        </div>

        <div className={`p-4 rounded-xl border text-sm transition-all ${isAllowed ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-rose-50 border-rose-200 text-rose-900'}`}>
          <div className="flex items-center gap-2 font-bold mb-1">
            <span>{isAllowed ? '✓ Access Granted (200 OK)' : '✕ 403 Forbidden / Redirection'}</span>
          </div>
          <p className="text-xs">
            {isAllowed 
              ? `User role [${currentRole}] fulfills authorization requirements for ${targetRoute}.`
              : `Route requires [${currentRouteDef.minRole.join(', ')}]. Access denied for [${currentRole}].`}
          </p>
        </div>
      </div>
    </div>
  );
}</code></pre>
            <div class="try-act-row">
                <button type="button" class="w3-btn-try">Try it Yourself &raquo;</button>
                <button type="button" class="w3-btn-copy">Copy Code</button>
            </div>
        </div>
    </div>
</div>
HTML
        ],

        // ==========================================
        // CHAPTER 38: Web Security Best Practices (XSS, CSRF & CSP)
        // ==========================================
        [
            'order' => 38,
            'section' => 'Authentication & Security',
            'name' => 'Web Security Best Practices: XSS Prevention, CSRF Defense & Content Security Policy (CSP)',
            'keywords' => 'next.js xss prevention, csrf nextjs, content security policy next.js, csp headers, next.js security headers, secure nextjs app',
            'quiz' => [
                'question' => 'How does Next.js Server Actions automatically protect against Cross-Site Request Forgery (CSRF)?',
                'options' => [
                    'By disabling all POST requests completely',
                    'By verifying Origin and Host headers matching on the incoming request, combined with SameSite cookie attributes and cryptographic action IDs',
                    'By storing tokens in window.sessionStorage',
                    'By forcing every user to solve a CAPTCHA'
                ],
                'correct' => 1,
                'explanation' => 'Next.js Server Actions automatically check that the Origin and Host headers match the host server and enforce POST-only execution with SameSite cookies, neutralizing CSRF attacks out of the box.'
            ],
            'content' => <<<'HTML'
<div class="content-body space-y-6">
    <div class="bg-gradient-to-r from-slate-900 to-indigo-950 p-6 rounded-2xl border border-indigo-900/50 shadow-xl text-white">
        <span class="px-3 py-1 bg-indigo-500/20 text-indigo-300 rounded-full text-xs font-mono font-bold uppercase tracking-wider border border-indigo-500/30">Module 8: Authentication &amp; Security</span>
        <h1 class="text-3xl font-black mt-3 mb-2">Web Security: XSS, CSRF &amp; Content Security Policy</h1>
        <p class="text-slate-300 text-sm leading-relaxed">Secure your production Next.js applications against OWASP Top 10 vulnerabilities. Learn how to configure strict Content Security Policy (CSP) with nonces, enforce security headers in <code>next.config.js</code>, and sanitize inputs.</p>
        <div class="mt-4 flex items-center gap-3 text-xs text-indigo-400">
            <span>Author: <strong>Instructor NectraDigital</strong></span>
            <span>•</span>
            <span>OWASP &amp; Enterprise Compliance</span>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <h2 class="text-xl font-black text-slate-800">1. Enterprise Security Headers in `next.config.mjs`</h2>
        <p class="text-slate-600 text-sm leading-relaxed">Hardening your application headers mitigates clickjacking, MIME sniffing, and unauthorized framing.</p>

        <div class="w3-example-box">
            <div class="w3-example-title flex items-center justify-between">
                <span>next.config.mjs - Production Security Headers</span>
                <span class="text-xs bg-slate-800 text-slate-300 px-2 py-0.5 rounded font-mono">JavaScript</span>
            </div>
            <pre class="w3-code notranslate"><code class="language-javascript">/** @type {import('next').NextConfig} */
const nextConfig = {
  async headers() {
    return [
      {
        source: "/:path*",
        headers: [
          { key: "X-DNS-Prefetch-Control", value: "on" },
          { key: "Strict-Transport-Security", value: "max-age=63072000; includeSubDomains; preload" },
          { key: "X-Frame-Options", value: "DENY" },
          { key: "X-Content-Type-Options", value: "nosniff" },
          { key: "Referrer-Policy", value: "strict-origin-when-cross-origin" },
          { key: "Permissions-Policy", value: "camera=(), microphone=(), geolocation=()" }
        ]
      }
    ];
  }
};

export default nextConfig;</code></pre>
            <div class="try-act-row">
                <button type="button" class="w3-btn-try">Try it Yourself &raquo;</button>
                <button type="button" class="w3-btn-copy">Copy Code</button>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <h2 class="text-xl font-black text-slate-800">2. Interactive XSS Sanitization Sandbox</h2>
        <p class="text-slate-600 text-sm leading-relaxed">See firsthand why React's automatic string escaping protects against injection attacks, and how to safely render user content.</p>

        <div class="w3-example-box">
            <div class="w3-example-title flex items-center justify-between">
                <span>SanitizationDemo.jsx - Interactive XSS Defense</span>
                <span class="text-xs bg-slate-800 text-slate-300 px-2 py-0.5 rounded font-mono">React / Next.js</span>
            </div>
            <pre class="w3-code notranslate"><code class="language-jsx">function SanitizationDemo() {
  const [inputVal, setInputVal] = React.useState('<img src="x" onerror="alert(1)"><b>Bold Text</b>');

  return (
    <div className="p-6 max-w-lg mx-auto bg-white rounded-2xl border border-slate-200 shadow-md font-sans space-y-4">
      <h3 className="text-lg font-black text-slate-900">XSS Defense Inspector</h3>
      
      <div>
        <label className="block text-xs font-bold text-slate-600 mb-1">Untrusted User Input Payload:</label>
        <textarea
          value={inputVal}
          onChange={(e) => setInputVal(e.target.value)}
          className="w-full border border-slate-300 rounded-lg p-2 text-xs font-mono focus:ring-2 focus:ring-indigo-500 outline-none"
          rows={3}
        />
      </div>

      <div className="space-y-3">
        <div className="p-3 bg-emerald-50 border border-emerald-200 rounded-xl">
          <div className="text-xs font-bold text-emerald-800 mb-1">✓ Native React Safe Render (Auto-Escaped):</div>
          <div className="text-sm font-mono text-slate-700 bg-white p-2 rounded border border-emerald-100">
            {inputVal}
          </div>
          <span className="text-[11px] text-emerald-700 font-semibold block mt-1">
            Browser treated payload as raw string literal. Execution blocked.
          </span>
        </div>

        <div className="p-3 bg-amber-50 border border-amber-200 rounded-xl">
          <div className="text-xs font-bold text-amber-800 mb-1">⚠ DangerouslySetInnerHTML Warning:</div>
          <p className="text-xs text-slate-600 leading-relaxed">
            Never bypass React's auto-escaping with <code>dangerouslySetInnerHTML</code> unless the string was passed through a certified sanitizer like <code>isomorphic-dompurify</code>.
          </p>
        </div>
      </div>
    </div>
  );
}</code></pre>
            <div class="try-act-row">
                <button type="button" class="w3-btn-try">Try it Yourself &raquo;</button>
                <button type="button" class="w3-btn-copy">Copy Code</button>
            </div>
        </div>
    </div>
</div>
HTML
        ],

        // ==========================================
        // CHAPTER 39: Static & Dynamic Metadata API
        // ==========================================
        [
            'order' => 39,
            'section' => 'SEO, Metadata & Optimization',
            'name' => 'Static & Dynamic Metadata API: generateMetadata(), Open Graph & Twitter Cards',
            'keywords' => 'next.js metadata, generateMetadata, open graph nextjs, twitter cards, next.js seo, dynamic seo next.js 14, canonical urls',
            'quiz' => [
                'question' => 'How do you create dynamic page titles and Open Graph images based on route parameters in Next.js App Router?',
                'options' => [
                    'By setting document.title inside useEffect',
                    'By exporting an async generateMetadata({ params, searchParams }) function from the page or layout',
                    'By importing react-helmet',
                    'By modifying package.json'
                ],
                'correct' => 1,
                'explanation' => 'Next.js App Router provides the async generateMetadata() export, which executes on the server and streams fully-rendered <title>, <meta>, and <link rel="canonical"> tags directly in the initial HTML head.'
            ],
            'content' => <<<'HTML'
<div class="content-body space-y-6">
    <div class="bg-gradient-to-r from-slate-900 to-indigo-950 p-6 rounded-2xl border border-indigo-900/50 shadow-xl text-white">
        <span class="px-3 py-1 bg-indigo-500/20 text-indigo-300 rounded-full text-xs font-mono font-bold uppercase tracking-wider border border-indigo-500/30">Module 9: SEO &amp; Optimization</span>
        <h1 class="text-3xl font-black mt-3 mb-2">Static &amp; Dynamic Metadata API</h1>
        <p class="text-slate-300 text-sm leading-relaxed">Optimize Next.js web applications for search crawlers and social platforms using the unified Metadata API. Learn static metadata objects, dynamic <code>generateMetadata()</code> execution, and Open Graph / Twitter Card tags.</p>
        <div class="mt-4 flex items-center gap-3 text-xs text-indigo-400">
            <span>Author: <strong>Instructor NectraDigital</strong></span>
            <span>•</span>
            <span>App Router Metadata Spec</span>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <h2 class="text-xl font-black text-slate-800">1. Static vs. Dynamic Metadata</h2>
        <p class="text-slate-600 text-sm leading-relaxed">For fixed marketing pages, export a static <code>metadata</code> object. For dynamic entity pages (e.g. <code>/products/[slug]</code>), export the <code>generateMetadata()</code> function.</p>

        <div class="w3-example-box">
            <div class="w3-example-title flex items-center justify-between">
                <span>app/products/[id]/page.tsx - Dynamic Metadata</span>
                <span class="text-xs bg-slate-800 text-slate-300 px-2 py-0.5 rounded font-mono">TypeScript</span>
            </div>
            <pre class="w3-code notranslate"><code class="language-typescript">import type { Metadata } from "next";

type Props = {
  params: { id: string };
  searchParams: { [key: string]: string | string[] | undefined };
};

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const product = await fetch(`https://api.nectradigital.com/products/${params.id}`).then(r => r.json());

  return {
    title: `${product.title} | Nectra Enterprise`,
    description: product.description,
    openGraph: {
      title: product.title,
      description: product.description,
      url: `https://nectradigital.com/products/${params.id}`,
      siteName: "Nectra Digital Learning",
      images: [
        {
          url: product.imageUrl,
          width: 1200,
          height: 630,
          alt: product.title,
        }
      ],
      type: "article",
    },
    twitter: {
      card: "summary_large_image",
      title: product.title,
      description: product.description,
      creator: "@NectraDigital",
    },
    alternates: {
      canonical: `https://nectradigital.com/products/${params.id}`,
    }
  };
}</code></pre>
            <div class="try-act-row">
                <button type="button" class="w3-btn-try">Try it Yourself &raquo;</button>
                <button type="button" class="w3-btn-copy">Copy Code</button>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <h2 class="text-xl font-black text-slate-800">2. Interactive Social Card Preview</h2>
        <div class="w3-example-box">
            <div class="w3-example-title flex items-center justify-between">
                <span>SocialPreview.jsx - Live Open Graph Simulation</span>
                <span class="text-xs bg-slate-800 text-slate-300 px-2 py-0.5 rounded font-mono">React / Next.js</span>
            </div>
            <pre class="w3-code notranslate"><code class="language-jsx">function SocialPreview() {
  const [title, setTitle] = React.useState("Next.js Enterprise Architecture Masterclass");
  const [desc, setDesc] = React.useState("Learn Server Components, 4-tier caching, and Auth.js v5 with Instructor NectraDigital.");

  return (
    <div className="p-6 max-w-lg mx-auto bg-white rounded-2xl border border-slate-200 shadow-md font-sans space-y-4">
      <h3 className="text-lg font-black text-slate-900">Social Share Card Preview</h3>

      <div className="space-y-3">
        <div>
          <label className="block text-xs font-bold text-slate-600 mb-1">OG Title:</label>
          <input 
            type="text" 
            value={title} 
            onChange={(e) => setTitle(e.target.value)} 
            className="w-full border border-slate-300 rounded-lg p-2 text-xs focus:ring-2 focus:ring-indigo-500 outline-none"
          />
        </div>
        <div>
          <label className="block text-xs font-bold text-slate-600 mb-1">OG Description:</label>
          <input 
            type="text" 
            value={desc} 
            onChange={(e) => setDesc(e.target.value)} 
            className="w-full border border-slate-300 rounded-lg p-2 text-xs focus:ring-2 focus:ring-indigo-500 outline-none"
          />
        </div>
      </div>

      <div className="border border-slate-200 rounded-xl overflow-hidden shadow-sm bg-slate-50">
        <div className="h-44 bg-gradient-to-br from-indigo-900 via-slate-900 to-slate-950 flex flex-col justify-end p-4 text-white">
          <span className="text-[10px] uppercase font-bold text-indigo-400 tracking-wider">NECTRADIGITAL.COM</span>
          <h4 className="text-base font-black truncate">{title}</h4>
        </div>
        <div className="p-3 bg-white">
          <p className="text-xs text-slate-500 uppercase font-semibold">learn.nectradigital.com</p>
          <p className="text-xs font-bold text-slate-800 line-clamp-1">{title}</p>
          <p className="text-xs text-slate-600 line-clamp-2 mt-0.5">{desc}</p>
        </div>
      </div>
    </div>
  );
}</code></pre>
            <div class="try-act-row">
                <button type="button" class="w3-btn-try">Try it Yourself &raquo;</button>
                <button type="button" class="w3-btn-copy">Copy Code</button>
            </div>
        </div>
    </div>
</div>
HTML
        ],

        // ==========================================
        // CHAPTER 40: Dynamic Open Graph Image Generation
        // ==========================================
        [
            'order' => 40,
            'section' => 'SEO, Metadata & Optimization',
            'name' => 'Dynamic Open Graph Image Generation with @vercel/og (ImageResponse)',
            'keywords' => 'next.js og image, image response, @vercel/og, dynamic og images, opengraph-image.tsx, nextjs social cards, satori next.js',
            'quiz' => [
                'question' => 'What is the primary advantage of Next.js opengraph-image.tsx using ImageResponse over pre-rendering static images in Photoshop?',
                'options' => [
                    'It generates JPEG files only',
                    'It dynamically renders custom 1200x630 social share images using JSX and CSS flexbox at runtime with edge-speed caching',
                    'It removes the need for HTML title tags',
                    'It increases page bundle size significantly'
                ],
                'correct' => 1,
                'explanation' => 'Next.js ImageResponse (powered by Satori) dynamically computes PNG images on-the-fly from React JSX elements at the edge, allowing every article, product, or user profile to have a customized social preview without manual design work.'
            ],
            'content' => <<<'HTML'
<div class="content-body space-y-6">
    <div class="bg-gradient-to-r from-slate-900 to-indigo-950 p-6 rounded-2xl border border-indigo-900/50 shadow-xl text-white">
        <span class="px-3 py-1 bg-indigo-500/20 text-indigo-300 rounded-full text-xs font-mono font-bold uppercase tracking-wider border border-indigo-500/30">Module 9: SEO &amp; Optimization</span>
        <h1 class="text-3xl font-black mt-3 mb-2">Dynamic Open Graph Image Generation with `@vercel/og`</h1>
        <p class="text-slate-300 text-sm leading-relaxed">Generate bespoke 1200×630 social preview images dynamically from JSX components using <code>ImageResponse</code>. Eliminate manual graphic design while ensuring viral social share CTR.</p>
        <div class="mt-4 flex items-center gap-3 text-xs text-indigo-400">
            <span>Author: <strong>Instructor NectraDigital</strong></span>
            <span>•</span>
            <span>Edge Image Synthesis</span>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <h2 class="text-xl font-black text-slate-800">1. Special File Convention: `opengraph-image.tsx`</h2>
        <p class="text-slate-600 text-sm leading-relaxed">Placing an <code>opengraph-image.tsx</code> inside any route folder automatically wires up the corresponding <code>&lt;meta property="og:image"&gt;</code> tag with the correct dimensions and MIME type.</p>

        <div class="w3-example-box">
            <div class="w3-example-title flex items-center justify-between">
                <span>app/blog/[slug]/opengraph-image.tsx - Dynamic Edge OG Generator</span>
                <span class="text-xs bg-slate-800 text-slate-300 px-2 py-0.5 rounded font-mono">TypeScript</span>
            </div>
            <pre class="w3-code notranslate"><code class="language-typescript">import { ImageResponse } from "next/og";

export const runtime = "edge";
export const alt = "Nectra Digital Engineering Tutorial";
export const size = { width: 1200, height: 630 };
export const contentType = "image/png";

export default async function Image({ params }: { params: { slug: string } }) {
  // Format slug for presentation: e.g. "nextjs-caching-guide" -> "Nextjs Caching Guide"
  const formattedTitle = params.slug.replace(/-/g, " ").toUpperCase();

  return new ImageResponse(
    (
      <div
        style={{
          height: "100%",
          width: "100%",
          display: "flex",
          flexDirection: "column",
          alignItems: "flex-start",
          justifyContent: "space-between",
          backgroundImage: "linear-gradient(to bottom right, #0f172a, #1e1b4b)",
          padding: "60px 80px",
          color: "white",
          fontFamily: "sans-serif"
        }}
      >
        <div style={{ display: "flex", alignItems: "center", gap: "16px" }}>
          <div style={{ width: "24px", height: "24px", borderRadius: "50%", background: "#10b981" }} />
          <span style={{ fontSize: "24px", fontWeight: "bold", letterSpacing: "2px", color: "#34d399" }}>
            LEARN.NECTRADIGITAL.COM
          </span>
        </div>

        <div style={{ display: "flex", flexDirection: "column" }}>
          <span style={{ fontSize: "56px", fontWeight: 900, lineHeight: 1.15, color: "#f8fafc" }}>
            {formattedTitle}
          </span>
          <span style={{ fontSize: "28px", color: "#94a3b8", marginTop: "16px" }}>
            Instructor NectraDigital • Production Engineering Series
          </span>
        </div>

        <div style={{ display: "flex", justifyContent: "space-between", width: "100%", borderTop: "2px solid #334155", paddingTop: "24px" }}>
          <span style={{ fontSize: "20px", color: "#cbd5e1" }}>Next.js 14/15 App Router</span>
          <span style={{ fontSize: "20px", color: "#10b981", fontWeight: "bold" }}>Verified Enterprise Curriculum</span>
        </div>
      </div>
    ),
    { ...size }
  );
}</code></pre>
            <div class="try-act-row">
                <button type="button" class="w3-btn-try">Try it Yourself &raquo;</button>
                <button type="button" class="w3-btn-copy">Copy Code</button>
            </div>
        </div>
    </div>
</div>
HTML
        ],

        // ==========================================
        // CHAPTER 41: Dynamic Sitemaps & Robots
        // ==========================================
        [
            'order' => 41,
            'section' => 'SEO, Metadata & Optimization',
            'name' => 'Dynamic Sitemaps (sitemap.ts) & Robots (robots.ts) Generation in App Router',
            'keywords' => 'next.js sitemap, sitemap.ts, robots.ts, dynamic sitemap nextjs, search engine indexing, next.js seo architecture',
            'quiz' => [
                'question' => 'Where should sitemap.ts and robots.ts be placed in the Next.js App Router?',
                'options' => [
                    'Inside the /public folder as static text files',
                    'Directly in the root of the app/ directory (app/sitemap.ts and app/robots.ts)',
                    'Inside node_modules',
                    'Inside .next/static/'
                ],
                'correct' => 1,
                'explanation' => 'In Next.js App Router, placing sitemap.ts and robots.ts directly in the app/ directory instructs Next.js to dynamically render and cache sitemap.xml and robots.txt with automated Content-Type headers.'
            ],
            'content' => <<<'HTML'
<div class="content-body space-y-6">
    <div class="bg-gradient-to-r from-slate-900 to-indigo-950 p-6 rounded-2xl border border-indigo-900/50 shadow-xl text-white">
        <span class="px-3 py-1 bg-indigo-500/20 text-indigo-300 rounded-full text-xs font-mono font-bold uppercase tracking-wider border border-indigo-500/30">Module 9: SEO &amp; Optimization</span>
        <h1 class="text-3xl font-black mt-3 mb-2">Dynamic Sitemaps &amp; Robots.txt in App Router</h1>
        <p class="text-slate-300 text-sm leading-relaxed">Automate search engine indexing for millions of dynamic URLs. Learn how to programmatically generate <code>sitemap.xml</code> and <code>robots.txt</code> directly from database records using TypeScript.</p>
        <div class="mt-4 flex items-center gap-3 text-xs text-indigo-400">
            <span>Author: <strong>Instructor NectraDigital</strong></span>
            <span>•</span>
            <span>Crawler Orchestration</span>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <h2 class="text-xl font-black text-slate-800">1. Programmatic Sitemap: `app/sitemap.ts`</h2>
        <div class="w3-example-box">
            <div class="w3-example-title flex items-center justify-between">
                <span>app/sitemap.ts - Dynamic URL Manifest Generator</span>
                <span class="text-xs bg-slate-800 text-slate-300 px-2 py-0.5 rounded font-mono">TypeScript</span>
            </div>
            <pre class="w3-code notranslate"><code class="language-typescript">import type { MetadataRoute } from "next";

export default async function sitemap(): Promise<MetadataRoute.Sitemap> {
  const baseUrl = "https://learn.nectradigital.com";

  // Fetch all dynamic course slugs from the database
  const courses = await fetch("https://api.nectradigital.com/courses").then(r => r.json());

  const courseUrls: MetadataRoute.Sitemap = courses.map((c: any) => ({
    url: `${baseUrl}/course/${c.slug}`,
    lastModified: new Date(c.updatedAt),
    changeFrequency: "weekly" as const,
    priority: 0.9,
  }));

  return [
    {
      url: baseUrl,
      lastModified: new Date(),
      changeFrequency: "daily",
      priority: 1.0,
    },
    {
      url: `${baseUrl}/contact`,
      lastModified: new Date(),
      changeFrequency: "monthly",
      priority: 0.5,
    },
    ...courseUrls
  ];
}</code></pre>
            <div class="try-act-row">
                <button type="button" class="w3-btn-try">Try it Yourself &raquo;</button>
                <button type="button" class="w3-btn-copy">Copy Code</button>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <h2 class="text-xl font-black text-slate-800">2. Programmatic Robots: `app/robots.ts`</h2>
        <div class="w3-example-box">
            <div class="w3-example-title flex items-center justify-between">
                <span>app/robots.ts - Crawler Directives Generator</span>
                <span class="text-xs bg-slate-800 text-slate-300 px-2 py-0.5 rounded font-mono">TypeScript</span>
            </div>
            <pre class="w3-code notranslate"><code class="language-typescript">import type { MetadataRoute } from "next";

export default function robots(): MetadataRoute.Robots {
  return {
    rules: [
      {
        userAgent: "*",
        allow: "/",
        disallow: ["/api/", "/admin/", "/private/", "/auth/"]
      },
      {
        userAgent: "GPTBot",
        disallow: ["/private/"]
      }
    ],
    sitemap: "https://learn.nectradigital.com/sitemap.xml",
    host: "https://learn.nectradigital.com"
  };
}</code></pre>
            <div class="try-act-row">
                <button type="button" class="w3-btn-try">Try it Yourself &raquo;</button>
                <button type="button" class="w3-btn-copy">Copy Code</button>
            </div>
        </div>
    </div>
</div>
HTML
        ],

        // ==========================================
        // CHAPTER 42: Next.js Image Optimization
        // ==========================================
        [
            'order' => 42,
            'section' => 'SEO, Metadata & Optimization',
            'name' => 'Next.js Image Optimization: next/image Layouts, WebP/AVIF Formats & Responsive Sizing',
            'keywords' => 'next/image, image optimization nextjs, next.js avif webp, responsive images, cumulative layout shift cls, sizes attribute nextjs',
            'quiz' => [
                'question' => 'How does the next/image component prevent Cumulative Layout Shift (CLS)?',
                'options' => [
                    'By downloading images before the HTML parses',
                    'By requiring width and height attributes (or fill with a sized container) so the browser reserves exact DOM geometry before the image downloads',
                    'By disabling lazy loading',
                    'By converting all images to text'
                ],
                'correct' => 1,
                'explanation' => 'next/image prevents CLS by reserving the exact aspect ratio / space in the layout prior to image download, eliminating disruptive layout jumps during page rendering.'
            ],
            'content' => <<<'HTML'
<div class="content-body space-y-6">
    <div class="bg-gradient-to-r from-slate-900 to-indigo-950 p-6 rounded-2xl border border-indigo-900/50 shadow-xl text-white">
        <span class="px-3 py-1 bg-indigo-500/20 text-indigo-300 rounded-full text-xs font-mono font-bold uppercase tracking-wider border border-indigo-500/30">Module 9: SEO &amp; Optimization</span>
        <h1 class="text-3xl font-black mt-3 mb-2">Next.js Image Optimization (`next/image`)</h1>
        <p class="text-slate-300 text-sm leading-relaxed">Achieve a perfect 100 Google Lighthouse score. Learn how Next.js automatically resizes images, transcodes to modern AVIF and WebP formats, enforces lazy loading, and eliminates Cumulative Layout Shift (CLS).</p>
        <div class="mt-4 flex items-center gap-3 text-xs text-indigo-400">
            <span>Author: <strong>Instructor NectraDigital</strong></span>
            <span>•</span>
            <span>Core Web Vitals Engineering</span>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <h2 class="text-xl font-black text-slate-800">1. Core Features of `next/image`</h2>
        <ul class="list-disc list-inside text-xs text-slate-700 space-y-2">
            <li><strong>On-Demand Format Conversion:</strong> Automatically transcodes PNG/JPEG into AVIF and WebP based on user browser capability.</li>
            <li><strong>Responsive Device Sizing:</strong> Dynamically serves smaller image variants to smartphones and retina resolutions to 4K displays via the <code>sizes</code> attribute.</li>
            <li><strong>Native Lazy Loading:</strong> Defers off-screen image decoding until scrolled within viewport threshold.</li>
            <li><strong>CLS Zero-Guarantee:</strong> Layout geometry is locked to prevent page jumping during network latency.</li>
        </ul>

        <div class="w3-example-box">
            <div class="w3-example-title flex items-center justify-between">
                <span>HeroBanner.tsx - Responsive Next/Image with Fill and Priority</span>
                <span class="text-xs bg-slate-800 text-slate-300 px-2 py-0.5 rounded font-mono">TypeScript</span>
            </div>
            <pre class="w3-code notranslate"><code class="language-typescript">import Image from "next/image";

export function HeroBanner() {
  return (
    <div className="relative w-full h-[480px] rounded-2xl overflow-hidden shadow-2xl">
      <Image
        src="https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=1600&auto=format&fit=crop"
        alt="Nectra Digital Engineering Cloud Architecture"
        fill
        priority // Preloads for Largest Contentful Paint (LCP)
        sizes="(max-width: 768px) 100vw, (max-width: 1200px) 80vw, 1200px"
        className="object-cover transition-transform duration-700 hover:scale-105"
        quality={85}
      />
      <div className="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-900/40 to-transparent flex items-end p-8">
        <div className="text-white">
          <span className="px-2.5 py-1 bg-emerald-500 text-white text-xs font-bold rounded-full">PRODUCTION LCP</span>
          <h2 className="text-2xl font-black mt-2">Zero-CLS Enterprise Asset Pipeline</h2>
        </div>
      </div>
    </div>
  );
}</code></pre>
            <div class="try-act-row">
                <button type="button" class="w3-btn-try">Try it Yourself &raquo;</button>
                <button type="button" class="w3-btn-copy">Copy Code</button>
            </div>
        </div>
    </div>
</div>
HTML
        ],

        // ==========================================
        // CHAPTER 43: Font & Script Optimization
        // ==========================================
        [
            'order' => 43,
            'section' => 'SEO, Metadata & Optimization',
            'name' => 'Font & Script Optimization: next/font (Google & Local) and next/script Strategies',
            'keywords' => 'next/font, next/script, font optimization nextjs, google fonts zero layout shift, script loading strategies next.js',
            'quiz' => [
                'question' => 'How does next/font eliminate external network requests to Google Fonts servers at runtime?',
                'options' => [
                    'It deletes all font files from the build',
                    'It downloads font CSS and binary files at build time and self-hosts them locally with zero external Google server requests',
                    'It converts fonts to SVG images',
                    'It forces the browser to use Times New Roman'
                ],
                'correct' => 1,
                'explanation' => 'next/font automatically downloads and self-hosts font files at build time. No requests are sent to Google by the visitor\'s browser, guaranteeing privacy compliance (GDPR) and zero font layout shift.'
            ],
            'content' => <<<'HTML'
<div class="content-body space-y-6">
    <div class="bg-gradient-to-r from-slate-900 to-indigo-950 p-6 rounded-2xl border border-indigo-900/50 shadow-xl text-white">
        <span class="px-3 py-1 bg-indigo-500/20 text-indigo-300 rounded-full text-xs font-mono font-bold uppercase tracking-wider border border-indigo-500/30">Module 9: SEO &amp; Optimization</span>
        <h1 class="text-3xl font-black mt-3 mb-2">Font &amp; Script Optimization (`next/font` &amp; `next/script`)</h1>
        <p class="text-slate-300 text-sm leading-relaxed">Optimize web typography and third-party tracking scripts. Discover build-time font self-hosting, CSS variable injection, and script loading strategies (<code>afterInteractive</code>, <code>lazyOnload</code>, <code>worker</code>).</p>
        <div class="mt-4 flex items-center gap-3 text-xs text-indigo-400">
            <span>Author: <strong>Instructor NectraDigital</strong></span>
            <span>•</span>
            <span>GDPR &amp; LCP Compliance</span>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <h2 class="text-xl font-black text-slate-800">1. Zero-Shift Fonts: `app/layout.tsx`</h2>
        <div class="w3-example-box">
            <div class="w3-example-title flex items-center justify-between">
                <span>app/layout.tsx - Next/Font Setup</span>
                <span class="text-xs bg-slate-800 text-slate-300 px-2 py-0.5 rounded font-mono">TypeScript</span>
            </div>
            <pre class="w3-code notranslate"><code class="language-typescript">import { Inter, JetBrains_Mono } from "next/font/google";
import Script from "next/script";

const inter = Inter({
  subsets: ["latin"],
  variable: "--font-inter",
  display: "swap"
});

const jetbrainsMono = JetBrains_Mono({
  subsets: ["latin"],
  variable: "--font-mono",
  display: "swap"
});

export default function RootLayout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="en" className={`${inter.variable} ${jetbrainsMono.variable}`}>
      <body className="font-sans antialiased bg-slate-50 text-slate-900">
        {children}

        {/* Third-party Analytics script loaded without blocking UI thread */}
        <Script
          src="https://www.googletagmanager.com/gtag/js?id=G-NECTRA2026"
          strategy="afterInteractive"
        />
      </body>
    </html>
  );
}</code></pre>
            <div class="try-act-row">
                <button type="button" class="w3-btn-try">Try it Yourself &raquo;</button>
                <button type="button" class="w3-btn-copy">Copy Code</button>
            </div>
        </div>
    </div>
</div>
HTML
        ],

        // ==========================================
        // CHAPTER 44: Testing Next.js Applications
        // ==========================================
        [
            'order' => 44,
            'section' => 'Testing & Production Deployment',
            'name' => 'Testing Next.js Applications: Unit Tests with Jest & React Testing Library, and E2E with Playwright',
            'keywords' => 'testing next.js, jest nextjs, react testing library next.js 14, playwright e2e nextjs, mock server actions, test runner',
            'quiz' => [
                'question' => 'Which tool is recommended for end-to-end (E2E) testing of Next.js Server Actions and streaming App Router pages?',
                'options' => [
                    'curl commands in bash',
                    'Playwright, which runs real headless browser engines (Chromium, Firefox, WebKit) and tests actual user clicks, network streaming, and server transitions',
                    'React.renderToString()',
                    'console.log'
                ],
                'correct' => 1,
                'explanation' => 'Playwright is the industry-standard E2E framework for Next.js, allowing full testing of actual server responses, cookies, client hydration, and user journeys across all major browsers.'
            ],
            'content' => <<<'HTML'
<div class="content-body space-y-6">
    <div class="bg-gradient-to-r from-slate-900 to-indigo-950 p-6 rounded-2xl border border-indigo-900/50 shadow-xl text-white">
        <span class="px-3 py-1 bg-indigo-500/20 text-indigo-300 rounded-full text-xs font-mono font-bold uppercase tracking-wider border border-indigo-500/30">Module 10: Testing &amp; Production</span>
        <h1 class="text-3xl font-black mt-3 mb-2">Testing Next.js Applications: Unit &amp; E2E</h1>
        <p class="text-slate-300 text-sm leading-relaxed">Build robust test suites for modern Next.js systems. Learn component unit testing with Jest and React Testing Library, mocking Server Actions, and end-to-end (E2E) browser automation with Playwright.</p>
        <div class="mt-4 flex items-center gap-3 text-xs text-indigo-400">
            <span>Author: <strong>Instructor NectraDigital</strong></span>
            <span>•</span>
            <span>Quality Assurance &amp; CI/CD</span>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <h2 class="text-xl font-black text-slate-800">1. Unit Testing Client Components with Jest &amp; RTL</h2>
        <div class="w3-example-box">
            <div class="w3-example-title flex items-center justify-between">
                <span>__tests__/Counter.test.tsx - Unit Test Specification</span>
                <span class="text-xs bg-slate-800 text-slate-300 px-2 py-0.5 rounded font-mono">TypeScript</span>
            </div>
            <pre class="w3-code notranslate"><code class="language-typescript">import { render, screen, fireEvent } from "@testing-library/react";
import "@testing-library/jest-dom";
import { Counter } from "@/components/Counter";

describe("Counter Component", () => {
  it("renders with initial value of 0", () => {
    render(<Counter />);
    expect(screen.getByTestId("counter-display")).toHaveTextContent("0");
  });

  it("increments value when increment button is clicked", () => {
    render(<Counter />);
    const incrementBtn = screen.getByRole("button", { name: /increment/i });
    fireEvent.click(incrementBtn);
    expect(screen.getByTestId("counter-display")).toHaveTextContent("1");
  });
});</code></pre>
            <div class="try-act-row">
                <button type="button" class="w3-btn-try">Try it Yourself &raquo;</button>
                <button type="button" class="w3-btn-copy">Copy Code</button>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <h2 class="text-xl font-black text-slate-800">2. E2E Testing with Playwright</h2>
        <div class="w3-example-box">
            <div class="w3-example-title flex items-center justify-between">
                <span>e2e/auth.spec.ts - Full Playwright User Journey</span>
                <span class="text-xs bg-slate-800 text-slate-300 px-2 py-0.5 rounded font-mono">TypeScript</span>
            </div>
            <pre class="w3-code notranslate"><code class="language-typescript">import { test, expect } from "@playwright/test";

test.describe("Authentication & Route Protection Flow", () => {
  test("redirects unauthenticated user from /admin to /login", async ({ page }) => {
    await page.goto("/admin");
    await expect(page).toHaveURL(/.*\/login\?callbackUrl=%2Fadmin/);
  });

  test("allows authorized administrator into admin dashboard", async ({ page }) => {
    await page.goto("/login");
    await page.fill('input[name="email"]', "admin@nectradigital.com");
    await page.fill('input[name="password"]', "NectraPass2026!");
    await page.click('button[type="submit"]');

    await expect(page).toHaveURL(/.*\/admin/);
    await expect(page.locator("h1")).toContainText("Enterprise Dashboard");
  });
});</code></pre>
            <div class="try-act-row">
                <button type="button" class="w3-btn-try">Try it Yourself &raquo;</button>
                <button type="button" class="w3-btn-copy">Copy Code</button>
            </div>
        </div>
    </div>
</div>
HTML
        ],

        // ==========================================
        // CHAPTER 45: Production Build Analysis & Docker Deployment
        // ==========================================
        [
            'order' => 45,
            'section' => 'Testing & Production Deployment',
            'name' => 'Production Build Analysis: @next/bundle-analyzer, Core Web Vitals Auditing & Docker Deployment',
            'keywords' => 'bundle-analyzer nextjs, next.js docker container, standalone output, core web vitals audit, next.js production build, lightweight dockerfile',
            'quiz' => [
                'question' => 'What is the purpose of setting output: "standalone" in next.config.mjs for Docker containers?',
                'options' => [
                    'It compiles Next.js into a single .exe file',
                    'It automatically traces and bundles only the exact node_modules dependencies needed for production, reducing Docker image size from 1GB+ down to under 120MB',
                    'It disables SSR',
                    'It makes all pages static HTML'
                ],
                'correct' => 1,
                'explanation' => 'Next.js standalone output copies only the precise production dependencies needed to run the server, drastically shrinking container footprints and accelerating CI/CD deployment pipelines.'
            ],
            'content' => <<<'HTML'
<div class="content-body space-y-6">
    <div class="bg-gradient-to-r from-slate-900 to-indigo-950 p-6 rounded-2xl border border-indigo-900/50 shadow-xl text-white">
        <span class="px-3 py-1 bg-indigo-500/20 text-indigo-300 rounded-full text-xs font-mono font-bold uppercase tracking-wider border border-indigo-500/30">Module 10: Testing &amp; Production</span>
        <h1 class="text-3xl font-black mt-3 mb-2">Production Build Analysis &amp; Docker Containerization</h1>
        <p class="text-slate-300 text-sm leading-relaxed">Analyze client bundle sizes using <code>@next/bundle-analyzer</code> and deploy lean, multi-stage Docker containers configured with <code>output: "standalone"</code> for Kubernetes, AWS ECS, or Google Cloud Run.</p>
        <div class="mt-4 flex items-center gap-3 text-xs text-indigo-400">
            <span>Author: <strong>Instructor NectraDigital</strong></span>
            <span>•</span>
            <span>Container Ops &amp; DevOps</span>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <h2 class="text-xl font-black text-slate-800">1. Multi-Stage Production Dockerfile</h2>
        <div class="w3-example-box">
            <div class="w3-example-title flex items-center justify-between">
                <span>Dockerfile - Enterprise Multi-Stage Build</span>
                <span class="text-xs bg-slate-800 text-slate-300 px-2 py-0.5 rounded font-mono">Docker</span>
            </div>
            <pre class="w3-code notranslate"><code class="language-dockerfile"># Stage 1: Base image
FROM node:20-alpine AS base

# Stage 2: Install dependencies
FROM base AS deps
RUN apk add --no-cache libc6-compat
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci

# Stage 3: Build application
FROM base AS builder
WORKDIR /app
COPY --from=deps /app/node_modules ./node_modules
COPY . .
ENV NEXT_TELEMETRY_DISABLED=1
RUN npm run build

# Stage 4: Production Runner (Ultra-lightweight)
FROM base AS runner
WORKDIR /app
ENV NODE_ENV=production
ENV NEXT_TELEMETRY_DISABLED=1
RUN addgroup --system --gid 1001 nodejs
RUN adduser --system --uid 1001 nextjs

COPY --from=builder /app/public ./public
# Automatically leveraged by output: "standalone" in next.config.js
COPY --from=builder --chown=nextjs:nodejs /app/.next/standalone ./
COPY --from=builder --chown=nextjs:nodejs /app/.next/static ./.next/static

USER nextjs
EXPOSE 3000
ENV PORT=3000
ENV HOSTNAME="0.0.0.0"

CMD ["node", "server.js"]</code></pre>
            <div class="try-act-row">
                <button type="button" class="w3-btn-try">Try it Yourself &raquo;</button>
                <button type="button" class="w3-btn-copy">Copy Code</button>
            </div>
        </div>
    </div>
</div>
HTML
        ],

        // ==========================================
        // CHAPTER 46: Capstone Project Architecture & Design
        // ==========================================
        [
            'order' => 46,
            'section' => 'Capstone Enterprise Project',
            'name' => 'Capstone Project: Architecture & Database Design of a Full-Stack Enterprise CloudMetrics Platform',
            'keywords' => 'nextjs capstone project, full stack next.js project, cloudmetrics saas, next.js architecture design, prisma schema nextjs, server actions project',
            'quiz' => [
                'question' => 'In a full-stack Next.js SaaS architecture, what is the best strategy for handling high-volume telemetry queries alongside transactional mutations?',
                'options' => [
                    'Write all queries directly in the client side using Firebase client SDK without security rules',
                    'Use Server Components with connection pooling (e.g. Prisma + PgBouncer) and Data Cache tags for telemetry reads, while routing write mutations through Zod-validated Server Actions with revalidateTag()',
                    'Store telemetry in browser cookies',
                    'Disable caching globally across the app'
                ],
                'correct' => 1,
                'explanation' => 'This hybrid architecture leverages Server Components and tag-based Data Caching for blazingly fast read performance, while Server Actions enforce strict schema validation and targeted cache invalidation upon writes.'
            ],
            'content' => <<<'HTML'
<div class="content-body space-y-6">
    <div class="bg-gradient-to-r from-slate-900 to-indigo-950 p-6 rounded-2xl border border-indigo-900/50 shadow-xl text-white">
        <span class="px-3 py-1 bg-indigo-500/20 text-indigo-300 rounded-full text-xs font-mono font-bold uppercase tracking-wider border border-indigo-500/30">Capstone Project (Part 1 of 2)</span>
        <h1 class="text-3xl font-black mt-3 mb-2">Capstone Project: Enterprise CloudMetrics Platform Architecture</h1>
        <p class="text-slate-300 text-sm leading-relaxed">Design and engineer a production-ready, full-stack B2B SaaS platform: <strong>Enterprise CloudMetrics</strong>. Master schema modeling, database relations, Server Action contracts, and data-flow topology.</p>
        <div class="mt-4 flex items-center gap-3 text-xs text-indigo-400">
            <span>Author: <strong>Instructor NectraDigital</strong></span>
            <span>•</span>
            <span>Full-Stack SaaS Blueprint</span>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <h2 class="text-xl font-black text-slate-800">1. SaaS Platform Requirements &amp; Scope</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-2">
                <h3 class="text-sm font-bold text-slate-800">Key Functional Capabilities:</h3>
                <ul class="text-xs text-slate-600 list-disc list-inside space-y-1">
                    <li>Real-time telemetry KPIs (MRR, Active Nodes, Error Rates, Uptime)</li>
                    <li>Organization &amp; User Member Management with RBAC</li>
                    <li>Server Action mutations for provisioning server nodes</li>
                    <li>Optimistic UI for node status toggles</li>
                    <li>Tag-based multi-layer cache revalidation</li>
                </ul>
            </div>
            <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-2">
                <h3 class="text-sm font-bold text-slate-800">Enterprise Stack:</h3>
                <ul class="text-xs text-slate-600 list-disc list-inside space-y-1">
                    <li>Next.js 14/15 App Router (RSC + Client Islands)</li>
                    <li>TypeScript Strict Mode with Zod runtime validation</li>
                    <li>Prisma ORM with PostgreSQL database</li>
                    <li>Auth.js v5 with HTTP-Only Session Cookies</li>
                    <li>Tailwind CSS Design System</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <h2 class="text-xl font-black text-slate-800">2. Prisma Schema Design: `prisma/schema.prisma`</h2>
        <div class="w3-example-box">
            <div class="w3-example-title flex items-center justify-between">
                <span>prisma/schema.prisma - Multi-Tenant Relational Schema</span>
                <span class="text-xs bg-slate-800 text-slate-300 px-2 py-0.5 rounded font-mono">Prisma</span>
            </div>
            <pre class="w3-code notranslate"><code class="language-prisma">datasource db {
  provider = "postgresql"
  url      = env("DATABASE_URL")
}

generator client {
  provider = "prisma-client-js"
}

enum Role {
  OWNER
  ADMIN
  OPERATOR
  VIEWER
}

enum NodeStatus {
  HEALTHY
  DEGRADED
  MAINTENANCE
  TERMINATED
}

model Organization {
  id        String       @id @default(cuid())
  name      String
  slug      String       @unique
  plan      String       @default("ENTERPRISE")
  users     User[]
  nodes     ServerNode[]
  createdAt DateTime     @default(now())
}

model User {
  id             String       @id @default(cuid())
  email          String       @unique
  name           String
  role           Role         @default(OPERATOR)
  organizationId String
  organization   Organization @relation(fields: [organizationId], references: [id])
  createdAt      DateTime     @default(now())
}

model ServerNode {
  id             String       @id @default(cuid())
  name           String
  region         String
  ipAddress      String
  status         NodeStatus   @default(HEALTHY)
  cpuUsage       Float        @default(0.0)
  memoryUsage    Float        @default(0.0)
  organizationId String
  organization   Organization @relation(fields: [organizationId], references: [id])
  updatedAt      DateTime     @updatedAt
}</code></pre>
            <div class="try-act-row">
                <button type="button" class="w3-btn-try">Try it Yourself &raquo;</button>
                <button type="button" class="w3-btn-copy">Copy Code</button>
            </div>
        </div>
    </div>
</div>
HTML
        ],

        // ==========================================
        // CHAPTER 47: Capstone Project Live Interactive Demo
        // ==========================================
        [
            'order' => 47,
            'section' => 'Capstone Enterprise Project',
            'name' => 'Capstone Project: Interactive Live Demo – Enterprise Analytics & User Management Dashboard',
            'keywords' => 'next.js live project, interactive nextjs demo, capstone dashboard, nextjs live demo, server actions live simulation, cloudmetrics dashboard',
            'quiz' => [
                'question' => 'How does the Capstone Project demonstrate modern Next.js best practices?',
                'options' => [
                    'By avoiding React and using jQuery instead',
                    'By integrating stateful client interactive islands with optimistic updates, role filtering, simulated server actions, and responsive telemetry KPIs',
                    'By making the entire dashboard a single static HTML page',
                    'By removing all styling'
                ],
                'correct' => 1,
                'explanation' => 'The capstone demonstrates Next.js architecture: client interactivity for instant user feedback, Server Action simulation with payload feedback, and responsive data visualization.'
            ],
            'content' => <<<'HTML'
<div class="content-body space-y-6">
    <div class="bg-gradient-to-r from-slate-900 to-indigo-950 p-6 rounded-2xl border border-indigo-900/50 shadow-xl text-white">
        <span class="px-3 py-1 bg-emerald-500/20 text-emerald-300 rounded-full text-xs font-mono font-bold uppercase tracking-wider border border-emerald-500/30">Capstone Project (Part 2 of 2 - Live Interactive Demo)</span>
        <h1 class="text-3xl font-black mt-3 mb-2">Enterprise CloudMetrics SaaS Dashboard</h1>
        <p class="text-slate-300 text-sm leading-relaxed">Experience the fully functional, interactive live demo of our Enterprise Capstone Project. Test real-time cluster telemetry, toggle node operational health states optimistically, deploy new server instances, and audit logs.</p>
        <div class="mt-4 flex items-center gap-3 text-xs text-indigo-400">
            <span>Author: <strong>Instructor NectraDigital</strong></span>
            <span>•</span>
            <span>Complete Full-Stack Sandbox</span>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <h2 class="text-xl font-black text-slate-800">1. Interactive Live Capstone Application</h2>
        <p class="text-slate-600 text-sm leading-relaxed">Click <strong>Try it Yourself &raquo;</strong> below to run this complete Next.js dashboard right inside the sandbox! You can deploy nodes, search clusters, filter by operational status, and inspect simulated server mutation payloads.</p>

        <div class="w3-example-box">
            <div class="w3-example-title flex items-center justify-between">
                <span>CloudMetricsDashboard.jsx - Full Interactive SaaS Demo</span>
                <span class="text-xs bg-slate-800 text-slate-300 px-2 py-0.5 rounded font-mono">React / Next.js</span>
            </div>
            <pre class="w3-code notranslate"><code class="language-jsx">function CloudMetricsDashboard() {
  const [nodes, setNodes] = React.useState([
    { id: "node_01", name: "prod-us-east-cluster-01", region: "us-east-1", status: "HEALTHY", cpu: 42, memory: 68 },
    { id: "node_02", name: "prod-eu-west-cluster-02", region: "eu-west-1", status: "HEALTHY", cpu: 58, memory: 74 },
    { id: "node_03", name: "prod-ap-south-cluster-03", region: "ap-south-1", status: "DEGRADED", cpu: 89, memory: 91 },
    { id: "node_04", name: "staging-us-central-04", region: "us-central1", status: "MAINTENANCE", cpu: 12, memory: 28 },
  ]);

  const [filter, setFilter] = React.useState("ALL");
  const [search, setSearch] = React.useState("");
  const [newNodeName, setNewNodeName] = React.useState("");
  const [newNodeRegion, setNewNodeRegion] = React.useState("us-east-1");
  const [actionLog, setActionLog] = React.useState("System initialized. Telemetry sync active.");
  const [isDeploying, setIsDeploying] = React.useState(false);

  // Toggle node status (Optimistic UI)
  const toggleNodeHealth = (id) => {
    setNodes(prev => prev.map(n => {
      if (n.id === id) {
        const nextStatus = n.status === "HEALTHY" ? "DEGRADED" : n.status === "DEGRADED" ? "MAINTENANCE" : "HEALTHY";
        setActionLog(`[Server Action] revalidateTag('nodes') triggered. Node ${n.name} status updated to ${nextStatus}.`);
        return { ...n, status: nextStatus };
      }
      return n;
    }));
  };

  // Add new server node (Server Action Simulation)
  const handleDeployNode = (e) => {
    e.preventDefault();
    if (!newNodeName.trim()) return;

    setIsDeploying(true);
    setActionLog(`[Server Action] provisionServerNode({ name: "${newNodeName}", region: "${newNodeRegion}" }) dispatched...`);

    setTimeout(() => {
      const newNode = {
        id: "node_" + Date.now().toString().slice(-4),
        name: newNodeName.trim(),
        region: newNodeRegion,
        status: "HEALTHY",
        cpu: Math.floor(Math.random() * 30) + 15,
        memory: Math.floor(Math.random() * 40) + 20
      };
      setNodes(prev => [newNode, ...prev]);
      setNewNodeName("");
      setIsDeploying(false);
      setActionLog(`[Server Action Success] Node ${newNode.name} deployed. revalidatePath('/dashboard') executed.`);
    }, 600);
  };

  const filteredNodes = nodes.filter(n => {
    const matchesFilter = filter === "ALL" || n.status === filter;
    const matchesSearch = n.name.toLowerCase().includes(search.toLowerCase()) || n.region.toLowerCase().includes(search.toLowerCase());
    return matchesFilter && matchesSearch;
  });

  const healthyCount = nodes.filter(n => n.status === "HEALTHY").length;
  const avgCpu = Math.round(nodes.reduce((acc, curr) => acc + curr.cpu, 0) / nodes.length);

  return (
    <div className="p-4 sm:p-6 max-w-4xl mx-auto bg-slate-900 text-slate-100 rounded-2xl shadow-2xl border border-slate-800 font-sans">
      {/* Top Header */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-800 gap-3">
        <div>
          <div className="flex items-center gap-2">
            <span className="w-3 h-3 rounded-full bg-emerald-400 animate-pulse"></span>
            <h2 className="text-xl font-black text-white">Enterprise CloudMetrics SaaS</h2>
          </div>
          <p className="text-xs text-slate-400 mt-0.5">Author: Instructor NectraDigital • Next.js App Router Architecture</p>
        </div>
        <div className="flex items-center gap-2 text-xs">
          <span className="px-3 py-1 bg-slate-800 border border-slate-700 rounded-full font-mono text-emerald-400 font-bold">
            Cluster Health: {Math.round((healthyCount / nodes.length) * 100)}%
          </span>
        </div>
      </div>

      {/* Telemetry Metric KPI Cards */}
      <div className="grid grid-cols-2 sm:grid-cols-4 gap-3 my-4">
        <div className="p-3 bg-slate-800/80 rounded-xl border border-slate-700/80">
          <span className="text-[11px] uppercase font-bold text-slate-400">Total Nodes</span>
          <div className="text-2xl font-black text-white mt-1">{nodes.length}</div>
          <span className="text-[10px] text-emerald-400">All regions active</span>
        </div>
        <div className="p-3 bg-slate-800/80 rounded-xl border border-slate-700/80">
          <span className="text-[11px] uppercase font-bold text-slate-400">Healthy Nodes</span>
          <div className="text-2xl font-black text-emerald-400 mt-1">{healthyCount}</div>
          <span className="text-[10px] text-slate-400">{nodes.length - healthyCount} attention</span>
        </div>
        <div className="p-3 bg-slate-800/80 rounded-xl border border-slate-700/80">
          <span className="text-[11px] uppercase font-bold text-slate-400">Avg CPU Load</span>
          <div className="text-2xl font-black text-indigo-400 mt-1">{avgCpu}%</div>
          <span className="text-[10px] text-slate-400">Within threshold</span>
        </div>
        <div className="p-3 bg-slate-800/80 rounded-xl border border-slate-700/80">
          <span className="text-[11px] uppercase font-bold text-slate-400">Uptime SLA</span>
          <div className="text-2xl font-black text-amber-400 mt-1">99.98%</div>
          <span className="text-[10px] text-slate-400">Past 30 days</span>
        </div>
      </div>

      {/* Deploy Server Node Form (Server Action Simulator) */}
      <form onSubmit={handleDeployNode} className="p-3 bg-slate-800/50 rounded-xl border border-slate-700/60 mb-4 flex flex-col sm:flex-row gap-2 items-center">
        <input 
          type="text" 
          placeholder="New Node Name (e.g. prod-asia-cluster-05)" 
          value={newNodeName} 
          onChange={(e) => setNewNodeName(e.target.value)} 
          className="flex-1 w-full sm:w-auto bg-slate-900 border border-slate-700 rounded-lg px-3 py-1.5 text-xs text-white focus:outline-none focus:border-indigo-500 font-mono"
        />
        <select 
          value={newNodeRegion} 
          onChange={(e) => setNewNodeRegion(e.target.value)} 
          className="w-full sm:w-auto bg-slate-900 border border-slate-700 rounded-lg px-3 py-1.5 text-xs text-white focus:outline-none focus:border-indigo-500 font-mono"
        >
          <option value="us-east-1">us-east-1 (N. Virginia)</option>
          <option value="eu-west-1">eu-west-1 (Ireland)</option>
          <option value="ap-south-1">ap-south-1 (Mumbai)</option>
          <option value="us-central1">us-central1 (Iowa)</option>
        </select>
        <button 
          type="submit" 
          disabled={isDeploying || !newNodeName.trim()} 
          className="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-500 disabled:opacity-50 text-white font-bold px-4 py-1.5 rounded-lg text-xs transition whitespace-nowrap cursor-pointer"
        >
          {isDeploying ? "Provisioning..." : "+ Deploy Node (Action)"}
        </button>
      </form>

      {/* Filter and Search Bar */}
      <div className="flex flex-col sm:flex-row items-center justify-between gap-2 mb-3">
        <input 
          type="text" 
          placeholder="Search by node name or region..." 
          value={search} 
          onChange={(e) => setSearch(e.target.value)} 
          className="w-full sm:w-64 bg-slate-950 border border-slate-800 rounded-lg px-3 py-1.5 text-xs text-white focus:outline-none focus:border-emerald-500"
        />
        <div className="flex gap-1 w-full sm:w-auto overflow-x-auto">
          {["ALL", "HEALTHY", "DEGRADED", "MAINTENANCE"].map(st => (
            <button
              key={st}
              onClick={() => setFilter(st)}
              className={`px-2.5 py-1 text-[11px] font-bold rounded-lg border transition cursor-pointer ${filter === st ? 'bg-emerald-600 text-white border-emerald-500' : 'bg-slate-800 text-slate-400 border-slate-700 hover:text-white'}`}
            >
              {st}
            </button>
          ))}
        </div>
      </div>

      {/* Node Table */}
      <div className="overflow-x-auto border border-slate-800 rounded-xl bg-slate-950/60 mb-4">
        <table className="w-full text-xs text-left">
          <thead className="bg-slate-800/80 text-slate-400 uppercase text-[10px] font-bold tracking-wider border-b border-slate-800">
            <tr>
              <th className="p-3">Node Name</th>
              <th className="p-3">Region</th>
              <th className="p-3">Status</th>
              <th className="p-3">CPU</th>
              <th className="p-3">Memory</th>
              <th className="p-3 text-right">Quick Action</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-slate-800/60 font-mono">
            {filteredNodes.length > 0 ? (
              filteredNodes.map(node => (
                <tr key={node.id} className="hover:bg-slate-900/50 transition">
                  <td className="p-3 font-semibold text-white">{node.name}</td>
                  <td className="p-3 text-slate-400">{node.region}</td>
                  <td className="p-3">
                    <span className={`px-2 py-0.5 rounded-full text-[10px] font-bold ${
                      node.status === 'HEALTHY' ? 'bg-emerald-950 text-emerald-400 border border-emerald-800' :
                      node.status === 'DEGRADED' ? 'bg-rose-950 text-rose-400 border border-rose-800' :
                      'bg-amber-950 text-amber-400 border border-amber-800'
                    }`}>
                      {node.status}
                    </span>
                  </td>
                  <td className="p-3 text-slate-300">{node.cpu}%</td>
                  <td className="p-3 text-slate-300">{node.memory}%</td>
                  <td className="p-3 text-right">
                    <button 
                      onClick={() => toggleNodeHealth(node.id)}
                      className="px-2 py-1 bg-slate-800 hover:bg-slate-700 text-[11px] text-slate-200 rounded border border-slate-700 transition cursor-pointer font-sans"
                    >
                      Cycle Status
                    </button>
                  </td>
                </tr>
              ))
            ) : (
              <tr>
                <td colSpan={6} className="p-4 text-center text-slate-500 font-sans text-xs">
                  No server nodes match the current filter criteria.
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </div>

      {/* Real-time Server Action Telemetry Log */}
      <div className="p-3 bg-slate-950 border border-slate-800 rounded-xl">
        <div className="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1 flex items-center gap-1.5">
          <span className="w-2 h-2 rounded-full bg-emerald-500"></span>
          Live Server Action &amp; Cache Revalidation Telemetry Log:
        </div>
        <div className="font-mono text-xs text-emerald-400/90 truncate">
          {actionLog}
        </div>
      </div>
    </div>
  );
}</code></pre>
            <div class="try-act-row">
                <button type="button" class="w3-btn-try">Try it Yourself &raquo;</button>
                <button type="button" class="w3-btn-copy">Copy Code</button>
            </div>
        </div>
    </div>
</div>
HTML
        ],
    ];
}
