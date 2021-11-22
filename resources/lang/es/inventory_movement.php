<?php return [

    'nav'   => 'Movimiento de Inventario',

    'details'       => [
        'Detalles'
    ],

    'branch_id'  => [
        'Enviar desde Sucursal',
        '_' => 'Sucursal',
        '?' => 'Sucursal help text',
    ],

    'to_branch_id'  => [
        'Enviar a Sucursal',
        '_' => 'Sucursal',
        '?' => 'Sucursal help text',
    ],

    'warehouse_id'  => [
        'Enviar desde Depósito',
        '_' => 'Depósito',
        '?' => 'Depósito help text',
    ],

    'to_warehouse_id'  => [
        'Enviar a Depósito',
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

        'empty-quantity'        => 'The product :product :variant doesn\'t has quantity set.',
        'empty-toLocator'       => 'The product :product :variant doesn\'t has destination locator set.',
        'has-open-inventories'  => 'The product :product :variant has pending inventories on branch :branch.',
        'no-enough-stock'       => 'The product :product :variant hasn\'t enough stock, only :available available.',

    ] + __('inventory::inventory_movement_line'),

    'no-lines'      => 'El Movimiento de Inventario no tiene lineas',
    'not-approved'  => 'El Movimiento de Inventario no fue aprobado',

];
