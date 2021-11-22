<?php return [

    'nav'   => 'PriceChange',

    'details'       => [
        'Details'
    ],

    'document_number'  => [
        'Document Number',
        '_' => 'Document Number',
        '?' => 'Document Number help text',
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

    ] + __('inventory::price_change_line'),

    'file'          => [
        'Excel File',
        '_' => 'Select Excel file to import',
        '?' => '',
    ],

    'import'        => [
        'sku'           => 'SKU / Product',
        'warehouse'     => 'Warehouse',
        'stock'         => 'Stock Quantity',
        'success'       => 'Excel was sucessfully imported',
    ],

];
