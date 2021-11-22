<?php return [

    'details'       => [
        'Detalles'
    ],

    'document_number'  => [
        'Número de Documento',
        '_' => 'Número de Documento',
        '?' => 'Número de Documento help text',
    ],

    'branch_id'  => [
        'Sucursal',
        '_' => 'Sucursal',
        '?' => 'Sucursal help text',
    ],

    'warehouse_id'  => [
        'Depósito',
        '_' => 'Depósito',
        '?' => 'Depósito help text',
    ],

    'description'  => [
        'Descripción',
        '_' => 'Descripción',
        '?' => 'Descripción help text',
    ],

    'created_at'  => [
        'Fecha Creación',
        '_' => 'Fecha Creación',
        '?' => 'Fecha Creación help text',
    ],

    'document_status'  => [
        'Estado Documento',
        '_' => 'Estado Documento',
        '?' => 'Estado Documento help text',
    ],

    'lines'  => [
        'Líneas',
        '_' => 'Líneas',
        '?' => 'Líneas help text',

    ] + __('inventory::inventory_line'),

    'file'          => [
        'Documento Excel',
        '_' => 'Seleccione documento Excel a importar',
        '?' => '',
    ],

    'import'        => [
        'sku'           => 'SKU / Producto',
        'warehouse'     => 'Depósito',
        'stock'         => 'Cantidad Stock',
        'success'       => 'El documento Excel se importo exitósamente',
    ],

];
