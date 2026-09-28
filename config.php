<?php
/**
 * My Metaji - Main Configuration
 * Brand: #2563EB (Blue) | Dark: #111827
 */

// ============ BRAND SETTINGS ============
define('BRAND_NAME', 'My Metaji');
define('BRAND_TAGLINE', 'India\'s Trusted GST Billing Software for MSMEs');
define('BRAND_DESCRIPTION', 'Best GST Billing Software for Small Businesses');

// ============ BRAND COLORS ============
$colors = [
    'primary'          => '#2563EB',
    'primary_dark'     => '#1D4ED8',
    'primary_light'    => '#3B82F6',
    'primary_rgb'      => '37, 99, 235',
    'accent'           => '#3B82F6',
    'accent_light'     => '#60A5FA',
    'black'            => '#111827',
    'dark'             => '#0F172A',
    'dark_rgb'         => '15, 23, 42',
    'white'            => '#FFFFFF',
    'gray_light'       => '#F8FAFC',
    'gray'             => '#64748B',
    'gray_dark'        => '#475569',
    'text'             => '#1E293B',
    'border'           => '#E2E8F0',
    'success'          => '#10B981',
    'warning'          => '#F59E0B',
    'error'            => '#EF4444',
    'info'             => '#2563EB',
    'gradient_primary' => 'linear-gradient(135deg, #1E40AF, #2563EB)',
    'gradient_accent'  => 'linear-gradient(135deg, #2563EB, #3B82F6)',
    'gradient_card'    => 'linear-gradient(135deg, #F8FAFC, #F1F5F9)',
];

// ============ FONT SIZES ============
$fonts = [
    'font_family'     => "'Inter', 'Poppins', sans-serif",
    'xs'              => '11px',
    'sm'              => '12px',
    'base'            => '13px',
    'md'              => '14px',
    'lg'              => '15px',
    'xl'              => '16px',
    '2xl'             => '17px',
    '3xl'             => '20px',
    '4xl'             => '24px',
    '5xl'             => '28px',
    '6xl'             => '30px',
    '7xl'             => '34px',
    '8xl'             => '40px',
    '9xl'             => '44px',
    '10xl'            => '50px',
    'weight_normal'   => '400',
    'weight_medium'   => '500',
    'weight_semi'     => '600',
    'weight_bold'     => '700',
    'weight_extra'    => '800',
    'leading_tight'   => '1.2',
    'leading_normal'  => '1.5',
    'leading_relaxed' => '1.6',
    'leading_loose'   => '1.7',
];

// ============ SITE URLs ============
$site = [
    'url'         => 'https://mymetaji.com',
    'assets'      => 'https://mymetaji.com/assets',
    'images'      => 'https://mymetaji.com/assets/images',
    'css'         => 'https://mymetaji.com/assets/css',
    'js'          => 'https://mymetaji.com/assets/js',
];
define('SITE_URL', $site['url']);

// ============ CONTACT INFO ============
$contact = [
    'phone1'     => '+91-97732-16606',
    // Ke dojri number nathi. Khaali rakhelo che etle phone2 vali jagya
    // (about.php, contact.php) mate guard mukya che.
    'phone2'     => '',
    'whatsapp'   => '+91-6366-827-420',
    'email'      => 'info.mymetaji@gmail.com',
    'address'    => '218 Sahjanand Business Hub, Opp. JIO Petrol Pump, Near Savaliya Circle, Yogi Chowk, Surat',
    'hours'      => 'Mon-Sat 9AM-9PM | Sunday 10AM-7PM',
];

// ============ SOCIAL LINKS ============
$social = [
    'facebook'  => '#',
    'twitter'   => '#',
    'linkedin'  => '#',
    'youtube'   => '#',
    'instagram' => '#',
];

// ============ NAVIGATION ============
$nav_links = [
    ['label' => 'Home', 'url' => '#'],
    ['label' => 'Features', 'url' => '#features'],
    ['label' => 'Industries', 'url' => '#industries'],
    ['label' => 'Pricing', 'url' => '#pricing'],
    ['label' => 'Contact', 'url' => '#contact'],
];

$solutions_menu = [
    ['label' => 'Billing Software', 'url' => '#'],
    ['label' => 'Accounting Software', 'url' => '#'],
    ['label' => 'Inventory Management', 'url' => '#'],
    ['label' => 'Invoicing Software', 'url' => '#'],
    ['label' => 'POS Software', 'url' => '#'],
    ['label' => 'E-Invoice', 'url' => '#'],
];

$industries_menu = [
    ['label' => 'Retail', 'url' => 'retail-software.php'],
    ['label' => 'Grocery', 'url' => 'grocery-software.php'],
    ['label' => 'Pharmacy', 'url' => 'pharmacy-software.php'],
    ['label' => 'Restaurant', 'url' => 'restaurant-software.php'],
    ['label' => 'Jewellery', 'url' => 'jewellery-software.php'],
    ['label' => 'Clothing', 'url' => 'clothing-software.php'],
];

// ============ STATS DATA ============
$stats = [
    ['value' => '50,000+', 'label' => 'Businesses Using My Metaji'],
    ['value' => '5,00,000+', 'label' => 'Bills Created Today'],
    ['value' => '10,000+', 'label' => 'GST Returns Prepared'],
    ['value' => '100%', 'label' => 'GST Calculation Accuracy'],
];

// ============ FEATURES DATA ============
$features = [
    [
        'icon' => '📄',
        'title' => 'GST-Ready Billing',
        'desc' => 'Create GST-compliant invoices with auto tax calculation. Generate e-invoices and e-way bills instantly.'
    ],
    [
        'icon' => '📦',
        'title' => 'Inventory Management',
        'desc' => 'Track stock levels in real-time, manage items efficiently with low stock alerts and barcode support.'
    ],
    [
        'icon' => '📊',
        'title' => 'Accounting & Reports',
        'desc' => 'Get complete business insights with powerful reports — profit/loss, balance sheets, and more.'
    ],
    [
        'icon' => '💳',
        'title' => 'Multiple Payment Options',
        'desc' => 'Accept cash, UPI, card, or bank transfer. Record all payments properly in bills automatically.'
    ],
    [
        'icon' => '📱',
        'title' => 'Multi-Device Sync',
        'desc' => 'Manage your business from mobile and desktop. Data syncs instantly via secure cloud.'
    ],
    [
        'icon' => '🔗',
        'title' => 'WhatsApp Integration',
        'desc' => 'Send bills and payment reminders directly to customers via WhatsApp. Collect payments faster.'
    ],
    [
        'icon' => '📶',
        'title' => 'Offline Mode',
        'desc' => 'Internet down? No problem. Your billing keeps working. Data syncs automatically when connected.'
    ],
    [
        'icon' => '🏷️',
        'title' => 'Barcode Generator',
        'desc' => 'Quickly add products to bills using barcode scanning. Reduce manual effort and improve speed.'
    ],
    [
        'icon' => '🧾',
        'title' => 'Customisable Invoices',
        'desc' => 'Choose from various bill formats. Add your shop name, logo, and details for branded invoices.'
    ],
];

// ============ INDUSTRIES DATA ============
$industries = [
    ['icon' => '🏪', 'title' => 'Retail & Kirana', 'desc' => 'Manage daily sales, GST bills & inventory', 'link' => '#'],
    ['icon' => '🛒', 'title' => 'Grocery Store', 'desc' => 'Fast billing & stock tracking', 'link' => '#'],
    ['icon' => '💊', 'title' => 'Pharmacy', 'desc' => 'Medicine billing & expiry tracking', 'link' => '#'],
    ['icon' => '🍽️', 'title' => 'Restaurant', 'desc' => 'Order billing & menu management', 'link' => '#'],
    ['icon' => '💍', 'title' => 'Jewellery', 'desc' => 'Gold/silver billing & stock control', 'link' => '#'],
    ['icon' => '👗', 'title' => 'Clothing & Apparel', 'desc' => 'Size, colour & variant billing', 'link' => '#'],
    ['icon' => '⚡', 'title' => 'Electronics', 'desc' => 'Product tracking & warranty billing', 'link' => '#'],
    ['icon' => '🏭', 'title' => 'Manufacturing', 'desc' => 'Production & raw material tracking', 'link' => '#'],
];

// ============ INDUSTRY PAGES ============
// One dedicated page per industry, e.g. retail-software.php. Each root file is a
// 5-line stub that sets $industry_slug and then requires
// includes/industry-page.php, so the copy lives here once and the pages, the
// nav, the footer and the sitemap are all driven from this one list.
//
// Add an industry here and drop in a matching stub file; nothing else to edit.
$industry_pages = [
    [
        'slug'  => 'retail',
        'file'  => 'retail-software.php',
        'icon'  => '🏪',
        'tag'   => 'Retail',
        'nav'   => 'Retail',
        'title' => 'Billing Software for Retail & Kirana Stores',
        'short' => 'Counter billing, barcode scanning, customer payments and daily sales reports',
        'desc'  => 'Billing and inventory software for retail and kirana shops. Fast GST counter billing, barcode scanning, customer payment tracking and daily sales reports in one app.',
        'lead'  => 'Bill at the counter in seconds, scan a barcode instead of typing the item name, and see what sold today and what is about to run out.',
        'features' => [
            ['icon' => '🧾', 'title' => 'Fast counter billing', 'desc' => 'Create a GST invoice in a few taps, with HSN lookup, tax slabs and discounts applied for you. Works on a phone, so the counter never waits.'],
            ['icon' => '🏷️', 'title' => 'Barcode scanning', 'desc' => 'Scan the barcode to pull up the item, price and tax rate, then add it to the bill. No typing, no wrong prices.'],
            ['icon' => '📊', 'title' => 'Daily sales reports', 'desc' => 'See the day\'s sales, profit and top-selling items the moment you close the shop, on any device.'],
            ['icon' => '👥', 'title' => 'Customer payment tracking', 'desc' => 'Record part payments and credit sales against the customer, so you always know who still owes what.'],
        ],
    ],
    [
        'slug'  => 'grocery',
        'file'  => 'grocery-software.php',
        'icon'  => '🛒',
        'tag'   => 'Grocery',
        'nav'   => 'Grocery',
        'title' => 'Billing Software for Grocery Stores',
        'short' => 'Weight-based billing, expiry tracking and high-volume checkout',
        'desc'  => 'Grocery store billing software with weight-based billing, expiry date tracking, fast checkout and stock management for kirana and supermarket counters.',
        'lead'  => 'Built for queues at the counter: bill by weight or piece, catch expiry dates before they become waste, and keep thousands of SKUs in order.',
        'features' => [
            ['icon' => '⚖️', 'title' => 'Weight-based billing', 'desc' => 'Enter the weight and the rate; the bill total, GST and round-off are calculated for you. Ideal for vegetables, pulses and loose goods.'],
            ['icon' => '📅', 'title' => 'Expiry date tracking', 'desc' => 'Track batch and expiry on every item and get alerted before stock goes past its date, so you discount it in time instead of writing it off.'],
            ['icon' => '⚡', 'title' => 'Fast checkout', 'desc' => 'Keyboard-friendly billing, saved customer carts and quick item lookup keep the counter moving even during evening rush.'],
            ['icon' => '📦', 'title' => 'Stock management', 'desc' => 'Low stock alerts, purchase entries and supplier records for the whole shop, from a single pack of sugar to full warehouse racks.'],
        ],
    ],
    [
        'slug'  => 'pharmacy',
        'file'  => 'pharmacy-software.php',
        'icon'  => '💊',
        'tag'   => 'Pharmacy',
        'nav'   => 'Pharmacy',
        'title' => 'Billing Software for Pharmacy & Medical Stores',
        'short' => 'Medicine billing at MRP, expiry alerts and Schedule H tracking',
        'desc'  => 'Pharmacy billing software for chemists and medical stores: medicine billing at MRP, expiry date alerts, Schedule H tracking and GST reports for medicines.',
        'lead'  => 'Sell at MRP without breaking GST rules, keep every batch and expiry under control, and produce the medicine reports a chemist is expected to keep.',
        'features' => [
            ['icon' => '💊', 'title' => 'Medicine billing at MRP', 'desc' => 'Bill at MRP with the correct HSN and GST treatment for every schedule, and print or WhatsApp the bill instantly.'],
            ['icon' => '📅', 'title' => 'Expiry date alerts', 'desc' => 'Near-expiry and expired stock is flagged on the dashboard, with batch numbers and purchase dates kept for every item.'],
            ['icon' => '📋', 'title' => 'Schedule H tracking', 'desc' => 'Record Schedule H and H1 sales with the prescription register, so the register your inspector asks for is already there.'],
            ['icon' => '📑', 'title' => 'GST reports for medicines', 'desc' => 'Hsn-wise purchase and sales registers, and GST returns data that match what you actually sold, not what you remember.'],
        ],
    ],
    [
        'slug'  => 'restaurant',
        'file'  => 'restaurant-software.php',
        'icon'  => '🍽️',
        'tag'   => 'Restaurant',
        'nav'   => 'Restaurant',
        'title' => 'Billing Software for Restaurants & Food Business',
        'short' => 'Table-wise billing, menu management, KOT and daily sales summary',
        'desc'  => 'Restaurant billing software for hotels, cafes and food businesses: table-wise billing, menu management, kitchen order tickets and daily sales summary with GST.',
        'lead'  => 'Take the order at the table, send it to the kitchen in one tap, and split the bill the way the guests actually want to pay it.',
        'features' => [
            ['icon' => '🪑', 'title' => 'Table-wise billing', 'desc' => 'Bill per table, merge tables when guests move, split the bill between two or three payers, all from the same order.'],
            ['icon' => '📖', 'title' => 'Menu management', 'desc' => 'Update prices, mark items sold out or available today, and add taxes or a service charge to the menu itself rather than to each bill.'],
            ['icon' => '🧾', 'title' => 'KOT kitchen order tickets', 'desc' => 'Send the order straight to the kitchen as a printed or digital KOT, so what was ordered and what was served always match.'],
            ['icon' => '📊', 'title' => 'Daily sales summary', 'desc' => 'Item-wise sales, peak hours and payment mix for the day, so you can plan staffing and stock for tomorrow.'],
        ],
    ],
    [
        'slug'  => 'jewellery',
        'file'  => 'jewellery-software.php',
        'icon'  => '💍',
        'tag'   => 'Jewellery',
        'nav'   => 'Jewellery',
        'title' => 'Billing Software for Jewellery Shops',
        'short' => 'Gold and silver rate management, making charges and hallmark tracking',
        'desc'  => 'Jewellery billing software for gold and silver shops: daily rate management, making charge calculation, weight-based billing and hallmark tracking with GST.',
        'lead'  => 'Bill gold on the rate of the day, add making charges and GST the way your customers expect, and keep hallmarked stock traceable piece by piece.',
        'features' => [
            ['icon' => '💰', 'title' => 'Gold/silver rate management', 'desc' => 'Set the day\'s rate once and every bill, estimate and quotation for that metal prices itself. Update it tomorrow without touching old bills.'],
            ['icon' => '🧮', 'title' => 'Making charge calculations', 'desc' => 'Add making charges, wastage and GST per item or per bill, and print a bill that shows the customer exactly what they are paying for.'],
            ['icon' => '⚖️', 'title' => 'Weight-based billing', 'desc' => 'Gross weight, net weight, rate per gram and a clean total, which is the format every jewellery customer expects to see.'],
            ['icon' => '🏅', 'title' => 'Hallmark tracking', 'desc' => 'Record the hallmark number against the piece and keep it linked to its bill, so a return, exchange or buy-back is traceable.'],
        ],
    ],
    [
        'slug'  => 'clothing',
        'file'  => 'clothing-software.php',
        'icon'  => '👗',
        'tag'   => 'Apparel',
        'nav'   => 'Clothing',
        'title' => 'Billing Software for Clothing & Apparel Stores',
        'short' => 'Size and colour variants, barcoded garments and returns handling',
        'desc'  => 'Clothing store billing software with size and colour variant management, barcoded garments, seasonal stock handling and return or exchange tracking.',
        'lead'  => 'Every size and colour is a real item with its own barcode and stock, so the counter bills the right variant first time and returns stop being guesswork.',
        'features' => [
            ['icon' => '🧵', 'title' => 'Size/colour variant management', 'desc' => 'Create M, L, XL and every colour as separate stock items, each with its own price, barcode and stock count.'],
            ['icon' => '📆', 'title' => 'Seasonal stock handling', 'desc' => 'Plan for a festive season or a new collection with purchase entries, arrivals notes and slow-moving stock reports.'],
            ['icon' => '🏷️', 'title' => 'Barcode for garments', 'desc' => 'Print and scan garment tags so the counter picks the exact size and colour instead of reading it off a hanger.'],
            ['icon' => '↩️', 'title' => 'Return/exchange tracking', 'desc' => 'Record exchanges and returns against the original bill, so stock goes back in and the sale stays accurate.'],
        ],
    ],
];

// ============ SEO: SERVICES ============
// Drives the nationwide service pages: seo_find_service('pos-software') is what
// each of them uses for its meta description and Service JSON-LD node.
// 'page' is the national landing page this service belongs to.
$services = [
    [
        'slug'    => 'accounting-software',
        'name'    => 'Accounting Software',
        'kw'      => 'accounting software',
        'page'    => 'accounting-software.php',
        'state'   => 'Gujarat',
        'short'   => 'Automated profit & loss, balance sheet and cash flow reports',
        'lead'    => 'Track profit & loss, balance sheets and cash flow without hiring a chartered accountant.',
        'desc'    => 'Simple accounting software for Indian businesses. Auto profit & loss, balance sheet, cash flow and expense tracking. Works offline.',
        'features' => [
            ['icon' => '💰', 'title' => 'Profit & Loss Report',  'desc' => 'Automated P&L that updates with every bill, purchase and expense. No spreadsheets.'],
            ['icon' => '📋', 'title' => 'Auto Balance Sheet',    'desc' => 'Assets, liabilities and equity prepared automatically in Schedule III format.'],
            ['icon' => '💵', 'title' => 'Cash Flow Tracking',    'desc' => 'See every rupee coming in and going out, updated live as you bill.'],
            ['icon' => '📑', 'title' => 'Expense Categorisation','desc' => 'Tag every expense to a head so you know exactly where the money went.'],
            ['icon' => '🔄', 'title' => 'Receivables & Payables', 'desc' => 'Know who owes you, who you owe, and follow up on every pending amount.'],
            ['icon' => '📊', 'title' => 'GST Summary Reports',  'desc' => 'GSTR-1 and GSTR-3B ready summaries exported in one click.'],
        ],
    ],
    [
        'slug'    => 'billing-software',
        'name'    => 'GST Billing Software',
        'kw'      => 'GST billing software',
        'page'    => 'billing-software.php',
        'state'   => 'Gujarat',
        'short'   => 'GST-compliant invoices with auto CGST/SGST/IGST in seconds',
        'lead'    => 'Create GST-compliant bills in seconds — barcode scanning, HSN lookup and WhatsApp sharing built in.',
        'desc'    => 'GST billing software for Indian shops. Create compliant invoices, auto-calculate CGST/SGST/IGST, share on WhatsApp. Works offline.',
        'features' => [
            ['icon' => '🧾', 'title' => 'GST Invoice Builder',  'desc' => 'Tax invoices with HSN/SAC, unit, batch and reverse charge — every field the CGST Act asks for.'],
            ['icon' => '🏷️', 'title' => 'Barcode Billing',      'desc' => 'Scan and bill in one second. Print item barcodes or scan supplier codes.'],
            ['icon' => '🔢', 'title' => 'Auto GST Calculation', 'desc' => 'CGST, SGST and IGST picked automatically from place of supply. No rounding errors.'],
            ['icon' => '📱', 'title' => 'Bill on WhatsApp',     'desc' => 'Send the invoice PDF to your customer on WhatsApp the moment it is saved.'],
            ['icon' => '🖨️', 'title' => 'Thermal Print Support', 'desc' => 'Print on any 58mm or 80mm thermal printer, or export a branded A4 invoice.'],
            ['icon' => '📉', 'title' => 'Day-End Sales Report', 'desc' => 'Close the day with total sales, tax collected, discounts and cash position.'],
        ],
    ],
    [
        'slug'    => 'inventory-software',
        'name'    => 'Inventory Management Software',
        'kw'      => 'inventory management software',
        'page'    => 'inventory-software.php',
        'state'   => 'Gujarat',
        'short'   => 'Live stock, batch and expiry tracking with low-stock alerts',
        'lead'    => 'Live stock for every item, batch and godown — with low-stock alerts before you run out.',
        'desc'    => 'Inventory management software for Indian businesses. Real-time stock, batch & expiry tracking, low-stock alerts, multi-godown support.',
        'features' => [
            ['icon' => '📦', 'title' => 'Real-Time Stock',      'desc' => 'Every sale and purchase updates stock instantly across all counters.'],
            ['icon' => '🏷️', 'title' => 'Barcode & SKU Library', 'desc' => 'One item master with barcode, HSN, unit of measure and multiple price slabs.'],
            ['icon' => '📅', 'title' => 'Batch & Expiry',       'desc' => 'FIFO batch tracking with expiry alerts — essential for pharma and food.'],
            ['icon' => '🏬', 'title' => 'Multi-Godown',         'desc' => 'Transfer stock between shops or godowns with a full movement trail.'],
            ['icon' => '⚠️', 'title' => 'Low-Stock Alerts',     'desc' => 'Set a reorder level per item and get warned before the shelf goes empty.'],
            ['icon' => '💰', 'title' => 'Stock Valuation',      'desc' => 'Valued stock by moving average or FIFO, matching your accounting method.'],
        ],
    ],
    [
        'slug'    => 'gst-software',
        'name'    => 'GST Software',
        'kw'      => 'GST software',
        'page'    => 'billing-software.php',
        'state'   => 'Gujarat',
        'short'   => 'GST invoices, e-invoices, e-way bills and return-ready reports',
        'lead'    => 'One place for invoices, e-invoices, e-way bills and return-ready GST reports.',
        'desc'    => 'All-in-one GST software for Indian businesses. GST invoices, e-invoicing, e-way bills, HSN summary and return-ready reports.',
        'features' => [
            ['icon' => '🇮🇳', 'title' => 'GST Invoice & IRN',   'desc' => 'Generate IRN-enabled e-invoices and share the signed PDF with your customer.'],
            ['icon' => '🚚', 'title' => 'E-Way Bill Generation', 'desc' => 'Create e-way bills for consignments above the notified threshold, in bulk.'],
            ['icon' => '🔍', 'title' => 'HSN Wise Summary',    'desc' => 'Rate-wise and HSN-wise tax summary, exactly what GSTR-1 asks for.'],
            ['icon' => '📑', 'title' => 'GSTR-1 / 3B Ready',   'desc' => 'Export reconciled data for your return without retyping a single bill.'],
            ['icon' => '🧮', 'title' => 'Input Tax Credit',    'desc' => 'Track input credit on purchases so you never over-claim or miss a refund.'],
            ['icon' => '🧾', 'title' => 'Cancellation & Credit Notes', 'desc' => 'Issue credit and debit notes with the same GST number printed correctly.'],
        ],
    ],
    [
        'slug'    => 'pos-software',
        'name'    => 'POS Software',
        'kw'      => 'POS software',
        'page'    => 'pos-software.php',
        'state'   => 'Gujarat',
        'short'   => 'Barcode counter billing with UPI, card and offline mode',
        'lead'    => 'Fast counter billing with barcode scanning, split discounts and UPI collection.',
        'desc'    => 'POS software for Indian retail counters. Barcode billing, split discounts, UPI/card payment and offline mode with real-time sync.',
        'features' => [
            ['icon' => '⚡', 'title' => 'Fast Counter Billing', 'desc' => 'Search-as-you-type and barcode scan to add items — no mouse needed.'],
            ['icon' => '🎯', 'title' => 'Flexible Discounts',   'desc' => 'Item-level, bill-level and combo discounts, all GST-correct.'],
            ['icon' => '💳', 'title' => 'UPI & Card Payment',   'desc' => 'Record UPI, card, cash and bank transfer against each bill.'],
            ['icon' => '🧾', 'title' => 'Thermal Receipts',     'desc' => '58mm and 80mm thermal printing with your shop name and GSTIN.'],
            ['icon' => '📴', 'title' => 'Works Offline',        'desc' => 'Internet down? Billing continues and syncs automatically later.'],
            ['icon' => '🔐', 'title' => 'Cashier Login',        'desc' => 'Separate logins with sales reports per cashier to stop billing leaks.'],
        ],
    ],
    [
        'slug'    => 'e-invoice-software',
        'name'    => 'E-Invoice Software',
        'kw'      => 'e-invoice software',
        'page'    => 'e-invoice.php',
        'state'   => 'Gujarat',
        'short'   => 'Generate IRN, QR code and signed e-invoice PDFs in one click',
        'lead'    => 'IRN and QR code on every invoice above the notified turnover — generated in one click.',
        'desc'    => 'E-invoice software for Indian businesses. Generate IRN, QR code and signed JSON for e-invoicing, with GST-ready PDF download.',
        'features' => [
            ['icon' => '🔗', 'title' => 'IRN & QR Generation', 'desc' => 'Register on the e-invoice portal and get IRN with QR embedded automatically.'],
            ['icon' => '📄', 'title' => 'Signed PDF Download', 'desc' => 'The IRN-bearing PDF your accountant and customer both need.'],
            ['icon' => '🔁', 'title' => 'Cancel by IRN',       'desc' => 'Cancel an e-invoice within the permitted window, straight from the bill list.'],
            ['icon' => '📊', 'title' => 'Bulk Generation',     'desc' => 'Generate e-invoices for a whole day of sales in one run.'],
            ['icon' => '🔒', 'title' => 'GSTIN Validation',    'desc' => 'Recipient GSTIN verified before the invoice is saved — no rejected IRNs.'],
            ['icon' => '🧩', 'title' => 'Offline Drafting',    'desc' => 'Draft invoices without internet; IRN is fetched the moment you reconnect.'],
        ],
    ],
    [
        'slug'    => 'invoicing-software',
        'name'    => 'Invoicing Software',
        'kw'      => 'invoicing software',
        'page'    => 'invoicing-software.php',
        'state'   => 'Gujarat',
        'short'   => 'Branded invoices, recurring billing and payment tracking',
        'lead'    => 'Send professional invoices faster, bill customers on a recurring schedule, and always know who owes you.',
        'desc'    => 'Invoicing software for Indian businesses. Branded invoices, recurring billing, payment tracking and WhatsApp delivery.',
        'features' => [
            ['icon' => '🎨', 'title' => 'Branded Invoices',   'desc' => 'Your logo, your colours, your GSTIN — an invoice that looks like it came from a real company.'],
            ['icon' => '🔁', 'title' => 'Recurring Billing',  'desc' => 'Set a monthly or yearly invoice once and let it generate on its own.'],
            ['icon' => '⏰', 'title' => 'Payment Reminders', 'desc' => 'Automatic reminders go out on WhatsApp before an invoice goes overdue.'],
            ['icon' => '📊', 'title' => 'Outstanding Report', 'desc' => 'One list of every unpaid invoice, with age and amount, sorted by urgency.'],
            ['icon' => '🔢', 'title' => 'Progressive Numbers','desc' => 'Per-financial-year invoice numbering so your books stay continuous.'],
            ['icon' => '📎', 'title' => 'PDF & Print',       'desc' => 'Download a print-ready PDF or send it straight to the customer on WhatsApp.'],
        ],
    ],
];

// ============ SEO: CITIES ============
// CITIES ARE ADDED FROM THE MASTER PANEL: /admin/master/cities.php
//
// The "City Master" screen adds, renames, reorders and deletes cities, and
// saves them to data/cities.json. This line reads that file, so the nav
// dropdown, footer, sitemap and the generated service pages all follow
// automatically - there is nothing to copy into this file and no build step.
//
// The list is saved as data/cities.json:
//   'slug'   the URL word - lowercase, no spaces, no dots  (chennai)
//   'name'   what visitors see                            (Chennai)
//   'state'  shown on the page and in schema              (Tamil Nadu)
//
// The default 20 cities live in city_master_seed() in includes/city-master.php.
// They are used until the panel saves for the first time, and they are what
// "Restore default list" puts back, so leave that function alone.
require_once __DIR__ . '/includes/city-master.php';
$cities = city_master_all();

// ============ SEO: STATIC PAGES FOR SITEMAP ============
$seo_static_pages = [
    ['file' => 'index.php', 'priority' => '1.0', 'freq' => 'weekly'],
    ['file' => 'features.php',             'file_ignore' => '',            'priority' => '0.9', 'freq' => 'monthly'],
    ['file' => 'pricing.php',              'file_ignore' => '',            'priority' => '0.9', 'freq' => 'monthly'],
    ['file' => 'trial-signup.php',         'file_ignore' => '',            'priority' => '0.9', 'freq' => 'monthly'],
    // Enterprise is the only tier sold by the sales team, so this is the page
    // that has to be findable. The self-serve checkout and the success page are
    // deliberately absent: one-time, no value in an index.
    ['file' => 'enterprise.php',           'file_ignore' => '',            'priority' => '0.7', 'freq' => 'monthly'],
    ['file' => 'industries.php',           'file_ignore' => '',            'priority' => '0.8', 'freq' => 'monthly'],
    ['file' => 'about.php', 'priority' => '0.6', 'freq' => 'yearly'],
    ['file' => 'contact.php',              'file_ignore' => '',            'priority' => '0.6', 'freq' => 'yearly'],
    ['file' => 'partner.php',              'file_ignore' => '',            'priority' => '0.5', 'freq' => 'monthly'],
    ['file' => 'accounting-software.php',  'file_ignore' => '',            'priority' => '0.9', 'freq' => 'monthly'],
    ['file' => 'billing-software.php',     'file_ignore' => '',            'priority' => '0.9', 'freq' => 'monthly'],
    ['file' => 'inventory-software.php',   'file_ignore' => '',            'priority' => '0.9', 'freq' => 'monthly'],
    ['file' => 'invoicing-software.php',   'file_ignore' => '',            'priority' => '0.8', 'freq' => 'monthly'],
    ['file' => 'pos-software.php',         'file_ignore' => '',            'priority' => '0.8', 'freq' => 'monthly'],
    ['file' => 'e-invoice.php',            'file_ignore' => '',            'priority' => '0.8', 'freq' => 'monthly'],
    ['file' => 'gst-calculator.php',       'file_ignore' => '',            'priority' => '0.8', 'freq' => 'monthly'],
    ['file' => 'invoice-generator.php',    'file_ignore' => '',            'priority' => '0.7', 'freq' => 'monthly'],
    ['file' => 'quotation-maker.php',      'file_ignore' => '',            'priority' => '0.7', 'freq' => 'monthly'],
    ['file' => 'receipt-maker.php',        'file_ignore' => '',            'priority' => '0.7', 'freq' => 'monthly'],
    ['file' => 'purchase-order.php',       'file_ignore' => '',            'priority' => '0.7', 'freq' => 'monthly'],
    ['file' => 'proforma-invoice.php',     'file_ignore' => '',            'priority' => '0.7', 'freq' => 'monthly'],
    ['file' => 'qr-generator.php',         'file_ignore' => '',            'priority' => '0.6', 'freq' => 'monthly'],
    ['file' => 'barcode-generator.php',    'file_ignore' => '',            'priority' => '0.6', 'freq' => 'monthly'],
    ['file' => 'invoice-format.php',       'file_ignore' => '',            'priority' => '0.7', 'freq' => 'monthly'],
    ['file' => 'quotation-format.php',     'file_ignore' => '',            'priority' => '0.6', 'freq' => 'monthly'],
    ['file' => 'estimate-format.php',      'file_ignore' => '',            'priority' => '0.6', 'freq' => 'monthly'],
    ['file' => 'receipt-format.php',       'file_ignore' => '',            'priority' => '0.6', 'freq' => 'monthly'],
    ['file' => 'bill-book-format.php',     'file_ignore' => '',            'priority' => '0.6', 'freq' => 'monthly'],
    ['file' => 'credit-note.php',          'file_ignore' => '',            'priority' => '0.5', 'freq' => 'monthly'],
    ['file' => 'debit-note.php',           'file_ignore' => '',            'priority' => '0.5', 'freq' => 'monthly'],
    ['file' => 'delivery-challan.php',     'file_ignore' => '',            'priority' => '0.5', 'freq' => 'monthly'],
    ['file' => 'balance-sheet.php',        'file_ignore' => '',            'priority' => '0.6', 'freq' => 'monthly'],
    ['file' => 'work-order.php',           'file_ignore' => '',            'priority' => '0.5', 'freq' => 'monthly'],
    ['file' => 'terms-conditions.php',     'file_ignore' => '',            'priority' => '0.3', 'freq' => 'yearly'],
    ['file' => 'privacy-policy.php',       'file_ignore' => '',            'priority' => '0.3', 'freq' => 'yearly'],
    ['file' => 'refund-policy.php',        'file_ignore' => '',            'priority' => '0.3', 'freq' => 'yearly'],
];

// ============ TESTIMONIALS DATA ============
$testimonials = [
    [
        'name' => 'Rajesh Kumar',
        'role' => 'Gift Shop Owner, Delhi',
        'initial' => 'R',
        'text' => 'Switched to My Metaji and now I can manage GST billing, barcode billing, and inventory in one place. It\'s a lifesaver!',
        'stars' => 5
    ],
    [
        'name' => 'Priya Sharma',
        'role' => 'Pharmacy Chain, Mumbai',
        'initial' => 'P',
        'text' => 'Been using My Metaji for 2 years to manage billing and item tracking across my 3 branches. Excellent software!',
        'stars' => 5
    ],
    [
        'name' => 'Amit Patel',
        'role' => 'Grocery Store Owner, Ahmedabad',
        'initial' => 'A',
        'text' => 'The offline mode is fantastic! Even when internet goes down in our area, billing never stops. Data syncs perfectly.',
        'stars' => 5
    ],
];

// ============ PRICING DATA ============
// The tiers themselves live in includes/plan-data.php so the admin panel can
// read the prices without loading this file (which also regenerates the city
// pages). See that file for how to add a tier.
//
// plans.php holds the price helpers; the FAQ below quotes an amount, so it has to
// be loaded here. It only defines functions.
require_once __DIR__ . '/includes/plans.php';
$pricing  = require __DIR__ . '/includes/plan-data.php';
$plans    = $pricing['plans'];
$plan_groups = $pricing['groups'];
// ============ FAQ DATA ============
// After $plans, because the pricing answer quotes the real entry price.
$faqs = [
    [
        'question' => 'What is My Metaji billing software?',
        'answer' => 'My Metaji is a complete billing software for small businesses in India. It helps you create GST invoices, manage inventory, track payments, and handle accounting — all from one platform.'
    ],
    [
        'question' => 'How much does My Metaji cost?',
        'answer' => 'It starts at ' . plan_price_string($plans[0], 'monthly') . ' a month (' . plan_price_string($plans[0], 'yearly') . ' a year) on the ' . $plans[0]['name'] . ' plan, which covers dashboard, customers, products, invoices and quotations. Every plan can be paid monthly or yearly, and the yearly price is roughly two months free.'
    ],
    [
        'question' => 'Can I use My Metaji offline?',
        'answer' => 'Yes, My Metaji works both offline and online. You can create bills without internet, and data syncs automatically when you\'re back online.'
    ],
    [
        'question' => 'Does My Metaji support GST billing?',
        'answer' => 'Absolutely! My Metaji auto-calculates GST on every bill, applies correct tax rates, and generates GST-ready reports. No accounting expertise required.'
    ],
    [
        'question' => 'Which devices does My Metaji work on?',
        'answer' => 'My Metaji works on Android, iOS, Windows, and Mac. You can manage your business from any device with cloud sync keeping everything updated.'
    ],
    [
        'question' => 'Can I share bills via WhatsApp?',
        'answer' => 'Yes! My Metaji lets you create GST-compliant bills and share them instantly via WhatsApp, SMS, or PDF. You can also send automated payment reminders.'
    ],
];

// ============ FOOTER COLUMNS ============
$footer_products = [
    ['label' => 'Billing Software', 'url' => '#features'],
    ['label' => 'Accounting Software', 'url' => '#features'],
    ['label' => 'Inventory Software', 'url' => '#features'],
    ['label' => 'Invoicing Software', 'url' => '#features'],
    ['label' => 'POS Software', 'url' => '#features'],
];

$footer_industries = [
    ['label' => 'Retail', 'url' => 'retail-software.php'],
    ['label' => 'Grocery', 'url' => 'grocery-software.php'],
    ['label' => 'Pharmacy', 'url' => 'pharmacy-software.php'],
    ['label' => 'Restaurant', 'url' => 'restaurant-software.php'],
    ['label' => 'Jewellery', 'url' => 'jewellery-software.php'],
    ['label' => 'Clothing', 'url' => 'clothing-software.php'],
];

$footer_company = [
    ['label' => 'About Us', 'url' => '#about'],
    ['label' => 'Contact Us', 'url' => '#contact'],
    ['label' => 'Blog', 'url' => '#', 'title' => 'Coming Soon'],
    ['label' => 'Careers', 'url' => '#', 'title' => 'Coming Soon'],
    ['label' => 'Partner with Us', 'url' => 'partner.php'],
];

// ============ CITY PAGE FILES ============
// Runs last, once $services and $cities above are both defined. Writes one
// .php file per service x city (e.g. inventory-software-vadodara.php) and
// prunes any that no longer match config.php. Nothing to run by hand.
require_once __DIR__ . '/includes/city-pages.php';
?>
