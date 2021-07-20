<?php

namespace HDSSolutions\Laravel\Models;

use HDSSolutions\Laravel\Interfaces\Document;
use HDSSolutions\Laravel\Traits\HasDocumentActions;
use HDSSolutions\Laravel\Traits\HasPartnerable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Validator;

abstract class A_InOut extends X_InOut implements Document {
    use HasDocumentActions,
        HasPartnerable;

    public final static function nextDocumentNumber(bool $is_purchase = false):string {
        // return next document number for specified stamping
        return str_increment(self::where('is_purchase', $is_purchase)->max('document_number') ?? null);
    }

    protected final static function fromResource(Order|Invoice $resource, string $relation):array {
        // copy attributes from resource
        return [
            'branch_id'         => $resource->branch_id,
            'warehouse_id'      => $resource->warehouse_id,
            'employee_id'       => $resource->employee_id,
            'partnerable_type'  => $resource->partnerable_type,
            'partnerable_id'    => $resource->partnerable_id,
            $relation           => $resource->id,
            'transacted_at'     => $resource->transacted_at,
            'is_purchase'       => $resource->is_purchase,
        ];
    }

    public final function branch() {
        return $this->belongsTo(Branch::class);
    }

    public final function warehouse() {
        return $this->belongsTo(Warehouse::class);
    }

    public final function employee() {
        return $this->belongsTo(Employee::class);
    }

    public abstract function lines();

    public final function hasProduct(int|Product $product, int|Variant|null $variant = null) {
        // get order lines
        $lines = $this->lines();

        // filter product
        $lines->where('product_id', $product instanceof Product ? $product->id : $product);
        // filter variant if specified
        if ($variant !== null) $lines->where('variant_id', $variant instanceof Variant ? $variant->id : $variant);
        else $lines->whereNull('variant_id');

        // return if there is lines with specified product|variant
        return $lines->count() > 0;
    }

    public final function beforeSave(Validator $validator) {
        // TODO: set employee from session
        if (!$this->exists && $this->employee_id === null) $this->employee()->associate( auth()->user() );

        // if document is material return and invoice not set
        if ($this->is_material_return && $this->invoice === null)
            // reject it, Invoice must be specified when returning
            $validator->errors()->add('invoice_id', __('inventory::inout.material-return-invoice'));

        // check if new record and no document number is set
        if (!$this->exists && !$this->document_number)
            // set document number incrementing by 10
            $this->document_number = self::nextDocumentNumber();
    }

    public function prepareIt():?string {
        // return status InProgress
        return Document::STATUS_InProgress;
    }

    public final function completeIt():?string {
        // process lines, updating stock based on document type
        foreach ($this->lines as $line) {
            logger(__('Processing line #:line of '.class_basename(statis::class).' #:id: :product :variant', [
                'line'  => $line->id,
                'id'    => $this->id,
                'product'   => $line->product->name,
                'variant'   => $line->variant?->sku,
            ]));

            // save total quantity to move
            $quantityToMove = $line->quantity_movement;

            // get Variant|Product locators
            foreach (($line->variant ?? $line->product)->locators as $locator) {
                // check if locator belongs to current branch
                if ($locator->warehouse->branch_id !== $this->branch_id) continue;
                // update storage
                if (!$this->updateStorage(Storage::getFromProductOnLocator($line->product, $line->variant, $locator), $quantityToMove))
                    // stop process and return error
                    return false;
                // check if all movement quantity was already moved and exit loop
                if ($quantityToMove == 0) break;
            }

            // update stock for Variant|Product on existing Storages
            foreach (Storage::getFromProduct($line->product, $line->variant, $this->branch) as $storage) {
                // update existing storage
                if (!$this->updateStorage($storage, $quantityToMove))
                    // stop process and return error
                    return false;
                // check if all movement quantity was already moved and exit loop
                if ($quantityToMove == 0) break;
            }

            // if not all movement quantity can be moved, reject process
            if ($quantityToMove > 0)
                // return document error
                return $this->documentError('inventory::in_out.lines.'.($this->is_material_return ? 'no-storage-found' : 'no-stock'), [
                    'product'   => $line->product->name,
                    'variant'   => $line->variant?->sku,
                ]);

            // update delivered/received quantity on Order/Invoice
            if (!$this->is_material_return) {
                // if is_sale, update OrderLine.quantity_delivered
                if ($this->is_sale && !$line->orderLine->update([ 'quantity_delivered' => $line->quantity_movement ]))
                    // return document error
                    return $this->documentError( $line->orderLine->errors()->first() );

                // if is_purchase, update InvoiceLine.quantity_received
                if ($this->is_purchase && !$line->invoiceLine->update([ 'quantity_received' => $line->quantity_movement ]))
                    // return document error
                    return $this->documentError( $line->invoiceLine->errors()->first() );
            }
        }

        // if document is material_return, create a CreditNote for the returning amount
        if ($this->is_material_return) {
            // create CreditNote
            if (!($creditNote = CreditNote::createFromMaterialReturn( $this ))->exists || $creditNote->errors()->count() > 0)
                // redirect creditNote error
                return $this->documentError( $creditNote->errors()->first() );
        }

        // return completed status
        return Document::STATUS_Completed;
    }

    protected abstract function updateStorage(Storage $storage, int &$quantityToMove):bool;

}
