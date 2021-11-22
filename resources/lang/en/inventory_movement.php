<?php return [

    'nav'   => 'Inventory Movement',

    'details'       => [
        'Details'
    ],

    'branch_id'  => [
        'Send from Branch',
        '_' => 'Branch',
        '?' => 'Branch help text',
    ],

    'to_branch_id'  => [
        'Send to Branch',
        '_' => 'Branch',
        '?' => 'Branch help text',
    ],

    'warehouse_id'  => [
        'Send from Warehouse',
        '_' => 'Warehouse',
        '?' => 'Warehouse help text',
    ],

    'to_warehouse_id'  => [
        'Send to Warehouse',
        '_' => 'Warehouse',
        '?' => 'Warehouse help text',
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

        'empty-quantity'        => 'The product :product :variant doesn\'t has quantity set.',
        'empty-toLocator'       => 'The product :product :variant doesn\'t has destination locator set.',
        'has-open-inventories'  => 'The product :product :variant has pending inventories on branch :branch.',
        'no-enough-stock'       => 'The product :product :variant hasn\'t enough stock, only :available available.',

    ] + __('inventory::inventory_movement_line'),

    'no-lines'      => 'The Inventory Movement has no lines',
    'not-approved'  => 'The Inventory Movement isn\'t approved',

];
