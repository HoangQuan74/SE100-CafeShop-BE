<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class AdminService
{
    /**
     * Tạo một sản phẩm mới
     *
     * @param array $data
     * @return Product
     */
    public static function createProduct(array $data)
    {
        $product = new Product();
        $product->name = $data['name'];
        $product->description = $data['description'];
        $product->unit_price = $data['unit_price'];
        $product->category_id = $data['category_id'];
        $product->supplier_id = $data['supplier_id'];
        $product->save();

        return $product;
    }
}