<?php

namespace App\Http\Controllers;


use App\Models\Order;
use App\Models\Customer;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\CustomerRequest;

class CustomerController extends Controller
{
    /**
     * Display a listing of the customers.
     */
    public function index(): View
    {
        $customers =  Customer::paginate(20);
        dd($customers);
        return view('customers.index', compact('customers'));
    }



    /**
     * Show the form phpfor creating a new customer.
     */
    public function create(): View
    {
        return view('customers.create');
    }

    /**
     * Show customers who ordered the same products as Annabel Stehr.
     */
    public function sameProductsCustomers()
    {
        // Find the customer Annabel Stehr
        $customer =Customer::whereRaw("CONCAT(first_name, ' ', last_name) = ?", ['Annabel Stehr'])->first();
        if (!$customer) {
            return view('customers.same_products_customers', ['customers' => collect()]);
        }
        // Get product IDs ordered by Annabel Stehr
        $productIds = Order::join('product_orders', 'orders.id', '=', 'product_orders.order_id')
            ->where('orders.customer_id', $customer->id)
          ->pluck('product_orders.product_id');



        // Get other customers who ordered the same products
        $customers = DB::table('orders')
            ->join('product_orders', 'orders.id', '=', 'product_orders.order_id')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->join('products', 'product_orders.product_id', '=', 'products.id')
            ->whereIn('product_orders.product_id', $productIds)
            ->where('orders.customer_id', '!=', $customer->id)
            ->select([
                DB::raw("CONCAT(customers.first_name, ' ', customers.last_name) as customer_name"),
                'customers.email',
                'products.name as product_name',
                'orders.order_date as order_date',
            ])
            ->orderBy('customer_name')
            ->get();
        return view('customers.same_products_customers', compact('customers'));
    }


    /**
     * Store a newly created customer in storage.
     */
    public function store(CustomerRequest $request): RedirectResponse
    {

        // The request is automatically validated by the CustomerRequest class
        Customer::create($request->validated());
        // $customer = new Customer();
        // $customer->first_name = $request["first_name"];
        // $customer->last_name = $request["last_name"];
        // $customer->phone = $request["phone"];
        // $customer->address = $request["address"];
        // $customer->save();
        return redirect()->route('customers.index')
            ->with('success', 'Customer created successfully.');
        }


    /**
     * Show the form for editing the specified customer.
     */
    public function edit(Customer $customer): View
    {
        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(CustomerRequest $request, Customer $customer): RedirectResponse
    {
        $customer->update($request->validated());
        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }



    /**
     * Show the form for confirming deletion of the specified customer.
     */
    public function delete(Customer $customer): View
    {
        return view('customers.delete', compact('customer'));
    }



    /*** Remove the specified customer from storage.*/
    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
    }



    /*** Search for customers by name, email, phone or address.*/
    public function searchTerm(Request $request, $term)
    {
        $customers = Customer::where('first_name', 'like', "%{$term}%")
            ->orWhere('last_name', 'like', "%{$term}%")
            ->orWhere('email', 'like', "%{$term}%")
            ->orWhere('phone', 'like', "%{$term}%")
            ->orWhere('address', 'like', "%{$term}%")
            ->get();
        //    dd($customers); //dump and dey
        return response()->json($customers);
        }


  /*** Search for customers by name, email, phone or address.*/
    public function search(Request $request)
    {
        $term = $request->input('term');
        $customers = Customer::where('first_name', 'like', "%{$term}%")
            ->orWhere('last_name', 'like', "%{$term}%")
            ->orWhere('email', 'like', "%{$term}%")
            ->orWhere('phone', 'like', "%{$term}%")
            ->orWhere('address', 'like', "%{$term}%")
            ->paginate(10);

        return response()->json([
            'customers' => $customers->items(),
            'pagination' => [
                'total' => $customers->total(),
                'per_page' => $customers->perPage(),
                'current_page' => $customers->currentPage(),
                'last_page' => $customers->lastPage(),
                'from' => $customers->firstItem(),
                'to' => $customers->lastItem(),
                'links' => $customers->linkCollection()->toArray()
            ]
        ]);
        }
    }


