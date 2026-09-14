<?php
/**
 * PRISHA AYURVEDIC ERP - Shared small helpers.
 */
if (!function_exists('badge_class')) {
    function badge_class($value) {
        $green = ['Active', 'Won', 'Paid', 'Done', 'Completed', 'Accepted', 'Delivered', 'Approved', 'Converted'];
        $red   = ['Inactive', 'Lost', 'Overdue', 'Cancelled', 'Rejected', 'Expired'];
        $orange = ['Pending', 'Draft', 'To Do', 'On Hold', 'Sent', 'Negotiation', 'Follow-up', 'Partially Paid'];
        if (in_array($value, $green)) return 'green';
        if (in_array($value, $red)) return 'red';
        if (in_array($value, $orange)) return 'orange';
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
