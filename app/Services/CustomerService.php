<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\Product;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;

class CustomerService
{
    /**
     * Tạo một khách hàng mới
     *
     * @param array $data
     * @return Customer
     */
    public static function createCustomer(array $data)
    {
        $customer = new Customer();
        $customer->name = $data['name'];
        $customer->phone_number = $data['phone_number'];
        $customer->save();

        return $customer;
    }

     /**
     * Cập nhật thông tin khách hàng
     *
     * @param int $customerId
     * @param array $data
     * @return bool
     */
    public static function updateCustomer(int $customerId, array $data)
    {
        $customer = Customer::find($customerId);
        if (!$customer) {
            return false;
        }

        $customer->name = $data['name'];
        $customer->phone_number = $data['phone_number'];
        $customer->save();

        return true;
    }

    /**
     * Tạo một đơn hàng mới
     *
     * @param array $data
     * @return Invoice
     */
    public static function createOrder(array $data)
    {
        $invoice = new Invoice();
        $invoice->user_id = $data['user_id'];
        $invoice->customer_id = $data['customer_id'];
        $invoice->table_number = $data['table_number'];
        $invoice->voucher_code = $data['voucher_code'] ?? null;
        $invoice->note = $data['note'] ?? null;
        $invoice->date = now();

        // Tính tổng giá trị đơn hàng
        $totalPrice = self::calculateOrderTotal($data['cart']);
        $invoice->total_price = $totalPrice;

        // Áp dụng mã giảm giá (nếu có)
        if ($data['voucher_code']) {
            $voucher = Voucher::where('voucher_code', $data['voucher_code'])->first();
            if ($voucher) {
                list($discountPrice, $finalPrice) = self::applyVoucher($totalPrice, $voucher->type, $voucher->amount);
                $invoice->discount_price = $discountPrice;
                $invoice->final_price = $finalPrice;
            }
        } else {
            $invoice->final_price = $totalPrice;
        }

        $invoice->status = 'pending';
        $invoice->save();

        // Lưu chi tiết đơn hàng
        self::storeOrderDetails($data['cart'], $invoice->id);

        return $invoice;
    }

     /**
     * Tính tổng giá trị đơn hàng
     *
     * @param array $cart
     * @return float
     */
    public static function calculateOrderTotal(array $cart)
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

    /**
     * Áp dụng mã giảm giá
     *
     * @param float $totalPrice
     * @param string $voucherType
     * @param float $voucherAmount
     * @return array
     */
    public static function applyVoucher(float $totalPrice, string $voucherType, float $voucherAmount)
    {
        if ($voucherType == 'direct') {
            $discountPrice = $voucherAmount;
            $finalPrice = $totalPrice - $discountPrice;

            return [$discountPrice, $finalPrice];
        }

        $discountPrice = $totalPrice * $voucherAmount / 100;
        $finalPrice = $totalPrice - $discountPrice;

        return [$discountPrice, $finalPrice];
    }

    /**
     * Lưu chi tiết đơn hàng
     *
     * @param array $cart
     * @param int $invoiceId
     * @return void
     */
    public static function storeOrderDetails(array $cart, int $invoiceId)
    {
        $productIdList = [];

        foreach ($cart as $pair) {
            $productIdList[] = $pair['product_id'];
        }

        $productsPrice = Product::whereIn('id', $productIdList)->pluck('unit_price', 'id');
        $productsName = Product::whereIn('id', $productIdList)->pluck('name', 'id');

        $data = [];

        foreach ($cart as $pair) {
            $productId = $pair['product_id'];

            $data[] = [
                'invoice_id' => $invoiceId,
                'product_id' => $productId,
                'quantity' => $pair['quantity'],
                'unit_price' => $productsPrice[$productId],
                'product_name' => $productsName[$productId],
            ];
        }

        InvoiceDetail::insert($data);
    }

    /**
     * Lấy danh sách đơn hàng của một khách hàng
     *
     * @param int $customerId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getCustomerOrders(int $customerId)
    {
        $orders = Invoice::where('customer_id', $customerId)
            ->with('invoiceDetails.products')
            ->get();

        return $orders;
    }

}