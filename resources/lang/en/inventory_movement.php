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

    ] + __('inventory::inventory_movement_line'),

    'prepareIt'     => [
        'no-lines'              => 'Document without lines',
        'empty-quantity'        => 'No quantity set fot product :product :variant',
        'has-open-inventories'  => 'There is pending inventories on branch :branch for the product :product :variant',
        'no-enough-stock'       => 'No enough stock available for product :product :variant, only :available available',
    ],

    'approveIt'     => [
        'empty-toLocator'       => 'No destination locator set for product :product :variant',
    ],

    'completeIt'    => [
        'not-approved'  => 'The document must be approved first',
    ],

];
