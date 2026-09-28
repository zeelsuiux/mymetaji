<?php
/**
 * My Metaji - SEO helpers
 *
 * Every page funnels through here so that canonical, Open Graph, Twitter Card
 * and JSON-LD are emitted consistently from one place.
 *
 * A page sets these before including includes/header.php:
 *   $page_title   string  <title>
 *   $page_desc    string  meta description
 *   $page_canonical string absolute URL, defaults to the current URL
 *   $page_og_type string  'website' | 'article'
 *   $page_schema  array   one or more JSON-LD nodes to embed
 *   $page_noindex bool    set true to keep a page out of the index
 */

if (!defined('SITE_URL')) {
    require_once __DIR__ . '/../config.php';
}

function seo_url(string $path = ''): string
{
    $base = rtrim(SITE_URL, '/');
    $path = ltrim($path, '/');
    return $path === '' ? $base . '/' : $base . '/' . $path;
}

function seo_current_path(): string
{
    $self = str_replace('\\', '/', $_SERVER['PHP_SELF'] ?? '/');
    $dir  = rtrim(str_replace('\\', '/', dirname($self)), '/');
    $file = basename($self);
    $path = ($dir === '/' || $dir === '.') ? '/' : $dir . '/' . $file;
    return ltrim($path, '/');
}

function seo_canonical(): string
{
    global $page_canonical;
    if (!empty($page_canonical)) {
        return $page_canonical;
    }
    return seo_url(seo_current_path());
}

function seo_description(): string
{
    global $page_desc;
    $desc = trim((string)($page_desc ?? ''));
    if ($desc === '') {
        $desc = BRAND_DESCRIPTION . ' - ' . BRAND_TAGLINE;
    }
    // Meta descriptions get truncated in SERPs around 155-160 chars.
    if (mb_strlen($desc) > 158) {
        $desc = rtrim(mb_substr($desc, 0, 155)) . '...';
    }
    return $desc;
}

function seo_title(): string
{
    global $page_title;
    $title = trim((string)($page_title ?? ''));
    if ($title === '') {
        $title = BRAND_NAME . ' - ' . BRAND_DESCRIPTION;
    }
    // Keep the <title> inside the ~60 char window Google renders fully.
    if (mb_strlen($title) > 62) {
        $title = rtrim(mb_substr($title, 0, 59)) . '...';
    }
    return $title;
}

function seo_esc(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Renders every <head> tag that is not layout-specific.
 */
function seo_head(): void
{
    global $page_og_type, $page_noindex;

    $canonical = seo_canonical();
    $title     = seo_title();
    $desc      = seo_description();
    $ogType    = $page_og_type ?? 'website';
    $ogImage   = seo_url('assets/images/mymetaji-logo.png');
    $siteName  = BRAND_NAME;
    ?>
    <title><?= seo_esc($title) ?></title>
    <meta name="description" content="<?= seo_esc($desc) ?>">
    <link rel="canonical" href="<?= seo_esc($canonical) ?>">
    <?php if (!empty($page_noindex)): ?>
        <meta name="robots" content="noindex, follow">
    <?php else: ?>
        <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1">
    <?php endif; ?>
    <meta property="og:site_name" content="<?= seo_esc($siteName) ?>">
    <meta property="og:type" content="<?= seo_esc($ogType) ?>">
    <meta property="og:locale" content="en_IN">
    <meta property="og:title" content="<?= seo_esc($title) ?>">
    <meta property="og:description" content="<?= seo_esc($desc) ?>">
    <meta property="og:url" content="<?= seo_esc($canonical) ?>">
    <meta property="og:image" content="<?= seo_esc($ogImage) ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= seo_esc($title) ?>">
    <meta name="twitter:description" content="<?= seo_esc($desc) ?>">
    <meta name="twitter:image" content="<?= seo_esc($ogImage) ?>">
    <?php seo_schema(); ?>
    <?php
}

function seo_json_ld(array $data): string
{
    $flags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
    return (string)json_encode($data, $flags);
}

/**
 * Baseline schema for pages that do not declare their own $page_schema.
 * Every indexable page should at minimum identify the brand entity, otherwise
 * the Organization/SoftwareApplication graph is only reachable from the home
 * page and the city pages.
 */
function seo_default_schema(): array
{
    $title = seo_title();
    // "Accounting Software - My Metaji" -> "Accounting Software"
    $label = trim(preg_replace('~\s*[|\x{2013}\x{2014}-]\s*' . preg_quote(BRAND_NAME, '~') . '\s*$~u', '', $title));
    if ($label === '' || $label === $title) {
        $label = $title;
    }

    return [
        seo_organization_node(),
        seo_web_site_node(),
        seo_breadcrumb_node([
            ['name' => 'Home', 'url' => 'index.php'],
            ['name' => $label, 'url' => seo_current_path()],
        ]),
    ];
}

/**
 * Emits every node queued in $page_schema, wrapped in a single @graph so the
 * nodes can reference each other by @id instead of duplicating data.
 */
function seo_schema(): void
{
    global $page_schema;

    $nodes = array_values(array_filter((array)($page_schema ?? [])));
    if (!$nodes) {
        $nodes = seo_default_schema();
    }

    $graph = [];
    $ids   = [];
    foreach ($nodes as $node) {
        if (isset($node['@id'])) {
            $ids[] = $node['@id'];
        }
    }

    $index = [];
    foreach ($nodes as $node) {
        if (!isset($node['@id'])) {
            continue;
        }
        $index[] = ['@id' => $node['@id'], '@type' => $node['@type'] ?? 'Thing'];
    }
    if ($index) {
        array_unshift($nodes, [
            '@context' => 'https://schema.org',
            '@graph'   => $nodes,
        ]);
        $payload = $nodes[0];
    } else {
        $payload = ['@context' => 'https://schema.org', '@graph' => $nodes];
    }

    echo '<script type="application/ld+json">' . seo_json_ld($payload) . '</script>' . "\n";
}

/**
 * Brand node. Reused across the site so the @id is always identical.
 */
function seo_organization_node(): array
{
    global $social, $contact;

    $socials = array_values(array_filter((array)$social, function ($url) {
        return $url && $url !== '#';
    }));

    $node = [
        '@type'       => 'Organization',
        '@id'         => SITE_URL . '/#organization',
        'name'        => BRAND_NAME,
        'url'         => SITE_URL . '/',
        'description' => BRAND_DESCRIPTION . ' - ' . BRAND_TAGLINE,
        'logo'        => [
            '@type'  => 'ImageObject',
            '@id'    => SITE_URL . '/#logo',
            'url'    => seo_url('assets/images/m-logo.png'),
            'width'  => 512,
            'height' => 512,
        ],
        'image'       => ['@id' => SITE_URL . '/#logo'],
        'email'       => $contact['email'],
        'telephone'   => $contact['phone1'],
        'foundingDate' => '2024',
        'address'     => [
            '@type'           => 'PostalAddress',
            'streetAddress'   => $contact['address'],
            'addressLocality' => 'Surat',
            'addressRegion'   => 'Gujarat',
            'addressCountry'  => 'IN',
        ],
    ];

    if ($socials) {
        $node['sameAs'] = $socials;
    }

    return $node;
}

/**
 * The software product itself. Used on the home page and on every service page.
 */
function seo_software_node(): array
{
    global $plans;

    $offers = [];
    foreach ((array)$plans as $plan) {
        foreach (['monthly', 'yearly'] as $cycle) {
            $price = (float)str_replace(['₹', ','], '', $plan[$cycle]);
            $offers[] = [
                '@type'         => 'Offer',
                'name'          => $plan['name'] . ' plan (' . $cycle . ')',
                'price'         => $price,
                'priceCurrency' => 'INR',
                'description'   => $plan['desc'],
                'availability'  => 'https://schema.org/InStock',
                'url'           => seo_url('pricing.php'),
            ];
        }
    }

    return [
        '@type'       => 'SoftwareApplication',
        '@id'         => SITE_URL . '/#software',
        'name'        => BRAND_NAME . ' - ' . BRAND_DESCRIPTION,
        'url'         => SITE_URL . '/',
        'applicationCategory' => 'FinanceApplication',
        'applicationSubCategory' => 'Accounting',
        'operatingSystem' => 'Android, iOS, Windows, Mac',
        'description' => BRAND_DESCRIPTION . '. ' . BRAND_TAGLINE . '.',
        'publisher'   => ['@id' => SITE_URL . '/#organization'],
        'offers'      => $offers,
    ];
}

/**
 * The plan ladder in one sentence, built from $plans:
 *   "Starter at ₹299 a month or ₹2,999 a year, Business at ..."
 *
 * Every city and industry page quotes the pricing this way, so changing a price
 * in config.php updates the FAQ answers on all 160-odd pages at once.
 */
function seo_plans_summary(): string
{
    global $plans;

    $bits = [];
    foreach ((array)$plans as $plan) {
        $bits[] = $plan['name'] . ' at ' . plan_price_string($plan, 'monthly') . ' a month or ' . plan_price_string($plan, 'yearly') . ' a year';
    }
    if (!$bits) {
        return '';
    }
    if (count($bits) === 1) {
        return $bits[0];
    }
    $last = array_pop($bits);
    return implode(', ', $bits) . ' and ' . $last;
}

/**
 * The name of the entry-level plan, for copy that needs to point at the cheapest
 * way to start ("start on the Starter plan").
 */
function seo_entry_plan_name(): string
{
    global $plans;
    return (string)($plans[0]['name'] ?? BRAND_NAME);
}

/**
 * The site itself. Used on the home page so the brand entity is unambiguous.
 * No SearchAction is declared because the site has no search results page.
 */
function seo_web_site_node(): array
{
    return [
        '@type'  => 'WebSite',
        '@id'    => SITE_URL . '/#website',
        'url'    => SITE_URL . '/',
        'name'   => BRAND_NAME,
        'inLanguage' => 'en-IN',
        'publisher'  => ['@id' => SITE_URL . '/#organization'],
    ];
}

/**
 * Service node for the nationwide service page (accounting-software.php and
 * friends), as opposed to a specific city.
 */
function seo_national_service_node(array $service): array
{
    return [
        '@type'       => 'Service',
        '@id'         => seo_url($service['page']) . '#service',
        'name'        => $service['name'],
        'serviceType' => $service['kw'],
        'url'         => seo_url($service['page']),
        'description' => $service['desc'],
        'provider'    => ['@id' => SITE_URL . '/#organization'],
        'areaServed'  => [
            '@type'   => 'Country',
            'name'    => 'India',
        ],
        'hasOfferCatalog' => [
            '@type'           => 'OfferCatalog',
            'name'            => $service['name'] . ' features',
            'itemListElement' => array_map(function ($f) {
                return [
                    '@type'    => 'Offer',
                    'itemOffered' => [
                        '@type'       => 'Service',
                        'name'        => $f['title'],
                        'description' => $f['desc'],
                    ],
                ];
            }, $service['features']),
        ],
    ];
}

function seo_breadcrumb_node(array $crumbs): array
{
    $items = [];
    foreach ($crumbs as $i => $crumb) {
        $item = [
            '@type'    => 'ListItem',
            'position' => $i + 1,
            'name'     => $crumb['name'],
        ];
        if (!empty($crumb['url'])) {
            $item['item'] = seo_url($crumb['url']);
        }
        $items[] = $item;
    }

    return [
        '@type'           => 'BreadcrumbList',
        '@id'             => seo_canonical() . '#breadcrumb',
        'itemListElement' => $items,
    ];
}

function seo_faq_node(array $faqs): array
{
    if (!$faqs) {
        return [];
    }

    return [
        '@type'      => 'FAQPage',
        '@id'        => seo_canonical() . '#faq',
        'mainEntity' => array_map(function ($faq) {
            return [
                '@type'          => 'Question',
                'name'           => $faq['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => $faq['answer'],
                ],
            ];
        }, $faqs),
    ];
}

// ============ CITIES ============

/**
 * The public URL of a city page, relative to the site root:
 *   seo_city_path(['slug' => 'inventory-software'], ['slug' => 'vadodara'])
 *   -> 'inventory-software-vadodara.php'
 *
 * That filename is a real file, written by includes/city-pages.php from the same
 * $services and $cities that this function reads. So a link built here can
 * never point at a page that was not generated.
 */
function seo_city_path(array $service, array $city): string
{
    return $service['slug'] . '-' . $city['slug'] . '.php';
}

function seo_city_url(array $service, array $city): string
{
    return seo_url(seo_city_path($service, $city));
}

function seo_find_city(string $slug): ?array
{
    global $cities;
    foreach ($cities as $city) {
        if ($city['slug'] === $slug) {
            return $city;
        }
    }
    return null;
}

function seo_find_service(string $slug): ?array
{
    global $services;
    foreach ($services as $service) {
        if ($service['slug'] === $slug) {
            return $service;
        }
    }
    return null;
}

/**
 * Every city page that exists right now, as [service, city] pairs.
 * Single source of truth for the sitemap, the nav and the footer, so none of
 * them can list a city that has been renamed or removed from config.php.
 */
function seo_all_city_pages(): array
{
    global $services, $cities;
    $pairs = [];
    foreach ($services as $service) {
        foreach ($cities as $city) {
            $pairs[] = [$service, $city];
        }
    }
    return $pairs;
}

function seo_city_meta_desc(array $service, array $city): string
{
    return $service['name'] . ' in ' . $city['name'] . ' - ' . $service['desc'];
}

/**
 * Service node for a city page.
 *
 * This uses Service + areaServed rather than LocalBusiness on purpose. The
 * company has one real office (Surat), so claiming a physical presence in
 * every listed city would be false and would fall foul of Google's doorway
 * policy. areaServed makes no such claim - it only states the software is
 * available to businesses in that city, which is accurate.
 */
function seo_city_service_node(array $service, array $city): array
{
    return [
        '@type'       => 'Service',
        '@id'         => seo_city_url($service, $city) . '#service',
        'name'        => $service['name'] . ' in ' . $city['name'],
        'serviceType' => $service['kw'],
        'url'         => seo_city_url($service, $city),
        'description' => seo_city_meta_desc($service, $city),
        'provider'    => ['@id' => SITE_URL . '/#organization'],
        'areaServed'  => [
            [
                '@type'   => 'City',
                'name'    => $city['name'],
                'containedInPlace' => [
                    '@type' => 'State',
                    'name'  => $city['state'],
                ],
            ],
        ],
        'hasOfferCatalog' => [
            '@type'           => 'OfferCatalog',
            'name'            => $service['name'] . ' features',
            'itemListElement' => array_map(function ($f) {
                return [
                    '@type'    => 'Offer',
                    'itemOffered' => [
                        '@type'       => 'Service',
                        'name'        => $f['title'],
                        'description' => $f['desc'],
                    ],
                ];
            }, $service['features']),
        ],
    ];
}

/**
 * FAQ block for a city page. Every question embeds the service keyword and the
 * city name, which is what earns these pages the "accounting software in
 * Surat" style long-tail impressions.
 */
function seo_city_faqs(array $service, array $city): array
{
    $kw    = $service['kw'];
    $cn    = $city['name'];
    $st    = $city['state'];
    $feats = array_column($service['features'], 'title');

    return [
        [
            'question' => "What is the best $kw for a business in $cn?",
            'answer'   => BRAND_NAME . " is a practical fit for businesses in $cn because it handles {$feats[0]} without needing an accountant to set it up. It runs on Android, iOS, Windows and Mac, and the " . seo_entry_plan_name() . " plan is enough to run a single counter. You get the same GST handling whether you trade inside $st or across state lines.",
        ],
        [
            'question' => "How much does $kw cost in $cn?",
            'answer'   => BRAND_NAME . " has four plans: " . seo_plans_summary() . ". There is no installation charge and no per-outlet fee, and every plan can be paid monthly or yearly.",
        ],
        [
            'question' => "Is $kw available in $cn?",
            'answer'   => "Yes. " . BRAND_NAME . " is available to businesses across $cn and the rest of $st. Billing works even without an internet connection and syncs automatically once you are back online, so billing never stops.",
        ],
        [
            'question' => "Is my data safe if I use $kw in $cn?",
            'answer'   => "Your data is stored per business account and backed up so you can export it whenever you want. " . BRAND_NAME . " also keeps a full activity trail of who created or edited a bill, which matters when more than one person bills from the same shop in $cn.",
        ],
        [
            'question' => "Can I switch to $kw without losing my old bills?",
            'answer'   => "Yes. Start on the " . seo_entry_plan_name() . " plan, run your next billing cycle on " . BRAND_NAME . ", and only move across once you trust the numbers. Your accountant can keep working on the old records in parallel, so nothing in $st has to stop while you switch.",
        ],
    ];
}

// ============ INDUSTRIES ============

/**
 * The public URL of an industry page, relative to the site root:
 *   seo_industry_path(['slug' => 'retail'])  -> 'retail-software.php'
 *
 * The filename is stored on the entry itself in config.php and the root file is
 * a hand-written stub, so a link built here can never point at a page that does
 * not exist. The stub then hands rendering to includes/industry-page.php.
 */
function seo_industry_path(array $industry): string
{
    return (string)($industry['file'] ?? '');
}

function seo_industry_url(array $industry): string
{
    return seo_url(seo_industry_path($industry));
}

function seo_find_industry(string $slug): ?array
{
    global $industry_pages;
    foreach ((array)$industry_pages as $industry) {
        if (($industry['slug'] ?? '') === $slug) {
            return $industry;
        }
    }
    return null;
}

/**
 * Every industry page except the one being rendered, so each page links to its
 * five siblings. Driven by the same $industry_pages list as everything else.
 */
function seo_other_industries(array $current): array
{
    global $industry_pages;
    $others = [];
    foreach ((array)$industry_pages as $industry) {
        if (($industry['slug'] ?? '') !== ($current['slug'] ?? '')) {
            $others[] = $industry;
        }
    }
    return $others;
}

function seo_industry_meta_desc(array $industry): string
{
    return (string)$industry['desc'];
}

/**
 * Service node for an industry page. areaServed is a Country rather than a list
 * of cities, because the software is not tied to one place - the city pages
 * carry that signal.
 */
function seo_industry_service_node(array $industry): array
{
    return [
        '@type'       => 'Service',
        '@id'         => seo_industry_url($industry) . '#service',
        'name'        => $industry['title'],
        'serviceType' => 'billing software for ' . strtolower($industry['tag']),
        'url'         => seo_industry_url($industry),
        'description' => seo_industry_meta_desc($industry),
        'provider'    => ['@id' => SITE_URL . '/#organization'],
        'areaServed'  => [
            '@type' => 'Country',
            'name'  => 'India',
        ],
        'hasOfferCatalog' => [
            '@type'           => 'OfferCatalog',
            'name'            => $industry['tag'] . ' features',
            'itemListElement' => array_map(function ($f) {
                return [
                    '@type'    => 'Offer',
                    'itemOffered' => [
                        '@type'       => 'Service',
                        'name'        => $f['title'],
                        'description' => $f['desc'],
                    ],
                ];
            }, $industry['features']),
        ],
    ];
}

/**
 * FAQ block for an industry page. The questions name the industry and its first
 * headline feature, which is what earns these the "billing software for grocery
 * store" style long-tail impressions.
 */
function seo_industry_faqs(array $industry): array
{
    $tag    = $industry['tag'];
    $nav    = $industry['nav'];
    $first  = $industry['features'][0]['title'];
    $second = $industry['features'][1]['title'];

    return [
        [
            'question' => "Which billing software is best for a $tag business?",
            'answer'   => BRAND_NAME . " is built for this trade specifically. Out of the box it gives you $first and $second, which are the two things a $nav business needs most, and the same GST handling as every other industry. It runs on Android, iOS, Windows and Mac.",
        ],
        [
            'question' => "How much does $nav billing software cost?",
            'answer'   => BRAND_NAME . " has four plans: " . seo_plans_summary() . ". There is no installation charge and no per-counter fee.",
        ],
        [
            'question' => "Is it suitable for a small $tag shop?",
            'answer'   => "Yes, that is the main case it is built for. One phone at the counter is enough to start, several staff can bill from their own logins, and the " . seo_entry_plan_name() . " plan covers a single outlet. You can add a second device whenever you need one.",
        ],
        [
            'question' => "Does billing keep working without internet?",
            'answer'   => "Yes. " . BRAND_NAME . " keeps billing offline and syncs automatically once the connection is back, so a power cut or a weak network never stops your counter. Nothing is lost in the gap.",
        ],
        [
            'question' => "Can I keep my old bills and stock when I switch?",
            'answer'   => "Yes. Start on the " . seo_entry_plan_name() . " plan, run your next billing cycle on " . BRAND_NAME . ", and move across only once you trust the numbers. Your old records can stay in your previous software in parallel, and your accountant can keep working on both during the changeover.",
        ],
    ];
}
