<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class ProductOrderController extends Controller
{
    public function index()
    {
        // Fetch products ordered in July 2024 with client, category, and supplier names
        $orders = Order::join('product_orders', 'product_orders.order_id', '=', 'orders.id')
            ->join('products', 'product_orders.product_id', '=', 'products.id')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('suppliers', 'products.supplier_id', '=', 'suppliers.id')
            ->where(DB::raw("month('orders.order_date') = 7"))
            // ->whereMonth('orders.order_date', 7) see whereNull, whereIn, OrWhere, whereNotNul.....
            ->whereYear('orders.order_date', 2024)
            ->orderBy(DB::raw("CONCAT(customers.first_name, ' ', customers.last_name)"), "desc")
            ->select([
                'products.name as product_name',
                DB::raw("CONCAT(customers.first_name, ' ', customers.last_name) as customer_name"),
                'categories.name as category_name',
                DB::raw("CONCAT(suppliers.first_name, ' ', suppliers.last_name) as supplier_name"),
                'orders.order_date as order_date',
            ])
            ->get();

        return view('products.ordered_products', compact('orders'));
    }
}
