<?php
require_once __DIR__ . '/auth.php';
require_login();
if (!is_admin()) { http_response_code(403); die('Admin access required.'); }
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/modules.php';
require_once __DIR__ . '/helpers.php';

require_active_license();

$module = $_GET['module'] ?? 'all';
if (!can_access_module($module) || ($module === 'all' && !is_admin())) {
    http_response_code(403);
    die('You do not have permission to export this data.');
}
if (is_subadmin() && in_array($module, ['products', 'team', 'meetings', 'expenses', 'payments', 'finance', 'all'], true)) {
    http_response_code(403);
    die('Subadmin cannot export this data.');
}
$range = $_GET['range'] ?? 'all';
$customFrom = normalize_date_input($_GET['from'] ?? '');
$customTo = normalize_date_input($_GET['to'] ?? '');
$download = isset($_GET['download']) && $_GET['download'] == '1';
$moduleList = ($module === 'all') ? array_keys($MODULES) : [$module];

function get_record_date_value($row, $fallback = '') {
    $keys = ['date', 'due_date', 'valid_until', 'created_at', 'updated_at'];
    foreach ($keys as $key) {
        if (!empty($row[$key] ?? '')) return $row[$key];
    }
    if (isset($row['created_at'])) return $row['created_at'];
    return $fallback;
}

function match_date_range($value, $range, $from = '', $to = '') {
    if ($range === 'all' && $from === '' && $to === '') return true;
    $stamp = strtotime((string)$value);
    if ($stamp === false) return true;
    if ($range === 'all') {
        if ($from !== '' && $stamp < strtotime($from)) return false;
        if ($to !== '' && $stamp > strtotime($to . ' 23:59:59')) return false;
        return true;
    }
    $now = new DateTime('today');
    $today = $now->format('Y-m-d');
    switch ($range) {
        case 'today':
            return date('Y-m-d', $stamp) === $today;
        case 'yesterday':
            return date('Y-m-d', $stamp) === date('Y-m-d', strtotime('-1 day'));
        case '7days':
            return $stamp >= strtotime('-6 days') && $stamp <= time();
        case '30days':
            return $stamp >= strtotime('-29 days') && $stamp <= time();
        case 'month':
            return date('Y-m', $stamp) === date('Y-m');
        case 'year':
            return date('Y', $stamp) === date('Y');
        case 'lastyear':
            return date('Y', $stamp) === (string)(date('Y') - 1);
        case 'custom':
            if ($from !== '' && $stamp < strtotime($from)) return false;
            if ($to !== '' && $stamp > strtotime($to . ' 23:59:59')) return false;
            return true;
        default:
            return true;
    }
}

function filter_rows_by_range($rows, $range, $from = '', $to = '') {
    if ($range === 'all' && $from === '' && $to === '') {
        return $rows;
    }

    return array_values(array_filter($rows, function ($row) use ($range, $from, $to) {
        $value = get_record_date_value($row);
        return match_date_range($value, $range, $from, $to);
    }));
}

function export_slug($value) {
    $value = strtolower(trim((string)$value));
    $value = preg_replace('/[^a-z0-9]+/', '-', $value);
    return trim($value, '-') ?: 'data';
}

function export_file_name($module, $range, $from = '', $to = '') {
    $moduleSlug = $module === 'all' ? 'data' : ($module === 'customers' ? 'patients' : export_slug($module));
    $today = new DateTimeImmutable('today');
    $start = null;
    $end = null;
    if ($range === 'custom' && ($from !== '' || $to !== '')) {
        $start = $from !== '' ? new DateTimeImmutable($from) : null;
        $end = $to !== '' ? new DateTimeImmutable($to) : null;
    } elseif ($range === 'today') {
        $start = $today;
        $end = $today;
    } elseif ($range === 'yesterday') {
        $start = $today->modify('-1 day');
        $end = $start;
    } elseif ($range === '7days') {
        $start = $today->modify('-6 days');
        $end = $today;
    } elseif ($range === '30days') {
        $start = $today->modify('-29 days');
        $end = $today;
    } elseif ($range === 'month') {
        $start = $today->modify('first day of this month');
        $end = $today->modify('last day of this month');
    } elseif ($range === 'year') {
        $start = $today->modify('first day of January')->setDate((int)$today->format('Y'), 1, 1);
        $end = $today->modify('last day of December')->setDate((int)$today->format('Y'), 12, 31);
    } elseif ($range === 'lastyear') {
        $year = (int)$today->format('Y') - 1;
        $start = new DateTimeImmutable($year . '-01-01');
        $end = new DateTimeImmutable($year . '-12-31');
    }
    $rangeSlug = $start && $end ? $start->format('Y-m-d') . '-to-' . $end->format('Y-m-d') : 'all-to-all';
    return $moduleSlug . '-' . $rangeSlug . '.xlsx';
}

function xlsx_escape($value) {
    if (is_array($value)) {
        $value = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
    return str_replace(['&', '<', '>', '"', "'"], ['&amp;', '&lt;', '&gt;', '&quot;', '&apos;'], (string)$value);
}

function xlsx_column_name($index) {
    $name = '';
    while ($index >= 0) {
        $name = chr(($index % 26) + 65) . $name;
        $index = intdiv($index, 26) - 1;
    }
    return $name;
}

function xlsx_sheet_xml($headers, $rows) {
    $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" standalone="yes"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"></worksheet>');
    $sheetData = $xml->addChild('sheetData');

    $headerRow = $sheetData->addChild('row');
    foreach ($headers as $index => $header) {
        $cell = $headerRow->addChild('c');
        $cell->addAttribute('r', xlsx_column_name($index) . '1');
        $cell->addAttribute('t', 'inlineStr');
        $is = $cell->addChild('is');
        $t = $is->addChild('t', xlsx_escape($header));
    }

    foreach ($rows as $rowIndex => $row) {
        $dataRow = $sheetData->addChild('row');
        foreach ($headers as $cellIndex => $header) {
            $cell = $dataRow->addChild('c');
            $cell->addAttribute('r', xlsx_column_name($cellIndex) . ($rowIndex + 2));
            $cell->addAttribute('t', 'inlineStr');
            $value = $row[$header] ?? '';
            $is = $cell->addChild('is');
            $is->addChild('t', xlsx_escape($value));
        }
    }

    return $xml->asXML();
}

function export_pending_by_invoice($invoice, $payments) {
    $paid = 0.0;
    foreach ($payments as $payment) {
        if (($payment['invoice_no'] ?? '') === ($invoice['invoice_no'] ?? '')) {
            $paid += (float)($payment['amount'] ?? 0);
        }
    }
    return max(0, (float)($invoice['amount'] ?? 0) - $paid);
}

function export_workbook_for_modules($modules, $range, $customFrom, $customTo) {
    $tempPath = tempnam(sys_get_temp_dir(), 'erp_xlsx_');
    unlink($tempPath);
    $zipPath = $tempPath . '.zip';
    $zip = new ZipArchive();
    if (!$zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
        return false;
    }

    $sheetFiles = [];
    foreach ($modules as $moduleName) {
        $records = member_visible_rows($moduleName, db_get_all($moduleName));
        $records = filter_rows_by_range($records, $range, $customFrom, $customTo);
        $allInvoices = db_get_all('invoices');
        $allPayments = db_get_all('payments');
        if (in_array($moduleName, ['customers', 'team'], true)) {
            foreach ($records as &$record) {
                $name = trim((string)($record['name'] ?? ''));
                $record['pending'] = 0.0;
                foreach ($allInvoices as $invoice) {
                    if (strcasecmp(trim((string)($invoice['customer'] ?? '')), $name) === 0) {
                        $record['pending'] += export_pending_by_invoice($invoice, $allPayments);
                    }
                }
            }
            unset($record);
        } elseif ($moduleName === 'invoices') {
            foreach ($records as &$record) {
                $record['pending'] = export_pending_by_invoice($record, $allPayments);
            }
            unset($record);
        }
        $headers = [];
        $rows = [];
        foreach ($records as $record) {
            $record = array_diff_key($record, array_flip(['created_at', 'updated_at']));
            foreach (array_keys($record) as $key) {
                if (!in_array($key, $headers, true)) {
                    $headers[] = $key;
                }
            }
        }
        if (empty($headers)) {
            $headers = ['id', 'name'];
        }
        foreach ($records as $record) {
            $record = array_diff_key($record, array_flip(['created_at', 'updated_at']));
            $row = [];
            foreach ($headers as $header) {
                $row[$header] = in_array($header, ['date', 'due_date', 'valid_until', 'created_at', 'updated_at'], true)
                    ? format_display_date($record[$header] ?? '')
                    : ($record[$header] ?? '');
            }
            $rows[] = $row;
        }

        $sheetName = preg_replace('/[^A-Za-z0-9 _-]/', '', $MODULES[$moduleName]['label'] ?? ucfirst($moduleName));
        $sheetName = substr(trim($sheetName) ?: ucfirst($moduleName), 0, 31);
        $baseSheetName = $sheetName;
        $suffix = 2;
        while (in_array($sheetName, array_column($sheetFiles, 'name'), true)) {
            $suffixText = ' (' . $suffix++ . ')';
            $sheetName = substr($baseSheetName, 0, 31 - strlen($suffixText)) . $suffixText;
        }
        $sheetFiles[] = ['name' => $sheetName, 'xml' => xlsx_sheet_xml($headers, $rows), 'id' => count($sheetFiles) + 1];
    }

    $relsXml = <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>
  <Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>
</Relationships>
XML;
    $zip->addFromString('_rels/.rels', $relsXml);

    $coreXml = <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:dcmitype="http://purl.org/dc/dcmitype/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <dc:creator>PRISHA AYURVEDIC ERP</dc:creator>
    <cp:lastModifiedBy>PRISHA AYURVEDIC ERP</cp:lastModifiedBy>
  <dcterms:created xsi:type="dcterms:W3CDTF">2025-01-01T00:00:00Z</dcterms:created>
  <dcterms:modified xsi:type="dcterms:W3CDTF">2025-01-01T00:00:00Z</dcterms:modified>
</cp:coreProperties>
XML;
    $zip->addFromString('docProps/core.xml', $coreXml);

    $appXml = <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties" xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes">
    <Application>PRISHA AYURVEDIC ERP</Application>
</Properties>
XML;
    $zip->addFromString('docProps/app.xml', $appXml);

    $contentTypes = <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
  <Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
  <Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>
  <Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>
XML;
    foreach ($sheetFiles as $sheet) {
        $contentTypes .= '<Override PartName="/xl/worksheets/sheet' . $sheet['id'] . '.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>';
    }
    $contentTypes .= '</Types>';
    $zip->addFromString('[Content_Types].xml', $contentTypes);

    $sheetNodes = '';
    $rels = <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
XML;
    foreach ($sheetFiles as $sheet) {
        $sheetNodes .= '<sheet name="' . xlsx_escape($sheet['name']) . '" sheetId="' . $sheet['id'] . '" r:id="rId' . ($sheet['id'] + 2) . '"/>';
        $rels .= '<Relationship Id="rId' . ($sheet['id'] + 2) . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet' . $sheet['id'] . '.xml"/>';
    }
    $rels .= '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>';
    $rels .= '</Relationships>';

    $workbookXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets>' . $sheetNodes . '</sheets></workbook>';
    $zip->addFromString('xl/workbook.xml', $workbookXml);
    $zip->addFromString('xl/_rels/workbook.xml.rels', $rels);

    $stylesXml = <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <fonts count="1"><font><sz val="11"/><name val="Calibri"/></font></fonts>
  <fills count="1"><fill><patternFill patternType="none"/></fill></fills>
  <borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>
  <cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>
  <cellXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/></cellXfs>
  <cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>
</styleSheet>
XML;
    $zip->addFromString('xl/styles.xml', $stylesXml);

    foreach ($sheetFiles as $sheet) {
        $zip->addFromString('xl/worksheets/sheet' . $sheet['id'] . '.xml', $sheet['xml']);
    }

    $zip->close();
    return $zipPath;
}

if (!$download) {
    $dateRanges = [
        'all' => 'All Records',
        'today' => 'Today',
        'yesterday' => 'Yesterday',
        '7days' => 'Last 7 Days',
        '30days' => 'Last 30 Days',
        'month' => 'This Month',
        'year' => 'This Year',
        'lastyear' => 'Last Year',
        'custom' => 'Custom Range',
    ];

    echo '<!DOCTYPE html>'
        . '<html lang="en">'
        . '<head>'
        . '<meta charset="UTF-8">'
        . '<meta name="viewport" content="width=device-width, initial-scale=1.0">'
        . '<title>Export Data</title>'
        . '<style>'
        . 'body{font-family:Segoe UI,sans-serif;background:#f3f6fa;margin:0;padding:30px;color:#252A34}'
        . '.box{max-width:520px;margin:40px auto;background:#fff;border:1px solid #e3e8ef;border-radius:12px;box-shadow:0 1px 3px rgba(37,42,52,.08)}'
        . '.head{padding:20px 22px;border-bottom:1px solid #e3e8ef;font-size:18px;font-weight:700}'
        . '.body{padding:22px}'
        . '.field{display:grid;gap:8px;margin-bottom:16px}'
        . '.label{font-size:13px;color:#6B7280;font-weight:600}'
        . '.input,.select{padding:10px 12px;border:1px solid #e3e8ef;border-radius:8px;font-size:14px;width:100%}'
        . '.row{display:grid;grid-template-columns:1fr 1fr;gap:12px}'
        . '.btn{background:#2F7D32;color:#fff;border:none;border-radius:8px;padding:11px 18px;font-size:14px;font-weight:600;cursor:pointer}'
        . '.btn:hover{background:#256628}'
        . '.muted{font-size:12px;color:#6B7280;margin-top:6px}'
        . '</style>'
        . '</head>'
        . '<body>'
        . '<div class="box">'
        . '<div class="head">Export Data</div>'
        . '<div class="body">'
        . '<form method="get" action="export.php">'
        . '<input type="hidden" name="download" value="1">'
        . '<div class="field">'
        . '<div class="label">Module</div>'
        . '<select class="select" name="module">'
        . '<option value="all"' . (($module === 'all') ? ' selected' : '') . '>All Modules</option>';

    foreach ($MODULES as $key => $moduleMeta) {
        echo '<option value="' . htmlspecialchars($key, ENT_QUOTES) . '"' . (($module === $key) ? ' selected' : '') . '>' . htmlspecialchars($moduleMeta['label']) . '</option>';
    }

    echo '</select>'
        . '</div>'
        . '<div class="field">'
        . '<div class="label">Date Range</div>'
        . '<select class="select" name="range" id="rangeSelect">';

    foreach ($dateRanges as $key => $label) {
        echo '<option value="' . htmlspecialchars($key, ENT_QUOTES) . '"' . (($range === $key) ? ' selected' : '') . '>' . htmlspecialchars($label) . '</option>';
    }

    echo '</select>'
        . '</div>'
        . '<div class="row">'
        . '<div class="field">'
        . '<div class="label">From</div>'
        . '<input class="input" type="text" name="from" value="' . htmlspecialchars(format_input_date($customFrom, '')) . '" placeholder="dd/mm/yyyy" inputmode="numeric" id="fromDate">'
        . '</div>'
        . '<div class="field">'
        . '<div class="label">To</div>'
        . '<input class="input" type="text" name="to" value="' . htmlspecialchars(format_input_date($customTo, '')) . '" placeholder="dd/mm/yyyy" inputmode="numeric" id="toDate">'
        . '</div>'
        . '</div>'
        . '<div class="muted">Choose a period and export only the matching data.</div>'
        . '<div style="margin-top:18px;">'
        . '<button class="btn" type="submit">Export Excel</button>'
        . '</div>'
        . '</form>'
        . '</div>'
        . '</div>'
        . '<script>'
        . "const rangeSelect = document.getElementById('rangeSelect');"
        . "const fromDate = document.getElementById('fromDate');"
        . "const toDate = document.getElementById('toDate');"
        . "function updateRangeState(){const isCustom = rangeSelect.value === 'custom'; fromDate.disabled = !isCustom; toDate.disabled = !isCustom; if(!isCustom){fromDate.value='';toDate.value='';}}"
        . "rangeSelect.addEventListener('change', updateRangeState);"
        . "updateRangeState();"
        . '</script>'
        . '</body>'
        . '</html>';
    exit;
}

$folder = export_workbook_for_modules($moduleList, $range, $customFrom, $customTo);
if ($folder === false) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="export.csv"');
    echo "error,export_failed\n";
    exit;
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . export_file_name($module, $range, $customFrom, $customTo) . '"');
readfile($folder);
unlink($folder);
exit;
