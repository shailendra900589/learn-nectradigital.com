<?php
// seed_nextjs_part2.php: Chapters 12 to 23 (RSC, Client Components, Data Fetching & Server Actions)

// -------------------------------------------------------------
// Chapter 12
// -------------------------------------------------------------
$content12 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700"><strong>React Server Components (RSC)</strong> represent the biggest paradigm shift in modern web architecture. In the Next.js App Router, all components are Server Components by default. They run <strong>exclusively on the server</strong> and never send their JavaScript dependencies or source code to the browser.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">React Server Components (RSC) vs. Traditional Client Components</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Capability / Metric</th>
                <th style="width: 35%;">React Server Component (RSC)</th>
                <th style="width: 40%;">Client Component (<code>'use client'</code>)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell">Bundle Size Impact</td>
                <td><strong>0 kB JavaScript sent to client:</strong> Heavy dependencies (e.g. <code>markdown-to-jsx</code>, <code>moment</code>, database drivers) stay on server.</td>
                <td>Sent and parsed by client browser JS bundle.</td>
            </tr>
            <tr>
                <td class="code-cell">Direct Database Access</td>
                <td><strong>Yes:</strong> Query PostgreSQL, Prisma, Drizzle, or Redis directly inside the component without an API layer.</td>
                <td><strong>No:</strong> Must call remote API via <code>fetch()</code>.</td>
            </tr>
            <tr>
                <td class="code-cell">React Hooks &amp; State</td>
                <td><strong>No:</strong> Cannot use <code>useState</code>, <code>useEffect</code>, or <code>useContext</code>.</td>
                <td>Full access to all React state and lifecycle hooks.</td>
            </tr>
            <tr>
                <td class="code-cell">Browser Event Listeners</td>
                <td><strong>No:</strong> Cannot use <code>onClick</code>, <code>onChange</code>, or browser APIs (<code>window</code>, <code>localStorage</code>).</td>
                <td>Full access to DOM event handlers and browser APIs.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. The Mental Model of RSC</h3>
<p class="text-slate-600 mb-4">Think of Server Components as backend template generators with superpowers: they execute on the server, render into a virtual UI representation (RSC Payload), and stream directly to the browser. Sensitive database credentials, API secret keys, and internal algorithms remain 100% secure.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Interactive Comparison: Server Component vs Client Component</div>
    <pre><code class="language-javascript">function RscMentalModelDemo() {
    const [selectedType, setSelectedType] = React.useState('server');

    return (
        &lt;div className="p-6 max-w-lg mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
            &lt;h3 className="text-base font-bold text-slate-900 mb-2"&gt;Server Component vs Client Component&lt;/h3&gt;
            &lt;p className="text-xs text-slate-500 mb-4"&gt;Switch between paradigms to understand execution boundaries:&lt;/p&gt;

            &lt;div className="flex gap-2 mb-4"&gt;
                &lt;button onClick={() =&gt; setSelectedType('server')} className={`px-3 py-1.5 text-xs font-bold rounded ${selectedType === 'server' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700'}`}&gt;
                    Server Component (Default)
                &lt;/button&gt;
                &lt;button onClick={() =&gt; setSelectedType('client')} className={`px-3 py-1.5 text-xs font-bold rounded ${selectedType === 'client' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-700'}`}&gt;
                    Client Component ('use client')
                &lt;/button&gt;
            &lt;/div&gt;

            {selectedType === 'server' ? (
                &lt;div className="p-4 bg-slate-900 text-slate-200 rounded-lg text-xs font-mono space-y-2"&gt;
                    &lt;div className="text-emerald-400"&gt;// Server Component (Zero Bundle Size)&lt;/div&gt;
                    &lt;div&gt;&lt;span className="text-indigo-400"&gt;export default async function&lt;/span&gt; ProductFeed() &#123;"&#123;"&#125;&lt;/div&gt;
                    &lt;div className="pl-4 text-slate-400"&gt;// Direct SQL query on server! No API route needed!&lt;/div&gt;
                    &lt;div className="pl-4"&gt;const products = &lt;span className="text-amber-400"&gt;await db.query&lt;/span&gt;('SELECT * FROM items');&lt;/div&gt;
                    &lt;div className="pl-4"&gt;&#123;"return <ProductGrid items={products} />;"&#125;&lt;/div&gt;
                    &lt;div&gt;&#123;"&#125;"&#125;&lt;/div&gt;
                    &lt;div className="border-t border-slate-800 pt-2 text-[10px] text-emerald-400 font-sans font-bold"&gt;
                        ✓ Client receives zero DB driver code or API secrets.
                    &lt;/div&gt;
                &lt;/div&gt;
            ) : (
                &lt;div className="p-4 bg-indigo-50 border border-indigo-200 rounded-lg text-xs font-mono space-y-2"&gt;
                    &lt;div className="text-indigo-700 font-bold"&gt;// Client Component (Interactive)&lt;/div&gt;
                    &lt;div className="text-slate-800"&gt;'use client';&lt;/div&gt;
                    &lt;div className="text-slate-800"&gt;&lt;span className="text-indigo-600 font-bold"&gt;export default function&lt;/span&gt; LikeButton() &#123;"&#123;"&#125;&lt;/div&gt;
                    &lt;div className="pl-4 text-slate-600"&gt;const [likes, setLikes] = useState(0);&lt;/div&gt;
                    &lt;div className="pl-4 text-slate-600"&gt;&#123;"return <button onClick={() => setLikes(l + 1)}>Likes: {likes}</button>;"&#125;&lt;/div&gt;
                    &lt;div className="text-slate-800"&gt;&#123;"&#125;"&#125;&lt;/div&gt;
                    &lt;div className="border-t border-indigo-200 pt-2 text-[10px] text-indigo-700 font-sans font-bold"&gt;
                        ✓ Handles DOM click events and re-renders in the browser.
                    &lt;/div&gt;
                &lt;/div&gt;
            )}
        &lt;/div&gt;
    );
}</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 12, 'React Server & Client Components', 'React Server Components (RSC) Deep Dive: Mental Model, Zero Bundle Size & Security', $content12, 'react server components, rsc in nextjs, zero bundle size, server components vs client components, nextjs rsc mental model');

// -------------------------------------------------------------
// Chapter 13
// -------------------------------------------------------------
$content13 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">When your UI requires interactive browser capabilities (such as clicking buttons with <code>onClick</code>, managing dynamic state with <code>useState</code>, or accessing <code>window.navigator</code>), you declare a <strong>Client Component</strong> using the <strong><code>'use client'</code></strong> directive at the top of the file.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">When to Use Server Components vs. Client Components</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 50%;">Use Server Components (Default) When:</th>
                <th style="width: 50%;">Use Client Components (<code>'use client'</code>) When:</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Fetching data from databases, microservices, or APIs.</td>
                <td>Attaching event listeners (<code>onClick</code>, <code>onChange</code>, <code>onKeyDown</code>).</td>
            </tr>
            <tr>
                <td>Keeping secret keys, tokens, and database passwords secure.</td>
                <td>Using state and lifecycle effects (<code>useState</code>, <code>useReducer</code>, <code>useEffect</code>).</td>
            </tr>
            <tr>
                <td>Large dependencies that should not increase client bundle size.</td>
                <td>Using browser APIs (<code>localStorage</code>, <code>geolocation</code>, <code>window</code>).</td>
            </tr>
            <tr>
                <td>Rendering static marketing content, headers, footers, blogs.</td>
                <td>Using custom React hooks that depend on client state.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Pushing Client Components to the Leaves</h3>
<p class="text-slate-600 mb-4">A critical Next.js architectural best practice is to <strong>push Client Components to the leaves of your component tree</strong>. Instead of marking an entire page as <code>'use client'</code>, keep the page as a Server Component and only mark the interactive button or search bar as a Client Component.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Interactive Demonstration: Client Component Interactive Leaf Node</div>
    <pre><code class="language-javascript">// 'use client';
function InteractiveCartLeaf() {
    const [quantity, setQuantity] = React.useState(1);
    const unitPrice = 249;

    return (
        &lt;div className="p-6 max-w-md mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
            &lt;div className="border-b pb-3 mb-3"&gt;
                &lt;span className="text-[10px] font-bold uppercase tracking-wider text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded"&gt;
                    Client Component Leaf Node ('use client')
                &lt;/span&gt;
                &lt;h3 className="text-base font-bold text-slate-900 mt-2"&gt;Enterprise Cloud Appliance&lt;/h3&gt;
                &lt;p className="text-xs text-slate-500"&gt;Unit Price: \${unitPrice}/month&lt;/p&gt;
            &lt;/div&gt;

            &lt;div className="flex items-center justify-between mb-4"&gt;
                &lt;span className="text-xs font-bold text-slate-600"&gt;Nodes Desired:&lt;/span&gt;
                &lt;div className="flex items-center gap-2"&gt;
                    &lt;button onClick={() =&gt; setQuantity(q =&gt; Math.max(1, q - 1))} className="w-7 h-7 bg-slate-100 hover:bg-slate-200 rounded font-bold text-sm"&gt;-&lt;/button&gt;
                    &lt;span className="text-sm font-bold w-6 text-center"&gt;{quantity}&lt;/span&gt;
                    &lt;button onClick={() =&gt; setQuantity(q =&gt; q + 1)} className="w-7 h-7 bg-slate-100 hover:bg-slate-200 rounded font-bold text-sm"&gt;+&lt;/button&gt;
                &lt;/div&gt;
            &lt;/div&gt;

            &lt;div className="p-3 bg-slate-50 border rounded-lg flex justify-between items-center text-xs"&gt;
                &lt;span className="text-slate-600 font-bold"&gt;Monthly Billed:&lt;/span&gt;
                &lt;strong className="text-emerald-700 text-sm font-black"&gt;\${quantity * unitPrice}&lt;/strong&gt;
            &lt;/div&gt;
        &lt;/div&gt;
    );
}</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 13, 'React Server & Client Components', 'Client Components (\'use client\'): When & Where to Use Client Interactivity', $content13, 'use client in nextjs, nextjs client components, client interactivity, push to leaves, useState in nextjs, event listeners nextjs');

// -------------------------------------------------------------
// Chapter 14
// -------------------------------------------------------------
$content14 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">A common point of confusion is how Server and Client Components interleave. You <strong>cannot import a Server Component directly into a Client Component</strong>, but you CAN pass a Server Component as a <strong>child or prop</strong> (<code>children: React.ReactNode</code>) to a Client Component!</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">Component Composition Rules in Next.js</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 35%;">Composition Pattern</th>
                <th style="width: 25%;">Permitted?</th>
                <th style="width: 40%;">Reason &amp; Technical Explanation</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell">Server Component imports Client Component</td>
                <td><strong class="text-emerald-600">YES &check;</strong></td>
                <td>Supported standard pattern: Server Component passes server-fetched props to client widget.</td>
            </tr>
            <tr>
                <td class="code-cell">Client Component imports Server Component</td>
                <td><strong class="text-red-600">NO &cross;</strong></td>
                <td>A Client Component cannot run server-side code (it converts imported files into client bundle!).</td>
            </tr>
            <tr>
                <td class="code-cell">Client Component renders Server Component via <code>children</code></td>
                <td><strong class="text-emerald-600">YES &check;</strong></td>
                <td><strong>Recommended Solution:</strong> Server Component renders first on server, and is passed into Client Component slot!</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. The "Children Slot" Solution</h3>
<pre class="bg-slate-900 text-slate-200 p-4 rounded-xl font-mono text-xs mb-4">
// 1. Client Component (Modal or Tab Wrapper)
'use client';
export default function ClientModalWrapper({ children }) {
  const [isOpen, setIsOpen] = useState(false);
  return (
    &lt;div&gt;
      &lt;button onClick={() =&gt; setIsOpen(true)}&gt;Open Modal&lt;/button&gt;
      {isOpen &amp;&amp; &lt;div className="modal"&gt;{children}&lt;/div&gt;}
    &lt;/div&gt;
  );
}

// 2. Server Component Page: Passes Server Component directly into children!
export default function Page() {
  return (
    &lt;ClientModalWrapper&gt;
      &lt;ServerDataHeavyFeed /&gt; {/* Runs on server with zero client bundle! */}
    &lt;/ClientModalWrapper&gt;
  );
}
</pre>

<div class="w3-example-box">
    <div class="w3-example-title">Interactive Composition Sandbox: Client Wrapper Hosting Server Slot</div>
    <pre><code class="language-javascript">function CompositionSimulator() {
    const [collapsed, setCollapsed] = React.useState(false);

    return (
        &lt;div className="p-6 max-w-lg mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
            &lt;div className="flex justify-between items-center mb-3"&gt;
                &lt;h3 className="text-base font-bold text-slate-900"&gt;Client Modal Hosting Server Child&lt;/h3&gt;
                &lt;button onClick={() =&gt; setCollapsed(!collapsed)} className="px-2.5 py-1 bg-indigo-600 text-white rounded text-xs font-bold shadow"&gt;
                    {collapsed ? 'Expand Card' : 'Collapse Card'}
                &lt;/button&gt;
            &lt;/div&gt;

            {!collapsed && (
                &lt;div className="p-4 bg-slate-900 text-slate-200 rounded-lg text-xs font-mono"&gt;
                    &lt;span className="text-emerald-400 font-bold block mb-1"&gt;[Server Component Child Content]&lt;/span&gt;
                    &lt;p className="text-slate-400"&gt;This content was rendered securely on the server with direct database connection pooling, then passed into the client wrapper via the children prop!&lt;/p&gt;
                &lt;/div&gt;
            )}
        &lt;/div&gt;
    );
}</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 14, 'React Server & Client Components', 'Component Composition Patterns: Passing Server Components as Children to Client Components', $content14, 'component composition nextjs, pass server component as children, rsc composition patterns, children prop in nextjs, interleave server and client');

// -------------------------------------------------------------
// Chapter 15
// -------------------------------------------------------------
$content15 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">Many popular third-party React libraries (such as theme providers, state managers like Redux/Zustand, or component libraries) rely on the React <strong>Context API</strong>. Because React Context is not supported in Server Components, you must wrap third-party providers in a dedicated <strong><code>'use client'</code> Provider Component</strong>.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">Using Context API in Next.js App Router</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Step</th>
                <th style="width: 35%;">File Location</th>
                <th style="width: 40%;">Implementation Pattern</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell">1. Create Client Wrapper</td>
                <td><code>components/providers/ThemeProvider.tsx</code></td>
                <td>Add <code>'use client'</code> at the top; export the third-party provider or custom Context.</td>
            </tr>
            <tr>
                <td class="code-cell">2. Wrap in Root Layout</td>
                <td><code>app/layout.tsx</code></td>
                <td>Import <code>&lt;ThemeProvider&gt;</code> and wrap around <code>{children}</code>.</td>
            </tr>
            <tr>
                <td class="code-cell">3. Maintain Server Nature</td>
                <td><code>app/layout.tsx</code> remains RSC</td>
                <td><strong>Crucial insight:</strong> Wrapping children in a Client Provider does NOT turn the children or root layout into Client Components!</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Provider Wrapper Blueprint</h3>
<pre class="bg-slate-900 text-slate-200 p-4 rounded-xl font-mono text-xs mb-4">
// components/providers.tsx
'use client';

import { ThemeProvider as NextThemesProvider } from 'next-themes';

export function Providers({ children }: { children: React.ReactNode }) {
  return (
    &lt;NextThemesProvider attribute="class" defaultTheme="system" enableSystem&gt;
      {children}
    &lt;/NextThemesProvider&gt;
  );
}
</pre>

<div class="w3-example-box">
    <div class="w3-example-title">Interactive Theme Context Provider Simulation</div>
    <pre><code class="language-javascript">const ThemeContext = React.createContext();

function ProvidersSimulationDemo() {
    const [theme, setTheme] = React.useState('dark');

    return (
        &lt;ThemeContext.Provider value={{ theme, toggle: () =&gt; setTheme(t =&gt; t === 'dark' ? 'light' : 'dark') }}&gt;
            &lt;div className={`p-6 max-w-lg mx-auto rounded-xl shadow border transition-colors ${theme === 'dark' ? 'bg-slate-900 text-white border-slate-800' : 'bg-white text-slate-900 border-slate-200'}`}&gt;
                &lt;div className="flex justify-between items-center mb-4"&gt;
                    &lt;h3 className="text-base font-bold"&gt;Client Provider Wrapper Demo&lt;/h3&gt;
                    &lt;button onClick={() =&gt; setTheme(t =&gt; t === 'dark' ? 'light' : 'dark')} className="px-3 py-1 bg-emerald-600 text-white rounded text-xs font-bold shadow"&gt;
                        Toggle: {theme.toUpperCase()}
                    &lt;/button&gt;
                &lt;/div&gt;
                &lt;p className="text-xs opacity-75"&gt;
                    Current Active Theme: &lt;strong className="font-mono text-emerald-400"&gt;{theme}&lt;/strong&gt;. Context consumed seamlessly via client wrapper.
                &lt;/p&gt;
            &lt;/div&gt;
        &lt;/ThemeContext.Provider&gt;
    );
}</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 15, 'React Server & Client Components', 'Third-Party Packages & Provider Wrappers in App Router (Context API in RSC)', $content15, 'nextjs context api, providers in nextjs app router, use client provider wrapper, third party packages nextjs, theme provider nextjs');

// -------------------------------------------------------------
// Chapter 16
// -------------------------------------------------------------
$content16 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">In full-stack Next.js applications, accidentally importing server-only modules (like database secrets, internal API keys, or private crypto utilities) into a Client Component can leak sensitive credentials to the browser. Next.js provides the <strong>server-only</strong> package to catch these mistakes at build time.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">Boundary Enforcement Packages: server-only & client-only</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Package</th>
                <th style="width: 35%;">Installation &amp; Usage</th>
                <th style="width: 40%;">Build-Time Behavior</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell"><code>server-only</code></td>
                <td><code>import 'server-only';</code></td>
                <td>Throws an immediate compilation build error if this file (or any file importing it) is imported into a Client Component.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>client-only</code></td>
                <td><code>import 'client-only';</code></td>
                <td>Throws a build error if imported into a Server Component (e.g. modules using <code>window</code>).</td>
            </tr>
            <tr>
                <td class="code-cell">Public Environment Variables</td>
                <td><code>NEXT_PUBLIC_API_URL</code></td>
                <td>Only variables prefixed with <code>NEXT_PUBLIC_</code> are exposed to the client browser!</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Securing Database Client Files</h3>
<pre class="bg-slate-900 text-slate-200 p-4 rounded-xl font-mono text-xs mb-4">
// lib/db.ts
import 'server-only'; // Guarantees this file will NEVER be bundled for client browser!

export async function getAdminUsers() {
  const secretKey = process.env.DATABASE_SECRET_KEY; // Safe from client eyes!
  return db.query('SELECT * FROM admins');
}
</pre>

<div class="w3-example-box">
    <div class="w3-example-title">Interactive Security Boundary Validator</div>
    <pre><code class="language-javascript">function SecurityBoundarySimulator() {
    const [envType, setEnvType] = React.useState('server');

    return (
        &lt;div className="p-6 max-w-lg mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
            &lt;h3 className="text-base font-bold text-slate-900 mb-2"&gt;Environment Boundary Audit&lt;/h3&gt;
            &lt;p className="text-xs text-slate-500 mb-4"&gt;Simulate what happens if private modules leak into client bundles:&lt;/p&gt;

            &lt;div className="flex gap-2 mb-4"&gt;
                &lt;button onClick={() =&gt; setEnvType('server')} className={`px-3 py-1.5 text-xs font-bold rounded ${envType === 'server' ? 'bg-emerald-600 text-white' : 'bg-slate-100'}`}&gt;
                    Safe: Server Module
                &lt;/button&gt;
                &lt;button onClick={() =&gt; setEnvType('leak')} className={`px-3 py-1.5 text-xs font-bold rounded ${envType === 'leak' ? 'bg-red-600 text-white' : 'bg-slate-100'}`}&gt;
                    Blocked: Accidental Client Import
                &lt;/button&gt;
            &lt;/div&gt;

            {envType === 'server' ? (
                &lt;div className="p-4 bg-emerald-50 border border-emerald-200 rounded-lg text-xs"&gt;
                    &lt;strong className="text-emerald-950 font-bold block mb-1"&gt;✓ Build Success (server-only respected)&lt;/strong&gt;
                    &lt;span className="text-emerald-800"&gt;process.env.DB_PASSWORD executed safely in isolated Node.js context.&lt;/span&gt;
                &lt;/div&gt;
            ) : (
                &lt;div className="p-4 bg-red-50 border border-red-200 rounded-lg text-xs"&gt;
                    &lt;strong className="text-red-900 font-bold block mb-1"&gt;🚨 Build Error: server-only Violation&lt;/strong&gt;
                    &lt;span className="text-red-700 font-mono text-[11px]"&gt;
                        Error: You're importing a component that needs "server-only". That only works in Server Components.
                    &lt;/span&gt;
                &lt;/div&gt;
            )}
        &lt;/div&gt;
    );
}</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 16, 'React Server & Client Components', 'Server-Only vs Client-Only Code: Enforcing Boundaries with server-only Package', $content16, 'server-only in nextjs, client-only package, secure database in nextjs, prevent secret leaks, NEXT_PUBLIC_ environment variables');

// -------------------------------------------------------------
// Chapter 17
// -------------------------------------------------------------
$content17 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">Data fetching in the Next.js App Router is built directly on native JavaScript <strong><code>async/await</code></strong> inside React Server Components. The legacy boilerplate methods (<code>useEffect</code>, <code>getStaticProps</code>, <code>getServerSideProps</code>) are completely replaced by simple native fetch statements.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">Data Fetching Evolution in Next.js</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Pattern</th>
                <th style="width: 45%;">Code Blueprint</th>
                <th style="width: 30%;">Resulting Cache Behavior</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell">Static Fetch (Default)</td>
                <td><code>await fetch('https://api.../posts')</code></td>
                <td><strong>Cached indefinitely</strong> at build time (equivalent to <code>getStaticProps</code>).</td>
            </tr>
            <tr>
                <td class="code-cell">Dynamic Fetch</td>
                <td><code>await fetch(url, { cache: 'no-store' })</code></td>
                <td><strong>Fetched fresh</strong> on every user request (equivalent to <code>getServerSideProps</code>).</td>
            </tr>
            <tr>
                <td class="code-cell">Time Revalidation (ISR)</td>
                <td><code>await fetch(url, { next: { revalidate: 60 } })</code></td>
                <td>Cached on CDN; automatically regenerates in background every 60 seconds.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Native Async Server Component</h3>
<pre class="bg-slate-900 text-slate-200 p-4 rounded-xl font-mono text-xs mb-4">
// app/users/page.tsx
export default async function UsersPage() {
  // Directly await data inside the component body!
  const res = await fetch('https://jsonplaceholder.typicode.com/users', {
    next: { revalidate: 3600 } // Cache for 1 hour
  });
  const users = await res.json();

  return (
    &lt;div&gt;
      &lt;h1&gt;Users Directory&lt;/h1&gt;
      &lt;ul&gt;
        {users.map((u) =&gt; &lt;li key={u.id}&gt;{u.name}&lt;/li&gt;)}
      &lt;/ul&gt;
    &lt;/div&gt;
  );
}
</pre>

<div class="w3-example-box">
    <div class="w3-example-title">Interactive Data Fetching Cache Strategy Visualizer</div>
    <pre><code class="language-javascript">function DataFetchCacheDemo() {
    const [cacheMode, setCacheMode] = React.useState('force-cache');

    const cacheDetails = {
        'force-cache': { label: 'Static (Default)', desc: 'Fetched once at build time. Cached indefinitely across all users for ultra-fast CDN delivery.', speed: '12ms' },
        'no-store': { label: 'Dynamic (no-store)', desc: 'Fetched fresh from origin server on every individual HTTP request. Always shows newest data.', speed: '240ms' },
        'revalidate-60': { label: 'ISR (Revalidate 60s)', desc: 'Served stale from cache while fresh data is generated in background after 60 seconds.', speed: '15ms' }
    };

    return (
        &lt;div className="p-6 max-w-lg mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
            &lt;h3 className="text-base font-bold text-slate-900 mb-3"&gt;Extended fetch() Cache Engine&lt;/h3&gt;
            &lt;div className="flex gap-2 mb-4"&gt;
                {Object.keys(cacheDetails).map(mode =&gt; (
                    &lt;button key={mode} onClick={() =&gt; setCacheMode(mode)} className={`px-2.5 py-1 text-xs font-bold rounded ${cacheMode === mode ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700'}`}&gt;
                        {cacheDetails[mode].label}
                    &lt;/button&gt;
                ))}
            &lt;/div&gt;

            &lt;div className="p-4 bg-slate-50 border rounded-lg text-xs"&gt;
                &lt;div className="flex justify-between items-center mb-2"&gt;
                    &lt;strong className="text-slate-900"&gt;Strategy: {cacheDetails[cacheMode].label}&lt;/strong&gt;
                    &lt;span className="text-emerald-700 font-mono font-bold bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200"&gt;
                        Latency: {cacheDetails[cacheMode].speed}
                    &lt;/span&gt;
                &lt;/div&gt;
                &lt;p className="text-slate-600 mb-3"&gt;{cacheDetails[cacheMode].desc}&lt;/p&gt;
                &lt;div className="bg-slate-900 text-emerald-400 p-2.5 rounded font-mono text-[11px]"&gt;
                    &#123;`fetch(url, $&#123;cacheMode === 'force-cache' ? "{ cache: 'force-cache' }" : cacheMode === 'no-store' ? "{ cache: 'no-store' }" : "{ next: { revalidate: 60 } }"&#125;)`&#125;
                &lt;/div&gt;
            &lt;/div&gt;
        &lt;/div&gt;
    );
}</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 17, 'Data Fetching, Streaming & Suspense', 'Modern Data Fetching in Server Components (async/await without useEffect or getStaticProps)', $content17, 'nextjs data fetching, async server components, extended fetch nextjs, cache no-store, next revalidate, isr in nextjs');

// -------------------------------------------------------------
// Chapter 18
// -------------------------------------------------------------
$content18 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">In traditional SSR, the browser must wait for the slowest database query to finish before receiving any HTML. Next.js solves this with <strong>Streaming Server-Side Rendering (SSR)</strong> using React <strong>&lt;Suspense&gt;</strong>, allowing fast parts of the page to stream instantly while slow parts load progressively in the background.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">Blocking SSR vs. Streaming SSR</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">SSR Strategy</th>
                <th style="width: 35%;">User Experience (Time to First Byte)</th>
                <th style="width: 40%;">How Suspense Works</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell">Traditional Blocking SSR</td>
                <td>White blank screen until <strong>all</strong> database queries complete (Slowest query blocks entire page).</td>
                <td>No Suspense. Client waits for 100% of data before receiving byte 1 of HTML.</td>
            </tr>
            <tr>
                <td class="code-cell">Streaming SSR with Suspense</td>
                <td><strong>Instant TTFB:</strong> Shell, header, navigation, and loading skeletons render in milliseconds.</td>
                <td>Slow components are wrapped in <code>&lt;Suspense fallback={&lt;Skeleton /&gt;}&gt;</code>. Chunks stream over HTTP as they finish!</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Granular Suspense Boundaries</h3>
<pre class="bg-slate-900 text-slate-200 p-4 rounded-xl font-mono text-xs mb-4">
import { Suspense } from 'react';
import Header from '@/components/Header';
import SlowDatabaseAnalytics from '@/components/SlowAnalytics';
import AnalyticsSkeleton from '@/components/AnalyticsSkeleton';

export default function Dashboard() {
  return (
    &lt;div&gt;
      &lt;Header /&gt; {/* Renders immediately! */}
      &lt;Suspense fallback={&lt;AnalyticsSkeleton /&gt;}&gt;
        &lt;SlowDatabaseAnalytics /&gt; {/* Streams in when ready! */}
      &lt;/Suspense&gt;
    &lt;/div&gt;
  );
}
</pre>

<div class="w3-example-box">
    <div class="w3-example-title">Interactive Streaming Suspense Simulator</div>
    <pre><code class="language-javascript">function StreamingSuspenseDemo() {
    const [loaded, setLoaded] = React.useState(false);

    const triggerStreaming = () => {
        setLoaded(false);
        setTimeout(() => setLoaded(true), 1200);
    };

    return (
        &lt;div className="p-6 max-w-lg mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
            &lt;div className="flex justify-between items-center mb-4"&gt;
                &lt;h3 className="text-base font-bold text-slate-900"&gt;Streaming SSR &amp; Suspense Demo&lt;/h3&gt;
                &lt;button onClick={triggerStreaming} className="px-3 py-1 bg-emerald-600 text-white rounded text-xs font-bold shadow"&gt;
                    Stream Page Chunks
                &lt;/button&gt;
            &lt;/div&gt;

            &#123;/* Instant Header Shell */&#125;
            &lt;div className="p-3 bg-slate-900 text-white rounded-t-lg text-xs flex justify-between items-center"&gt;
                &lt;span className="font-bold text-emerald-400 font-mono"&gt;Instant Shell (0ms)&lt;/span&gt;
                &lt;span className="text-[10px] text-slate-400"&gt;Header &amp; Navigation ready&lt;/span&gt;
            &lt;/div&gt;

            &#123;/* Streaming Suspense Body */&#125;
            &lt;div className="p-4 bg-slate-50 border border-t-0 rounded-b-lg text-xs"&gt;
                {!loaded ? (
                    &lt;div className="animate-pulse space-y-2 py-2"&gt;
                        &lt;div className="h-4 bg-slate-200 rounded w-3/4"&gt;&lt;/div&gt;
                        &lt;div className="h-3 bg-slate-200 rounded w-1/2"&gt;&lt;/div&gt;
                        &lt;span className="text-[11px] text-indigo-600 font-mono block mt-2"&gt;
                            &bull; Suspense Fallback Active: Streaming slow query (1.2s)...
                        &lt;/span&gt;
                    &lt;/div&gt;
                ) : (
                    &lt;div className="space-y-2 text-slate-800"&gt;
                        &lt;strong className="text-emerald-700 block font-bold"&gt;✓ Heavy Analytics Streamed In Successfully!&lt;/strong&gt;
                        &lt;p className="text-[11px] text-slate-600"&gt;
                            Total active cloud instances: &lt;strong&gt;4,290&lt;/strong&gt; | Revenue: &lt;strong&gt;\$94,200 MRR&lt;/strong&gt;.
                        &lt;/p&gt;
                    &lt;/div&gt;
                )}
            &lt;/div&gt;
        &lt;/div&gt;
    );
}</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 18, 'Data Fetching, Streaming & Suspense', 'Streaming Server-Side Rendering (SSR) with React Suspense & Skeleton Fallbacks', $content18, 'streaming ssr nextjs, react suspense nextjs, progressive loading, skeleton fallbacks nextjs, ttfb optimization nextjs');

// -------------------------------------------------------------
// Chapter 19
// -------------------------------------------------------------
$content19 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">When building full-stack pages that require data from multiple sources (such as user profile data, recent orders, and system notifications), executing requests sequentially creates an avoidable <strong>Waterfall Bottleneck</strong>. Next.js developers leverage <strong>Parallel Data Fetching</strong> with <code>Promise.all()</code> to maximize server throughput.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">Sequential Fetching vs. Parallel Fetching</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Pattern</th>
                <th style="width: 40%;">Execution Diagram</th>
                <th style="width: 35%;">Total Elapsed Latency</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell">Sequential (Waterfall)</td>
                <td><code>await getUser();</code><br>then <code>await getOrders();</code></td>
                <td><strong>Time(User) + Time(Orders)</strong> (e.g. 500ms + 600ms = 1,100ms!).</td>
            </tr>
            <tr>
                <td class="code-cell">Parallel (Promise.all)</td>
                <td><code>Promise.all([getUser(), getOrders()])</code></td>
                <td><strong>Max(Time(User), Time(Orders))</strong> (Both run at once = 600ms total!).</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Promise.all Implementation in Server Components</h3>
<pre class="bg-slate-900 text-slate-200 p-4 rounded-xl font-mono text-xs mb-4">
export default async function UserDashboard({ params }: { params: { id: string } }) {
  // Initiate both requests in parallel!
  const userPromise = getUser(params.id);
  const ordersPromise = getOrders(params.id);

  // Wait for both to resolve simultaneously
  const [user, orders] = await Promise.all([userPromise, ordersPromise]);

  return &lt;DashboardView user={user} orders={orders} /&gt;;
}
</pre>

<div class="w3-example-box">
    <div class="w3-example-title">Interactive Waterfall vs Parallel Latency Benchmark</div>
    <pre><code class="language-javascript">function WaterfallSimulator() {
    const [results, setResults] = React.useState(null);
    const [running, setRunning] = React.useState(false);

    const runBenchmark = async (mode) => {
        setRunning(true);
        setResults(null);
        const start = Date.now();

        if (mode === 'sequential') {
            await new Promise(r => setTimeout(r, 400)); // Query 1
            await new Promise(r => setTimeout(r, 400)); // Query 2
        } else {
            await Promise.all([
                new Promise(r => setTimeout(r, 400)), // Query 1
                new Promise(r => setTimeout(r, 400))  // Query 2 in parallel
            ]);
        }

        const elapsed = Date.now() - start;
        setResults({ mode, elapsed });
        setRunning(false);
    };

    return (
        &lt;div className="p-6 max-w-lg mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
            &lt;h3 className="text-base font-bold text-slate-900 mb-3"&gt;Network Waterfall Benchmark&lt;/h3&gt;
            &lt;div className="flex gap-2 mb-4"&gt;
                &lt;button onClick={() =&gt; runBenchmark('sequential')} disabled={running} className="px-3 py-1.5 bg-amber-600 disabled:bg-slate-300 text-white rounded text-xs font-bold"&gt;
                    Test Sequential (Waterfall)
                &lt;/button&gt;
                &lt;button onClick={() =&gt; runBenchmark('parallel')} disabled={running} className="px-3 py-1.5 bg-emerald-600 disabled:bg-slate-300 text-white rounded text-xs font-bold shadow"&gt;
                    Test Parallel (Promise.all)
                &lt;/button&gt;
            &lt;/div&gt;

            {running && &lt;div className="text-xs text-slate-500 font-mono animate-pulse"&gt;Executing simulated server queries...&lt;/div&gt;}

            {results && (
                &lt;div className="p-4 bg-slate-50 border rounded-lg text-xs"&gt;
                    &lt;strong className="text-slate-900 block font-bold mb-1"&gt;Execution Mode: {results.mode.toUpperCase()}&lt;/strong&gt;
                    &lt;div className="text-sm font-black text-indigo-600"&gt;Total Duration: {results.elapsed}ms&lt;/div&gt;
                    &lt;p className="text-slate-500 text-[11px] mt-1"&gt;
                        {results.mode === 'parallel' ? '✓ Both queries ran simultaneously, cutting latency in half!' : '⚠️ Sequential queries chained together, doubling total wait time.'}
                    &lt;/p&gt;
                &lt;/div&gt;
            )}
        &lt;/div&gt;
    );
}</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 19, 'Data Fetching, Streaming & Suspense', 'Parallel vs Sequential Data Fetching & Preventing Waterfall Bottlenecks', $content19, 'parallel data fetching nextjs, sequential fetching, promise.all nextjs, prevent waterfalls nextjs, performance optimization nextjs');

// -------------------------------------------------------------
// Chapter 20
// -------------------------------------------------------------
$content20 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">When multiple components in a tree (e.g. <code>layout.tsx</code>, <code>page.tsx</code>, and a <code>&lt;Navbar&gt;</code>) all need the same user data, passing props down 5 levels is tedious. Next.js automatically memoizes <code>fetch()</code> requests with identical URLs, and provides React's <strong>cache()</strong> function to memoize custom database queries per request.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">Automatic Fetch Deduplication vs. React cache()</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Tool</th>
                <th style="width: 35%;">Syntax Example</th>
                <th style="width: 40%;">Scope &amp; Lifetime</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell">Automatic <code>fetch()</code> Deduplication</td>
                <td><code>fetch('/api/user')</code> (called in 3 components)</td>
                <td>Next.js executes the HTTP request <strong>only once</strong> per render cycle; all callers share the result.</td>
            </tr>
            <tr>
                <td class="code-cell">React <code>cache()</code> Function</td>
                <td><code>export const getUser = cache(async (id) =&gt; db.find(id));</code></td>
                <td>Memoizes custom ORM / database calls for the duration of a single incoming server request.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Using React cache() with ORMs (Prisma, Drizzle, SQL)</h3>
<pre class="bg-slate-900 text-slate-200 p-4 rounded-xl font-mono text-xs mb-4">
import { cache } from 'react';
import db from '@/lib/db';

// Wrap custom database query in cache()
export const getCurrentUser = cache(async (userId: string) => {
  console.log('Querying database for user: ', userId); // Executes only ONCE!
  return db.user.findUnique({ where: { id: userId } });
});
</pre>

<div class="w3-example-box">
    <div class="w3-example-title">Interactive Request Memoization Demonstration</div>
    <pre><code class="language-javascript">function MemoizationDemo() {
    const [callCount, setCallCount] = React.useState(0);
    const [dbQueries, setDbQueries] = React.useState(0);

    const simulateComponentCall = () => {
        setCallCount(prev => prev + 1);
        if (dbQueries === 0) {
            setDbQueries(1); // Only 1 database query executed!
        }
    };

    return (
        &lt;div className="p-6 max-w-lg mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
            &lt;h3 className="text-base font-bold text-slate-900 mb-2"&gt;Request Memoization Simulator&lt;/h3&gt;
            &lt;p className="text-xs text-slate-500 mb-4"&gt;Click to simulate 3 separate components in the tree requesting user session:&lt;/p&gt;

            &lt;button onClick={simulateComponentCall} className="px-3 py-1.5 bg-emerald-600 text-white rounded text-xs font-bold shadow mb-4"&gt;
                + Render Component Requesting User
            &lt;/button&gt;

            &lt;div className="grid grid-cols-2 gap-3 text-xs"&gt;
                &lt;div className="p-3 bg-slate-50 border rounded-lg"&gt;
                    &lt;span className="text-slate-400 block"&gt;Components Calling Data:&lt;/span&gt;
                    &lt;strong className="text-slate-800 text-lg font-black"&gt;{callCount}&lt;/strong&gt;
                &lt;/div&gt;
                &lt;div className="p-3 bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-950"&gt;
                    &lt;span className="text-emerald-700 block"&gt;Actual DB Queries:&lt;/span&gt;
                    &lt;strong className="text-emerald-900 text-lg font-black"&gt;{dbQueries}&lt;/strong&gt;
                &lt;/div&gt;
            &lt;/div&gt;
            &lt;span className="text-[10px] text-slate-400 block mt-3"&gt;
                ✓ React cache() deduplicates redundant queries within the same request lifecycle.
            &lt;/span&gt;
        &lt;/div&gt;
    );
}</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 20, 'Data Fetching, Streaming & Suspense', 'Preloading Data Patterns & In-Memory Request Memoization (cache())', $content20, 'react cache function, request memoization nextjs, fetch deduplication nextjs, preloading data nextjs, database query caching');

// -------------------------------------------------------------
// Chapter 21
// -------------------------------------------------------------
$content21 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700"><strong>Server Actions</strong> are asynchronous JavaScript functions that execute directly on the server. They are marked with the <strong><code>'use server'</code></strong> directive. Server Actions allow you to handle form submissions, database mutations, and cookie management directly without writing separate API routes or boilerplate <code>fetch()</code> handlers!</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">Server Actions vs. Traditional API Routes</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Criteria</th>
                <th style="width: 35%;">Traditional API Routes (<code>/api/create</code>)</th>
                <th style="width: 40%;">Next.js Server Actions (<code>'use server'</code>)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell">Boilerplate</td>
                <td>Requires creating <code>route.ts</code>, serializing JSON, and client <code>fetch()</code> call.</td>
                <td><strong>Zero boilerplate:</strong> Call regular async function directly from a form <code>action={myAction}</code>.</td>
            </tr>
            <tr>
                <td class="code-cell">Type Safety</td>
                <td>Requires manual TypeScript request/response contracts or Zod routers.</td>
                <td><strong>End-to-End Type Safety:</strong> Function arguments and return types are strictly typed.</td>
            </tr>
            <tr>
                <td class="code-cell">Progressive Enhancement</td>
                <td>Forms break if client JavaScript fails to download or load.</td>
                <td><strong>Works without JavaScript:</strong> Standard native HTML form submission handles fallback!</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. The 'use server' Directive</h3>
<p class="text-slate-600 mb-4">You can declare Server Actions inline inside a Server Component, or in a dedicated file with <code>'use server'</code> at the top so Client Components can import and trigger them:</p>

<div class="w3-example-box">
    <div class="w3-example-title">Interactive Server Action Mutation Simulator</div>
    <pre><code class="language-javascript">function ServerActionDemo() {
    const [submitting, setSubmitting] = React.useState(false);
    const [auditLog, setAuditLog] = React.useState([]);

    // Simulated Server Action execution
    const handleServerMutation = async (formData) => {
        setSubmitting(true);
        const name = formData.get('projectName') || 'Unnamed Service';
        
        // Simulating RPC call over the wire to server
        await new Promise(r => setTimeout(r, 600));

        setAuditLog(prev => [
            { id: Date.now(), text: `Mutated record in DB: "${name}" at ${new Date().toLocaleTimeString()}` },
            ...prev
        ]);
        setSubmitting(false);
    };

    return (
        &lt;div className="p-6 max-w-lg mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
            &lt;h3 className="text-base font-bold text-slate-900 mb-1"&gt;Server Action RPC Mutation&lt;/h3&gt;
            &lt;p className="text-xs text-slate-500 mb-4"&gt;Direct database write via &lt;code className="font-mono bg-slate-100 px-1 py-0.5 rounded"&gt;'use server'&lt;/code&gt; function:&lt;/p&gt;

            &lt;form onSubmit={(e) =&gt; {
                e.preventDefault();
                handleServerMutation(new FormData(e.currentTarget));
                e.currentTarget.reset();
            }} className="space-y-3 mb-4"&gt;
                &lt;input 
                    type="text" 
                    name="projectName" 
                    required 
                    placeholder="Enter project name..." 
                    className="w-full px-3 py-2 border rounded-lg text-xs"
                /&gt;
                &lt;button 
                    type="submit" 
                    disabled={submitting} 
                    className="w-full py-2 bg-slate-900 disabled:bg-slate-400 text-white rounded-lg text-xs font-bold shadow"
                &gt;
                    {submitting ? 'Executing Server Mutation...' : 'Invoke Server Action &raquo;'}
                &lt;/button&gt;
            &lt;/form&gt;

            &lt;div className="p-3 bg-slate-50 border rounded-lg text-xs"&gt;
                &lt;span className="font-bold text-slate-700 block mb-1"&gt;Server-Side Mutation Audit Log:&lt;/span&gt;
                {auditLog.length === 0 ? (
                    &lt;span className="text-slate-400 italic text-[11px]"&gt;No actions executed yet.&lt;/span&gt;
                ) : (
                    &lt;ul className="space-y-1 font-mono text-[11px] text-emerald-700"&gt;
                        {auditLog.map(item =&gt; &lt;li key={item.id}&gt;&bull; {item.text}&lt;/li&gt;)}
                    &lt;/ul&gt;
                )}
            &lt;/div&gt;
        &lt;/div&gt;
    );
}</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 21, 'Server Actions & Mutations', 'Server Actions Deep Dive: The \'use server\' Directive & Zero-API Mutations', $content21, 'server actions nextjs, use server directive, zero api mutations, form actions nextjs, full stack nextjs actions, rpc in nextjs');

// -------------------------------------------------------------
// Chapter 22
// -------------------------------------------------------------
$content22 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700"><strong>Progressive Enhancement</strong> is a core philosophy of the web: an application should remain functional even when client-side JavaScript is slow, blocked by browser extensions, or disabled. By passing a Server Action directly to the HTML <code>&lt;form action={myAction}&gt;</code>, forms can submit and mutate databases with zero JavaScript!</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">Progressive Enhancement in Action</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">User Environment</th>
                <th style="width: 35%;">Submission Method</th>
                <th style="width: 40%;">User Experience</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell">JavaScript Disabled / Slow</td>
                <td>Native HTTP <code>POST</code> request.</td>
                <td>Browser submits standard multipart form data; server processes mutation and sends updated page.</td>
            </tr>
            <tr>
                <td class="code-cell">JavaScript Hydrated (Normal)</td>
                <td>Asynchronous background <code>fetch()</code> RPC.</td>
                <td>Instant client-side transition without page refresh, preserving local UI state.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Native FormData Parsing</h3>
<p class="text-slate-600 mb-4">Server Actions automatically receive a standard Web API <code>FormData</code> object as their first argument:</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Progressive Form with Native FormData Parsing</div>
    <pre><code class="language-javascript">// app/newsletter/actions.ts
'use server';

export async function subscribeNewsletter(formData: FormData) {
  const email = formData.get('userEmail') as string;
  if (!email || !email.includes('@')) {
    throw new Error('Valid email required.');
  }

  // Direct database insertion
  await db.subscriber.create({ data: { email } });
  console.log(`Subscribed: ${email}`);
}

// app/newsletter/page.tsx (Server Component)
export default function NewsletterPage() {
  return (
    &lt;form action={subscribeNewsletter} className="space-y-3"&gt;
      &lt;input type="email" name="userEmail" required placeholder="name@domain.com" /&gt;
      &lt;button type="submit"&gt;Subscribe Now&lt;/button&gt;
    &lt;/form&gt;
  );
}</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 22, 'Server Actions & Mutations', 'Forms & Progressive Enhancement: Submitting Actions without JavaScript', $content22, 'progressive enhancement nextjs, form action nextjs, formdata in server action, submit without javascript, native html forms nextjs');

// -------------------------------------------------------------
// Chapter 23
// -------------------------------------------------------------
$content23 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">Managing loading states and validation errors during Server Action execution is streamlined by React 19 / Next.js 15 hooks: <strong>useActionState</strong> (handles action state &amp; return values) and <strong>useFormStatus</strong> (detects when a parent form is pending without prop drilling).</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">Action State Hooks: useActionState vs. useFormStatus</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Hook</th>
                <th style="width: 35%;">Syntax Blueprint</th>
                <th style="width: 40%;">Primary Responsibility</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell"><code>useActionState</code> (React 19)</td>
                <td><code>const [state, formAction, isPending] = useActionState(action, initialState);</code></td>
                <td>Stores the response returned by the Server Action (e.g. error messages or confirmation flags).</td>
            </tr>
            <tr>
                <td class="code-cell"><code>useFormStatus</code></td>
                <td><code>const { pending } = useFormStatus();</code></td>
                <td>Called inside child button components to automatically disable submit buttons and show spinners.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Submit Button Component with useFormStatus</h3>
<p class="text-slate-600 mb-4">Because <code>useFormStatus</code> tracks the nearest parent <code>&lt;form&gt;</code>, it must be called in a child component placed inside the form:</p>

<div class="w3-example-box">
    <div class="w3-example-title">Interactive Form State & Pending Spinner Demonstration</div>
    <pre><code class="language-javascript">function ActionStateInteractiveDemo() {
    const [state, setState] = React.useState({ success: false, error: null });
    const [isPending, setIsPending] = React.useState(false);

    const simulateAction = async (e) => {
        e.preventDefault();
        setIsPending(true);
        setState({ success: false, error: null });

        const email = e.currentTarget.elements.email.value;
        await new Promise(r => setTimeout(r, 700));

        if (!email.includes('@nectradigital.com')) {
            setState({ success: false, error: 'Corporate email must end in @nectradigital.com' });
        } else {
            setState({ success: true, error: null });
        }
        setIsPending(false);
    };

    return (
        &lt;div className="p-6 max-w-md mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
            &lt;h3 className="text-base font-bold text-slate-900 mb-3"&gt;Action State &amp; Validation&lt;/h3&gt;

            &lt;form onSubmit={simulateAction} className="space-y-3"&gt;
                &lt;div&gt;
                    &lt;label className="block text-xs font-bold text-slate-600 mb-1"&gt;Corporate Work Email:&lt;/label&gt;
                    &lt;input 
                        type="email" 
                        name="email" 
                        required 
                        placeholder="you@nectradigital.com" 
                        className="w-full px-3 py-2 border rounded-lg text-xs"
                    /&gt;
                &lt;/div&gt;

                &lt;button 
                    type="submit" 
                    disabled={isPending} 
                    className="w-full py-2 bg-emerald-600 disabled:bg-slate-400 text-white rounded-lg text-xs font-bold shadow flex items-center justify-center gap-2"
                &gt;
                    {isPending ? 'Verifying with Server Action...' : 'Verify Access Credentials'}
                &lt;/button&gt;
            &lt;/form&gt;

            {state.error && (
                &lt;div className="mt-3 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-xs font-bold"&gt;
                    &times; {state.error}
                &lt;/div&gt;
            )}

            {state.success && (
                &lt;div className="mt-3 p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-xs font-bold"&gt;
                    ✓ Access Approved! Verified by Server Action.
                &lt;/div&gt;
            )}
        &lt;/div&gt;
    );
}</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 23, 'Server Actions & Mutations', 'Action State Management: useActionState (React 19) & useFormStatus', $content23, 'useactionstate, useformstatus, server actions state, pending state in nextjs, react 19 server actions, form pending hook');
