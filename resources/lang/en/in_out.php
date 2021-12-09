<?php return [

    'details'       => [
        'Details'
    ],

    'document_number'  => [
        'Document Number',
        '_' => 'Document Number',
        '?' => 'Document Number help text',
    ],

    'branch_id'  => [
        'Branch',
        '_' => 'Branch',
        '?' => 'Branch help text',
    ],

    'warehouse_id'  => [
        'Warehouse',
        '_' => 'Warehouse',
        '?' => 'Warehouse help text',
    ],

    'employee_id'   => [
        'Employee',
        '_' => 'Employee',
        '?' => 'Employee help text',
    ],

    'partnerable_id'=> [
        'Partner',
        '_' => 'Partner',
        '?' => 'Partner help text',
    ],

    'customer_id'   => [
        'Customer',
        '_' => 'Customer',
        '?' => 'Customer help text',
    ],

    'provider_id'   => [
        'Provider',
        '_' => 'Provider',
        '?' => 'Provider help text',
    ],

    'order_id'      => [
        'Order',
        '_' => 'Order',
        '?' => 'Order help text',
    ],

    'invoice_id'    => [
        'Invoice',
        '_' => 'Invoice',
        '?' => 'Invoice help text',
    ],

    'transacted_at' => [
        'Date',
        '_' => 'Date',
        '?' => 'Date help text',
    ],

    'is_purchase' => [
        'Is Purchase?',
        '_' => 'Yes, It\'s a Purchase',
        '?' => 'Is Purchase help text',
    ],

    'description'  => [
        'Description',
        '_' => 'Description',
        '?' => 'Description help text',
    ],

    'created_at'  => [
        'Created At',
        '_' => 'Created At',
        '?' => 'Created At help text',
    ],

    'document_status'  => [
        'Document Status',
        '_' => 'Document Status',
        '?' => 'Document Status help text',
    ],

    'lines'  => [
        'Lines',
        '_' => 'Lines',
        '?' => 'Lines help text',
    ] + __('inventory::in_out_line'),

    'beforeSave'    => [
    ],

    'completeIt'    => [
        'remaining-sale'        => 'No stock left to deliver of product :product :variant',
        'remaining-purchase'    => 'No storages found to receive product :product :variant',
    ],

    'voidIt'        => [
        'already-completed'     => 'The merchandise was already delivered. Use MaterialReturn document to return merchandise.',
        'no-storage'            => 'No storages found to return product :product :variant',
    ],

];
