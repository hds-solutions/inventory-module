<?php return [

    'nav'   => 'Cambio de Precios',

    'details'       => [
        'Detalles'
    ],

    'document_number'  => [
        'Número de Documento',
        '_' => 'Número de Documento',
        '?' => 'Número de Documento help text',
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

    ] + __('inventory::price_change_line'),

    'file'          => [
        'Documento Excel',
        '_' => 'Seleccione documento Excel a importar',
        '?' => '',
    ],

    'import'        => [
        'sku'           => 'SKU / Producto',
        'warehouse'     => 'Depósito',
        'stock'         => 'Cantidad Stock',
        'success'       => 'El documento Excel fue importado exitosamente',
    ],

    'prepareIt'     => [
        'no-lines'      => 'El documento no tiene lineas',
        'empty-price'   => 'No se asigno el precio para el producto :product :variant',
    ],

    'completeIt'    => [
        'not-approved'  => 'El documento debe ser aprobado',
    ],

];
