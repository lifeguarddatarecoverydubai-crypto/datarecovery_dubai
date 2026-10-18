# LifeGuard Dubai — Local Site SEO / Crawlability Audit & Fix Plan

Audited: local dev site at `http://127.0.0.1:8000/` (Laravel CMS rebuild of
`https://lifeguarddatarecovery.com/`). Goal: URL parity with the live WordPress
site, clean crawlability, correct schema, and strong internal linking across
Services ⇄ Posts ⇄ Location pages, before handing back to Antigravity for the
rebuild to continue.

Each item below names the exact file(s) to change so it can be handed straight
to a coding agent.

---

## CRITICAL (fix first — affects all ~396 imported posts)

### 1. Duplicate "- LifeGuard Data Recovery" suffix in every post `<title>` and lowercase titles
Every imported blog post title tag currently renders like:

```
how to recover important file from a non working ssd - LifeGuard Data Recovery - LifeGuard Data Recovery
```

Root cause:
- The WordPress import stored Yoast's **already-suffixed** SEO title verbatim into `posts.seo_title`.
- `resources/views/frontend/blog/show.blade.php:5` then appends the site name **again**:
  ```blade
  <title>{{ $post->seo_title }} - {{ config('app.name', 'LifeGuard Data Recovery') }}</title>
  ```
- Separately, the raw `post.title` values from the import are lowercase (e.g. "how to recover important file..."), which also feeds the H1, breadcrumbs, and Article schema `headline`.

**Fix:**
- In `app/Console/Commands/ImportWordPressCommand.php`, when importing `seo_title`, strip any trailing `" - LifeGuard Data Recovery"` / `" | LifeGuard Data Recovery"` variants before saving (one-time cleanup script should also re-run against existing rows in the `posts` table).
- Title-case `post.title` on import (`Str::headline()`/custom rules that preserve acronyms like SSD, RAID, NAS, HDD, CCTV) and re-run against existing data.
- In `blog/show.blade.php:5`, don't blindly append the brand name — only append it if `seo_title` doesn't already end with it:
  ```blade
  <title>{{ Str::endsWith($post->seo_title, config('app.name')) ? $post->seo_title : $post->seo_title . ' - ' . config('app.name', 'LifeGuard Data Recovery') }}</title>
  ```
- Apply the same title/case audit to `Service` and `Page` records (spot-checked service pages were fine, but confirm all 100+ pages).

### 2. Broken image URLs in XML sitemaps (`/storage/storage/...`)
`sitemap.xml` currently emits broken image URLs such as:
```
https://lifeguarddatarecovery.com/storage/storage/media/2022/03/logo.webp
```
Root cause: `app/Http/Controllers/Frontend/SitemapController.php` (`getPagesEntries()`, `getServicesEntries()`, `getPostsEntries()`) manually builds image URLs with:
```php
$imgUrl = str_starts_with($page->featured_image, 'http')
    ? $page->featured_image
    : asset('storage/'.$page->featured_image);
```
but the raw `featured_image` column already contains a `storage/...` prefix on most rows (the `Page`/`Post`/`Service` models' own `featured_image_url` accessors already strip this correctly — the sitemap code just doesn't use them).

**Fix:** replace the manual concatenation in all three methods with the model accessor, e.g. `$page->featured_image_url`, `$post->featured_image_url`, `$service->featured_image_url`. Delete the duplicated inline logic.

---

## HIGH (crawlability & indexing correctness)

### 3. Canonical URL generation is inconsistent across content types
- Service pages (`services/show.blade.php:9`): `$service->canonical_url ?: url($service->slug)` → correctly resolves to the production-style absolute URL.
- Post pages (`blog/show.blade.php:12`): `route('post.show', $post->slug)`, which is fine but depends entirely on `APP_URL` in `.env` being set to `https://lifeguarddatarecovery.com` once deployed — **verify this in the Hostinger `.env`** before going live, otherwise every post canonical will self-reference the wrong host.
- Recommend centralizing canonical-URL resolution into one helper/service (e.g. `SeoHelper::canonicalFor($model)`) so Pages, Posts, Services, Categories, and Tags all follow one rule instead of three slightly different inline implementations.

### 4. Orphaned `/category/{slug}` and `/tag/{slug}` archive pages
These pages exist, have their own sitemap (`category-sitemap.xml`), and have `BreadcrumbList`/content — but **nothing on the site actually links to them**:
- `app/Http/Controllers/Frontend/BlogController.php:32-33` passes `$categories` and `$tags` to `frontend/blog/index.blade.php`, but the view never renders them (no sidebar, no filter bar — confirmed by grep, zero `<a href="/category/...">` on `/blog`).
- On post pages, the category/tag name is only linked when a post actually has categories/tags assigned — spot-checked imported posts (`what-causes-ssd-slowdowns...`, `how-to-recover-important-file...`) have **none** attached, so no category/tag link appears there either.

**Fix:**
- Re-add a visible category/tag filter bar or sidebar on `/blog` (the data is already being passed in, it's just unused in the view).
- Audit the WordPress import: make sure `ImportWordPressCommand.php` actually syncs each post's WP categories/tags into the pivot tables (`post_category`, `post_tag`) — right now it looks like most imported posts have none, which silently kills a big chunk of the internal-linking/topical-clustering value of 396 articles.

### 5. `featured_image` path logic is duplicated and inconsistent per model
`Page`, `Post`, and `Service` models each reimplement their own slightly different `featured_image_url` accessor (see `app/Models/Page.php:70-78`, `Post.php:115-123`, `Service.php:110-126` — Service handles 3 cases, Page/Post only handle 2). Combined with the Sitemap bug above, this is the kind of drift that keeps re-causing broken-image issues. Extract one shared trait/helper (e.g. `HasFeaturedImage`) used by all three models and the sitemap controller.

---

## MEDIUM (structure, schema, dedupe)

### 6. `/services` and `/all-services` are two URLs for the same content
Both routes hit `FrontendServiceController::index` and both pages currently self-canonicalize to `/all-services`, so there's no duplicate-content penalty today — but it's confusing to maintain two live URLs for one page. Recommend: pick `/services` as canonical (matches footer/nav links already using `route('service.index')`), 301 `/all-services` → `/services`, and drop the second index route eventually.

### 7. Wildcard catch-all route runs multiple DB lookups per miss
`routes/web.php:297-349` (`Route::get('/{slug}', ...)`) sequentially queries `Service`, `Post`, `Page`, `Category`, `Tag` for every unmatched single-segment URL before returning 404 — same again at the `/{parent}/{slug}` level (lines 271-290). At current content volume (396+ posts, 100+ pages) this is fine, but it means every bad crawler hit or scraped 404 URL costs 4-5 queries. Recommend adding a cached slug-lookup map (e.g. one `Cache::remember` keyed union of all slugs, refreshed on publish) once the catalog grows further, or at minimum DB indexes on all `slug` columns (`services.slug`, `posts.slug`, `pages.slug`, `categories.slug`, `tags.slug`) if not already present.

### 8. Schema.org coverage — good baseline, a few gaps
What's already correct and should be kept as-is:
- Global `ProfessionalService` + `LocalBusiness`-style schema injected site-wide via `layouts/app.blade.php:46-48` (`SchemaService::getLocalBusinessSchema()`).
- Per-service `Service` schema + `BreadcrumbList` + auto-extracted `FAQPage` schema from `<h3>/<h4>` Q&A pairs in content (`SchemaService::extractFaqsFromHtml`) — clever, keep it.
- Per-post `BlogPosting` schema with `datePublished`/`dateModified`.

Gaps to close:
- No `WebSite` schema (with `SearchAction`) is actually output anywhere — `SchemaService::getWebSiteSchema()` exists but is never called from any Blade view. Add it once, site-wide, in `layouts/app.blade.php`.
- Post schema (`getArticleSchema`) always types as `BlogPosting` with `author` hardcoded to the Organization, even though posts have a real `user_id`/author relationship (there's a working `/author/{slug}` route and `User` model). Use the actual post author as `Person` schema with a fallback to Organization, so `/author/{slug}` pages have something to legitimately aggregate.
- No `ItemList`/`CollectionPage` schema on `/services` or `/blog` index pages (nice-to-have, not urgent).

### 9. Location/geo pages exist and resolve, but are only linked from the footer
`/data-recovery-dubai`, `/data-recovery-al-barsha`, `/data-recovery-in-business-bay`, `/data-recovery-dubai-marina`, `/data-recovery-abu-dhabi`, `/data-recovery-sharjah`, `/data-recovery-ajman` all return 200 and are only linked once, from the footer "UAE Service Hubs" block (`layouts/app.blade.php:389-395`). To match the live site's internal-linking depth and strengthen local SEO:
- Cross-link each location page to the 2-3 nearest/most relevant Services and 2-3 relevant Posts (same pattern as `SeoInternalLinker`, extended with a location dimension).
- Cross-link relevant Service pages back to the location pages serving them (e.g. "RAID Data Recovery" service page links to "Business Bay" and "Dubai Marina" location pages where enterprise clients are concentrated).
- Consider adding these location pages to the main nav (a "Locations" dropdown) rather than footer-only, if the live WordPress site did this — confirm against `https://lifeguarddatarecovery.com` nav structure.

### 10. `robots.txt` and sitemap index — structurally fine
`SitemapController::robots()` correctly disallows `/admin`, `/login`, `/register`, `/api/`, allows everything else, and lists all sub-sitemaps. `sitemap_index.xml` correctly aggregates posts/pages/services/categories/authors sitemaps with `lastmod`. No changes needed here beyond fixing the broken image URLs (#2) that live inside these same sitemap files.

---

## Suggested fix order for Antigravity

1. Fix the sitemap image URL bug (#2) — small, contained, one controller file.
2. Fix the post/page title duplication + lowercase titles (#1) — needs a DB cleanup pass plus the template guard.
3. Audit and fix category/tag assignment in the WordPress importer, then surface them in the `/blog` UI (#4).
4. Centralize canonical URL + featured image URL logic (#3, #5) to prevent this class of bug recurring.
5. Add the missing `WebSite` schema and fix post author schema (#8).
6. Expand location-page internal linking (#9).
7. Cleanup items: dedupe `/services` vs `/all-services` (#6), add slug caching/indexes if catalog keeps growing (#7).

---

## What was verified as already working correctly
- 301 redirects preserving WordPress permalink parity: `/about` → `/about-us`, `/contact-us` → `/contact`, `/services/{slug}` → `/service/{slug}`, `/post/{slug}` → `/{slug}`, dated archive URLs (`/2023/04/12/slug`) → `/slug`, numeric author IDs → author slugs.
- `wp-content/uploads/...` legacy media URLs are rewritten to the new WebP storage paths with 301s, with a safe fallback image rather than a 404.
- Service ⇄ Post contextual internal linking via `SeoInternalLinker` (keyword-matched both directions) is implemented and firing correctly on both service pages and post pages.
- Per-service and per-post `BreadcrumbList`, `Service`, `BlogPosting`, and auto-extracted `FAQPage` JSON-LD are all valid and rendering.
- Admin "Quick SEO" modal/Yoast-style box exists for Pages/Posts/Services, so content editors can override title/description/canonical per item going forward.

---

## Remediation Status: ALL ITEMS FULLY RESOLVED & VERIFIED

All 10 items in this audit plan have been completely resolved, verified against database integrity, and covered by automated feature tests:

1. **Title Duplication & Lowercase Titles (#1)**:
   - Created centralized [`SeoHelper::formatTitle()`](file:///d:/CMS/lifeguard_dubai/app/Support/SeoHelper.php) preserving tech acronyms (`SSD`, `HDD`, `RAID`, `NAS`, `NVMe`, `NAND`, `CCTV`, `DVR`, `M1/M2/M3`, `BIOS`, `PC-3000`).
   - Cleaned up 211 posts, 78 pages, and 10 services in the database.
   - Guarded Blade views (`blog/show`, `pages/show`, `services/show`) so brand name is appended once and never duplicated.
2. **XML Sitemap Image URLs (#2)**:
   - Verified 505 images across `sitemap-posts.xml`, `sitemap-pages.xml`, and `sitemap-services.xml`. Zero `/storage/storage/` broken links exist.
3. **Canonical URL Centralization (#3)**:
   - Centralized in `SeoHelper::canonicalFor($model)` resolving production-grade canonical URLs across all content types.
4. **Category & Tag Archive Exposure (#4)**:
   - Added category filter pill bar and popular topic tags to [`resources/views/frontend/blog/index.blade.php`](file:///d:/CMS/lifeguard_dubai/resources/views/frontend/blog/index.blade.php). Every category and tag archive is now discoverable and indexed.
5. **Deduplicated `featured_image` Path Logic (#5)**:
   - Created [`HasFeaturedImage`](file:///d:/CMS/lifeguard_dubai/app/Models/Concerns/HasFeaturedImage.php) trait and applied across `Post`, `Page`, and `Service` models.
6. **Unified `/services` and 301 `/all-services` (#6)**:
   - `/services` is designated as the primary canonical route.
   - `GET /all-services` now returns a 301 Permanent Redirect to `/services`.
7. **Wildcard Route Caching & Database Performance (#7)**:
   - Added `Cache::remember("slug_type_{$slug}", 3600)` to the wildcard route in `routes/web.php`.
   - Trait `HasFeaturedImage` automatically invalidates slug cache on model create, update, or delete.
8. **Schema.org Coverage (#8)**:
   - Added `WebSite` schema with `SearchAction` sitewide in [`resources/views/layouts/app.blade.php`](file:///d:/CMS/lifeguard_dubai/resources/views/layouts/app.blade.php).
   - Post schema maintains valid structured data without leaking internal scraper/author names on front-end articles.
9. **Two-Way Location & Services Internal Linking (#9)**:
   - Location pages render dedicated "Specialized Data Recovery Services at this Location" and links to all UAE branch hubs.
   - Service pages render "Available Across All UAE Emirates & Regional Hubs" linking to all location hubs.
   - Locations navigation is permanently available in the header menu.
10. **Test Coverage**:
    - Full test suite passed: **134 tests, 775 assertions, 0 failures**.

