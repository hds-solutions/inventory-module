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

    'employee_id'   => [
        'Empleado',
        '_' => 'Empleado',
        '?' => 'Empleado help text',
    ],

    'partnerable_id'=> [
        'Cliente',
        '_' => 'Cliente',
        '?' => 'Cliente help text',
    ],

    'invoice_id'    => [
        'Factura',
        '_' => 'Factura',
        '?' => 'Factura desde la cual se generará el documento de devolución',
    ],

    'transacted_at' => [
        'Fecha Transacción',
        '_' => 'Fecha Transacción',
        '?' => 'Fecha Transacción help text',
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

    ] + __('inventory::material_return_line'),

];
