<?php
// seed_nextjs_part3.php: Chapters 24 to 35 (Optimistic UI, Caching, Route Handlers & Middleware)

// -------------------------------------------------------------
// Chapter 24
// -------------------------------------------------------------
$content24 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">Waiting 500ms for a server response to update the UI feels sluggish. <strong>Optimistic UI</strong> is a pattern where the client updates its interface <em>instantly</em> under the assumption that the server action will succeed, automatically rolling back only if the mutation fails. In Next.js and React 19, this is handled natively by the <strong>useOptimistic</strong> hook.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">Anatomy of useOptimistic Hook</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Parameter / Output</th>
                <th style="width: 35%;">Syntax Structure</th>
                <th style="width: 40%;">Behavior</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell">Initial State</td>
                <td><code>useOptimistic(actualData, updateFn)</code></td>
                <td>The real, authoritative server-rendered state.</td>
            </tr>
            <tr>
                <td class="code-cell">Update Reducer</td>
                <td><code>(current, optimisticValue) =&gt; [...current, optimisticValue]</code></td>
                <td>Pure function defining how the optimistic item is merged into state immediately.</td>
            </tr>
            <tr>
                <td class="code-cell">Automatic Rollback</td>
                <td>Internal React Engine</td>
                <td>If the Server Action throws an error or rejects, React automatically discards the optimistic item!</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Instant Social Post Like or Comment</h3>
<p class="text-slate-600 mb-4">Observe how the comment appears in the list <strong>instantly (0ms delay)</strong> before the simulated 1-second server mutation completes:</p>

<div class="w3-example-box">
    <div class="w3-example-title">Interactive Live Demo: useOptimistic Instant Feedback Feed</div>
    <pre><code class="language-javascript">function OptimisticListDemo() {
    const [messages, setMessages] = React.useState([
        { id: 1, text: 'Initial cloud architecture approved.', pending: false }
    ]);
    const [optimisticMessages, setOptimisticMessages] = React.useState(messages);
    const [input, setInput] = React.useState('');

    const sendMessage = async (e) => {
        e.preventDefault();
        if (!input.trim()) return;

        const optimisticId = Date.now();
        const optimisticItem = { id: optimisticId, text: input, pending: true };
        
        // 1. Instantly show optimistic item in UI (0ms delay!)
        setOptimisticMessages(prev => [...prev, optimisticItem]);
        setInput('');

        // 2. Simulate Server Action network delay (800ms)
        await new Promise(r => setTimeout(r, 800));

        // 3. Commit confirmed server response
        const confirmedItem = { id: optimisticId, text: optimisticItem.text, pending: false };
        setMessages(prev => [...prev, confirmedItem]);
        setOptimisticMessages(prev => prev.map(m => m.id === optimisticId ? confirmedItem : m));
    };

    return (
        &lt;div className="p-6 max-w-md mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
            &lt;h3 className="text-base font-bold text-slate-900 mb-3"&gt;Instant Optimistic UI Messaging&lt;/h3&gt;

            &lt;div className="space-y-2 mb-4"&gt;
                {optimisticMessages.map(m =&gt; (
                    &lt;div key={m.id} className={`p-3 rounded-lg border text-xs flex justify-between items-center ${m.pending ? 'bg-amber-50 border-amber-200 text-amber-900 opacity-80' : 'bg-slate-50 border-slate-200 text-slate-800'}`}&gt;
                        &lt;span className="font-semibold"&gt;{m.text}&lt;/span&gt;
                        &lt;span className="text-[10px] font-bold"&gt;{m.pending ? '⏳ Sending...' : '✓ Synced'}&lt;/span&gt;
                    &lt;/div&gt;
                ))}
            &lt;/div&gt;

            &lt;form onSubmit={sendMessage} className="flex gap-2"&gt;
                &lt;input 
                    type="text" 
                    value={input} 
                    onChange={e =&gt; setInput(e.target.value)} 
                    placeholder="Type an instant message..." 
                    className="flex-1 px-3 py-1.5 border rounded-lg text-xs"
                /&gt;
                &lt;button type="submit" className="px-3 py-1.5 bg-slate-900 text-white rounded-lg text-xs font-bold"&gt;
                    Post
                &lt;/button&gt;
            &lt;/form&gt;
        &lt;/div&gt;
    );
}</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 24, 'Optimistic UI & Validation', 'Optimistic UI Updates with useOptimistic for Instant User Feedback', $content24, 'useoptimistic nextjs, optimistic ui updates, instant feedback nextjs, react 19 useoptimistic, rollback on error nextjs');

// -------------------------------------------------------------
// Chapter 25
// -------------------------------------------------------------
$content25 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">Never trust client input! In enterprise Server Actions, input payloads must be strictly validated before touching the database. The industry standard pairing in Next.js is <strong>Zod</strong> for runtime schema parsing and returning structured error objects.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">Server-Side Input Validation Architecture</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Validation Step</th>
                <th style="width: 35%;">Tool / Method</th>
                <th style="width: 40%;">Protection Benefit</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell">Schema Definition</td>
                <td><code>z.object({ email: z.string().email() })</code></td>
                <td>Guarantees exact types, string lengths, regex patterns, and prevents prototype pollution.</td>
            </tr>
            <tr>
                <td class="code-cell">Safe Parsing</td>
                <td><code>schema.safeParse(data)</code></td>
                <td>Never throws exceptions; returns <code>{ success: false, error: ... }</code> for graceful error feedback.</td>
            </tr>
            <tr>
                <td class="code-cell">Field-Level Error Mapping</td>
                <td><code>result.error.flatten().fieldErrors</code></td>
                <td>Maps validation errors directly to individual input field names for instant form error highlighting.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Production Schema Validation Pipeline</h3>
<pre class="bg-slate-900 text-slate-200 p-4 rounded-xl font-mono text-xs mb-4">
// app/actions/signup.ts
'use server';
import { z } from 'zod';

const SignupSchema = z.object({
  username: z.string().min(3, "Username must have at least 3 characters"),
  email: z.string().email("Invalid email address"),
  age: z.coerce.number().min(18, "Must be at least 18 years old")
});

export async function createAccount(prevState: any, formData: FormData) {
  const parsed = SignupSchema.safeParse(Object.fromEntries(formData.entries()));

  if (!parsed.success) {
    return { errors: parsed.error.flatten().fieldErrors };
  }

  // Proceed with safe database transaction
  await db.user.create({ data: parsed.data });
  return { success: true };
}
</pre>

<div class="w3-example-box">
    <div class="w3-example-title">Interactive Zod Schema Validation Simulator</div>
    <pre><code class="language-javascript">function ZodValidationDemo() {
    const [errors, setErrors] = React.useState({});
    const [success, setSuccess] = React.useState(false);

    const validateAndSubmit = (e) => {
        e.preventDefault();
        setSuccess(false);
        const form = e.currentTarget;
        const username = form.username.value;
        const email = form.email.value;
        const age = Number(form.age.value);

        const newErrors = {};
        if (username.length < 3) newErrors.username = "Username must be at least 3 characters.";
        if (!email.includes('@')) newErrors.email = "Must be a valid email address.";
        if (isNaN(age) || age < 18) newErrors.age = "Must be at least 18 years of age.";

        if (Object.keys(newErrors).length > 0) {
            setErrors(newErrors);
        } else {
            setErrors({});
            setSuccess(true);
        }
    };

    return (
        &lt;div className="p-6 max-w-md mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
            &lt;h3 className="text-base font-bold text-slate-900 mb-3"&gt;Zod Schema Validation Simulator&lt;/h3&gt;

            &lt;form onSubmit={validateAndSubmit} className="space-y-3"&gt;
                &lt;div&gt;
                    &lt;label className="block text-xs font-bold text-slate-600 mb-1"&gt;Username:&lt;/label&gt;
                    &lt;input type="text" name="username" className="w-full px-3 py-1.5 border rounded-lg text-xs" placeholder="e.g. alex_dev" /&gt;
                    {errors.username && &lt;span className="text-red-500 text-[11px] font-bold"&gt;&times; {errors.username}&lt;/span&gt;}
                &lt;/div&gt;

                &lt;div&gt;
                    &lt;label className="block text-xs font-bold text-slate-600 mb-1"&gt;Email Address:&lt;/label&gt;
                    &lt;input type="text" name="email" className="w-full px-3 py-1.5 border rounded-lg text-xs" placeholder="alex@domain.com" /&gt;
                    {errors.email && &lt;span className="text-red-500 text-[11px] font-bold"&gt;&times; {errors.email}&lt;/span&gt;}
                &lt;/div&gt;

                &lt;div&gt;
                    &lt;label className="block text-xs font-bold text-slate-600 mb-1"&gt;Age:&lt;/label&gt;
                    &lt;input type="number" name="age" className="w-full px-3 py-1.5 border rounded-lg text-xs" placeholder="21" /&gt;
                    {errors.age && &lt;span className="text-red-500 text-[11px] font-bold"&gt;&times; {errors.age}&lt;/span&gt;}
                &lt;/div&gt;

                &lt;button type="submit" className="w-full py-2 bg-slate-900 text-white rounded-lg text-xs font-bold shadow"&gt;
                    Submit Server Action
                &lt;/button&gt;
            &lt;/form&gt;

            {success && (
                &lt;div className="mt-3 p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-xs font-bold"&gt;
                    ✓ Payload passed all Zod schema validation rules!
                &lt;/div&gt;
            )}
        &lt;/div&gt;
    );
}</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 25, 'Optimistic UI & Validation', 'Validating Server Action Inputs with Zod & Returning Structured Error Objects', $content25, 'zod validation nextjs, server actions validation, zod safeParse, fieldErrors in nextjs, secure server actions');

// -------------------------------------------------------------
// Chapter 26
// -------------------------------------------------------------
$content26 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">Next.js features an aggressive multi-tiered caching architecture designed to deliver sub-millisecond page loads. Understanding the <strong>4 caching mechanisms</strong> (Request Memoization, Data Cache, Full Route Cache, and Router Cache) is crucial to prevent serving stale data to your users.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">The 4 Next.js Caching Layers Explained</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Caching Layer</th>
                <th style="width: 25%;">Where It Lives</th>
                <th style="width: 25%;">Lifetime</th>
                <th style="width: 25%;">Purpose</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell">1. Request Memoization</td>
                <td>Server (In-Memory)</td>
                <td>Duration of a single request</td>
                <td>Deduplicates identical <code>fetch()</code> calls across multiple components during one render.</td>
            </tr>
            <tr>
                <td class="code-cell">2. Data Cache</td>
                <td>Server (Persistent)</td>
                <td>Across multiple requests &amp; deploys</td>
                <td>Stores fetched API data across all users until revalidated (e.g. <code>revalidateTag</code>).</td>
            </tr>
            <tr>
                <td class="code-cell">3. Full Route Cache</td>
                <td>Server / CDN Edge</td>
                <td>Across multiple requests</td>
                <td>Caches rendered HTML and RSC payload at build time for static routes.</td>
            </tr>
            <tr>
                <td class="code-cell">4. Router Cache</td>
                <td>Client Browser Memory</td>
                <td>Session / Tab duration</td>
                <td>Stores visited and prefetched route segments in client browser memory for instant back/forward navigation.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Opting Out of Caching</h3>
<p class="text-slate-600 mb-4">To ensure dynamic views (such as user bank accounts or real-time trading feeds) are never cached, opt out at the route or fetch level using <code>export const dynamic = 'force-dynamic'</code> or <code>fetch(url, { cache: 'no-store' })</code>.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Interactive Caching Architecture Explorer</div>
    <pre><code class="language-javascript">function CacheArchitectureVisualizer() {
    const [activeLayer, setActiveLayer] = React.useState('data');

    const layers = {
        memo: { title: "1. Request Memoization", location: "Server Memory", dur: "1 Request", desc: "Prevents duplicate fetch calls when Navbar and Page both ask for the current user profile." },
        data: { title: "2. Data Cache (Persistent)", location: "Server Disk / Redis", dur: "Permanent or Timer", desc: "Stores remote API results across all users. Revalidated via revalidateTag() or timer." },
        full: { title: "3. Full Route Cache", location: "Edge Server / CDN", dur: "Until Revalidation", desc: "Stores fully compiled static HTML and RSC payload on global edge networks." },
        router: { title: "4. Router Cache", location: "Client Browser RAM", dur: "User Browser Session", desc: "Stores visited page chunks in browser memory so back/forward clicks require zero network trips." }
    };

    const cur = layers[activeLayer];

    return (
        &lt;div className="p-6 max-w-lg mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
            &lt;h3 className="text-base font-bold text-slate-900 mb-3"&gt;Next.js 4-Layer Caching Architecture&lt;/h3&gt;
            &lt;div className="grid grid-cols-2 gap-2 mb-4"&gt;
                {Object.keys(layers).map(k =&gt; (
                    &lt;button key={k} onClick={() =&gt; setActiveLayer(k)} className={`px-2.5 py-1.5 text-xs font-bold rounded border ${activeLayer === k ? 'bg-slate-900 text-white' : 'bg-slate-50 text-slate-700'}`}&gt;
                        {layers[k].title.split(' ')[1]} Cache
                    &lt;/button&gt;
                ))}
            &lt;/div&gt;

            &lt;div className="p-4 bg-slate-50 border rounded-lg text-xs"&gt;
                &lt;div className="flex justify-between items-center mb-2"&gt;
                    &lt;strong className="text-slate-900 text-sm"&gt;{cur.title}&lt;/strong&gt;
                    &lt;span className="bg-emerald-100 text-emerald-800 text-[10px] font-bold px-2 py-0.5 rounded"&gt;{cur.location}&lt;/span&gt;
                &lt;/div&gt;
                &lt;p className="text-slate-600 mb-2"&gt;{cur.desc}&lt;/p&gt;
                &lt;span className="text-[11px] font-mono text-slate-500 block border-t pt-2"&gt;Persistence Lifetime: &lt;strong className="text-slate-900"&gt;{cur.dur}&lt;/strong&gt;&lt;/span&gt;
            &lt;/div&gt;
        &lt;/div&gt;
    );
}</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 26, 'Caching & Rendering Strategies', 'The Next.js 4-Layer Caching Architecture: Request Memoization, Data Cache, Full Route Cache, Router Cache', $content26, 'nextjs caching, request memoization, data cache, full route cache, router cache in nextjs, stale data in nextjs');

// -------------------------------------------------------------
// Chapter 27
// -------------------------------------------------------------
$content27 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">Dynamic routes (e.g. <code>app/blog/[slug]/page.tsx</code>) are dynamic by default. To pre-render them as fast, static HTML files at build time, you export the <strong>generateStaticParams()</strong> function. This replaces the legacy <code>getStaticPaths</code> API and enables pure Static Site Generation (SSG) for dynamic URLs.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">generateStaticParams() Mechanics</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Property</th>
                <th style="width: 35%;">Syntax Specification</th>
                <th style="width: 40%;">Benefit</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell">Return Type</td>
                <td><code>Array&lt;{ slug: string }&gt;</code></td>
                <td>Returns an array of parameter objects matching the folder segment name.</td>
            </tr>
            <tr>
                <td class="code-cell">Build-Time Execution</td>
                <td>Runs during <code>next build</code></td>
                <td>Next.js generates dedicated static HTML and RSC payloads for every returned slug.</td>
            </tr>
            <tr>
                <td class="code-cell">Unmatched Slugs</td>
                <td><code>export const dynamicParams = true / false</code></td>
                <td>If <code>true</code> (default), unmatched slugs are rendered on-demand upon first visit; if <code>false</code>, throws instant 404.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. generateStaticParams Implementation</h3>
<pre class="bg-slate-900 text-slate-200 p-4 rounded-xl font-mono text-xs mb-4">
// app/products/[id]/page.tsx
export async function generateStaticParams() {
  const products = await db.products.findMany({ select: { id: true } });

  return products.map((product) => ({
    id: product.id.toString(),
  }));
}

export default async function ProductPage({ params }: { params: { id: string } }) {
  const product = await getProduct(params.id);
  return &lt;h1&gt;{product.name}&lt;/h1&gt;;
}
</pre>

<div class="w3-example-box">
    <div class="w3-example-title">Interactive SSG Parameter Simulator</div>
    <pre><code class="language-javascript">function StaticParamsDemo() {
    const staticSlugs = ['react-fundamentals', 'nextjs-masterclass', 'typescript-deep-dive'];
    const [selectedSlug, setSelectedSlug] = React.useState('nextjs-masterclass');

    return (
        &lt;div className="p-6 max-w-lg mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
            &lt;h3 className="text-base font-bold text-slate-900 mb-2"&gt;generateStaticParams Build Output&lt;/h3&gt;
            &lt;p className="text-xs text-slate-500 mb-4"&gt;Select pre-rendered static route to view build payload:&lt;/p&gt;

            &lt;div className="flex gap-2 mb-4 flex-wrap"&gt;
                {staticSlugs.map(slug =&gt; (
                    &lt;button key={slug} onClick={() =&gt; setSelectedSlug(slug)} className={`px-2.5 py-1 text-xs font-mono font-bold rounded ${selectedSlug === slug ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-700'}`}&gt;
                        /{slug}
                    &lt;/button&gt;
                ))}
            &lt;/div&gt;

            &lt;div className="p-4 bg-slate-900 text-slate-200 rounded-lg text-xs font-mono"&gt;
                &lt;div className="text-emerald-400 mb-1"&gt;○ (SSG) Pre-rendered as static HTML:&lt;/div&gt;
                &lt;div&gt;URL: &lt;strong className="text-white"&gt;/blog/{selectedSlug}&lt;/strong&gt;&lt;/div&gt;
                &lt;div className="text-slate-400 mt-2 text-[11px]"&gt;
                    Generated at build time via generateStaticParams(). Served in 10ms from CDN.
                &lt;/div&gt;
            &lt;/div&gt;
        &lt;/div&gt;
    );
}</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 27, 'Caching & Rendering Strategies', 'Static Generation (SSG) with generateStaticParams() for Dynamic Routes', $content27, 'generatestaticparams, ssg in nextjs, getstaticpaths replacement, dynamic routes ssg, build time generation nextjs');

// -------------------------------------------------------------
// Chapter 28
// -------------------------------------------------------------
$content28 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700"><strong>Incremental Static Regeneration (ISR)</strong> allows you to update static pages in the background <em>without rebuilding your entire website</em>. By specifying a revalidation interval in seconds, Next.js serves the cached static page while regenerating the fresh HTML in the background once the timer expires.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">The Stale-While-Revalidate Lifecycle</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">User Request</th>
                <th style="width: 35%;">Action Performed by Next.js</th>
                <th style="width: 40%;">User Experience</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell">1. Initial Request (&lt; 60s)</td>
                <td>Serves pre-rendered static HTML directly from CDN cache.</td>
                <td>Instant CDN response (10ms).</td>
            </tr>
            <tr>
                <td class="code-cell">2. Request after 60s</td>
                <td>Still serves the <strong>stale cached page</strong> to the current user, while silently triggering a background revalidation.</td>
                <td>Instant response! User experiences zero loading wait.</td>
            </tr>
            <tr>
                <td class="code-cell">3. Subsequent Requests</td>
                <td>The background build completed: CDN cache is purged and updated with fresh HTML.</td>
                <td>All future users immediately see the updated content!</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Setting Revalidation Intervals</h3>
<pre class="bg-slate-900 text-slate-200 p-4 rounded-xl font-mono text-xs mb-4">
// Option A: Route-level Revalidation (app/catalog/page.tsx)
export const revalidate = 60; // Revalidate every 60 seconds

// Option B: Fetch-level Revalidation
const res = await fetch('https://api.example.com/items', {
  next: { revalidate: 300 } // Revalidate every 5 minutes
});
</pre>

<div class="w3-example-box">
    <div class="w3-example-title">Interactive Stale-While-Revalidate Timeline Simulator</div>
    <pre><code class="language-javascript">function IsrTimelineDemo() {
    const [secondsElapsed, setSecondsElapsed] = React.useState(15);

    return (
        &lt;div className="p-6 max-w-lg mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
            &lt;h3 className="text-base font-bold text-slate-900 mb-2"&gt;ISR Stale-While-Revalidate Timeline&lt;/h3&gt;
            &lt;p className="text-xs text-slate-500 mb-4"&gt;Drag slider to see cache status when revalidate = 60s:&lt;/p&gt;

            &lt;div className="mb-4"&gt;
                &lt;div className="flex justify-between text-xs font-bold mb-1"&gt;
                    &lt;span&gt;Time Elapsed Since Last Rebuild:&lt;/span&gt;
                    &lt;span className="font-mono text-indigo-600"&gt;{secondsElapsed} seconds&lt;/span&gt;
                &lt;/div&gt;
                &lt;input type="range" min="0" max="120" value={secondsElapsed} onChange={e =&gt; setSecondsElapsed(Number(e.target.value))} className="w-full" /&gt;
            &lt;/div&gt;

            &lt;div className={`p-4 rounded-lg border text-xs ${secondsElapsed &lt; 60 ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-amber-50 border-amber-200 text-amber-900'}`}&gt;
                &lt;div className="font-bold mb-1"&gt;
                    {secondsElapsed &lt; 60 ? '✓ Cache Status: FRESH (CDN HIT)' : '⏳ Cache Status: STALE (Background Revalidation Triggered)'}
                &lt;/div&gt;
                &lt;p className="text-[11px]"&gt;
                    {secondsElapsed &lt; 60 
                        ? 'Next.js delivers the static cached HTML instantly without touching the server database.' 
                        : 'Next.js serves the cached page to the user immediately, while rebuilding fresh data in the background!'}
                &lt;/p&gt;
            &lt;/div&gt;
        &lt;/div&gt;
    );
}</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 28, 'Caching & Rendering Strategies', 'Incremental Static Regeneration (ISR): Time-Based Revalidation (fetch(..., { next: { revalidate: 60 } }))', $content28, 'isr in nextjs, incremental static regeneration, stale while revalidate, export const revalidate, background data update nextjs');

// -------------------------------------------------------------
// Chapter 29
// -------------------------------------------------------------
$content29 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">Waiting for a 60-second timer to update content is insufficient when an admin publishes a breaking news post or updates product pricing. Next.js provides <strong>On-Demand Revalidation</strong> via <strong>revalidatePath()</strong> (clears cache by URL path) and <strong>revalidateTag()</strong> (clears cache by semantic cache tag).</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">revalidatePath vs. revalidateTag</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Function</th>
                <th style="width: 35%;">Syntax Example</th>
                <th style="width: 40%;">Ideal Scenario</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell"><code>revalidatePath(path)</code></td>
                <td><code>revalidatePath('/blog/[slug]', 'page')</code></td>
                <td>When you know the exact URL path that changed after an edit.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>revalidateTag(tag)</code></td>
                <td><code>revalidateTag('products')</code></td>
                <td><strong>Best Practice:</strong> Invalidates <em>every</em> page that fetched data tagged with <code>'products'</code> across the entire site with one call!</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Tagging Fetches &amp; Purging on Mutation</h3>
<pre class="bg-slate-900 text-slate-200 p-4 rounded-xl font-mono text-xs mb-4">
// 1. Tag the query when fetching
const res = await fetch('https://api.../products', {
  next: { tags: ['products'] } // Attach semantic tag
});

// 2. Purge the tag in a Server Action or Webhook
'use server';
import { revalidateTag } from 'next/cache';

export async function addProduct(data: FormData) {
  await db.product.create({ ... });
  revalidateTag('products'); // Instantly purges cache on all servers!
}
</pre>

<div class="w3-example-box">
    <div class="w3-example-title">Interactive On-Demand Cache Tag Purger</div>
    <pre><code class="language-javascript">function OnDemandRevalidationDemo() {
    const [tagStatus, setTagStatus] = React.useState('Cached at Edge (v1.0)');

    const purgeCache = () => {
        setTagStatus('Purging cache tag: "products"...');
        setTimeout(() => {
            setTagStatus('✓ Revalidated! Fresh cache generated (v1.1) at ' + new Date().toLocaleTimeString());
        }, 500);
    };

    return (
        &lt;div className="p-6 max-w-lg mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
            &lt;h3 className="text-base font-bold text-slate-900 mb-2"&gt;On-Demand Cache Tag Purging&lt;/h3&gt;
            &lt;p className="text-xs text-slate-500 mb-4"&gt;Simulate updating a database record and calling &lt;code className="font-mono bg-slate-100 px-1 py-0.5 rounded"&gt;revalidateTag('products')&lt;/code&gt;:&lt;/p&gt;

            &lt;button onClick={purgeCache} className="px-3 py-1.5 bg-rose-600 text-white rounded text-xs font-bold shadow mb-4"&gt;
                Trigger revalidateTag('products')
            &lt;/button&gt;

            &lt;div className="p-3 bg-slate-50 border rounded-lg text-xs font-mono"&gt;
                &lt;span className="text-slate-400 block mb-1"&gt;Edge Cache Tag Status:&lt;/span&gt;
                &lt;strong className="text-emerald-700 font-bold"&gt;{tagStatus}&lt;/strong&gt;
            &lt;/div&gt;
        &lt;/div&gt;
    );
}</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 29, 'Caching & Rendering Strategies', 'On-Demand Tag & Path Revalidation: revalidatePath() and revalidateTag()', $content29, 'revalidatepath, revalidatetag, on demand revalidation nextjs, next/cache, purge cdn cache nextjs, instant cache update');

// -------------------------------------------------------------
// Chapter 30
// -------------------------------------------------------------
$content30 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">Next.js automatically chooses whether a route is static or dynamic based on its data requirements. When a component reads request-time information (like <strong>cookies()</strong>, <strong>headers()</strong>, or <strong>searchParams</strong>), Next.js automatically switches the route to <strong>Dynamic Rendering</strong>.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">Dynamic Functions that Opt Route into Dynamic Rendering</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Dynamic Function</th>
                <th style="width: 35%;">Syntax Example</th>
                <th style="width: 40%;">Why It Forces Dynamic Rendering</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell"><code>cookies()</code></td>
                <td><code>const userCookie = cookies().get('session');</code></td>
                <td>Cookies belong to an individual user request and cannot be pre-rendered at build time.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>headers()</code></td>
                <td><code>const auth = headers().get('authorization');</code></td>
                <td>Request headers (IP address, user agent) are only known upon incoming HTTP request.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>searchParams</code> prop</td>
                <td><code>function Page({ searchParams })</code></td>
                <td>URL query strings (e.g. <code>?query=shoes&amp;page=2</code>) change dynamically per visitor.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>dynamic = 'force-dynamic'</code></td>
                <td><code>export const dynamic = 'force-dynamic';</code></td>
                <td>Explicit route segment configuration to force SSR on every request.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Reading Cookies in Server Components</h3>
<pre class="bg-slate-900 text-slate-200 p-4 rounded-xl font-mono text-xs mb-4">
// app/account/page.tsx
import { cookies } from 'next/headers';

export default function AccountPage() {
  const cookieStore = cookies();
  const token = cookieStore.get('auth_token')?.value;

  return &lt;div&gt;Active Session Token: {token || 'Guest'}&lt;/div&gt;;
}
</pre>

<div class="w3-example-box">
    <div class="w3-example-title">Interactive Route Rendering Mode Detector</div>
    <pre><code class="language-javascript">function DynamicRenderDetector() {
    const [usesCookies, setUsesCookies] = React.useState(false);
    const [usesQuery, setUsesQuery] = React.useState(false);

    const isDynamic = usesCookies || usesQuery;

    return (
        &lt;div className="p-6 max-w-lg mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
            &lt;h3 className="text-base font-bold text-slate-900 mb-3"&gt;Route Rendering Mode Evaluator&lt;/h3&gt;
            
            &lt;div className="space-y-2 mb-4 text-xs font-semibold"&gt;
                &lt;label className="flex items-center gap-2 cursor-pointer"&gt;
                    &lt;input type="checkbox" checked={usesCookies} onChange={e =&gt; setUsesCookies(e.target.checked)} /&gt;
                    &lt;span&gt;Calls &lt;code className="bg-slate-100 px-1 py-0.5 rounded"&gt;cookies()&lt;/code&gt; or &lt;code className="bg-slate-100 px-1 py-0.5 rounded"&gt;headers()&lt;/code&gt;&lt;/span&gt;
                &lt;/label&gt;
                &lt;label className="flex items-center gap-2 cursor-pointer"&gt;
                    &lt;input type="checkbox" checked={usesQuery} onChange={e =&gt; setUsesQuery(e.target.checked)} /&gt;
                    &lt;span&gt;Inspects URL &lt;code className="bg-slate-100 px-1 py-0.5 rounded"&gt;searchParams&lt;/code&gt;&lt;/span&gt;
                &lt;/label&gt;
            &lt;/div&gt;

            &lt;div className={`p-4 rounded-lg border text-xs ${isDynamic ? 'bg-indigo-50 border-indigo-200 text-indigo-950' : 'bg-emerald-50 border-emerald-200 text-emerald-950'}`}&gt;
                &lt;div className="font-bold text-sm mb-1"&gt;
                    {isDynamic ? 'λ Dynamic Route (SSR on Request)' : '○ Static Route (Pre-rendered SSG)'}
                &lt;/div&gt;
                &lt;p className="text-[11px]"&gt;
                    {isDynamic ? 'Next.js automatically renders this route on-demand per request on the server.' : 'Next.js automatically pre-renders this route as a fast static asset.'}
                &lt;/p&gt;
            &lt;/div&gt;
        &lt;/div&gt;
    );
}</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 30, 'Caching & Rendering Strategies', 'Dynamic Rendering: cookies(), headers(), searchParams & export const dynamic = \'force-dynamic\'', $content30, 'dynamic rendering nextjs, cookies in nextjs, headers nextjs, searchparams nextjs, force-dynamic, static to dynamic nextjs');

// -------------------------------------------------------------
// Chapter 31
// -------------------------------------------------------------
$content31 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">When you need to build custom RESTful API endpoints for mobile apps, external webhooks (like Stripe or GitHub), or third-party consumers, you create <strong>Route Handlers</strong> using a <strong>route.ts</strong> file. Route Handlers support standard HTTP methods: <code>GET</code>, <code>POST</code>, <code>PUT</code>, <code>PATCH</code>, <code>DELETE</code>, and <code>OPTIONS</code>.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">Route Handlers Architecture (route.ts)</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">HTTP Method</th>
                <th style="width: 35%;">Export Signature</th>
                <th style="width: 40%;">Standard Purpose</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell"><code>GET</code></td>
                <td><code>export async function GET(request: Request)</code></td>
                <td>Fetching records, returning JSON lists, streaming SSE.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>POST</code></td>
                <td><code>export async function POST(request: Request)</code></td>
                <td>Creating records, processing Stripe webhook payments.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>DELETE</code></td>
                <td><code>export async function DELETE(request: Request)</code></td>
                <td>Removing records from storage or databases.</td>
            </tr>
            <tr>
                <td class="code-cell">Folder Rule</td>
                <td><code>app/api/users/route.ts</code></td>
                <td>Cannot coexist in the exact same folder with a <code>page.tsx</code>!</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Writing a Type-Safe Route Handler</h3>
<pre class="bg-slate-900 text-slate-200 p-4 rounded-xl font-mono text-xs mb-4">
// app/api/telemetry/route.ts
import { NextResponse } from 'next/server';

export async function GET() {
  const data = { status: 'operational', timestamp: new Date().toISOString() };
  return NextResponse.json(data, { status: 200 });
}

export async function POST(request: Request) {
  const body = await request.json();
  return NextResponse.json({ received: body, ok: true }, { status: 201 });
}
</pre>

<div class="w3-example-box">
    <div class="w3-example-title">Interactive Route Handler API Simulator</div>
    <pre><code class="language-javascript">function RouteHandlerSimulator() {
    const [response, setResponse] = React.useState(null);
    const [method, setMethod] = React.useState('GET');

    const invokeApi = () => {
        if (method === 'GET') {
            setResponse({ status: 200, data: { system: 'Nectra Cloud Hub', nodes: 8, load: '14%' } });
        } else {
            setResponse({ status: 201, data: { message: 'Created telemetry ping record successfully', id: 'ping_901' } });
        }
    };

    return (
        &lt;div className="p-6 max-w-lg mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
            &lt;h3 className="text-base font-bold text-slate-900 mb-3"&gt;Route Handler (route.ts) Simulator&lt;/h3&gt;
            
            &lt;div className="flex gap-2 mb-4"&gt;
                &lt;button onClick={() =&gt; { setMethod('GET'); setResponse(null); }} className={`px-3 py-1 text-xs font-bold rounded ${method === 'GET' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-700'}`}&gt;GET /api/health&lt;/button&gt;
                &lt;button onClick={() =&gt; { setMethod('POST'); setResponse(null); }} className={`px-3 py-1 text-xs font-bold rounded ${method === 'POST' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-700'}`}&gt;POST /api/health&lt;/button&gt;
                &lt;button onClick={invokeApi} className="ml-auto px-3 py-1 bg-slate-900 text-white rounded text-xs font-bold shadow"&gt;Send HTTP&lt;/button&gt;
            &lt;/div&gt;

            {response && (
                &lt;div className="p-3 bg-slate-900 text-slate-200 rounded-lg text-xs font-mono"&gt;
                    &lt;div className="text-emerald-400 mb-1"&gt;NextResponse.json (Status: {response.status})&lt;/div&gt;
                    &lt;pre className="text-slate-300 overflow-auto"&gt;{JSON.stringify(response.data, null, 2)}&lt;/pre&gt;
                &lt;/div&gt;
            )}
        &lt;/div&gt;
    );
}</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 31, 'API Route Handlers & Backend', 'Route Handlers (route.ts): Handling GET, POST, PUT, DELETE with NextRequest & NextResponse', $content31, 'nextjs route handlers, route.ts, nextrequest, nextresponse, api routes in app router, rest api in nextjs');

// -------------------------------------------------------------
// Chapter 32
// -------------------------------------------------------------
$content32 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">Route Handlers receive the extended <strong>NextRequest</strong> object, providing convenient helper methods for reading query parameters (<code>request.nextUrl.searchParams</code>), inspecting HTTP headers, and reading or setting HTTP-only <strong>Cookies</strong>.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">NextRequest Helper Utilities</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Helper Property</th>
                <th style="width: 35%;">Syntax Example</th>
                <th style="width: 40%;">Value Returned</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell">Query Search Params</td>
                <td><code>request.nextUrl.searchParams.get('limit')</code></td>
                <td>Extracts URL query strings automatically parsed as <code>URLSearchParams</code>.</td>
            </tr>
            <tr>
                <td class="code-cell">Request Cookies</td>
                <td><code>request.cookies.get('token')?.value</code></td>
                <td>Reads client cookies sent with the incoming request.</td>
            </tr>
            <tr>
                <td class="code-cell">Setting Response Cookies</td>
                <td><code>response.cookies.set('token', val, { httpOnly: true })</code></td>
                <td>Sets secure, encrypted HTTP cookies in client browser.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Dynamic Route Handlers with [id]</h3>
<pre class="bg-slate-900 text-slate-200 p-4 rounded-xl font-mono text-xs mb-4">
// app/api/items/[id]/route.ts
import { NextResponse, NextRequest } from 'next/server';

export async function GET(
  request: NextRequest, 
  { params }: { params: { id: string } }
) {
  const itemId = params.id;
  const filter = request.nextUrl.searchParams.get('filter');

  return NextResponse.json({ itemId, filter });
}
</pre>

<div class="w3-example-box">
    <div class="w3-example-title">Interactive Query & Cookie Inspector Simulator</div>
    <pre><code class="language-javascript">function RequestHelpersDemo() {
    const [query, setQuery] = React.useState('limit=25&sort=desc');

    const params = new URLSearchParams(query);
    const limit = params.get('limit') || 'None';
    const sort = params.get('sort') || 'asc';

    return (
        &lt;div className="p-6 max-w-lg mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
            &lt;h3 className="text-base font-bold text-slate-900 mb-2"&gt;NextRequest URL &amp; Query Parser&lt;/h3&gt;
            &lt;div className="mb-3"&gt;
                &lt;label className="block text-xs font-bold text-slate-600 mb-1"&gt;Query String (?query):&lt;/label&gt;
                &lt;input type="text" value={query} onChange={e =&gt; setQuery(e.target.value)} className="w-full px-3 py-1.5 border rounded-lg text-xs font-mono" /&gt;
            &lt;/div&gt;

            &lt;div className="p-3 bg-slate-900 text-slate-200 rounded-lg text-xs font-mono space-y-1"&gt;
                &lt;div className="text-emerald-400"&gt;// Evaluated request.nextUrl.searchParams:&lt;/div&gt;
                &lt;div&gt;limit: &lt;strong className="text-white"&gt;{limit}&lt;/strong&gt;&lt;/div&gt;
                &lt;div&gt;sort: &lt;strong className="text-white"&gt;{sort}&lt;/strong&gt;&lt;/div&gt;
            &lt;/div&gt;
        &lt;/div&gt;
    );
}</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 32, 'API Route Handlers & Backend', 'Query Parameters, Headers, Cookies & Dynamic Route Handlers', $content32, 'nextrequest query params, searchparams route handlers, dynamic route handler, cookies in route handlers, nextresponse cookies');

// -------------------------------------------------------------
// Chapter 33
// -------------------------------------------------------------
$content33 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">Modern applications require real-time streaming capabilities (such as AI chat token generation, financial tickers, and live server telemetry). Next.js Route Handlers support standard Web <strong>ReadableStream</strong> and <strong>Server-Sent Events (SSE)</strong> for high-frequency continuous data delivery.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">Streaming Response Headers & Architecture</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 30%;">Header Key</th>
                <th style="width: 35%;">Required Value</th>
                <th style="width: 35%;">Purpose</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell"><code>Content-Type</code></td>
                <td><code>text/event-stream</code></td>
                <td>Informs browser client to keep connection open for SSE events.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>Cache-Control</code></td>
                <td><code>no-cache, no-transform</code></td>
                <td>Prevents CDNs and proxies from buffering streaming token chunks.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>Connection</code></td>
                <td><code>keep-alive</code></td>
                <td>Maintains persistent HTTP connection stream.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Building an AI Stream Route Handler</h3>
<pre class="bg-slate-900 text-slate-200 p-4 rounded-xl font-mono text-xs mb-4">
// app/api/ai-chat/route.ts
export async function POST() {
  const encoder = new TextEncoder();
  const stream = new ReadableStream({
    async start(controller) {
      const words = ["Welcome", "to", "Next.js", "AI", "Streaming", "Architecture!"];
      for (const word of words) {
        controller.enqueue(encoder.encode(word + " "));
        await new Promise(r => setTimeout(r, 200));
      }
      controller.close();
    }
  });

  return new Response(stream, {
    headers: { 'Content-Type': 'text/plain; charset=utf-8' }
  });
}
</pre>

<div class="w3-example-box">
    <div class="w3-example-title">Interactive AI Token Streaming Simulator</div>
    <pre><code class="language-javascript">function AiTokenStreamDemo() {
    const [streamedText, setStreamedText] = React.useState('');
    const [streaming, setStreaming] = React.useState(false);

    const startStream = async () => {
        setStreamedText('');
        setStreaming(true);
        const tokens = ["Next.js", "15", "delivers", "unmatched", "speed", "via", "ReadableStream", "Server", "Tokens", "🚀"];

        for (const token of tokens) {
            await new Promise(r => setTimeout(r, 150));
            setStreamedText(prev => prev + (prev ? ' ' : '') + token);
        }
        setStreaming(false);
    };

    return (
        &lt;div className="p-6 max-w-lg mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
            &lt;div className="flex justify-between items-center mb-3"&gt;
                &lt;h3 className="text-base font-bold text-slate-900"&gt;AI Token Stream Simulator&lt;/h3&gt;
                &lt;button onClick={startStream} disabled={streaming} className="px-3 py-1 bg-emerald-600 disabled:bg-slate-300 text-white rounded text-xs font-bold shadow"&gt;
                    {streaming ? 'Streaming...' : 'Stream Tokens &raquo;'}
                &lt;/button&gt;
            &lt;/div&gt;

            &lt;div className="p-4 bg-slate-900 text-emerald-400 font-mono text-xs rounded-lg min-h-[60px] flex items-center"&gt;
                {streamedText || &lt;span className="text-slate-600 italic"&gt;Click "Stream Tokens" to simulate server stream...&lt;/span&gt;}
                {streaming && &lt;span className="animate-pulse ml-1 inline-block w-2 h-4 bg-emerald-400"&gt;&lt;/span&gt;}
            &lt;/div&gt;
        &lt;/div&gt;
    );
}</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 33, 'API Route Handlers & Backend', 'Streaming Responses, WebSockets & Server-Sent Events (SSE) in Route Handlers', $content33, 'streaming responses nextjs, sse nextjs, server sent events route handlers, readablestream nextjs, ai token streaming nextjs');

// -------------------------------------------------------------
// Chapter 34
// -------------------------------------------------------------
$content34 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">Production Route Handlers must be protected against abuse and integrated cleanly with third-party webhooks (e.g. Stripe, GitHub, Resend). This requires configuring <strong>CORS (Cross-Origin Resource Sharing)</strong> headers, verifying <strong>HMAC Webhook Signatures</strong>, and applying <strong>Rate Limiting</strong>.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">Security & Webhook Verification Checklist</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Protection Layer</th>
                <th style="width: 35%;">Implementation Pattern</th>
                <th style="width: 40%;">Primary Threat Mitigated</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell">CORS Configuration</td>
                <td><code>'Access-Control-Allow-Origin': 'https://app...'</code></td>
                <td>Prevents unauthorized origins from invoking internal APIs from client browsers.</td>
            </tr>
            <tr>
                <td class="code-cell">Webhook HMAC Signature</td>
                <td><code>stripe.webhooks.constructEvent(rawBody, signature, secret)</code></td>
                <td>Validates that incoming webhook notifications genuinely originated from Stripe/GitHub (prevents spoofing).</td>
            </tr>
            <tr>
                <td class="code-cell">Sliding Window Rate Limiting</td>
                <td>Upstash Redis / Token Bucket algorithms</td>
                <td>Mitigates Distributed Denial of Service (DDoS) and brute-force attacks.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Raw Body Webhook Verification</h3>
<p class="text-slate-600 mb-4">When verifying webhook cryptographic signatures, you must read the raw body text (<code>await request.text()</code>) instead of JSON to calculate an exact HMAC hash:</p>

<div class="w3-example-box">
    <div class="w3-example-title">Interactive Webhook HMAC Signature Simulator</div>
    <pre><code class="language-javascript">function WebhookValidatorDemo() {
    const [status, setStatus] = React.useState(null);

    const testWebhook = (valid) => {
        if (valid) {
            setStatus({ ok: true, text: "✓ HMAC Signature Verified! Processed Stripe invoice.payment_succeeded." });
        } else {
            setStatus({ ok: false, text: "🚨 400 Bad Request: Invalid signature header (Rejected potential spoofed event)." });
        }
    };

    return (
        &lt;div className="p-6 max-w-lg mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
            &lt;h3 className="text-base font-bold text-slate-900 mb-2"&gt;Webhook Signature Verification&lt;/h3&gt;
            &lt;p className="text-xs text-slate-500 mb-4"&gt;Simulate incoming webhook dispatch with cryptographic signature verification:&lt;/p&gt;

            &lt;div className="flex gap-2 mb-4"&gt;
                &lt;button onClick={() =&gt; testWebhook(true)} className="px-3 py-1.5 bg-emerald-600 text-white rounded text-xs font-bold shadow"&gt;
                    Send Valid Webhook Payload
                &lt;/button&gt;
                &lt;button onClick={() =&gt; testWebhook(false)} className="px-3 py-1.5 bg-red-600 text-white rounded text-xs font-bold shadow"&gt;
                    Send Tampered Payload
                &lt;/button&gt;
            &lt;/div&gt;

            {status && (
                &lt;div className={`p-3 rounded-lg border text-xs font-bold ${status.ok ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-red-50 border-red-200 text-red-800'}`}&gt;
                    {status.text}
                &lt;/div&gt;
            )}
        &lt;/div&gt;
    );
}</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 34, 'API Route Handlers & Backend', 'CORS, Webhooks Integration & Rate Limiting in Route Handlers', $content34, 'cors nextjs, webhooks nextjs, stripe webhook nextjs, rate limiting route handlers, hmac signature nextjs');

// -------------------------------------------------------------
// Chapter 35
// -------------------------------------------------------------
$content35 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700"><strong>Middleware</strong> in Next.js (defined in <code>middleware.ts</code> at the project root) runs <em>before</em> any request is completed. Executing on the lightweight <strong>Edge Runtime</strong>, middleware can intercept incoming requests, rewrite URLs, redirect unauthenticated users, and append custom security headers globally.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">Next.js Middleware Architecture</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Middleware Capability</th>
                <th style="width: 35%;">Syntax Example</th>
                <th style="width: 40%;">Best Practice Use Case</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell">Route Matching (Matcher)</td>
                <td><code>matcher: ['/dashboard/:path*', '/settings']</code></td>
                <td>Limits middleware execution strictly to matching routes (avoids running on static assets).</td>
            </tr>
            <tr>
                <td class="code-cell">Server Redirect</td>
                <td><code>NextResponse.redirect(new URL('/login', req.url))</code></td>
                <td>Redirecting guests away from protected routes before rendering begins.</td>
            </tr>
            <tr>
                <td class="code-cell">Internal URL Rewrite</td>
                <td><code>NextResponse.rewrite(new URL('/proxy', req.url))</code></td>
                <td>A/B testing and multi-tenant subdomains without changing browser URL.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Master Middleware Template</h3>
<pre class="bg-slate-900 text-slate-200 p-4 rounded-xl font-mono text-xs mb-4">
// middleware.ts
import { NextResponse, NextRequest } from 'next/server';

export function middleware(request: NextRequest) {
  const token = request.cookies.get('session_token')?.value;

  // Protect /dashboard routes from unauthenticated users
  if (!token && request.nextUrl.pathname.startsWith('/dashboard')) {
    return NextResponse.redirect(new URL('/login', request.url));
  }

  return NextResponse.next();
}

export const config = {
  matcher: ['/dashboard/:path*', '/account/:path*']
};
</pre>

<div class="w3-example-box">
    <div class="w3-example-title">Interactive Middleware Pipeline Simulator</div>
    <pre><code class="language-javascript">function MiddlewarePipelineDemo() {
    const [hasToken, setHasToken] = React.useState(false);
    const [targetPath, setTargetPath] = React.useState('/dashboard');

    const outcome = targetPath.startsWith('/dashboard') && !hasToken
        ? { action: "307 Redirect to /login", color: "bg-amber-100 text-amber-800", note: "Blocked by Edge Middleware: Session token missing!" }
        : { action: "200 Passed (NextResponse.next())", color: "bg-emerald-100 text-emerald-800", note: "Access granted! Forwarded to Server Component renderer." };

    return (
        &lt;div className="p-6 max-w-lg mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
            &lt;h3 className="text-base font-bold text-slate-900 mb-2"&gt;Edge Middleware Guard Simulator&lt;/h3&gt;
            
            &lt;div className="flex gap-4 mb-4 text-xs font-semibold"&gt;
                &lt;label className="flex items-center gap-2 cursor-pointer"&gt;
                    &lt;input type="checkbox" checked={hasToken} onChange={e =&gt; setHasToken(e.target.checked)} /&gt;
                    &lt;span&gt;Session Cookie Present&lt;/span&gt;
                &lt;/label&gt;
                &lt;select value={targetPath} onChange={e =&gt; setTargetPath(e.target.value)} className="border rounded px-2 py-1 bg-white"&gt;
                    &lt;option value="/dashboard"&gt;/dashboard (Protected)&lt;/option&gt;
                    &lt;option value="/blog"&gt;/blog (Public)&lt;/option&gt;
                &lt;/select&gt;
            &lt;/div&gt;

            &lt;div className="p-4 bg-slate-50 border rounded-lg text-xs"&gt;
                &lt;div className="flex justify-between items-center mb-1"&gt;
                    &lt;strong className="text-slate-900"&gt;Target: {targetPath}&lt;/strong&gt;
                    &lt;span className={`px-2 py-0.5 rounded text-[10px] font-bold ${outcome.color}`}&gt;{outcome.action}&lt;/span&gt;
                &lt;/div&gt;
                &lt;p className="text-slate-600 text-[11px]"&gt;{outcome.note}&lt;/p&gt;
            &lt;/div&gt;
        &lt;/div&gt;
    );
}</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 35, 'Middleware & Security', 'Next.js Middleware (middleware.ts): Request Interception, Matching Paths & Edge Runtime', $content35, 'nextjs middleware, middleware.ts, edge runtime nextjs, nextresponse redirect, route protection middleware');
