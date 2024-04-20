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
}