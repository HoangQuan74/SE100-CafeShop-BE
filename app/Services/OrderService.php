<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * Lấy danh sách đơn hàng theo trạng thái
     *
     * @param string $status
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getOrdersByStatus(string $status)
    {
        return Invoice::where('status', $status)
            ->with('invoiceDetails.products', 'customer', 'staff')
            ->get();
    }
}