<?php

namespace HDSSolutions\Finpar\Models;

class PriceChangeLine extends X_PriceChangeLine {

    public function priceChange() {
        return $this->belongsTo(PriceChange::class);
    }

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function variant() {
        return $this->belongsTo(Variant::class);
    }

    public function currency() {
        return $this->belongsTo(Currency::class);
    }

    public static function getFromProduct(Product|int $product, Currency|int $currency, Variant|int|null $variant = null):PriceChangeLine {
        // get line for product on currency
        $line = self::where('product_id', $product instanceof Product ? $product->id : $product)
                    ->where('currency_id', $currency instanceof Currency ? $currency->id : $currency);
        // check if variant was speficied
        if ($variant === null) $line->whereNull('variant_id');
        else $line->where('variant_id', $variant instanceof Variant ? $variant->id : $variant);

        // get first result
        if (($line = $line->first()) === null)
            // create new line for product
            $line = self::make([
                'product_id'    => $product instanceof Product ? $product->id : $product,
                'variant_id'    => ($variant instanceof Variant ? $variant->id : $variant) ?? null,
                'currency_id'   => $currency instanceof Currency ? $currency->id : $currency,
                'current_cost'  => 0,
                'current_price' => 0,
                'current_limit' => 0,
            ]);
        // return line
        return $line;
    }

}
