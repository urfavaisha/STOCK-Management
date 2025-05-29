<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function getCustomerOrders(Customer $customer)
    {

        $orders = $customer->orders()->get();
        return response()->json($orders);
    }
        public function ordersGreaterThanOrder60()
    {
        // Subquery to get total amount for order 60
        $order60Total = DB::table('product_orders')
            ->where('order_id', 60)
            ->selectRaw('SUM(price * quantity)')
         //   ->get();
            ->value(DB::raw('SUM(price * quantity)')); // to recieve a scalar value instead of collection
         //   dd($order60Total);

        $orders = Order::join('customers', 'orders.customer_id', '=', 'customers.id')
            ->join('product_orders', 'orders.id', '=', 'product_orders.order_id')
            ->select(
                'orders.id',
                DB::raw("CONCAT(customers.first_name, ' ', customers.last_name) as customer_name"),
                'orders.order_date',
                DB::raw('SUM(product_orders.price * product_orders.quantity) as total_amount')
            )
            ->groupBy('orders.id', 'customers.first_name', 'customers.last_name', 'orders.order_date')
            ->having('total_amount', '>', $order60Total)
            ->orderBy('orders.id')
            ->get();

    //     $orders = Order::join('customers', 'orders.customer_id', '=', 'customers.id')
    // ->join('product_orders', 'orders.id', '=', 'product_orders.order_id')
    // ->select(
    //     'orders.id',
    //     DB::raw("CONCAT(customers.first_name, ' ', customers.last_name) as customer_name"),
    //     'orders.order_date',
    //     DB::raw('SUM(product_orders.price * product_orders.quantity) as total_amount')
    // )
    // ->groupBy('orders.id', 'customers.first_name', 'customers.last_name', 'orders.order_date')
    // ->havingRaw('SUM(product_orders.price * product_orders.quantity) > (
    //     SELECT SUM(price * quantity)
    //     FROM product_orders
    //     WHERE order_id = 60
    // )')
    // ->orderBy('orders.id')
    // ->get();
        return view('orders.orders_greater_than_60', compact('orders', 'order60Total'));
    }


    public function getOrderDetails(Order $order)
    {
        return view('orders._order_details', compact('order'));
    }
    public function orderTotals()
    {
        $orders = Order::join('customers', 'orders.customer_id', '=', 'customers.id')
            ->join('product_orders', 'orders.id', '=', 'product_orders.order_id')
            ->select(
                'orders.id',
                DB::raw("CONCAT(customers.first_name, ' ', customers.last_name) as customer_name"),
                'orders.order_date',
                DB::raw('SUM(product_orders.price * product_orders.quantity) as total_amount')
                )
            ->groupBy('orders.id', 'customers.first_name', 'customers.last_name', 'orders.order_date')
            ->orderBy('orders.id')
            ->get();
        return view('orders.order_totals', compact('orders'));
                }
}
