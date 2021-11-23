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

    ] + __('inventory::inventory_movement_line'),

    'prepareIt'     => [
        'no-lines'              => 'El documento no tiene lineas',
        'empty-quantity'        => 'No se asignó la cantidad para el producto :product :variant',
        'has-open-inventories'  => 'Existen inventarios pendientes en la sucursal :branch para el producto :product :variant',
        'no-enough-stock'       => 'No hay stock disponible para el producto :product :variant, solo :available disponible',
    ],

    'approveIt'     => [
        'empty-toLocator'       => 'No se asignó la ubicación destino para el producto :product :variant',
    ],

    'completeIt'    => [
        'not-approved'  => 'El documento debe ser aprobado',
    ],

];
