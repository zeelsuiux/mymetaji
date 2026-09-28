<?php
/**
 * MASTER PANEL - City Master
 *
 * Add, rename, reorder and delete the cities behind the site's city pages,
 * without opening config.php. Saving writes data/cities.json (read by
 * config.php) and then runs includes/city-pages.php, so the service pages for
 * a new city go live straight away and the pages of a deleted city are removed.
 */
require_once __DIR__ . '/auth.php';
require_master_login();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/icons.php';

// The city data layer, then the site config it feeds: $services says how many
// pages each city publishes, and including config.php is also what publishes
// the page files after every save below.
require_once __DIR__ . '/../../includes/city-master.php';
require_once __DIR__ . '/../../config.php';

/** Turn a sync report into the sentence the admin reads above the form. */
function city_master_sync_message($label, array $report) {
    $parts = [];
    if ($report['ran'] && $report['created']) {
        $parts[] = $report['created'] . ' new page' . ($report['created'] > 1 ? 's' : '') . ' published';
    }
    if ($report['ran'] && $report['pruned']) {
        $parts[] = $report['pruned'] . ' page' . ($report['pruned'] > 1 ? 's' : '') . ' removed';
    }
    $message = $label . ($parts ? ' - ' . implode(', ', $parts) . '.' : ' - its pages are already live.');
    if (empty($report['writable'])) {
        $message .= ' The site folder is not writable, so the page files could not be updated.';
    }
    return $message;
}

/** A stable pastel colour per state, for the avatar in each city row. */
function city_master_tint($text) {
    $hue = crc32((string)$text) % 360;
    return 'background:hsl(' . $hue . ' 72% 95%);color:hsl(' . $hue . ' 48% 32%);';
}

$cityRows    = city_master_all();
$serviceList = $services;
$stateList   = city_master_states();
$errors      = [];
$notice      = '';
$editSlug    = trim((string)($_GET['edit'] ?? ''));
$form        = ['name' => '', 'slug' => '', 'state' => ''];

/** A city page URL that is a real hand-written file must not be shadowed. */
function city_master_file_clash($slug, array $serviceList) {
    $root = __DIR__ . '/../../';
    foreach ($serviceList as $service) {
        $file = $service['slug'] . '-' . $slug . '.php';
        $path = $root . $file;
        if (!is_file($path)) continue;
        $head = (string) @file_get_contents($path, false, null, 0, 120);
        if (strpos($head, 'Auto-generated city page') === false) return $file;
    }
    return '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $do   = (string)($_POST['do'] ?? 'save');
    $rows = city_master_all();

    if ($do === 'save') {
        $editSlug = city_master_slug($_POST['edit'] ?? '');
        $name     = trim((string)($_POST['name'] ?? ''));
        $slugIn   = trim((string)($_POST['slug'] ?? ''));
        $slug     = city_master_slug($slugIn !== '' ? $slugIn : $name);
        $state    = trim((string)($_POST['state'] ?? ''));

        $form = ['name' => $name, 'slug' => $slug, 'state' => $state];

        if ($name === '') $errors[] = 'City name is required.';
        if ($slug === '')  $errors[] = 'URL slug is required.';
        if ($state === '') $errors[] = 'Please choose a state.';
        if ($state !== '' && !in_array($state, $stateList, true)) {
            $errors[] = 'Please choose a state from the list.';
        }
        if ($slug !== '') {
            foreach ($rows as $row) {
                if ($row['slug'] === $slug && $row['slug'] !== $editSlug) {
                    $errors[] = 'The URL "' . $slug . '" is already used by ' . $row['name'] . '. Pick another slug.';
                    break;
                }
            }
            if (!$errors && ($clash = city_master_file_clash($slug, $serviceList))) {
                $errors[] = $clash . ' is a real page in the site folder. Choose a different slug.';
            }
        }

        if (!$errors) {
            $record = ['slug' => $slug, 'name' => $name, 'state' => $state];
            $next   = [];
            $replaced = false;
            foreach ($rows as $row) {
                if ($editSlug !== '' && $row['slug'] === $editSlug) {
                    $next[]   = $record;
                    $replaced = true;
                } else {
                    $next[]   = $row;
                }
            }
            if (!$replaced) $next[] = $record;

            if (city_master_save($next)) {
                $cities    = city_master_all();
                $cityRows  = $cities;
                $report    = seo_sync_city_pages();
                $label     = $replaced ? 'City updated' : 'City added';
                $notice    = city_master_sync_message($label, $report);
                $editSlug  = '';
                $form      = ['name' => '', 'slug' => '', 'state' => ''];
            } else {
                $errors[] = 'Could not write data/cities.json. Check that the data folder is writable.';
            }
        }
    } elseif ($do === 'delete') {
        $slug   = city_master_slug($_POST['slug'] ?? '');
        $victim = city_master_find($slug);
        $next   = array_values(array_filter($rows, fn($row) => $row['slug'] !== $slug));

        if (!$victim) {
            $errors[] = 'That city no longer exists.';
        } elseif (count($rows) < 2) {
            $errors[] = 'This is the last city. Restore the default list instead of removing it.';
        } elseif (count($next) === count($rows)) {
            $errors[] = 'Could not remove ' . $victim['name'] . '.';
        } else {
            $removedName = $victim['name'];
            if (city_master_save($next)) {
                $cities   = city_master_all();
                $cityRows = $cities;
                $notice   = city_master_sync_message($removedName . ' removed', seo_sync_city_pages());
            } else {
                $errors[] = 'Could not write data/cities.json. Check that the data folder is writable.';
            }
        }
    } elseif ($do === 'move') {
        $slug = city_master_slug($_POST['slug'] ?? '');
        $step = ($_POST['dir'] ?? '') === 'up' ? -1 : 1;
        $at   = null;
        foreach ($rows as $i => $row) if ($row['slug'] === $slug) $at = $i;
        $to  = ($at === null) ? null : $at + $step;

        if ($at === null) {
            $errors[] = 'That city no longer exists.';
        } elseif ($to < 0 || $to >= count($rows)) {
            $notice = 'Already at the ' . ($step < 0 ? 'top' : 'bottom') . ' of the list.';
        } else {
            $moved    = $rows[$at];
            $rows[$at] = $rows[$to];
            $rows[$to] = $moved;
            if (city_master_save($rows)) {
                $cityRows = city_master_all();
                $notice   = $moved['name'] . ' moved ' . ($step < 0 ? 'up' : 'down') . '. This is the order the nav, footer and sitemap use.';
            } else {
                $errors[] = 'Could not write data/cities.json. Check that the data folder is writable.';
            }
        }
    } elseif ($do === 'reset') {
        if (city_master_save(city_master_seed())) {
            $cities   = city_master_all();
            $cityRows = $cities;
            $notice   = city_master_sync_message('Default city list restored', seo_sync_city_pages());
        } else {
            $errors[] = 'Could not write data/cities.json. Check that the data folder is writable.';
        }
    }
}

if ($editSlug !== '' && !$errors) {
    $editing = city_master_find($editSlug);
    if ($editing) {
        $form = ['name' => $editing['name'], 'slug' => $editing['slug'], 'state' => $editing['state']];
        if (!in_array($form['state'], $stateList, true)) array_unshift($stateList, $form['state']);
    } else {
        $editSlug = '';
    }
}

// Group by state, keeping the order the states first appear in, so the list
// reads as coverage rather than one long alphabetical dump.
$stateGroups = [];
foreach ($cityRows as $row) {
    $stateGroups[$row['state']][] = $row;
}

$pageCount  = count($cityRows) * count($serviceList);
$usingSaved = city_master_is_saved();
$liveFiles  = array_map('basename', (array) glob(__DIR__ . '/../../*-software-*.php'));

$pageTitle  = 'City Master';
$activePage = 'cities';
require __DIR__ . '/layout_top.php';
?>

<style>
    .cm-layout { display:grid;grid-template-columns:minmax(0,1.65fr) minmax(320px,0.95fr);gap:20px;align-items:start; }
    .cm-panel { overflow:hidden; }
    .cm-panel-head { padding:16px 20px;border-bottom:1px solid var(--border);display:flex;gap:12px;align-items:center;justify-content:space-between;flex-wrap:wrap; }
    .cm-panel-head h2 { margin:0;font-size:15px;font-weight:700;color:var(--dark-gray);display:flex;align-items:center;gap:8px; }
    .cm-count-pill { font-size:11px;font-weight:700;padding:3px 9px;border-radius:999px;background:var(--brand-blue-soft);color:var(--primary); }
    .cm-search { position:relative;flex:1;min-width:180px;max-width:280px; }
    .cm-search input { width:100%;padding:8px 12px 8px 32px;border:1px solid var(--border);border-radius:9px;font-size:13px;background:#fff; }
    .cm-search input:focus { outline:2px solid rgba(37,99,235,.18);border-color:var(--primary); }
    .cm-search svg { position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--text-muted); }

    .cm-chips { display:flex;gap:7px;flex-wrap:wrap;padding:12px 20px;border-bottom:1px solid var(--border);background:#fbfcfe; }
    .cm-chip { border:1px solid var(--border);background:#fff;color:#475569;font-size:12px;font-weight:600;padding:5px 11px;border-radius:999px;cursor:pointer; }
    .cm-chip:hover { background:#f1f5f9; }
    .cm-chip.active { background:var(--primary);border-color:var(--primary);color:#fff; }
    .cm-chip b { font-weight:700;opacity:.65;margin-left:4px; }

    .cm-body { max-height:min(70vh,760px);overflow:auto;padding:6px 0 12px; }
    .cm-group-head { position:sticky;top:0;z-index:1;display:flex;align-items:center;gap:8px;padding:10px 20px 8px;background:#fff;border-bottom:1px solid var(--border); }
    .cm-group-head span:first-child { font-size:12px;font-weight:700;letter-spacing:.02em;color:var(--dark-gray); }
    .cm-group-head span:last-child { font-size:11px;color:var(--text-muted); }
    .cm-group-dot { width:8px;height:8px;border-radius:50%; }

    .cm-row { display:flex;align-items:center;gap:12px;padding:10px 20px;border-bottom:1px solid #f1f5f9; }
    .cm-row:last-child { border-bottom:0; }
    .cm-row:hover { background:#f8fafc; }
    .cm-row.is-editing { background:#eff6ff;box-shadow:inset 3px 0 0 var(--primary); }
    .cm-row[hidden],.cm-group[hidden] { display:none !important; }
    .cm-avatar { width:34px;height:34px;flex-shrink:0;border-radius:10px;display:grid;place-items:center;font-weight:800;font-size:14px;text-transform:uppercase; }
    .cm-row-main { min-width:0;flex:1; }
    .cm-row-name { font-size:14px;font-weight:700;color:var(--dark-gray);display:flex;align-items:center;gap:8px;flex-wrap:wrap; }
    .cm-row-name .cm-state { font-size:11px;font-weight:600;padding:2px 8px;border-radius:999px;background:#f1f5f9;color:#475569; }
    .cm-row-sub { font-size:12px;color:var(--text-muted);display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-top:2px; }
    .cm-row-sub code { font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace;font-size:11.5px;background:#f1f5f9;padding:1px 6px;border-radius:5px;color:#475569; }
    .cm-row-actions { display:flex;align-items:center;gap:4px;flex-shrink:0; }
    .cm-row-actions form { display:inline-flex; }
    .cm-iconbtn { display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:8px;border:1px solid var(--border);background:#fff;color:#475569;text-decoration:none;cursor:pointer;font-size:11px;line-height:1; }
    .cm-iconbtn:hover { background:#f1f5f9;color:var(--dark-gray); }
    .cm-iconbtn .icon-svg { color:#475569 !important; }
    .cm-iconbtn:disabled { opacity:.35;cursor:not-allowed; }
    .cm-iconbtn.danger:hover { background:#fef2f2;border-color:#fecaca;color:#dc2626; }
    .cm-iconbtn.danger:hover .icon-svg { color:#dc2626 !important; }

    .cm-form { position:sticky;top:18px; }
    .cm-form .panel-body { padding:18px 20px 20px; }
    .cm-form .form-grid { grid-template-columns:1fr;gap:14px; }
    .cm-form.is-editing { border-color:#bfdbfe;box-shadow:0 0 0 3px rgba(37,99,235,.08); }
    .cm-hint { font-size:12px;color:var(--text-muted);margin:6px 0 0;line-height:1.5; }
    .cm-hint.bad { color:#dc2626;font-weight:600; }
    .cm-preview { margin-top:16px;border:1px solid var(--border);border-radius:12px;background:#fbfcfe;padding:13px 14px; }
    .cm-preview-head { font-size:11px;font-weight:700;letter-spacing:.03em;text-transform:uppercase;color:var(--text-muted);margin-bottom:9px; }
    .cm-url { font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace;font-size:12px;color:var(--primary);word-break:break-all;margin-bottom:10px; }
    .cm-url.dim { color:var(--text-muted); }
    .cm-files { display:flex;flex-wrap:wrap;gap:6px; }
    .cm-file { font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace;font-size:11px;padding:4px 8px;border-radius:7px;background:#fff;border:1px solid var(--border);color:#475569;text-decoration:none; }
    .cm-file:hover { border-color:var(--primary);color:var(--primary); }
    .cm-file.live { background:#ecfdf5;border-color:#a7f3d0;color:#047857; }
    .cm-file.new::after { content:' +';font-weight:700;color:#059669; }
    .cm-empty { padding:36px 20px;text-align:center;color:var(--text-muted);font-size:13px; }
    .cm-empty b { display:block;color:var(--dark-gray);font-size:15px;margin-bottom:6px; }
    .cm-ghost { border:0;background:none;color:var(--text-muted);font-size:12px;font-weight:600;text-decoration:underline;cursor:pointer;padding:4px; }
    .cm-ghost:hover { color:#dc2626; }
    @media (max-width:1100px) { .cm-layout { grid-template-columns:1fr; } .cm-form { position:static; order:-1; } }
    @media (max-width:700px) { .cm-row { padding:12px 14px; } .cm-chips,.cm-panel-head { padding:12px 14px; } }
</style>

<?php if ($notice): ?><div class="alert"><?= htmlspecialchars($notice) ?></div><?php endif; ?>
<?php if ($errors): ?><div class="alert alert-error"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div><?php endif; ?>

<div class="kpi-grid master-kpis">
    <div class="kpi-card"><div class="label">Cities Live</div><div class="value"><?= count($cityRows) ?></div></div>
    <div class="kpi-card"><div class="label">States Covered</div><div class="value"><?= count($stateGroups) ?></div></div>
    <div class="kpi-card"><div class="label">City Pages Live</div><div class="value green"><?= $pageCount ?></div></div>
    <div class="kpi-card"><div class="label">Pages Per City</div><div class="value"><?= count($serviceList) ?></div></div>
</div>

<div class="cm-layout">

    <!-- ============ CITY LIST ============ -->
    <section class="panel cm-panel">
        <div class="cm-panel-head">
            <h2><?= icon('building', 18) ?> All Cities <span class="cm-count-pill"><?= count($cityRows) ?></span></h2>
            <div class="cm-search">
                <?= icon('search', 15) ?>
                <input type="search" id="cmSearch" placeholder="Search city, slug or state" aria-label="Search cities">
            </div>
        </div>

        <div class="cm-chips" id="cmChips">
            <button type="button" class="cm-chip active" data-state="">All <b><?= count($cityRows) ?></b></button>
            <?php foreach ($stateGroups as $stateName => $group): ?>
                <button type="button" class="cm-chip" data-state="<?= htmlspecialchars($stateName) ?>"><?= htmlspecialchars($stateName) ?> <b><?= count($group) ?></b></button>
            <?php endforeach; ?>
        </div>

        <div class="cm-body" id="cmList">
            <?php if (!$cityRows): ?>
                <div class="cm-empty"><b>No cities yet</b>Add one from the form beside this list - all <?= count($serviceList) ?> pages go live at once.</div>
            <?php endif; ?>
            <?php foreach ($stateGroups as $stateName => $group): ?>
                <div class="cm-group" data-state="<?= htmlspecialchars($stateName) ?>">
                    <div class="cm-group-head">
                        <span class="cm-group-dot" style="<?= city_master_tint($stateName) ?>"></span>
                        <span><?= htmlspecialchars($stateName) ?></span>
                        <span><?= count($group) ?> <?= count($group) > 1 ? 'cities' : 'city' ?></span>
                    </div>
                    <?php foreach ($group as $city):
                        $firstPage = $serviceList[0]['slug'] . '-' . $city['slug'] . '.php';
                        $live      = count(array_intersect($liveFiles, array_map(fn($s) => $s['slug'] . '-' . $city['slug'] . '.php', $serviceList)));
                    ?>
                        <div class="cm-row<?= $editSlug === $city['slug'] ? ' is-editing' : '' ?>"
                             data-name="<?= htmlspecialchars(strtolower($city['name'])) ?>"
                             data-slug="<?= htmlspecialchars($city['slug']) ?>"
                             data-state="<?= htmlspecialchars($stateName) ?>">
                            <div class="cm-avatar" style="<?= city_master_tint($stateName) ?>"><?= htmlspecialchars(mb_substr($city['name'], 0, 1)) ?></div>
                            <div class="cm-row-main">
                                <div class="cm-row-name">
                                    <?= htmlspecialchars($city['name']) ?>
                                    <span class="cm-state"><?= htmlspecialchars($stateName) ?></span>
                                </div>
                                <div class="cm-row-sub">
                                    <code>/<?= htmlspecialchars($serviceList[0]['slug'] . '-' . $city['slug'] . '.php') ?></code>
                                    <span><?= $live ? $live . ' pages live' : 'no pages written yet' ?></span>
                                </div>
                            </div>
                            <div class="cm-row-actions">
                                <form method="post">
                                    <input type="hidden" name="do" value="move"><input type="hidden" name="dir" value="up">
                                    <input type="hidden" name="slug" value="<?= htmlspecialchars($city['slug']) ?>">
                                    <button class="cm-iconbtn" type="submit" title="Move up" <?= $city['slug'] === $cityRows[0]['slug'] ? 'disabled' : '' ?>>&#9650;</button>
                                </form>
                                <form method="post">
                                    <input type="hidden" name="do" value="move"><input type="hidden" name="dir" value="down">
                                    <input type="hidden" name="slug" value="<?= htmlspecialchars($city['slug']) ?>">
                                    <button class="cm-iconbtn" type="submit" title="Move down" <?= $city['slug'] === end($cityRows)['slug'] ? 'disabled' : '' ?>>&#9660;</button>
                                </form>
                                <a class="cm-iconbtn" href="../../<?= htmlspecialchars($firstPage) ?>" target="_blank" title="Open first page"><?= icon('view', 15) ?></a>
                                <a class="cm-iconbtn" href="?edit=<?= urlencode($city['slug']) ?>#cmForm" title="Edit city"><?= icon('edit', 15) ?></a>
                                <form method="post" class="cm-delete" data-name="<?= htmlspecialchars($city['name'], ENT_QUOTES) ?>">
                                    <input type="hidden" name="do" value="delete">
                                    <input type="hidden" name="slug" value="<?= htmlspecialchars($city['slug']) ?>">
                                    <button class="cm-iconbtn danger" type="submit" title="Delete city and its pages"><?= icon('delete', 15) ?></button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
            <div class="cm-empty" id="cmNoMatch" hidden><b>No match</b>Try a different search, or clear the state filter.</div>
        </div>

        <div class="cm-panel-head" style="border-bottom:0;border-top:1px solid var(--border);justify-content:space-between;">
            <span style="font-size:12px;color:var(--text-muted);">Order here is the order of the nav dropdown, footer hub and sitemap.</span>
            <?php if ($usingSaved): ?>
                <form method="post" class="cm-restore">
                    <input type="hidden" name="do" value="reset">
                    <button class="cm-ghost" type="submit">Restore the 20 default cities</button>
                </form>
            <?php endif; ?>
        </div>
    </section>

    <!-- ============ ADD / EDIT FORM ============ -->
    <aside class="panel cm-form<?= $editSlug ? ' is-editing' : '' ?>" id="cmForm">
        <div class="cm-panel-head">
            <h2><?= icon($editSlug ? 'edit' : 'add', 18) ?> <?= $editSlug ? 'Edit ' . htmlspecialchars($form['name']) : 'Add a City' ?></h2>
            <?php if ($editSlug): ?><a class="cm-ghost" href="cities.php">Cancel</a><?php endif; ?>
        </div>
        <div class="panel-body">
            <form method="post" id="cmCityForm" autocomplete="off">
                <input type="hidden" name="do" value="save">
                <input type="hidden" name="edit" value="<?= htmlspecialchars($editSlug) ?>">
                <div class="form-grid">
                    <div class="form-group">
                        <label>City Name *</label>
                        <input name="name" id="cmName" required value="<?= htmlspecialchars($form['name']) ?>" placeholder="e.g. Nashik">
                    </div>
                    <div class="form-group">
                        <label>URL Slug *</label>
                        <input name="slug" id="cmSlug" required value="<?= htmlspecialchars($form['slug']) ?>" placeholder="nashik">
                        <p class="cm-hint" id="cmSlugHint">Left blank, it is built from the name: lowercase, spaces become dashes.</p>
                    </div>
                    <div class="form-group">
                        <label>State *</label>
                        <select name="state" id="cmState" required>
                            <option value="">-- choose state --</option>
                            <?php foreach ($stateList as $stateOption): ?>
                                <option value="<?= htmlspecialchars($stateOption) ?>" <?= $form['state'] === $stateOption ? 'selected' : '' ?>><?= htmlspecialchars($stateOption) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="cm-preview">
                    <div class="cm-preview-head">Pages this city will publish</div>
                    <div class="cm-url dim" id="cmUrl">Type a city name to see its URL</div>
                    <div class="cm-files" id="cmFiles"></div>
                </div>

                <div class="form-actions">
                    <button class="btn btn-primary" type="submit" id="cmSave" style="width:100%;justify-content:center;">
                        <?= $editSlug ? 'Save Changes' : 'Add City &amp; Publish Pages' ?>
                    </button>
                </div>
            </form>
        </div>
    </aside>
</div>

<script id="cmConfig" type="application/json"><?= json_encode([
    'services' => array_column($serviceList, 'slug'),
    'taken'    => array_column($cityRows, 'name', 'slug'),
    'live'     => $liveFiles,
    'origin'   => $editSlug,
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>

<script>
(function () {
    var cfg   = JSON.parse(document.getElementById('cmConfig').textContent);
    var form  = document.getElementById('cmCityForm');
    var name  = document.getElementById('cmName');
    var slug  = document.getElementById('cmSlug');
    var state = document.getElementById('cmState');
    var hint  = document.getElementById('cmSlugHint');
    var url   = document.getElementById('cmUrl');
    var files = document.getElementById('cmFiles');
    var save  = document.getElementById('cmSave');
    var live  = cfg.live;

    function toSlug(value) {
        return value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
    }

    // Name drives the slug until the admin edits the slug by hand, so the common
    // case is one field and the URL is always right.
    var slugTouched = slug.value.trim() !== '';
    slug.addEventListener('input', function () { slugTouched = true; refresh(); });
    name.addEventListener('input', function () {
        if (!slugTouched) slug.value = toSlug(name.value);
        refresh();
    });
    state.addEventListener('change', refresh);

    function refresh() {
        var value = slug.value.trim().toLowerCase();
        var final = toSlug(value || name.value);
        var problem = '';

        if (!final) {
            url.textContent = 'Type a city name to see its URL';
            url.className = 'cm-url dim';
        } else {
            url.textContent = '/' + cfg.services[0] + '-' + final + '.php';
            url.className = 'cm-url';
        }

        if (final && cfg.taken[final] && final !== cfg.origin) {
            problem = 'Already used by ' + cfg.taken[final] + ' - pick another slug.';
        } else if (value && final !== value) {
            problem = 'Only lowercase letters, numbers and dashes are allowed.';
        } else if (final && cfg.origin && final !== cfg.origin) {
            problem = 'The old ' + cfg.services.length + ' ' + cfg.origin + ' pages will be replaced.';
        }

        hint.textContent = problem || 'Left blank, it is built from the name: lowercase, spaces become dashes.';
        hint.className = problem ? 'cm-hint bad' : 'cm-hint';

        files.innerHTML = '';
        if (final) {
            cfg.services.forEach(function (service) {
                var file = service + '-' + final + '.php';
                var a = document.createElement('a');
                a.className = 'cm-file' + (live.indexOf(file) !== -1 ? ' live' : ' new');
                a.href = '../../' + file;
                a.target = '_blank';
                a.textContent = file;
                files.appendChild(a);
            });
        }

        save.disabled = !!(problem && problem.indexOf('replaced') === -1);
    }

    refresh();
    if (cfg.origin) document.getElementById('cmForm').scrollIntoView({ behavior: 'smooth', block: 'nearest' });

    // ---- list: search + state chips
    var rows   = Array.prototype.slice.call(document.querySelectorAll('.cm-row'));
    var groups = Array.prototype.slice.call(document.querySelectorAll('.cm-group'));
    var chips  = Array.prototype.slice.call(document.querySelectorAll('#cmChips .cm-chip'));
    var search = document.getElementById('cmSearch');
    var noMatch = document.getElementById('cmNoMatch');
    var stateFilter = '';

    function applyFilter() {
        var q = search.value.trim().toLowerCase();
        var shown = 0;
        rows.forEach(function (row) {
            var hit = (!stateFilter || row.dataset.state === stateFilter)
                && (!q || (row.dataset.name + ' ' + row.dataset.slug + ' ' + row.dataset.state).indexOf(q) !== -1);
            row.hidden = !hit;
            if (hit) shown++;
        });
        groups.forEach(function (group) {
            group.hidden = !group.querySelector('.cm-row:not([hidden])');
        });
        if (noMatch) noMatch.hidden = shown > 0;
    }

    chips.forEach(function (chip) {
        chip.addEventListener('click', function () {
            chips.forEach(function (c) { c.classList.remove('active'); });
            chip.classList.add('active');
            stateFilter = chip.dataset.state;
            applyFilter();
        });
    });
    if (search) search.addEventListener('input', applyFilter);

    document.querySelectorAll('form.cm-delete').forEach(function (formEl) {
        formEl.addEventListener('submit', function (event) {
            if (!window.confirm('Delete ' + formEl.dataset.name + '?\n\nIts ' + cfg.services.length
                + ' city pages will be removed from the site and the sitemap.')) {
                event.preventDefault();
            }
        });
    });
    document.querySelectorAll('form.cm-restore').forEach(function (formEl) {
        formEl.addEventListener('submit', function (event) {
            if (!window.confirm('Replace the current list with the 20 default cities?\n\nAny city you added will be deleted along with its pages.')) {
                event.preventDefault();
            }
        });
    });
})();
</script>

<?php require __DIR__ . '/layout_bottom.php'; ?>
