<?php
/**
 * PRISHA AYURVEDIC ERP - Module Definitions
 * Har module ma: label, icon, list columns ane form fields define kariya che.
 * Each module stores its records in database/<module>.json.
 */

$MODULES = [
    'products' => [
        'label' => 'Products',
        'icon'  => 'products',
        'columns' => ['name', 'selling_price', 'purchase_price', 'duration_days'],
        'fields' => [
            'name'           => ['label' => 'Product Name', 'type' => 'text', 'required' => true],
            'selling_price'  => ['label' => 'Selling Price (₹)', 'type' => 'number', 'required' => true],
            'purchase_price' => ['label' => 'Purchase Price (₹)', 'type' => 'number', 'required' => true],
            'duration_days'  => ['label' => 'Valid For (Days)', 'type' => 'number'],
        ],
    ],

    'customers' => [
        'label' => 'Customers',
        'icon'  => 'customers',
        'columns' => ['name', 'company', 'phone', 'email'],
        'fields' => [
            'name'    => ['label' => 'Customer Name', 'type' => 'text', 'required' => true],
            'company' => ['label' => 'Company', 'type' => 'text'],
            'phone'   => ['label' => 'Phone', 'type' => 'text'],
            'email'   => ['label' => 'Email', 'type' => 'email'],
            'address' => ['label' => 'Address', 'type' => 'textarea'],
        ],
    ],

    'meetings' => [
        'label' => 'Meetings',
        'icon'  => 'calendar',
        'columns' => ['title', 'date', 'time', 'with_person', 'location'],
        'fields' => [
            'title' => ['label' => 'Meeting Title', 'type' => 'text', 'required' => true],
            'date' => ['label' => 'Date', 'type' => 'date', 'required' => true],
            'time' => ['label' => 'Time', 'type' => 'time', 'required' => true],
            'with_person' => ['label' => 'With', 'type' => 'text', 'required' => false],
            'location' => ['label' => 'Location', 'type' => 'text'],
            'notes' => ['label' => 'Notes', 'type' => 'textarea'],
        ],
    ],

    'leads' => [
        'label' => 'Leads',
        'icon'  => 'leads',
        'columns' => ['name', 'company', 'source', 'stage', 'owner', 'last_follow_up', 'next_follow_up'],
        'fields' => [
            'name'     => ['label' => 'Lead Name', 'type' => 'text', 'required' => true],
            'company'  => ['label' => 'Company', 'type' => 'text'],
            'phone'    => ['label' => 'Phone', 'type' => 'text'],
            'email'    => ['label' => 'Email', 'type' => 'email'],
            'source'   => ['label' => 'Source', 'type' => 'select', 'options' => ['Website', 'Referral', 'Social', 'Cold Call', 'Other']],
            'stage'    => ['label' => 'Pipeline Stage', 'type' => 'select', 'options' => ['New', 'Connected', 'Converted to Customer', 'Closed', 'Repeat Pending']],
            'owner'    => ['label' => 'Owner', 'type' => 'text'],
            'last_follow_up' => ['label' => 'Last Follow-up', 'type' => 'date'],
            'next_follow_up' => ['label' => 'Next Follow-up', 'type' => 'date'],
            'response' => ['label' => 'Lead Response / Message', 'type' => 'textarea'],
        ],
    ],

    'team' => [
        'label' => 'Wholeseller',
        'icon'  => 'customers',
        'columns' => ['name', 'phone', 'role', 'username', 'photo'],
        'fields' => [
            'name'     => ['label' => 'Member Name', 'type' => 'text', 'required' => true],
            'phone'    => ['label' => 'Phone Number', 'type' => 'tel', 'required' => true],
            'address'  => ['label' => 'Address', 'type' => 'textarea'],
            'photo'    => ['label' => 'Photo', 'type' => 'file'],
            'username' => ['label' => 'Username', 'type' => 'text', 'required' => false],
            'password' => ['label' => 'Password', 'type' => 'password'],
            'role'     => ['label' => 'Role', 'type' => 'radio', 'options' => ['Admin', 'Member'], 'default' => 'Member', 'required' => true],
        ],
    ],

    'quotations' => [
        'label' => 'Quotations',
        'icon'  => 'quotations',
        'columns' => ['quotation_no', 'customer', 'amount', 'status', 'valid_until'],
        'fields' => [
            'quotation_no' => ['label' => 'Quotation No.', 'type' => 'text', 'required' => true],
            'customer'     => ['label' => 'Customer', 'type' => 'customer_select', 'required' => true],
            'amount'       => ['label' => 'Amount (₹)', 'type' => 'number'],
            'status'       => ['label' => 'Status', 'type' => 'select', 'options' => ['Sent', 'Converted', 'Expired', 'Rejected']],
            'valid_until'  => ['label' => 'Valid Until', 'type' => 'date'],
            'terms'        => ['label' => 'Terms & Notes', 'type' => 'textarea'],
        ],
    ],

    'tasks' => [
        'label' => 'Tasks',
        'icon'  => 'tasks',
        'columns' => ['title', 'assigned_to', 'status', 'due_date'],
        'fields' => [
            'title'       => ['label' => 'Task Title', 'type' => 'text', 'required' => true],
            'assigned_to' => ['label' => 'Assigned To', 'type' => 'team_select'],
            'priority'    => ['label' => 'Priority', 'type' => 'select', 'options' => ['Low', 'Medium', 'High']],
            'status'      => ['label' => 'Status', 'type' => 'select', 'options' => ['To Do', 'In Progress', 'Review', 'Done']],
            'due_date'    => ['label' => 'Due Date', 'type' => 'date'],
        ],
    ],

    'invoices' => [
        'label' => 'Invoices',
        'icon'  => 'invoices',
        'columns' => ['invoice_no', 'customer', 'amount', 'status', 'due_date'],
        'fields' => [
            'invoice_no' => ['label' => 'Invoice No.', 'type' => 'text', 'required' => true],
            'customer'   => ['label' => 'Customer', 'type' => 'customer_select', 'required' => true],
            'amount'     => ['label' => 'Amount (₹)', 'type' => 'number', 'required' => true],
            'due_date'   => ['label' => 'Due Date', 'type' => 'date'],
            'notes'      => ['label' => 'Notes (shown on PDF)', 'type' => 'textarea'],
        ],
    ],

    'payments' => [
        'label' => 'Payments',
        'icon'  => 'payments',
        'columns' => ['invoice_no', 'customer', 'amount', 'mode', 'date'],
        'fields' => [
            'invoice_no' => ['label' => 'Invoice No.', 'type' => 'text'],
            'customer'   => ['label' => 'Customer', 'type' => 'customer_select', 'required' => true],
            'amount'     => ['label' => 'Amount (₹)', 'type' => 'number', 'required' => true],
            'mode'       => ['label' => 'Mode', 'type' => 'select', 'options' => ['Cash', 'Online']],
            'reference'  => ['label' => 'Reference No.', 'type' => 'text'],
            'date'       => ['label' => 'Date', 'type' => 'date'],
        ],
    ],

    'expenses' => [
        'label' => 'Expenses',
        'icon'  => 'expenses',
        'columns' => ['title', 'category', 'amount', 'mode', 'date'],
        'fields' => [
            'title'    => ['label' => 'Title', 'type' => 'text', 'required' => true],
            'category' => ['label' => 'Category', 'type' => 'select', 'options' => ['Salary', 'Rent', 'Electricity', 'Internet', 'Software', 'Hosting', 'Domain', 'Advertising', 'Travel', 'Courier', 'Material', 'Vendor', 'Equipment', 'Other']],
            'amount'   => ['label' => 'Amount (₹)', 'type' => 'number', 'required' => true],
            'mode'     => ['label' => 'Payment Mode', 'type' => 'select', 'options' => ['Cash', 'Online']],
            'date'     => ['label' => 'Date', 'type' => 'date'],
        ],
    ],
];
