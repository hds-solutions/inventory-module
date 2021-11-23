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

    'beforeSave'    => [
        'no-invoice'    => 'Se debe especificar la factura desde la cual se devolverá material',
    ],

    'prepareIt'     => [
        'order-has-pending-in_outs' => 'El pedido :order tiene documentos de Entrada/Salida pendientes que deben ser completados',
        'lines-with-qty-zero'       => 'No se asignó cantidad a devolver para el producto :product :variant (eliminar la linea si no se devuelve el producto)',
        'returning-gt-available'    => 'La cantidad a retornar del producto :product :variant es mayor que la cantidad disponible para devolver (disponible: :available)',
        'no-locator'                => 'No se asignó la ubicación de retorno para el producto :product :variant',
    ],

    'completeIt'    => [
        'remaining-sale'    => 'No se encontraron ubicaciones para devolver el producto :product :variant',
    ],

];
