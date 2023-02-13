<?php

namespace App\Services\Cart\Transformer;


use League\Fractal;
use App\Models\Cart;
use App\Models\OrderDiscount;
use App\Models\Product;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use League\Fractal\Serializer\ArraySerializer;
use App\Services\CartItem\Transformer\IndexTransformer as CartItemIndexTransformer;
use App\Services\OrderDiscount\Transformer\IndexTransformer as OrderDiscountIndexTransformer;

class ShowTransformer extends Fractal\TransformerAbstract
{
    protected $defaultIncludes = [
        'items',
        'orderDiscounts'
    ];
    
    private function transformDateTime($data)
    {
        $timezone = config('app.timezone');
        if (!$data) return $data;
        return Carbon::parse($data)
            ->setTimezone($timezone)
            ->format('Y-m-d H:i:s');
    }

    public function transform(Cart $cart)
    {
        $creator = Arr::get($cart, 'creator');
        $updater = Arr::get($cart, 'updater');
        $customer = Arr::get($cart, 'customer');
        $createdAtConverted = $this->transformDateTime(Arr::get($cart, 'created_at'));
        $updatedAtConverted = $this->transformDateTime(Arr::get($cart, 'updated_at'));
        return [
            'id' => (int) $cart->id,
            'item_price' => $cart->item_price,
            'shipping_price' => $cart->shipping_price,
            'tax_price' => $cart->tax_price,
            'discount_price' => $cart->discount_price,
            'total_price' => $cart->total_price,
            'created_at' => $createdAtConverted,
            'updated_at' => $updatedAtConverted,
            'customer_id' => $cart->customer_id,
            'created_by' => $cart->created_by,
            'updated_by' => $cart->updated_by,
            'creator' => $creator,
            'updater' => $updater,
            'customer' => $customer,
            'items' => $cart->items,
            'orderDiscounts' => $cart->orderDiscounts
        ];
    }

    public function includeItems(Cart $cart)
    {
        $cartItems = $cart->items;
        return $this->collection($cartItems, new CartItemIndexTransformer);
    }

    public function includeOrderDiscounts(Cart $cart)
    {
        $orderDiscounts = $cart->orderDiscounts;
        return $this->collection($orderDiscounts, new OrderDiscountIndexTransformer);
    }
}
