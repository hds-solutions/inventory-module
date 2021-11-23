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

    ] + __('inventory::material_return_line'),

    'beforeSave'    => [
        'no-invoice'    => 'Invoice must be especified when returning material',
    ],

    'prepareIt'     => [
        'order-has-pending-in_outs' => 'The order :order has pending InOut documents that must completed first',
        'lines-with-qty-zero'       => 'The product :product :variant has no returning quantity set (delete line if not returning this product)',
        'returning-gt-available'    => 'The returning quantity of product :product :variant is greater than available to return (available: :available)',
        'no-locator'                => 'No returning locator set for product :product :variant',
    ],

    'completeIt'    => [
        'remaining-sale'    => 'No storages found to return product :product :variant',
    ],

];
