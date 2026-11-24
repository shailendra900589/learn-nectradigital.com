<?php
// seed_nextjs_part1.php: Chapters 1 to 11 (Fundamentals & App Router Routing)

// -------------------------------------------------------------
// Chapter 1
// -------------------------------------------------------------
$content1 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700"><strong>Next.js</strong> (created and maintained by Vercel) is the industry-standard full-stack React framework. While vanilla React renders entirely on the user's browser (Client-Side Rendering), Next.js delivers hybrid rendering capabilities including <strong>Server-Side Rendering (SSR)</strong>, <strong>Static Site Generation (SSG)</strong>, <strong>Incremental Static Regeneration (ISR)</strong>, and cutting-edge <strong>React Server Components (RSC)</strong>.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">Web Rendering Paradigms: CSR vs. SSR vs. SSG vs. ISR vs. RSC</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 15%;">Paradigm</th>
                <th style="width: 30%;">When HTML is Generated</th>
                <th style="width: 25%;">SEO &amp; Initial Load</th>
                <th style="width: 30%;">Best Real-World Use Case</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell">CSR (Vanilla React)</td>
                <td>In the user's browser via client JavaScript.</td>
                <td>Poor initial SEO; slower First Contentful Paint (FCP).</td>
                <td>Gated dashboards, internal tools, single-user apps.</td>
            </tr>
            <tr>
                <td class="code-cell">SSR (Server-Side)</td>
                <td>On every user request on the Node.js / Edge server.</td>
                <td>Excellent SEO; always fresh data on every request.</td>
                <td>Dynamic social feeds, live stock prices, personalized views.</td>
            </tr>
            <tr>
                <td class="code-cell">SSG (Static Generation)</td>
                <td>At build time (<code>next build</code>). Pre-rendered HTML files.</td>
                <td>Ultra-fast CDN delivery; perfect Lighthouse scores.</td>
                <td>Marketing blogs, documentation, privacy policies.</td>
            </tr>
            <tr>
                <td class="code-cell">ISR (Incremental Static)</td>
                <td>At build time + background revalidation on timer.</td>
                <td>CDN speed + periodic background data refresh without redeploy.</td>
                <td>E-commerce product catalogs, news portals.</td>
            </tr>
            <tr>
                <td class="code-cell">RSC (Server Components)</td>
                <td>Rendered on server; streams virtual tree without client JS.</td>
                <td>Zero client bundle weight; direct secure database queries.</td>
                <td>Default in Next.js App Router for all page content.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Why Enterprise Teams Choose Next.js</h3>
<p class="text-slate-600 mb-4">Building production web applications with vanilla React requires stitching together dozens of independent libraries for routing, bundling, image compression, font loading, server communication, and SEO tags. Next.js provides an integrated, battle-tested standard:</p>
<ul class="list-disc pl-6 space-y-2 text-slate-600 mb-6">
    <li><strong>Zero Configuration Bundling:</strong> Built-in Rust-based compiler (Turbopack) for instant hot reloads.</li>
    <li><strong>Automatic Code Splitting:</strong> Each route only downloads the JavaScript required for that specific page.</li>
    <li><strong>Integrated Full-Stack Capabilities:</strong> Query PostgreSQL, MySQL, or Redis directly inside server components without creating separate REST backend servers.</li>
    <li><strong>Edge &amp; Serverless Deployment:</strong> Native instant deployments to Vercel, AWS Lambda, or self-hosted Docker containers.</li>
</ul>

<div class="w3-example-box">
    <div class="w3-example-title">Interactive Concept: Rendering Paradigm Comparator</div>
    <pre><code class="language-javascript">function RenderingDemo() {
    const [strategy, setStrategy] = React.useState('rsc');

    const descriptions = {
        csr: {
            title: "Client-Side Rendering (Vanilla React)",
            badge: "Client Heavy",
            color: "bg-amber-100 text-amber-800",
            htmlOutput: "HTML Shell: &lt;div id='root'&gt;&lt;/div&gt; (Loads 450KB JS bundle, then fetches data)",
            fcp: "1.8s First Contentful Paint",
            seo: "Limited (Search crawler must execute JS)"
        },
        ssr: {
            title: "Server-Side Rendering (Dynamic SSR)",
            badge: "Server Dynamic",
            color: "bg-blue-100 text-blue-800",
            htmlOutput: "Complete HTML generated on server upon each user request. Requires server compute.",
            fcp: "420ms First Contentful Paint",
            seo: "Excellent (HTML immediately readable)"
        },
        ssg: {
            title: "Static Site Generation (SSG)",
            badge: "CDN Cached",
            color: "bg-emerald-100 text-emerald-800",
            htmlOutput: "Pre-compiled HTML served directly from Cloudflare / Fastly CDN edge nodes.",
            fcp: "95ms First Contentful Paint",
            seo: "Perfect 100/100 Core Web Vitals"
        },
        rsc: {
            title: "React Server Components (Next.js 14/15 App Router)",
            badge: "Modern Standard",
            color: "bg-purple-100 text-purple-800",
            htmlOutput: "Direct server rendering with 0kB JavaScript sent to client for data-fetching logic.",
            fcp: "180ms First Contentful Paint",
            seo: "Unrivaled Performance & Zero-JS Payload"
        }
    };

    const current = descriptions[strategy];

    return (
        &lt;div className="p-6 max-w-lg mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
            &lt;h3 className="text-lg font-bold text-slate-900 mb-2"&gt;Next.js Architecture Simulator&lt;/h3&gt;
            &lt;p className="text-xs text-slate-500 mb-4"&gt;Select a rendering strategy to see how Next.js processes HTML and data:&lt;/p&gt;

            &lt;div className="grid grid-cols-2 gap-2 mb-4"&gt;
                {Object.keys(descriptions).map(key =&gt; (
                    &lt;button 
                        key={key} 
                        onClick={() =&gt; setStrategy(key)} 
                        className={`px-3 py-2 text-xs font-bold rounded-lg border transition ${strategy === key ? 'bg-slate-900 text-white border-slate-900' : 'bg-slate-50 hover:bg-slate-100 text-slate-700'}`}
                    &gt;
                        {key.toUpperCase()}
                    &lt;/button&gt;
                ))}
            &lt;/div&gt;

            &lt;div className="p-4 bg-slate-50 border rounded-lg"&gt;
                &lt;div className="flex justify-between items-center mb-2"&gt;
                    &lt;strong className="text-sm text-slate-900"&gt;{current.title}&lt;/strong&gt;
                    &lt;span className={`text-[10px] font-bold px-2 py-0.5 rounded ${current.color}`}&gt;{current.badge}&lt;/span&gt;
                &lt;/div&gt;
                &lt;p className="text-xs text-slate-600 mb-3"&gt;{current.htmlOutput}&lt;/p&gt;
                &lt;div className="grid grid-cols-2 gap-2 text-[11px] font-mono border-t pt-2"&gt;
                    &lt;div&gt;&lt;span className="text-slate-400 block"&gt;Speed (FCP):&lt;/span&gt;&lt;strong className="text-emerald-600"&gt;{current.fcp}&lt;/strong&gt;&lt;/div&gt;
                    &lt;div&gt;&lt;span className="text-slate-400 block"&gt;Search SEO:&lt;/span&gt;&lt;strong className="text-indigo-600"&gt;{current.seo}&lt;/strong&gt;&lt;/div&gt;
                &lt;/div&gt;
            &lt;/div&gt;
        &lt;/div&gt;
    );
}</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 1, 'Next.js Fundamentals', 'What is Next.js? Client-Side Rendering (CSR) vs SSR vs SSG vs ISR vs RSC', $content1, 'nextjs, next.js tutorial, csr vs ssr, ssg vs isr, react server components, learn nextjs, nextjs rendering strategies, vercel nextjs');

// -------------------------------------------------------------
// Chapter 2
// -------------------------------------------------------------
$content2 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">Next.js has two distinct routing systems: the legacy <strong>Pages Router</strong> (inside the <code>pages/</code> directory) and the modern <strong>App Router</strong> (inside the <code>app/</code> directory, introduced in Next.js 13.4). Today, the App Router is the official default, designed natively around React 18 & 19 Server Components.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">Pages Router vs. App Router Comparison</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Feature</th>
                <th style="width: 35%;">Pages Router (Legacy: <code>pages/</code>)</th>
                <th style="width: 40%;">App Router (Modern: <code>app/</code>)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell">Component Default</td>
                <td>Client Components (Hydrated on client).</td>
                <td><strong>React Server Components (RSC) by default.</strong></td>
            </tr>
            <tr>
                <td class="code-cell">Data Fetching</td>
                <td>Special export functions: <code>getServerSideProps</code>, <code>getStaticProps</code>.</td>
                <td><strong>Direct async/await in components</strong> (Zero boilerplate functions).</td>
            </tr>
            <tr>
                <td class="code-cell">Layout Hierarchy</td>
                <td>Manual <code>getLayout</code> pattern; re-renders entire page tree on navigation.</td>
                <td><strong>Native <code>layout.tsx</code>:</strong> Preserves state across route changes without re-rendering headers/sidebars.</td>
            </tr>
            <tr>
                <td class="code-cell">Data Mutations</td>
                <td>Manual API routes (<code>pages/api/...</code>) + client <code>fetch()</code>.</td>
                <td><strong>Server Actions (<code>'use server'</code>):</strong> Direct function invocation from forms without separate API endpoints.</td>
            </tr>
            <tr>
                <td class="code-cell">Streaming &amp; Suspense</td>
                <td>Page-level blocking SSR.</td>
                <td><strong>Granular Streaming SSR</strong> with React <code>&lt;Suspense&gt;</code> &amp; <code>loading.tsx</code>.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Coexistence of Both Routers</h3>
<p class="text-slate-600 mb-4">Next.js allows both routers to run side-by-side in the same project during gradual migrations. If a route exists in both directories, the App Router takes precedence over the Pages Router.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Comparing Data Fetching Syntax Between Routers</div>
    <pre><code class="language-javascript">// 1. Legacy Pages Router (pages/products.tsx): Requires separate getStaticProps boilerplate
/*
export async function getStaticProps() {
    const res = await fetch('https://api.nectradigital.com/products');
    const products = await res.json();
    return { props: { products } };
}
export default function ProductsPage({ products }) {
    return &lt;div&gt;{products.map(p =&gt; &lt;p key={p.id}&gt;{p.name}&lt;/p&gt;)}&lt;/div&gt;;
}
*/

// 2. Modern App Router (app/products/page.tsx): Direct, clean async/await Server Component!
function AppRouterComparisonDemo() {
    const [view, setView] = React.useState('app');

    return (
        &lt;div className="p-6 max-w-lg mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
            &lt;div className="flex justify-between items-center mb-4"&gt;
                &lt;h3 className="text-base font-bold text-slate-900"&gt;Router Evolution Sandbox&lt;/h3&gt;
                &lt;div className="flex gap-1 text-xs font-bold"&gt;
                    &lt;button onClick={() =&gt; setView('pages')} className={`px-2.5 py-1 rounded ${view === 'pages' ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-600'}`}&gt;Pages Router&lt;/button&gt;
                    &lt;button onClick={() =&gt; setView('app')} className={`px-2.5 py-1 rounded ${view === 'app' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-600'}`}&gt;App Router&lt;/button&gt;
                &lt;/div&gt;
            &lt;/div&gt;

            {view === 'pages' ? (
                &lt;div className="p-4 bg-amber-50 border border-amber-200 rounded-lg text-xs"&gt;
                    &lt;strong className="text-amber-900 block mb-1"&gt;Legacy Pages Router Pattern:&lt;/strong&gt;
                    &lt;p className="text-amber-800 mb-2"&gt;Requires exporting getServerSideProps or getStaticProps outside the component, passing serialized props across process boundaries.&lt;/p&gt;
                    &lt;div className="bg-white p-2.5 rounded font-mono text-[11px] text-slate-700 border"&gt;
                        &#123;"export async function getStaticProps() &#123; ... &#125;"&#125;
                    &lt;/div&gt;
                &lt;/div&gt;
            ) : (
                &lt;div className="p-4 bg-emerald-50 border border-emerald-200 rounded-lg text-xs"&gt;
                    &lt;strong className="text-emerald-900 block mb-1"&gt;Modern App Router (Next.js 14/15):&lt;/strong&gt;
                    &lt;p className="text-emerald-800 mb-2"&gt;Direct async Server Component. Data fetches natively inside the component with zero extra functions!&lt;/p&gt;
                    &lt;div className="bg-white p-2.5 rounded font-mono text-[11px] text-slate-700 border"&gt;
                        &#123;"export default async function Page() &#123; const data = await db.query(); ... &#125;"&#125;
                    &lt;/div&gt;
                &lt;/div&gt;
            )}
        &lt;/div&gt;
    );
}</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 2, 'Next.js Fundamentals', 'App Router vs Pages Router Architecture & Historical Evolution', $content2, 'app router vs pages router, nextjs app router, nextjs migration, getserversideprops, getstaticprops in nextjs, nextjs 14 app directory');

// -------------------------------------------------------------
// Chapter 3
// -------------------------------------------------------------
$content3 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">Starting a new Next.js project is powered by the official <strong>create-next-app</strong> CLI utility. It automatically scaffolds a production-ready application with <strong>TypeScript</strong>, <strong>Tailwind CSS</strong>, <strong>ESLint</strong>, <strong>App Router</strong>, and customizable import path aliases.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">create-next-app CLI Flags &amp; Options</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">CLI Flag</th>
                <th style="width: 35%;">Command Syntax</th>
                <th style="width: 40%;">Recommended Production Setting</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell"><code>--typescript</code></td>
                <td><code>npx create-next-app@latest my-app --typescript</code></td>
                <td><strong>Always Yes:</strong> Ensures type safety for server action payloads, route parameters, and metadata.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>--tailwind</code></td>
                <td><code>--tailwind</code></td>
                <td><strong>Recommended:</strong> Installs Tailwind CSS with pre-configured PostCSS rules.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>--app</code></td>
                <td><code>--app</code></td>
                <td>Enables the modern <code>app/</code> directory architecture.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>--src-dir</code></td>
                <td><code>--src-dir</code></td>
                <td>Keeps root directory clean by grouping application code inside <code>src/app/</code>.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>--import-alias</code></td>
                <td><code>--import-alias "@/*"</code></td>
                <td>Enables clean absolute imports (e.g. <code>import Button from '@/components/Button'</code>).</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Running the Local Development Server</h3>
<p class="text-slate-600 mb-4">Once generated, navigate into the project and launch the development environment:</p>
<pre class="bg-slate-900 text-emerald-400 p-4 rounded-xl font-mono text-sm mb-4 language-bash"><code class="language-bash">cd my-app
npm run dev
# or with Turbopack for lightning-fast compilation:
npm run dev -- --turbo</code></pre>
<p class="text-slate-600 mb-4">Open <code>http://localhost:3000</code> in your browser to view your live Next.js application.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Interactive Terminal Sandbox: Next.js CLI Scaffold Simulator</div>
    <pre><code class="language-javascript">function CliSimulator() {
    const [step, setStep] = React.useState(0);
    const steps = [
        "npx create-next-app@latest enterprise-saas",
        "✔ Would you like to use TypeScript? … Yes",
        "✔ Would you like to use ESLint? … Yes",
        "✔ Would you like to use Tailwind CSS? … Yes",
        "✔ Would you like to use `src/` directory? … Yes",
        "✔ Would you like to use App Router? … Yes",
        "✔ Would you like to customize the default import alias (@/*)? … No",
        "Installing dependencies: react, react-dom, next, typescript...",
        "Success! Created enterprise-saas at ./enterprise-saas",
        "Ready! Run: cd enterprise-saas && npm run dev"
    ];

    return (
        &lt;div className="p-6 max-w-lg mx-auto bg-slate-900 rounded-xl shadow border border-slate-800 text-slate-200 font-mono text-xs"&gt;
            &lt;div className="flex items-center gap-2 pb-3 mb-3 border-b border-slate-800"&gt;
                &lt;span className="w-3 h-3 rounded-full bg-red-500 inline-block"&gt;&lt;/span&gt;
                &lt;span className="w-3 h-3 rounded-full bg-yellow-500 inline-block"&gt;&lt;/span&gt;
                &lt;span className="w-3 h-3 rounded-full bg-green-500 inline-block"&gt;&lt;/span&gt;
                &lt;span className="text-slate-400 font-bold ml-2"&gt;terminal &mdash; bash&lt;/span&gt;
            &lt;/div&gt;

            &lt;div className="space-y-1 mb-4 text-emerald-400"&gt;
                {steps.slice(0, step + 1).map((s, i) =&gt; (
                    &lt;div key={i}&gt;{s}&lt;/div&gt;
                ))}
            &lt;/div&gt;

            &lt;div className="flex justify-between items-center pt-2 border-t border-slate-800 font-sans"&gt;
                &lt;button 
                    onClick={() =&gt; setStep(prev =&gt; Math.min(prev + 1, steps.length - 1))}
                    disabled={step &gt;= steps.length - 1}
                    className="px-3 py-1.5 bg-emerald-600 disabled:bg-slate-700 text-white rounded text-xs font-bold transition"
                &gt;
                    {step &gt;= steps.length - 1 ? 'Setup Finished ✓' : 'Next Step &raquo;'}
                &lt;/button&gt;
                &lt;button onClick={() =&gt; setStep(0)} className="text-slate-400 hover:text-white text-xs"&gt;Reset&lt;/button&gt;
            &lt;/div&gt;
        &lt;/div&gt;
    );
}</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 3, 'Next.js Fundamentals', 'Setting Up Next.js 14/15 (create-next-app), TypeScript, ESLint & Tailwind CSS', $content3, 'create-next-app, nextjs setup, nextjs typescript, install nextjs 14, install nextjs 15, turbopack, tailwind with nextjs');

// -------------------------------------------------------------
// Chapter 4
// -------------------------------------------------------------
$content4 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">The Next.js App Router relies on strict file-system conventions. File names inside the <code>app/</code> directory have special architectural meanings that define layouts, pages, loading states, error boundaries, and API handlers.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">The Essential App Directory File Conventions</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 20%;">Special File</th>
                <th style="width: 35%;">Role &amp; Responsibility</th>
                <th style="width: 45%;">Component Requirement</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell"><code>layout.tsx</code></td>
                <td>Shared UI shell wrapping children (persists on navigation).</td>
                <td>Must accept and render <code>children: React.ReactNode</code>.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>page.tsx</code></td>
                <td>The unique, publicly accessible UI for a route.</td>
                <td>Must export default React component. Makes route publicly reachable.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>loading.tsx</code></td>
                <td>Instant loading skeleton wrapped in React <code>&lt;Suspense&gt;</code>.</td>
                <td>Rendered while the page's async server data resolves.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>error.tsx</code></td>
                <td>Error boundary catching runtime failures in children.</td>
                <td><strong>Must be a Client Component (<code>'use client'</code>)</strong>.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>not-found.tsx</code></td>
                <td>Custom 404 UI rendered on missing routes or <code>notFound()</code>.</td>
                <td>Export default UI component for 404 views.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>route.ts</code></td>
                <td>Backend API Route Handler (GET, POST, PUT, DELETE).</td>
                <td>Cannot coexist in the same folder with <code>page.tsx</code>.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Typical Production Folder Tree</h3>
<pre class="bg-slate-900 text-slate-200 p-4 rounded-xl font-mono text-xs mb-4 language-tree"><code class="language-tree">my-app/
├── src/
│   ├── app/
│   │   ├── layout.tsx         # Root layout (required, contains &lt;html&gt; &amp; &lt;body&gt;)
│   │   ├── page.tsx           # Home page ("/")
│   │   ├── loading.tsx        # Global loading spinner
│   │   ├── error.tsx          # Global error fallback ('use client')
│   │   ├── dashboard/
│   │   │   ├── layout.tsx     # Nested layout for dashboard routes
│   │   │   ├── page.tsx       # Dashboard home ("/dashboard")
│   │   │   └── settings/
│   │   │       └── page.tsx   # Settings page ("/dashboard/settings")
│   │   └── api/
│   │       └── health/
│   │           └── route.ts   # Backend API ("/api/health")
│   └── components/            # Reusable UI widgets (Buttons, Modals)
├── public/                    # Static assets (images, favicon, SVGs)
├── next.config.js             # Next.js runtime configuration
└── package.json</code></pre>

<div class="w3-example-box">
    <div class="w3-example-title">Interactive Tree Explorer: Click Files to See What They Do</div>
    <pre><code class="language-javascript">function FileTreeExplorer() {
    const [selected, setSelected] = React.useState('layout.tsx');

    const fileDetails = {
        'layout.tsx': {
            desc: "Root Layout (Required): Wraps every page. Contains the primary &lt;html&gt; and &lt;body&gt; tags, global providers, and shared navigation bars. Never re-renders on page change.",
            code: "export default function RootLayout({ children }) {\n  return (\n    <html lang='en'>\n      <body>{children}</body>\n    </html>\n  );\n}"
        },
        'page.tsx': {
            desc: "Route Entry Point: The actual page UI displayed at the route path. By default in Next.js, this is a React Server Component with direct database access.",
            code: "export default async function HomePage() {\n  return <h1>Welcome to Next.js App Router</h1>;\n}"
        },
        'loading.tsx': {
            desc: "Streaming Fallback UI: Automatically wraps page.tsx inside a React Suspense boundary. Shown instantly while server data is streaming across the wire.",
            code: "export default function Loading() {\n  return <div className='animate-pulse'>Loading dashboard...</div>;\n}"
        },
        'error.tsx': {
            desc: "Client Error Boundary: Must include 'use client'. Catches any unexpected server or client errors, isolating the failure without crashing the whole application.",
            code: "'use client';\nexport default function ErrorBoundary({ error, reset }) {\n  return <button onClick={() => reset()}>Try Again</button>;\n}"
        }
    };

    return (
        &lt;div className="p-6 max-w-lg mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
            &lt;h3 className="text-base font-bold text-slate-900 mb-3"&gt;App Directory File Inspector&lt;/h3&gt;
            &lt;div className="flex gap-1 mb-4 flex-wrap"&gt;
                {Object.keys(fileDetails).map(f =&gt; (
                    &lt;button 
                        key={f} 
                        onClick={() =&gt; setSelected(f)} 
                        className={`px-2.5 py-1 text-xs font-mono font-bold rounded border ${selected === f ? 'bg-slate-900 text-white' : 'bg-slate-50 text-slate-700'}`}
                    &gt;
                        {f}
                    &lt;/button&gt;
                ))}
            &lt;/div&gt;

            &lt;div className="p-4 bg-slate-50 border rounded-lg"&gt;
                &lt;p className="text-xs text-slate-600 mb-2 leading-relaxed"&gt;{fileDetails[selected].desc}&lt;/p&gt;
                &lt;pre className="bg-slate-900 text-emerald-400 p-3 rounded text-[11px] font-mono overflow-auto"&gt;
                    {fileDetails[selected].code}
                &lt;/pre&gt;
            &lt;/div&gt;
        &lt;/div&gt;
    );
}</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 4, 'Next.js Fundamentals', 'Project Structure & App Directory Conventions (app/, public/, next.config.js)', $content4, 'nextjs project structure, app directory conventions, layout.tsx, page.tsx, loading.tsx, error.tsx, next.config.js');

// -------------------------------------------------------------
// Chapter 5
// -------------------------------------------------------------
$content5 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">In the App Router, <strong>folders define routes</strong> and <strong>files define UI</strong>. A route is a single path of nested folders, starting from the root <code>app/</code> directory down to the final folder containing a <code>page.tsx</code> file.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">Folder Structure to Public URL Mapping</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 35%;">Folder Path</th>
                <th style="width: 35%;">Resolved Browser URL</th>
                <th style="width: 30%;">File Responsible</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell"><code>app/page.tsx</code></td>
                <td><code>/</code> (Root Home)</td>
                <td>Root Page Component</td>
            </tr>
            <tr>
                <td class="code-cell"><code>app/about/page.tsx</code></td>
                <td><code>/about</code></td>
                <td>About Us Page</td>
            </tr>
            <tr>
                <td class="code-cell"><code>app/blog/news/page.tsx</code></td>
                <td><code>/blog/news</code></td>
                <td>Nested Sub-Route</td>
            </tr>
            <tr>
                <td class="code-cell"><code>app/components/Button.tsx</code></td>
                <td><strong>Not reachable via URL</strong></td>
                <td>Private helper (no <code>page.tsx</code>)</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Colocation: Placing Helper Files Inside Route Folders</h3>
<p class="text-slate-600 mb-4">Unlike the legacy Pages Router where every file became a public URL, in the App Router a folder is <strong>only publicly accessible if it contains a <code>page.tsx</code> file</strong>. This allows you to colocate test files, styles, and custom child components directly inside the route folder safely!</p>

<div class="w3-example-box">
    <div class="w3-example-title">Interactive Simulator: Route Resolution Engine</div>
    <pre><code class="language-javascript">function RouteResolutionDemo() {
    const [path, setPath] = React.useState('/pricing');

    const routeMap = {
        '/': { folder: 'app/page.tsx', title: 'Home Landing Page', status: '200 OK' },
        '/pricing': { folder: 'app/pricing/page.tsx', title: 'Pricing & Plans Page', status: '200 OK' },
        '/docs/api': { folder: 'app/docs/api/page.tsx', title: 'API Documentation Sub-route', status: '200 OK' },
        '/utils': { folder: 'app/utils/format.ts (No page.tsx)', title: '404 - Not Found (Colocated Helper)', status: '404 Not Found' }
    };

    const target = routeMap[path] || { folder: 'Unknown', title: '404 Not Found', status: '404' };

    return (
        &lt;div className="p-6 max-w-lg mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
            &lt;h3 className="text-base font-bold text-slate-900 mb-3"&gt;Route Resolution Simulator&lt;/h3&gt;
            &lt;div className="flex gap-2 mb-4"&gt;
                {Object.keys(routeMap).map(p =&gt; (
                    &lt;button 
                        key={p} 
                        onClick={() =&gt; setPath(p)} 
                        className={`px-3 py-1.5 text-xs font-mono font-bold rounded border ${path === p ? 'bg-indigo-600 text-white' : 'bg-slate-50 text-slate-700'}`}
                    &gt;
                        {p}
                    &lt;/button&gt;
                ))}
            &lt;/div&gt;

            &lt;div className="p-4 bg-slate-50 border rounded-lg text-xs"&gt;
                &lt;div className="flex justify-between items-center mb-2"&gt;
                    &lt;strong className="text-slate-900"&gt;Requested: {path}&lt;/strong&gt;
                    &lt;span className={`px-2 py-0.5 rounded text-[10px] font-bold ${target.status.includes('200') ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800'}`}&gt;
                        {target.status}
                    &lt;/span&gt;
                &lt;/div&gt;
                &lt;div className="font-mono text-slate-600 bg-white p-2 rounded border mb-2"&gt;
                    Maps to file: &lt;strong className="text-slate-900"&gt;{target.folder}&lt;/strong&gt;
                &lt;/div&gt;
                &lt;p className="text-slate-500"&gt;Rendered Component: {target.title}&lt;/p&gt;
            &lt;/div&gt;
        &lt;/div&gt;
    );
}</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 5, 'App Router Routing & Layouts', 'Defining Routes: Folders, page.tsx, and Hierarchical Segment Routing', $content5, 'nextjs routing, app router routes, page.tsx, hierarchical routing nextjs, colocation in nextjs, define routes nextjs');

// -------------------------------------------------------------
// Chapter 6
// -------------------------------------------------------------
$content6 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">A <strong>Layout</strong> is UI that is shared between multiple route segments. On navigation, layouts preserve state, remain interactive, and <strong>do not re-render</strong>. Next.js also provides <strong>Templates</strong> (<code>template.tsx</code>), which are similar to layouts but create a fresh instance and re-mount on every navigation.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">layout.tsx vs. template.tsx Architectural Differences</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Feature</th>
                <th style="width: 35%;"><code>layout.tsx</code></th>
                <th style="width: 40%;"><code>template.tsx</code></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell">Persistence</td>
                <td><strong>Persists:</strong> Survives route changes without tearing down DOM.</td>
                <td><strong>Re-mounts:</strong> Creates a new DOM instance on every navigation.</td>
            </tr>
            <tr>
                <td class="code-cell">State Preservation</td>
                <td>Preserves client state (e.g. search inputs, video playback, scroll position).</td>
                <td>Resets all state to initial defaults.</td>
            </tr>
            <tr>
                <td class="code-cell">useEffect Execution</td>
                <td><code>useEffect</code> only runs on initial mount.</td>
                <td><code>useEffect</code> re-fires on every page transition.</td>
            </tr>
            <tr>
                <td class="code-cell">Primary Use Cases</td>
                <td>Header navigation, sidebar drawers, footer copyright bars.</td>
                <td>Page enter/exit animations, page view analytics tracking, feedback forms.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Nested Layouts Hierarchy</h3>
<p class="text-slate-600 mb-4">Layouts nest automatically. A page at <code>app/dashboard/analytics/page.tsx</code> is wrapped by <code>app/dashboard/layout.tsx</code>, which in turn is wrapped by <code>app/layout.tsx</code>:</p>

<div class="w3-example-box">
    <div class="w3-example-title">Interactive Demonstration: Layout State Persistence During Navigation</div>
    <pre><code class="language-javascript">function LayoutPersistenceDemo() {
    const [subPage, setSubPage] = React.useState('metrics');
    const [sidebarSearch, setSidebarSearch] = React.useState('active'); // Persisted layout state!

    return (
        &lt;div className="p-6 max-w-lg mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
            &lt;h3 className="text-base font-bold text-slate-900 mb-1"&gt;Nested Layout &amp; State Persistence&lt;/h3&gt;
            &lt;p className="text-xs text-slate-500 mb-4"&gt;Notice how the sidebar search input keeps its value as you switch sub-pages:&lt;/p&gt;

            &#123;/* Simulated Dashboard Layout (app/dashboard/layout.tsx) */&#125;
            &lt;div className="border rounded-xl overflow-hidden flex flex-col sm:flex-row text-xs"&gt;
                &lt;div className="w-full sm:w-44 bg-slate-900 text-white p-3 flex flex-col justify-between"&gt;
                    &lt;div&gt;
                        &lt;strong className="block text-emerald-400 mb-2 font-mono"&gt;[Dashboard Layout]&lt;/strong&gt;
                        &lt;input 
                            type="text" 
                            value={sidebarSearch} 
                            onChange={(e) =&gt; setSidebarSearch(e.target.value)} 
                            className="w-full px-2 py-1 bg-slate-800 text-slate-200 rounded border border-slate-700 text-[11px] mb-3"
                            placeholder="Filter sidebar..."
                        /&gt;
                        &lt;div className="space-y-1 font-bold"&gt;
                            &lt;button onClick={() =&gt; setSubPage('metrics')} className={`w-full text-left px-2 py-1 rounded ${subPage === 'metrics' ? 'bg-emerald-600 text-white' : 'hover:bg-slate-800 text-slate-400'}`}&gt;&bull; Metrics&lt;/button&gt;
                            &lt;button onClick={() =&gt; setSubPage('reports')} className={`w-full text-left px-2 py-1 rounded ${subPage === 'reports' ? 'bg-emerald-600 text-white' : 'hover:bg-slate-800 text-slate-400'}`}&gt;&bull; Reports&lt;/button&gt;
                        &lt;/div&gt;
                    &lt;/div&gt;
                    &lt;span className="text-[10px] text-slate-500 mt-4"&gt;Layout state preserved ✓&lt;/span&gt;
                &lt;/div&gt;

                &#123;/* Simulated Child Page (page.tsx) */&#125;
                &lt;div className="flex-1 p-4 bg-slate-50"&gt;
                    {subPage === 'metrics' ? (
                        &lt;div&gt;
                            &lt;strong className="text-slate-900 block font-bold mb-1"&gt;Sub-Page: Metrics&lt;/strong&gt;
                            &lt;p className="text-slate-600 text-[11px]"&gt;Live system throughput: 1,480 req/sec across 12 pods.&lt;/p&gt;
                        &lt;/div&gt;
                    ) : (
                        &lt;div&gt;
                            &lt;strong className="text-slate-900 block font-bold mb-1"&gt;Sub-Page: Reports&lt;/strong&gt;
                            &lt;p className="text-slate-600 text-[11px]"&gt;Quarterly infrastructure spend and reliability audit generated.&lt;/p&gt;
                        &lt;/div&gt;
                    )}
                &lt;/div&gt;
            &lt;/div&gt;
        &lt;/div&gt;
    );
}</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 6, 'App Router Routing & Layouts', 'Root Layouts, Nested Layouts & Templates (layout.tsx vs template.tsx)', $content6, 'nextjs layouts, layout.tsx, template.tsx, nested layouts in nextjs, state preservation nextjs, root layout');

// -------------------------------------------------------------
// Chapter 7
// -------------------------------------------------------------
$content7 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">Client-side navigation in Next.js is performed using the built-in <strong>&lt;Link&gt;</strong> component or the programmatic <strong>useRouter()</strong> hook (from <code>next/navigation</code>). Next.js automatically <strong>prefetches</strong> linked route segments in the background as they enter the user's viewport, making page transitions instantaneous.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">Navigation Methods: Link vs. useRouter vs. redirect</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Tool</th>
                <th style="width: 35%;">Syntax Example</th>
                <th style="width: 40%;">Primary Purpose &amp; Environment</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell"><code>&lt;Link&gt;</code> Component</td>
                <td><code>&lt;Link href="/dashboard"&gt;Dashboard&lt;/Link&gt;</code></td>
                <td><strong>Primary Navigation:</strong> Accessible HTML <code>&lt;a&gt;</code> tag with viewport prefetching and fast client transitions.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>useRouter()</code> Hook</td>
                <td><code>const router = useRouter(); router.push('/home');</code></td>
                <td>Programmatic navigation inside <strong>Client Components</strong> (e.g. after form submit or button click).</td>
            </tr>
            <tr>
                <td class="code-cell"><code>redirect()</code> Function</td>
                <td><code>redirect('/login')</code></td>
                <td>Server-side redirect used inside <strong>Server Components, Server Actions &amp; Route Handlers</strong>.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Intelligent Prefetching in Viewport</h3>
<p class="text-slate-600 mb-4">Whenever a <code>&lt;Link&gt;</code> enters the browser viewport, Next.js prefetches the target route's RSC payload and data in the background. When the user finally clicks the link, the page renders with zero network latency!</p>

<div class="w3-example-box">
    <div class="w3-example-title">Interactive Navigation Simulator with next/navigation Hooks</div>
    <pre><code class="language-javascript">function NavigationSimulator() {
    const [currentRoute, setCurrentRoute] = React.useState('/products');
    const [history, setHistory] = React.useState(['/home', '/products']);

    const navigate = (to) => {
        setHistory(prev =&gt; [...prev, to]);
        setCurrentRoute(to);
    };

    return (
        &lt;div className="p-6 max-w-lg mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
            &lt;div className="flex justify-between items-center border-b pb-3 mb-4"&gt;
                &lt;div className="text-xs font-mono"&gt;
                    Browser URL: &lt;strong className="text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded"&gt;{currentRoute}&lt;/strong&gt;
                &lt;/div&gt;
                &lt;span className="text-[10px] text-slate-400 font-bold uppercase"&gt;Instant Prefetch Enabled&lt;/span&gt;
            &lt;/div&gt;

            &lt;nav className="flex gap-2 mb-4"&gt;
                &lt;button onClick={() =&gt; navigate('/home')} className={`px-3 py-1.5 text-xs font-bold rounded ${currentRoute === '/home' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700'}`}&gt;&amp;lt;Link&amp;gt; Home&lt;/button&gt;
                &lt;button onClick={() =&gt; navigate('/products')} className={`px-3 py-1.5 text-xs font-bold rounded ${currentRoute === '/products' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700'}`}&gt;&amp;lt;Link&amp;gt; Products&lt;/button&gt;
                &lt;button onClick={() =&gt; navigate('/checkout')} className={`px-3 py-1.5 text-xs font-bold rounded ${currentRoute === '/checkout' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700'}`}&gt;&amp;lt;Link&amp;gt; Checkout&lt;/button&gt;
            &lt;/nav&gt;

            &lt;div className="p-3 bg-slate-50 border rounded-lg text-xs"&gt;
                &lt;span className="font-bold text-slate-700 block mb-1"&gt;Navigation History Stack (useRouter):&lt;/span&gt;
                &lt;div className="flex gap-1 flex-wrap font-mono text-[11px]"&gt;
                    {history.map((h, i) =&gt; (
                        &lt;span key={i} className="bg-white px-2 py-0.5 rounded border text-slate-600"&gt;{h} {i &lt; history.length - 1 ? '&rarr;' : ''}&lt;/span&gt;
                    ))}
                &lt;/div&gt;
            &lt;/div&gt;
        &lt;/div&gt;
    );
}</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 7, 'App Router Routing & Layouts', 'Navigation in Next.js: <Link> Component, Prefetching & useRouter Hook', $content7, 'nextjs link component, userouter nextjs, next/navigation, prefetching in nextjs, client navigation nextjs, redirect in nextjs');

// -------------------------------------------------------------
// Chapter 8
// -------------------------------------------------------------
$content8 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">When you do not know the exact route segment names ahead of time (such as blog posts, user IDs, or complex e-commerce slugs), you use <strong>Dynamic Route Segments</strong> wrapped in square brackets: <code>[id]</code>, catch-all <code>[...slug]</code>, and optional catch-all <code>[[...slug]]</code>.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">Dynamic Route Syntax Patterns</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Syntax Pattern</th>
                <th style="width: 35%;">Matching URL Examples</th>
                <th style="width: 40%;">Extracted <code>params</code> Object</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell">Single: <code>app/blog/[id]/page.tsx</code></td>
                <td><code>/blog/123</code>, <code>/blog/nextjs-guide</code></td>
                <td><code>{ id: '123' }</code></td>
            </tr>
            <tr>
                <td class="code-cell">Catch-All: <code>app/docs/[...slug]/page.tsx</code></td>
                <td><code>/docs/getting-started/installation</code></td>
                <td><code>{ slug: ['getting-started', 'installation'] }</code></td>
            </tr>
            <tr>
                <td class="code-cell">Optional Catch-All: <code>app/shop/[[...slug]]/page.tsx</code></td>
                <td>Matches <code>/shop</code> AND <code>/shop/shoes/nike</code></td>
                <td><code>{ slug: undefined }</code> OR <code>{ slug: ['shoes', 'nike'] }</code></td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Accessing Parameters in Server &amp; Client Components</h3>
<p class="text-slate-600 mb-4">In Server Components, <code>params</code> and <code>searchParams</code> are passed directly as component props. In Client Components, you use the <code>useParams()</code> and <code>useSearchParams()</code> hooks from <code>next/navigation</code>.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Interactive Dynamic Segment & Slug Parser</div>
    <pre><code class="language-javascript">function DynamicSegmentParser() {
    const [inputUrl, setInputUrl] = React.useState('/docs/v14/routing/dynamic-routes');

    const segments = inputUrl.replace(/^\//, '').split('/');
    const docCategory = segments[1] || 'root';
    const subTopics = segments.slice(2);

    return (
        &lt;div className="p-6 max-w-lg mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
            &lt;h3 className="text-base font-bold text-slate-900 mb-3"&gt;Dynamic Route &amp; Catch-All Parser&lt;/h3&gt;
            
            &lt;div className="mb-4"&gt;
                &lt;label className="block text-xs font-bold text-slate-500 mb-1"&gt;Test Route URL Path:&lt;/label&gt;
                &lt;input 
                    type="text" 
                    value={inputUrl} 
                    onChange={(e) =&gt; setInputUrl(e.target.value)} 
                    className="w-full px-3 py-2 border rounded-lg text-xs font-mono bg-slate-50"
                /&gt;
            &lt;/div&gt;

            &lt;div className="p-4 bg-slate-900 text-slate-200 rounded-lg text-xs font-mono space-y-2"&gt;
                &lt;div className="text-emerald-400"&gt;// Evaluated params object:&lt;/div&gt;
                &lt;div&gt;&lt;span className="text-indigo-400"&gt;category:&lt;/span&gt; "{docCategory}"&lt;/div&gt;
                &lt;div&gt;&lt;span className="text-indigo-400"&gt;slug (Array):&lt;/span&gt; {JSON.stringify(subTopics)}&lt;/div&gt;
                &lt;div className="border-t border-slate-800 pt-2 text-slate-400 text-[11px]"&gt;
                    Total Path Depth: &lt;strong className="text-white"&gt;{segments.length}&lt;/strong&gt; segments
                &lt;/div&gt;
            &lt;/div&gt;
        &lt;/div&gt;
    );
}</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 8, 'App Router Routing & Layouts', 'Dynamic Route Segments ([id], [...slug] Catch-All & [[...slug]] Optional Catch-All)', $content8, 'dynamic routes nextjs, catch all routes nextjs, optional catch all, params in nextjs, useparams, nextjs route parameters');

// -------------------------------------------------------------
// Chapter 9
// -------------------------------------------------------------
$content9 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">As applications scale, you often need to organize files logically without altering public URL structures. Next.js provides <strong>Route Groups</strong> using parentheses <code>(folderName)</code> to omit folders from URLs, and <strong>Private Folders</strong> using an underscore <code>_folderName</code> to opt folders out of routing entirely.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">Route Groups vs. Private Folders</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Convention</th>
                <th style="width: 35%;">File Structure</th>
                <th style="width: 40%;">Effect on Browser URL</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell">Route Group <code>(marketing)</code></td>
                <td><code>app/(marketing)/about/page.tsx</code></td>
                <td><strong><code>/about</code></strong> (The <code>(marketing)</code> segment is completely omitted from URL!).</td>
            </tr>
            <tr>
                <td class="code-cell">Multiple Root Layouts</td>
                <td><code>app/(auth)/layout.tsx</code> &amp; <code>app/(app)/layout.tsx</code></td>
                <td>Enables completely separate layouts (e.g. clean Auth modal vs complex App sidebar).</td>
            </tr>
            <tr>
                <td class="code-cell">Private Folder <code>_components</code></td>
                <td><code>app/dashboard/_components/Card.tsx</code></td>
                <td><strong>Opted out of routing:</strong> Never publicly reachable, even if someone places a <code>page.tsx</code> inside.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Multiple Root Layouts for Auth vs App</h3>
<p class="text-slate-600 mb-4">With route groups, you can split your app into <code>app/(auth)</code> and <code>app/(main)</code>, giving your authentication pages a minimal centered card layout while logged-in pages receive full navigation drawers.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Interactive Route Group Visualizer</div>
    <pre><code class="language-javascript">function RouteGroupVisualizer() {
    const [group, setGroup] = React.useState('auth');

    return (
        &lt;div className="p-6 max-w-lg mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
            &lt;h3 className="text-base font-bold text-slate-900 mb-3"&gt;Route Group Organization Architecture&lt;/h3&gt;
            &lt;div className="flex gap-2 mb-4"&gt;
                &lt;button onClick={() =&gt; setGroup('auth')} className={`px-3 py-1.5 text-xs font-bold rounded ${group === 'auth' ? 'bg-indigo-600 text-white' : 'bg-slate-100'}`}&gt;Group (auth)&lt;/button&gt;
                &lt;button onClick={() =&gt; setGroup('portal')} className={`px-3 py-1.5 text-xs font-bold rounded ${group === 'portal' ? 'bg-indigo-600 text-white' : 'bg-slate-100'}`}&gt;Group (portal)&lt;/button&gt;
            &lt;/div&gt;

            {group === 'auth' ? (
                &lt;div className="p-4 bg-indigo-50 border border-indigo-200 rounded-lg text-xs"&gt;
                    &lt;strong className="text-indigo-950 block mb-1"&gt;Folder: app/(auth)/login/page.tsx&lt;/strong&gt;
                    &lt;p className="text-indigo-800 mb-2"&gt;Clean Centered Card Layout without Navigation Bar.&lt;/p&gt;
                    &lt;div className="font-mono bg-white p-2 rounded border text-slate-700"&gt;
                        Public URL: &lt;strong className="text-indigo-600"&gt;/login&lt;/strong&gt; (Group name omitted!)
                    &lt;/div&gt;
                &lt;/div&gt;
            ) : (
                &lt;div className="p-4 bg-emerald-50 border border-emerald-200 rounded-lg text-xs"&gt;
                    &lt;strong className="text-emerald-950 block mb-1"&gt;Folder: app/(portal)/dashboard/page.tsx&lt;/strong&gt;
                    &lt;p className="text-emerald-800 mb-2"&gt;Full SaaS Workspace with Sidebar &amp; Top Notifications.&lt;/p&gt;
                    &lt;div className="font-mono bg-white p-2 rounded border text-slate-700"&gt;
                        Public URL: &lt;strong className="text-emerald-600"&gt;/dashboard&lt;/strong&gt;
                    &lt;/div&gt;
                &lt;/div&gt;
            )}
        &lt;/div&gt;
    );
}</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 9, 'App Router Routing & Layouts', 'Route Groups (group) & Private Folders (_components) for Organization', $content9, 'nextjs route groups, private folders in nextjs, multiple root layouts, organize nextjs project, app router grouping');

// -------------------------------------------------------------
// Chapter 10
// -------------------------------------------------------------
$content10 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">Next.js provides dedicated UI conventions for handling edge cases: <strong>loading.tsx</strong> (instant skeleton fallbacks), <strong>error.tsx</strong> (Client-side React error boundaries), and <strong>not-found.tsx</strong> (custom 404 views triggered manually via <code>notFound()</code>).</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">Special UI Files Behavior &amp; Constraints</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 20%;">File</th>
                <th style="width: 35%;">Underlying Mechanism</th>
                <th style="width: 45%;">Key Rule to Remember</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell"><code>loading.tsx</code></td>
                <td>Wraps <code>page.tsx</code> in a React <code>&lt;Suspense fallback={...}&gt;</code>.</td>
                <td>Shows instantly while server async operations fetch data.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>error.tsx</code></td>
                <td>Wraps <code>page.tsx</code> in a React Error Boundary.</td>
                <td><strong>Must include <code>'use client'</code>!</strong> Receives <code>error</code> and <code>reset()</code> function to retry rendering.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>not-found.tsx</code></td>
                <td>Invoked when URL doesn't match or code calls <code>notFound()</code>.</td>
                <td>Can be placed globally in <code>app/not-found.tsx</code> or scoped to specific route folders.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Error Boundary Recovery with reset()</h3>
<p class="text-slate-600 mb-4">The <code>error.tsx</code> component receives a <code>reset()</code> method. Clicking it prompts Next.js to re-render the Server Component tree, recovering from temporary network glitches without forcing a full page reload.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Interactive Error Boundary & Loading State Simulator</div>
    <pre><code class="language-javascript">function ErrorAndLoadingDemo() {
    const [state, setState] = React.useState('normal');

    return (
        &lt;div className="p-6 max-w-lg mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
            &lt;div className="flex justify-between items-center mb-4"&gt;
                &lt;h3 className="text-base font-bold text-slate-900"&gt;Resilient UI Simulation&lt;/h3&gt;
                &lt;div className="flex gap-1 text-xs font-bold"&gt;
                    &lt;button onClick={() =&gt; setState('loading')} className="px-2 py-1 bg-slate-100 rounded text-slate-700"&gt;loading.tsx&lt;/button&gt;
                    &lt;button onClick={() =&gt; setState('error')} className="px-2 py-1 bg-red-100 text-red-800 rounded"&gt;error.tsx&lt;/button&gt;
                    &lt;button onClick={() =&gt; setState('normal')} className="px-2 py-1 bg-emerald-100 text-emerald-800 rounded"&gt;page.tsx&lt;/button&gt;
                &lt;/div&gt;
            &lt;/div&gt;

            {state === 'loading' && (
                &lt;div className="p-6 bg-slate-50 border rounded-lg text-center animate-pulse"&gt;
                    &lt;div className="w-2/3 h-4 bg-slate-300 rounded mx-auto mb-3"&gt;&lt;/div&gt;
                    &lt;div className="w-1/2 h-3 bg-slate-200 rounded mx-auto mb-2"&gt;&lt;/div&gt;
                    &lt;span className="text-xs text-slate-400 font-mono"&gt;Streaming SSR Data across the wire...&lt;/span&gt;
                &lt;/div&gt;
            )}

            {state === 'error' && (
                &lt;div className="p-4 bg-red-50 border border-red-200 rounded-lg text-xs"&gt;
                    &lt;strong className="text-red-900 block mb-1 font-bold"&gt;⚠️ Caught by error.tsx Boundary&lt;/strong&gt;
                    &lt;p className="text-red-700 mb-3"&gt;Database connection timed out during Server Component rendering.&lt;/p&gt;
                    &lt;button onClick={() =&gt; setState('normal')} className="px-3 py-1 bg-red-600 text-white rounded text-xs font-bold shadow"&gt;
                        Recover with reset() &raquo;
                    &lt;/button&gt;
                &lt;/div&gt;
            )}

            {state === 'normal' && (
                &lt;div className="p-4 bg-emerald-50 border border-emerald-200 rounded-lg text-xs"&gt;
                    &lt;strong className="text-emerald-950 block font-bold mb-1"&gt;✓ Rendered page.tsx Successfully&lt;/strong&gt;
                    &lt;p className="text-emerald-800"&gt;Enterprise data loaded and fully synchronized.&lt;/p&gt;
                &lt;/div&gt;
            )}
        &lt;/div&gt;
    );
}</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 10, 'App Router Routing & Layouts', 'Special Route Files: loading.tsx, not-found.tsx, and error.tsx (Error Boundaries)', $content10, 'loading.tsx, error.tsx, not-found.tsx, nextjs error boundaries, notfound in nextjs, streaming ui nextjs');

// -------------------------------------------------------------
// Chapter 11
// -------------------------------------------------------------
$content11 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">For advanced interfaces, Next.js provides <strong>Parallel Routes</strong> (using <code>@slot</code> syntax to render multiple independent sub-pages simultaneously) and <strong>Intercepting Routes</strong> (using <code>(.)</code>, <code>(..)</code> syntax to load a route inside a modal overlay while masking the browser URL).</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">Advanced Routing Modifiers: Parallel vs. Intercepting</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Feature</th>
                <th style="width: 35%;">Syntax Blueprint</th>
                <th style="width: 40%;">Primary Real-World Scenario</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell">Parallel Routes</td>
                <td><code>app/@analytics/page.tsx</code> &amp; <code>app/@team/page.tsx</code></td>
                <td>Complex dashboards: Slots are passed as props to <code>layout.tsx</code> (<code>props.analytics</code>, <code>props.team</code>).</td>
            </tr>
            <tr>
                <td class="code-cell">Intercepting Routes</td>
                <td><code>app/feed/(.)photo/[id]/page.tsx</code></td>
                <td>Instagram/Twitter-style Photo Modals: Clicking opens a modal, but reloading opens the full photo page directly!</td>
            </tr>
            <tr>
                <td class="code-cell">Default Fallback</td>
                <td><code>default.tsx</code></td>
                <td>Rendered when an unmatched parallel slot does not have a matching state.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. The Photo Modal Pattern (Instagram/Twitter Experience)</h3>
<p class="text-slate-600 mb-4">When a user clicks a photo in their feed, an intercepting route catches the click and displays the photo in a modal without leaving the feed. If someone shares that URL or presses F5 (hard refresh), Next.js renders the full photo standalone page!</p>

<div class="w3-example-box">
    <div class="w3-example-title">Interactive Intercepting Route Modal Experience</div>
    <pre><code class="language-javascript">function InterceptingRouteDemo() {
    const [modalPhoto, setModalPhoto] = React.useState(null);

    return (
        &lt;div className="p-6 max-w-lg mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
            &lt;h3 className="text-base font-bold text-slate-900 mb-2"&gt;Photo Feed &amp; Intercepting Route Modal&lt;/h3&gt;
            &lt;p className="text-xs text-slate-500 mb-4"&gt;Click any item to simulate an intercepting modal route: &lt;code className="font-mono bg-slate-100 px-1 py-0.5 rounded"&gt;(.)photo/[id]&lt;/code&gt;&lt;/p&gt;

            &lt;div className="grid grid-cols-2 gap-3 mb-4"&gt;
                &lt;div onClick={() =&gt; setModalPhoto({ id: 101, title: 'Server Rack Cluster', loc: '/photo/101' })} className="p-3 bg-slate-50 border rounded-lg cursor-pointer hover:border-indigo-500 transition text-center"&gt;
                    &lt;span className="text-2xl block mb-1"&gt;🖥️&lt;/span&gt;
                    &lt;strong className="text-xs text-slate-800"&gt;Cluster Photo #101&lt;/strong&gt;
                &lt;/div&gt;
                &lt;div onClick={() =&gt; setModalPhoto({ id: 102, title: 'Cloud Data Center', loc: '/photo/102' })} className="p-3 bg-slate-50 border rounded-lg cursor-pointer hover:border-indigo-500 transition text-center"&gt;
                    &lt;span className="text-2xl block mb-1"&gt;☁️&lt;/span&gt;
                    &lt;strong className="text-xs text-slate-800"&gt;Cluster Photo #102&lt;/strong&gt;
                &lt;/div&gt;
            &lt;/div&gt;

            {modalPhoto && (
                &lt;div className="p-4 bg-slate-900 text-white rounded-lg text-xs"&gt;
                    &lt;div className="flex justify-between items-center mb-2"&gt;
                        &lt;strong className="text-emerald-400 font-mono"&gt;Intercepted Route Modal Active&lt;/strong&gt;
                        &lt;button onClick={() =&gt; setModalPhoto(null)} className="text-slate-400 hover:text-white font-bold"&gt;&times; Close&lt;/button&gt;
                    &lt;/div&gt;
                    &lt;p className="text-slate-300"&gt;Displaying: &lt;strong className="text-white"&gt;{modalPhoto.title}&lt;/strong&gt;&lt;/p&gt;
                    &lt;span className="text-[10px] text-slate-500 font-mono block mt-1"&gt;Browser URL updated to: {modalPhoto.loc}&lt;/span&gt;
                &lt;/div&gt;
            )}
        &lt;/div&gt;
    );
}</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 11, 'App Router Routing & Layouts', 'Advanced Routing: Parallel Routes (@slot) & Intercepting Routes ((.), (..))', $content11, 'parallel routes nextjs, intercepting routes nextjs, @slot in nextjs, modal routes nextjs, default.tsx nextjs, advanced nextjs routing');
