<?php return [

    'nav'   => 'Inventory Movement',

    'details'       => [
        'Details'
    ],

    'branch_id'  => [
        'Branch',
        '_' => 'Branch',
        '?' => 'Branch help text',
    ],

    'to_branch_id'  => [
        'To Branch',
        '_' => 'To Branch',
        '?' => 'To Branch help text',
    ],

    'warehouse_id'  => [
        'Warehouse',
        '_' => 'Warehouse',
        '?' => 'Warehouse help text',
    ],

    'to_warehouse_id'  => [
        'To Warehouse',
        '_' => 'To Warehouse',
        '?' => 'To Warehouse help text',
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

        'image'         => [
            'Image',
            '_' => 'Image',
            '?' => '',
        ],

        'product_id'    => [
            'Product',
            '_' => 'Product',
            '?' => 'Product help text',
        ],

        'variant_id'    => [
            'Variant',
            '_' => 'Variant',
            '?' => 'Variant help text',
        ],

        'locator_id'    => [
            'Locator',
            '_' => 'Locator',
            '?' => 'Locator help text',
        ],

        'to_locator_id'    => [
            'To Locator',
            '_' => 'To Locator',
            '?' => 'To Locator help text',
        ],

        'quantity'       => [
            'Quantity',
            '_' => 'Quantity',
            '?' => 'Quantity help text',
        ],

        'empty-quantity'        => 'The product :product :variant doesn\'t has quantity set.',
        'empty-toLocator'       => 'The product :product :variant doesn\'t has destination locator set.',
        'has-open-inventories'  => 'The product :product :variant has pending inventories on branch :branch.',
        'no-enough-stock'       => 'The product :product :variant hasn\'t enough stock, only :available available.',

    ],

    'no-lines'      => 'The Inventory has no lines',
    'not-approved'  => 'The Inventory isn\'t approved',

];
