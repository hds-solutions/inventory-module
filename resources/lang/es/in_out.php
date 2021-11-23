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
        'Entidad',
        '_' => 'Entidad',
        '?' => 'Entidad help text',
    ],

    'order_id'      => [
        'Pedido',
        '_' => 'Pedido',
        '?' => 'Pedido help text',
    ],

    'transacted_at' => [
        'Fecha Transacción',
        '_' => 'Fecha Transacción',
        '?' => 'Fecha Transacción help text',
    ],

    'is_purchase' => [
        'Es Compra?',
        '_' => 'Si, es una compra',
        '?' => 'Es Compra help text',
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
        'Estado del Documento',
        '_' => 'Estado del Documento',
        '?' => 'Estado del Documento help text',
    ],

    'lines'  => [
        'Líneas',
        '_' => 'Líneas',
        '?' => 'Líneas help text',
    ] + __('inventory::in_out_line'),

    'beforeSave'    => [
    ],

    'completeIt'    => [
        'remaining-sale'        => 'No hay stock disponible para entregar del producto :product :variant',
        'remaining-purchase'    => 'No se encontraron ubicaciones para recibir el producto :product :variant',
    ],

    'voidIt'        => [
        'already-completed'     => 'La mercadería ya fue entregada. Utilice el documento de Devolución de Material para retornar mercadería.',
        'no-storage'            => 'No se encontraron ubicacionespara retornar el producto :product :variant',
    ],

];
