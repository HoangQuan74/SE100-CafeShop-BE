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

     /**
     * Cập nhật thông tin sản phẩm
     *
     * @param int $productId
     * @param array $data
     * @return bool
     */
    public static function updateProduct(int $productId, array $data)
    {
        $product = Product::find($productId);
        if (!$product) {
            return false;
        }

        $product->name = $data['name'];
        $product->description = $data['description'];
        $product->unit_price = $data['unit_price'];
        $product->category_id = $data['category_id'];
        $product->supplier_id = $data['supplier_id'];
        $product->save();

        return true;
    }

    /**
     * Xóa một sản phẩm
     *
     * @param int $productId
     * @return bool
     */
    public static function deleteProduct(int $productId)
    {
        $product = Product::find($productId);
        if (!$product) {
            return false;
        }

        $product->delete();

        return true;
    }

    /**
     * Tạo một danh mục sản phẩm mới
     *
     * @param string $name
     * @return Category
     */
    public static function createCategory(string $name)
    {
        $category = new Category();
        $category->name = $name;
        $category->save();

        return $category;
    }

    /**
     * Cập nhật thông tin danh mục sản phẩm
     *
     * @param int $categoryId
     * @param string $name
     * @return bool
     */
    public static function updateCategory(int $categoryId, string $name)
    {
        $category = Category::find($categoryId);
        if (!$category) {
            return false;
        }

        $category->name = $name;
        $category->save();

        return true;
    }

    /**
     * Xóa một danh mục sản phẩm
     *
     * @param int $categoryId
     * @return bool
     */
    public static function deleteCategory(int $categoryId)
    {
        $category = Category::find($categoryId);
        if (!$category) {
            return false;
        }

        // Xóa tất cả sản phẩm thuộc danh mục này
        $products = Product::where('category_id', $categoryId)->get();
        foreach ($products as $product) {
            $product->delete();
        }

        $category->delete();

        return true;
    }
}