<?php
/**
 * MY METAJI - Shared small helpers.
 */
if (!function_exists('badge_class')) {
    /**
     * Maps a status label to a badge colour.
     *
     * Money statuses are kept deliberately distinct so a list can be read at a
     * glance: Paid (green) is settled, Partly Paid (amber) is money still
     * outstanding but the invoice moved, Pending (blue) has not been touched
     * yet, and Overdue (red) is past its due date. Inactive/cancelled/dead
     * states stay neutral gray so a red badge always means "act on this".
     *
     * The comparison below is case-insensitive because the same label can be
     * written as "Partly Paid", "Partially Paid" or "Partly paid" depending on
     * which screen wrote the record.
     */
    function badge_class($value) {
        $status = trim((string)$value);
        $key = strtolower(preg_replace('/\s+/', ' ', $status));

        $map = [
            'paid'             => 'green',
            'fully paid'       => 'green',
            'done'             => 'green',
            'completed'        => 'green',
            'accepted'         => 'green',
            'delivered'        => 'green',
            'approved'         => 'green',
            'converted'        => 'green',
            'converted to invoice' => 'green',
            'won'              => 'green',
            'active'           => 'green',

            'overdue'          => 'red',
            'over due'         => 'red',

            'partly paid'      => 'amber',
            'partially paid'   => 'amber',
            'part paid'        => 'amber',
            'partial'          => 'amber',

            'pending'          => 'blue',
            'full pending'     => 'blue',
            'unpaid'           => 'blue',
            'to do'            => 'blue',
            'sent'             => 'blue',
            'negotiation'      => 'blue',
            'follow-up'        => 'blue',
            'on hold'          => 'amber',

            'draft'            => 'gray',
            'cancelled'        => 'gray',
            'canceled'         => 'gray',
            'lost'             => 'gray',
            'rejected'         => 'gray',
            'expired'          => 'gray',
            'inactive'         => 'gray',
            'in progress'      => 'blue',
        ];

        if (isset($map[$key])) return $map[$key];

        // Unknown labels keep the old behaviour so nothing loses its colour.
        if (in_array($status, ['Active', 'Won', 'Paid', 'Done', 'Completed', 'Accepted', 'Delivered', 'Approved', 'Converted'], true)) return 'green';
        if (in_array($status, ['Inactive', 'Lost', 'Overdue', 'Cancelled', 'Rejected', 'Expired'], true)) return 'red';
        if (in_array($status, ['Pending', 'Draft', 'To Do', 'On Hold', 'Sent', 'Negotiation', 'Follow-up', 'Partially Paid'], true)) return 'orange';

        return 'gray';
    }
}

if (!function_exists('quotation_effective_status')) {
    function quotation_effective_status($row) {
        $status = trim((string)($row['status'] ?? ''));
        $status = $status === 'Converted to Invoice' ? 'Converted' : $status;

        if (in_array($status, ['Converted', 'Rejected'], true)) {
            return $status;
        }

        $validUntil = trim((string)($row['valid_until'] ?? ''));
        if ($validUntil !== '') {
            $validTimestamp = strtotime($validUntil);
            if ($validTimestamp !== false && $validTimestamp < strtotime('today')) {
                return 'Expired';
            }
        }

        if ($status === '') return 'Sent';
        return in_array($status, ['Sent', 'Expired'], true) ? $status : 'Sent';
    }
}

if (!function_exists('normalize_field_value')) {
    function normalize_field_value($value) {
        if (is_array($value)) {
            if (empty($value)) return '';
            $flattened = [];
            foreach ($value as $item) {
                if (is_array($item)) {
                    $flattened[] = normalize_field_value($item);
                } elseif ($item === null) {
                    continue;
                } else {
                    $flattened[] = trim((string)$item);
                }
            }
            $value = implode(', ', array_filter($flattened, function ($item) {
                return $item !== '';
            }));
            return trim((string)$value);
        }

        if ($value === null) return '';
        if (is_bool($value)) return $value ? '1' : '0';
        if (is_object($value) && method_exists($value, '__toString')) return trim((string)$value);

        return trim((string)$value);
    }
}

if (!function_exists('format_display_date')) {
    function format_display_date($value, $fallback = '-') {
        $value = trim((string)$value);
        if ($value === '') return $fallback;
        $timestamp = strtotime($value);
        return $timestamp === false ? $value : date('d-m-Y', $timestamp);
    }
}

if (!function_exists('format_input_date')) {
    function format_input_date($value, $fallback = '') {
        $value = trim((string)$value);
        if ($value === '') return $fallback;
        $date = DateTime::createFromFormat('Y-m-d', substr($value, 0, 10));
        return $date ? $date->format('d/m/Y') : $fallback;
    }
}

if (!function_exists('normalize_date_input')) {
    function normalize_date_input($value) {
        $value = trim((string)$value);
        if ($value === '') return '';
        foreach (['d/m/Y', 'd-m-Y', 'Y-m-d'] as $format) {
            $date = DateTime::createFromFormat($format, $value);
            $errors = DateTime::getLastErrors();
            if ($date && ($errors === false || ($errors['warning_count'] === 0 && $errors['error_count'] === 0))) {
                return $date->format('Y-m-d');
            }
        }
        return $value;
    }
}

if (!function_exists('normalize_line_items')) {
    function normalize_line_items($items) {
        if (empty($items)) return [];
        if (is_string($items)) {
            $decoded = json_decode($items, true);
            if (is_array($decoded)) {
                $items = $decoded;
            } else {
                return [];
            }
        }
        if (!is_array($items)) return [];

        $normalized = [];
        foreach ($items as $item) {
            if (!is_array($item)) continue;
            $description = trim((string)($item['description'] ?? $item['name'] ?? ''));
            $qty = (float)($item['qty'] ?? $item['quantity'] ?? 0);
            // `price` is the original/list selling price.
            $price = (float)($item['price'] ?? $item['rate'] ?? 0);
            $storedActual = $item['actual_selling_price'] ?? null;
            if ($storedActual !== null && $storedActual !== '') {
                $actualSellingPrice = (float)$storedActual;
                $actualSellingPrice = max(0, min($price, $actualSellingPrice));
                $discountRate = $price > 0 ? (($price - $actualSellingPrice) / $price) * 100 : 0;
            } else {
                $discountRate = max(0, min(100, (float)($item['discount_rate'] ?? 0)));
                $actualSellingPrice = max(0, $price - (($price * $discountRate) / 100));
            }
            $discountRate = max(0, min(100, $discountRate));
            $discountAmount = round($qty * max(0, $price - $actualSellingPrice), 2);
            $total = round($qty * $actualSellingPrice, 2);
            if ($description === '' && $qty <= 0 && $price <= 0 && $total <= 0) {
                continue;
            }
            $normalized[] = [
                'product_id' => (int)($item['product_id'] ?? 0),
                'description' => $description,
                'hsn_code' => trim((string)($item['hsn_code'] ?? '')),
                'gst_rate' => max(0, (float)($item['gst_rate'] ?? 0)),
                'qty' => max(0, $qty),
                'price' => max(0, $price),
                'actual_selling_price' => max(0, $actualSellingPrice),
                'discount_rate' => $discountRate,
                'discount_amount' => $discountAmount,
                'total' => $total,
            ];
        }
        return $normalized;
    }
}

if (!function_exists('line_items_total')) {
    function line_items_total($items) {
        $items = normalize_line_items($items);
        $total = 0.0;
        foreach ($items as $item) {
            $total += (float)($item['total'] ?? ((float)($item['qty'] ?? 0) * (float)($item['price'] ?? 0)));
        }
        return $total;
    }
}

if (!function_exists('lead_automatic_stage')) {
    function lead_automatic_stage($lead, $invoices, $products) {
        $leadName = trim(mb_strtolower((string)($lead['name'] ?? '')));
        $stageMap = ['Contacted' => 'Connected', 'Won' => 'Converted to Customer', 'Lost' => 'Closed'];
        $storedStage = $stageMap[(string)($lead['stage'] ?? 'New')] ?? (string)($lead['stage'] ?? 'New');
        if (!in_array($storedStage, ['New', 'Connected', 'Converted to Customer', 'Closed', 'Repeat Pending'], true)) $storedStage = 'New';
        if ($leadName === '') return $storedStage;

        $productsById = [];
        foreach ($products as $product) {
            $productsById[(int)($product['id'] ?? 0)] = $product;
        }
        foreach ($invoices as $invoice) {
            if (trim(mb_strtolower((string)($invoice['customer'] ?? ''))) !== $leadName) continue;
            $purchaseDate = substr((string)($invoice['created_at'] ?? ''), 0, 10);
            if ($purchaseDate === '') continue;
            foreach (normalize_line_items($invoice['items'] ?? []) as $item) {
                $product = $productsById[(int)($item['product_id'] ?? 0)] ?? null;
                $duration = (int)($product['duration_days'] ?? 0);
                if (!$product || $duration <= 0) continue;
                $validUntil = DateTime::createFromFormat('Y-m-d', $purchaseDate);
                if ($validUntil && $validUntil->modify('+' . $duration . ' days')->format('Y-m-d') <= date('Y-m-d')) {
                    return 'Repeat Pending';
                }
            }
        }
        return $storedStage;
    }
}

// ============ NOTIFICATION HELPERS ============

if (!function_exists('get_overdue_invoices')) {
    function get_overdue_invoices() {
        $overdue = [];
        $payments = db_get_all('payments');
        foreach (db_get_all('invoices') as $invoice) {
            $status = (string)($invoice['status'] ?? '');
            if ($status === 'Paid') continue;
            $dueDate = trim((string)($invoice['due_date'] ?? ''));
            if ($dueDate !== '' && $dueDate < date('Y-m-d')) {
                $paid = 0.0;
                foreach ($payments as $p) {
                    if (($p['invoice_no'] ?? '') === ($invoice['invoice_no'] ?? '')) {
                        $paid += (float)($p['amount'] ?? 0);
                    }
                }
                $pending = max(0, (float)($invoice['amount'] ?? 0) - $paid);
                if ($pending > 0) {
                    $invoice['_pending'] = $pending;
                    $invoice['_days_overdue'] = (int)((strtotime('today') - strtotime($dueDate)) / 86400);
                    $overdue[] = $invoice;
                }
            }
        }
        usort($overdue, fn($a, $b) => ($b['_days_overdue'] ?? 0) <=> ($a['_days_overdue'] ?? 0));
        return $overdue;
    }
}

// ============ DUE SOON INVOICES (Advance Reminder) ============

if (!function_exists('get_due_soon_invoices')) {
    function get_due_soon_invoices($withinDays = 2) {
        $dueSoon = [];
        $payments = db_get_all('payments');
        $today = date('Y-m-d');
        $cutoff = date('Y-m-d', strtotime("+{$withinDays} days"));
        
        foreach (db_get_all('invoices') as $invoice) {
            $status = (string)($invoice['status'] ?? '');
            if ($status === 'Paid' || $status === 'Cancelled') continue;
            
            $dueDate = trim((string)($invoice['due_date'] ?? ''));
            if ($dueDate === '' || $dueDate < $today || $dueDate > $cutoff) continue;
            
            // Check if there's pending amount
            $paid = 0.0;
            foreach ($payments as $p) {
                if (($p['invoice_no'] ?? '') === ($invoice['invoice_no'] ?? '')) {
                    $paid += (float)($p['amount'] ?? 0);
                }
            }
            $pending = max(0, (float)($invoice['amount'] ?? 0) - $paid);
            if ($pending > 0) {
                $invoice['_pending'] = $pending;
                $invoice['_days_until_due'] = (int)((strtotime($dueDate) - strtotime('today')) / 86400);
                $dueSoon[] = $invoice;
            }
        }
        usort($dueSoon, fn($a, $b) => ($a['_days_until_due'] ?? 0) <=> ($b['_days_until_due'] ?? 0));
        return $dueSoon;
    }
}

if (!function_exists('get_pending_tasks')) {
    function get_pending_tasks() {
        $tasks = db_get_all('tasks');
        return array_values(array_filter($tasks, function ($task) {
            $status = strtolower(trim((string)($task['status'] ?? '')));
            return !in_array($status, ['done', 'completed', 'cancelled'], true);
        }));
    }
}

if (!function_exists('get_expiring_quotations')) {
    function get_expiring_quotations($withinDays = 7) {
        $quotations = db_get_all('quotations');
        $today = date('Y-m-d');
        $cutoff = date('Y-m-d', strtotime("+{$withinDays} days"));
        return array_values(array_filter($quotations, function ($q) use ($today, $cutoff) {
            $status = quotation_effective_status($q);
            if (in_array($status, ['Converted', 'Rejected', 'Expired'], true)) return false;
            $validUntil = trim((string)($q['valid_until'] ?? ''));
            return $validUntil !== '' && $validUntil >= $today && $validUntil <= $cutoff;
        }));
    }
}

if (!function_exists('format_currency')) {
    function format_currency($amount) {
        return '₹' . number_format((float)$amount, 2);
    }
}

if (!function_exists('time_ago')) {
    function time_ago($datetime) {
        $now = new DateTime();
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);
        if ($diff->y > 0) return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
        if ($diff->m > 0) return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
        if ($diff->d > 0) return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
        if ($diff->h > 0) return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
        if ($diff->i > 0) return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
        return 'Just now';
    }
}

if (!function_exists('truncate_text')) {
    function truncate_text($text, $length = 50) {
        $text = trim((string)$text);
        if (mb_strlen($text) <= $length) return $text;
        return mb_substr($text, 0, $length) . '...';
    }
}

// ============ GST HELPERS ============

if (!function_exists('indian_states')) {
    function indian_states() {
        return [
            '01' => 'Jammu & Kashmir',
            '02' => 'Himachal Pradesh',
            '03' => 'Punjab',
            '04' => 'Chandigarh',
            '05' => 'Uttarakhand',
            '06' => 'Haryana',
            '07' => 'Delhi',
            '08' => 'Rajasthan',
            '09' => 'Uttar Pradesh',
            '10' => 'Bihar',
            '11' => 'Sikkim',
            '12' => 'Arunachal Pradesh',
            '13' => 'Nagaland',
            '14' => 'Manipur',
            '15' => 'Mizoram',
            '16' => 'Tripura',
            '17' => 'Meghalaya',
            '18' => 'Assam',
            '19' => 'West Bengal',
            '20' => 'Jharkhand',
            '21' => 'Odisha',
            '22' => 'Chhattisgarh',
            '23' => 'Madhya Pradesh',
            '24' => 'Gujarat',
            '25' => 'Daman & Diu',
            '26' => 'Dadra & Nagar Haveli',
            '27' => 'Maharashtra',
            '28' => 'Andhra Pradesh (Old)',
            '29' => 'Karnataka',
            '30' => 'Goa',
            '31' => 'Lakshadweep',
            '32' => 'Kerala',
            '33' => 'Tamil Nadu',
            '34' => 'Puducherry',
            '35' => 'Andaman & Nicobar Islands',
            '36' => 'Telangana',
            '37' => 'Andhra Pradesh',
            '38' => 'Ladakh',
            '97' => 'Other Territory',
        ];
    }
}

if (!function_exists('get_state_code_by_name')) {
    function get_state_code_by_name($stateName) {
        $states = indian_states();
        $stateName = trim(mb_strtolower($stateName));
        foreach ($states as $code => $name) {
            if (mb_strtolower($name) === $stateName) return $code;
        }
        return '';
    }
}

if (!function_exists('get_state_name_by_code')) {
    function get_state_name_by_code($code) {
        $states = indian_states();
        return $states[trim((string)$code)] ?? '';
    }
}

if (!function_exists('get_seller_state_code')) {
    function get_seller_state_code() {
        $profile = shop_profile();
        $stateCode = trim((string)($profile['seller_state_code'] ?? ''));
        if ($stateCode !== '') return $stateCode;
        $stateName = trim((string)($profile['seller_state'] ?? ''));
        if ($stateName !== '') return get_state_code_by_name($stateName);
        return '';
    }
}

if (!function_exists('get_customer_state_code')) {
    function get_customer_state_code($customerName) {
        $name = trim((string)$customerName);
        if ($name === '') return '';
        foreach (db_get_all('customers') as $customer) {
            if (strcasecmp(trim((string)($customer['name'] ?? '')), $name) === 0) {
                $stateCode = trim((string)($customer['state_code'] ?? ''));
                if ($stateCode !== '') return $stateCode;
                $stateName = trim((string)($customer['state'] ?? ''));
                if ($stateName !== '') return get_state_code_by_name($stateName);
                return '';
            }
        }
        return '';
    }
}

if (!function_exists('is_intra_state')) {
    function is_intra_state($sellerStateCode, $buyerStateCode) {
        if ($sellerStateCode === '' || $buyerStateCode === '') return true;
        return $sellerStateCode === $buyerStateCode;
    }
}

if (!function_exists('get_gst_rate_for_product')) {
    function get_gst_rate_for_product($productId) {
        if ((int)$productId <= 0) return 0.0;
        $product = db_get_one('products', (int)$productId);
        return (float)($product['gst_rate'] ?? 0);
    }
}

if (!function_exists('calculate_gst_for_item')) {
    function calculate_gst_for_item($taxableAmount, $gstRate, $isIntraState) {
        $taxableAmount = max(0, (float)$taxableAmount);
        $gstRate = max(0, (float)$gstRate);
        $totalGst = round($taxableAmount * $gstRate / 100, 2);
        if ($isIntraState) {
            return [
                'cgst_rate' => round($gstRate / 2, 2),
                'sgst_rate' => round($gstRate / 2, 2),
                'igst_rate' => 0,
                'cgst_amount' => round($totalGst / 2, 2),
                'sgst_amount' => round($totalGst / 2, 2),
                'igst_amount' => 0,
                'total_gst' => $totalGst,
            ];
        } else {
            return [
                'cgst_rate' => 0,
                'sgst_rate' => 0,
                'igst_rate' => $gstRate,
                'cgst_amount' => 0,
                'sgst_amount' => 0,
                'igst_amount' => $totalGst,
                'total_gst' => $totalGst,
            ];
        }
    }
}

if (!function_exists('calculate_invoice_gst')) {
    function calculate_invoice_gst($items, $placeOfSupply) {
        $sellerStateCode = get_seller_state_code();
        $buyerStateCode = '';
        $pos = trim((string)$placeOfSupply);
        if ($pos !== '') {
            $buyerStateCode = get_state_code_by_name($pos);
            if ($buyerStateCode === '') $buyerStateCode = $pos;
        }
        $isIntraState = is_intra_state($sellerStateCode, $buyerStateCode);

        $totalCgst = 0.0;
        $totalSgst = 0.0;
        $totalIgst = 0.0;
        $totalTaxable = 0.0;
        $taxSummary = [];

        foreach ($items as $item) {
            $taxableAmount = (float)($item['total'] ?? 0);
            $gstRate = (float)($item['gst_rate'] ?? 0);
            $hsnCode = trim((string)($item['hsn_code'] ?? ''));

            $gst = calculate_gst_for_item($taxableAmount, $gstRate, $isIntraState);

            $totalTaxable += $taxableAmount;
            $totalCgst += $gst['cgst_amount'];
            $totalSgst += $gst['sgst_amount'];
            $totalIgst += $gst['igst_amount'];

            $hsnKey = $hsnCode !== '' ? $hsnCode : 'default';
            if (!isset($taxSummary[$hsnKey])) {
                $taxSummary[$hsnKey] = [
                    'hsn_code' => $hsnCode,
                    'gst_rate' => $gstRate,
                    'taxable_amount' => 0,
                    'cgst_amount' => 0,
                    'sgst_amount' => 0,
                    'igst_amount' => 0,
                    'total_gst' => 0,
                ];
            }
            $taxSummary[$hsnKey]['taxable_amount'] += $taxableAmount;
            $taxSummary[$hsnKey]['cgst_amount'] += $gst['cgst_amount'];
            $taxSummary[$hsnKey]['sgst_amount'] += $gst['sgst_amount'];
            $taxSummary[$hsnKey]['igst_amount'] += $gst['igst_amount'];
            $taxSummary[$hsnKey]['total_gst'] += $gst['total_gst'];
        }

        return [
            'is_intra_state' => $isIntraState,
            'place_of_supply' => $pos,
            'seller_state_code' => $sellerStateCode,
            'buyer_state_code' => $buyerStateCode,
            'total_taxable' => round($totalTaxable, 2),
            'total_cgst' => round($totalCgst, 2),
            'total_sgst' => round($totalSgst, 2),
            'total_igst' => round($totalIgst, 2),
            'total_gst' => round($totalCgst + $totalSgst + $totalIgst, 2),
            'grand_total' => round($totalTaxable + $totalCgst + $totalSgst + $totalIgst, 2),
            'tax_summary' => array_values($taxSummary),
        ];
    }
}

if (!function_exists('format_gst_number')) {
    function format_gst_number($gstin) {
        $gstin = strtoupper(trim((string)$gstin));
        if ($gstin === '') return '';
        if (preg_match('/^([0-9]{2})([A-Z]{5})([0-9]{4})([A-Z]{1})([0-9]{1})([Z]{1})([0-9A-Z]{1})$/', $gstin, $m)) {
            return $m[1] . ' ' . $m[2] . ' ' . $m[3] . ' ' . $m[4] . ' ' . $m[5] . ' ' . $m[6] . ' ' . $m[7];
        }
        return $gstin;
    }
}

// ============ RISK MANAGEMENT HELPERS ============

if (!function_exists('get_payment_delay_days')) {
    function get_payment_delay_days($customerName) {
        $name = trim(mb_strtolower($customerName));
        if ($name === '') return 0;
        $payments = db_get_all('payments');
        $invoices = db_get_all('invoices');
        $totalDelay = 0;
        $count = 0;
        foreach ($invoices as $inv) {
            if (trim(mb_strtolower((string)($inv['customer'] ?? ''))) !== $name) continue;
            $invDate = trim((string)($inv['created_at'] ?? $inv['date'] ?? ''));
            if ($invDate === '') continue;
            foreach ($payments as $p) {
                if (($p['invoice_no'] ?? '') === ($inv['invoice_no'] ?? '')) {
                    $payDate = trim((string)($p['date'] ?? $p['created_at'] ?? ''));
                    if ($payDate !== '') {
                        $invTs = strtotime(substr($invDate, 0, 10));
                        $payTs = strtotime(substr($payDate, 0, 10));
                        if ($invTs !== false && $payTs !== false && $payTs >= $invTs) {
                            $totalDelay += (int)(($payTs - $invTs) / 86400);
                            $count++;
                        }
                    }
                }
            }
        }
        return $count > 0 ? (int)round($totalDelay / $count) : 0;
    }
}

if (!function_exists('calculate_customer_risk_score')) {
    function calculate_customer_risk_score($customerName) {
        $name = trim(mb_strtolower($customerName));
        if ($name === '') return ['score' => 0, 'rating' => 'Low', 'label' => 'Low'];
        $payments = db_get_all('payments');
        $invoices = db_get_all('invoices');
        $customerInvoices = array_values(array_filter($invoices, function ($inv) use ($name) {
            return trim(mb_strtolower((string)($inv['customer'] ?? ''))) === $name;
        }));
        if (empty($customerInvoices)) return ['score' => 0, 'rating' => 'Low', 'label' => 'Low'];
        // 1. Overdue ratio (weight: 30%)
        $overdueCount = 0;
        $totalPending = 0;
        $totalInvoiced = 0;
        foreach ($customerInvoices as $inv) {
            $amt = (float)($inv['amount'] ?? 0);
            $totalInvoiced += $amt;
            $paid = 0;
            foreach ($payments as $p) {
                if (($p['invoice_no'] ?? '') === ($inv['invoice_no'] ?? '')) {
                    $paid += (float)($p['amount'] ?? 0);
                }
            }
            $pending = max(0, $amt - $paid);
            $totalPending += $pending;
            if (($inv['status'] ?? '') !== 'Paid' && !empty($inv['due_date']) && $inv['due_date'] < date('Y-m-d') && $pending > 0) {
                $overdueCount++;
            }
        }
        $overdueRatio = count($customerInvoices) > 0 ? ($overdueCount / count($customerInvoices)) * 100 : 0;
        // 2. Pending amount ratio (weight: 25%)
        $pendingRatio = $totalInvoiced > 0 ? ($totalPending / $totalInvoiced) * 100 : 0;
        // 3. Payment delay score (weight: 25%)
        $avgDelay = get_payment_delay_days($customerName);
        $delayScore = min(100, ($avgDelay / 60) * 100); // 60 days = max score
        // 4. Order frequency (weight: 10%) - fewer orders = higher risk
        $orderCount = count($customerInvoices);
        $freqScore = $orderCount <= 1 ? 80 : ($orderCount <= 3 ? 50 : ($orderCount <= 5 ? 30 : 10));
        // 5. Recency (weight: 10%)
        $lastDate = '';
        foreach ($customerInvoices as $inv) {
            $d = trim((string)($inv['created_at'] ?? $inv['date'] ?? ''));
            if ($d > $lastDate) $lastDate = $d;
        }
        $recencyScore = 50;
        if ($lastDate !== '') {
            $daysSince = (int)((strtotime('today') - strtotime(substr($lastDate, 0, 10))) / 86400);
            $recencyScore = min(100, max(0, $daysSince * 2));
        }
        // Weighted sum
        $score = ($overdueRatio * 0.30) + ($pendingRatio * 0.25) + ($delayScore * 0.25) + ($freqScore * 0.10) + ($recencyScore * 0.10);
        $score = max(0, min(100, (int)round($score)));
        if ($score <= 30) $rating = 'Low';
        elseif ($score <= 60) $rating = 'Medium';
        elseif ($score <= 80) $rating = 'High';
        else $rating = 'Critical';
        return ['score' => $score, 'rating' => $rating, 'label' => $rating];
    }
}

if (!function_exists('customer_credit_summary')) {
    function customer_credit_summary($customerName) {
        $name = trim((string)$customerName);
        $creditLimit = 0;
        $creditHold = 'No';
        foreach (db_get_all('customers') as $c) {
            if (strcasecmp(trim((string)($c['name'] ?? '')), $name) === 0) {
                $creditLimit = max(0, (float)($c['credit_limit'] ?? 0));
                $creditHold = trim((string)($c['credit_hold'] ?? 'No'));
                break;
            }
        }
        $stats = customer_stats($customerName);
        $pendingAmount = (float)($stats['pending_amount'] ?? 0);
        $usedCredit = $pendingAmount;
        $availableCredit = $creditLimit > 0 ? max(0, $creditLimit - $usedCredit) : -1; // -1 means unlimited
        $isOverLimit = $creditLimit > 0 && $usedCredit > $creditLimit;
        $usagePercent = $creditLimit > 0 ? min(100, ($usedCredit / $creditLimit) * 100) : 0;
        return [
            'credit_limit'     => $creditLimit,
            'credit_hold'      => $creditHold,
            'used_credit'      => $usedCredit,
            'available_credit' => $availableCredit,
            'is_over_limit'    => $isOverLimit,
            'usage_percent'    => $usagePercent,
        ];
    }
}

if (!function_exists('get_risky_customers')) {
    function get_risky_customers($limit = 10, $minScore = 0) {
        $customers = db_get_all('customers');
        $invoices = db_get_all('invoices');
        $payments = db_get_all('payments');
        
        // Build a map of all unique customer names from customers + invoices
        $allCustomerNames = [];
        foreach ($customers as $c) {
            $name = trim((string)($c['name'] ?? ''));
            if ($name !== '') $allCustomerNames[$name] = $c['id'] ?? 0;
        }
        foreach ($invoices as $inv) {
            $name = trim((string)($inv['customer'] ?? ''));
            if ($name !== '' && !isset($allCustomerNames[$name])) {
                $allCustomerNames[$name] = 0; // No customer record
            }
        }
        
        $risky = [];
        foreach ($allCustomerNames as $name => $customerId) {
            if ($name === '') continue;
            
            // Calculate pending and overdue for this customer
            $totalPending = 0;
            $totalInvoiced = 0;
            $overdueCount = 0;
            $overdueAmount = 0;
            $orderCount = 0;
            $lastDate = '';
            $nameLower = mb_strtolower($name);
            
            foreach ($invoices as $inv) {
                if (trim(mb_strtolower((string)($inv['customer'] ?? ''))) !== $nameLower) continue;
                $amt = (float)($inv['amount'] ?? 0);
                $totalInvoiced += $amt;
                $orderCount++;
                
                $invDate = trim((string)($inv['created_at'] ?? $inv['date'] ?? ''));
                if ($invDate > $lastDate) $lastDate = $invDate;
                
                $paid = 0;
                foreach ($payments as $p) {
                    if (($p['invoice_no'] ?? '') === ($inv['invoice_no'] ?? '')) {
                        $paid += (float)($p['amount'] ?? 0);
                    }
                }
                $pending = max(0, $amt - $paid);
                $totalPending += $pending;
                
                if (($inv['status'] ?? '') !== 'Paid' && ($inv['status'] ?? '') !== 'Cancelled') {
                    if (!empty($inv['due_date']) && $inv['due_date'] < date('Y-m-d') && $pending > 0) {
                        $overdueCount++;
                        $overdueAmount += $pending;
                    }
                }
            }
            
            if ($totalPending <= 0 && $overdueCount === 0) continue; // Skip fully paid customers
            
            // Calculate risk score
            $overdueRatio = $orderCount > 0 ? ($overdueCount / $orderCount) * 100 : 0;
            $pendingRatio = $totalInvoiced > 0 ? ($totalPending / $totalInvoiced) * 100 : 0;
            $avgDelay = get_payment_delay_days($name);
            $delayScore = min(100, ($avgDelay / 60) * 100);
            $freqScore = $orderCount <= 1 ? 80 : ($orderCount <= 3 ? 50 : ($orderCount <= 5 ? 30 : 10));
            $recencyScore = 50;
            if ($lastDate !== '') {
                $daysSince = (int)((strtotime('today') - strtotime(substr($lastDate, 0, 10))) / 86400);
                $recencyScore = min(100, max(0, $daysSince * 2));
            }
            $score = ($overdueRatio * 0.30) + ($pendingRatio * 0.25) + ($delayScore * 0.25) + ($freqScore * 0.10) + ($recencyScore * 0.10);
            $score = max(0, min(100, (int)round($score)));
            if ($score <= 30) $rating = 'Low';
            elseif ($score <= 60) $rating = 'Medium';
            elseif ($score <= 80) $rating = 'High';
            else $rating = 'Critical';
            
            // Get credit info if customer exists
            $creditLimit = 0;
            $creditHold = 'No';
            if ($customerId > 0) {
                foreach ($customers as $c) {
                    if ((int)($c['id'] ?? 0) === $customerId) {
                        $creditLimit = max(0, (float)($c['credit_limit'] ?? 0));
                        $creditHold = trim((string)($c['credit_hold'] ?? 'No'));
                        break;
                    }
                }
            }
            $isOverLimit = $creditLimit > 0 && $totalPending > $creditLimit;
            
            if ($score >= $minScore || $hasPending = $totalPending > 0) {
                $risky[] = [
                    'name'            => $name,
                    'id'              => $customerId,
                    'risk_score'      => $score,
                    'risk_rating'     => $rating,
                    'pending_amount'  => $totalPending,
                    'total_invoiced'  => $totalInvoiced,
                    'overdue_count'   => $overdueCount,
                    'credit_limit'    => $creditLimit,
                    'credit_hold'     => $creditHold,
                    'payment_delay'   => $avgDelay,
                    'is_over_limit'   => $isOverLimit,
                ];
            }
        }
        usort($risky, fn($a, $b) => $b['risk_score'] <=> $a['risk_score']);
        return array_slice($risky, 0, $limit);
    }
}

if (!function_exists('risk_color_class')) {
    function risk_color_class($rating) {
        $map = ['Low' => '#16a34a', 'Medium' => '#d97706', 'High' => '#ea580c', 'Critical' => '#dc2626'];
        return $map[$rating] ?? '#64748b';
    }
}

if (!function_exists('risk_bg_class')) {
    function risk_bg_class($rating) {
        $map = ['Low' => '#f0fdf4', 'Medium' => '#fef3c7', 'High' => '#fff7ed', 'Critical' => '#fef2f2'];
        return $map[$rating] ?? '#f8fafc';
    }
}

if (!function_exists('amount_in_words')) {
    function amount_in_words($amount) {
        $amount = round((float)$amount, 2);
        if ($amount == 0) return 'Zero';
        $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
        $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
        $words = '';
        $integerPart = (int)$amount;
        $decimalPart = round(($amount - $integerPart) * 100);
        if ($integerPart >= 10000000) { $words .= $ones[(int)($integerPart / 10000000)] . ' Crore '; $integerPart %= 10000000; }
        if ($integerPart >= 100000) { $words .= $ones[(int)($integerPart / 100000)] . ' Lakh '; $integerPart %= 100000; }
        if ($integerPart >= 1000) { $words .= $ones[(int)($integerPart / 1000)] . ' Thousand '; $integerPart %= 1000; }
        if ($integerPart >= 100) { $words .= $ones[(int)($integerPart / 100)] . ' Hundred '; $integerPart %= 100; }
        if ($integerPart >= 20) { $words .= $tens[(int)($integerPart / 10)] . ' '; $integerPart %= 10; }
        if ($integerPart > 0) { $words .= $ones[$integerPart] . ' '; }
        $words = trim($words) . ' Rupees';
        if ($decimalPart > 0) {
            $words .= ' and ';
            if ($decimalPart >= 20) { $words .= $tens[(int)($decimalPart / 10)] . ' '; $decimalPart %= 10; }
            if ($decimalPart > 0) { $words .= $ones[$decimalPart] . ' '; }
            $words = trim($words) . ' Paise';
        }
        $words .= ' Only';
        return $words;
    }
}
