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

}