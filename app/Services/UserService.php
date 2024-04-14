<?php

namespace App\Services;

use App\Models\InvoiceDetail;
use App\Models\Product;

class UserService
{
    public static function calculateCart(array $cart)
    {
        $productIdList = [];

        foreach ($cart as $pair) {
            $productIdList[] = $pair['product_id'];
        }

        $products = Product::whereIn('id', $productIdList)->pluck('unit_price', 'id');

        $totalPrice = 0;

        foreach ($cart as $pair) {
            $productId = $pair['product_id'];
            $quantity = $pair['quantity'];

            $unitPrice = $products[$productId];

            $totalPrice += $unitPrice * $quantity;
        }

        return $totalPrice;
    }
}
