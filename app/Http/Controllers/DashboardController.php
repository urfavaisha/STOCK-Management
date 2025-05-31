<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Stock;
use App\Models\Store;
use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    // Basic view controllers
    public function index(): View
    {
        return view('dashboard');
    }

    // Customer management
    public function customers(): View
    {
        // Paginate customers with 10 per page for better performance
        $customers = Customer::paginate(10);
        return view('customers.index', compact('customers'));
    }

    // Supplier management
    public function suppliers(): View
    {
        return view('suppliers.index', ['suppliers' => Supplier::all()]);
    }

    // Product management with related data
    public function products(): View
    {
        // Eager load relationships to prevent N+1 query problem
        return view('products.index', [
            'products' => Product::with(['category', 'supplier', 'stock'])->get()
        ]);
    }

    // Cookie, Session and Avatar management
    public function cooksess(): View
    {
        // Get current user and their avatar, use default if none exists
        $user = Auth::user();
        $pic = $user?->avatar ?? 'default-avatar.png';
        return view('cooksess', compact('pic'));
    }

    // Product-Supplier relationship views
    public function productsBySupplier(): View
    {
        $suppliers = Supplier::all();
        return view('products.by-supplier', compact('suppliers'));
    }

    public function getProductsBySupplier(Supplier $supplier)
    {
        // Get all products for a specific supplier with their stock and category info
        $products = Product::with(['stock','category'])
            ->where('supplier_id', $supplier->id)
            ->get();
        return view('products._products_by_supplier', compact('products'));
    }

    // Product-Store relationship views
    public function productsByStore(): View
    {
        $stores = Store::all();
        return view('products.by-store', compact('stores'));
    }

    public function getProductsByStore(Store $store)
    {
        // Get products that exist in a specific store
        $products = Product::with(['category', 'stock'])
            ->whereHas('stock', function($query) use ($store) {
                $query->where('store_id', $store->id);
            })
            ->get();

        return response()->json($products);
    }

    // Order management
    public function orders()
    {
        return view("orders.index");
    }

    // Customer orders with formatted names
    public function customerOrders(): View
    {
        // Join customers and orders tables to get customer names with their orders
        $orders = Order::join("customers","orders.customer_id","=","customers.id")
            ->select(DB::raw("concat(customers.first_name,' ', customers.last_name) as customer_name"),"orders.id as order_id", "orders.order_date as order_date")
            ->get();
        return view('dashboard.customer_orders', compact('orders'));
    }

    // Find suppliers for customers who have ordered products
    public function suppliersByCustomer(): View
    {
        // Find first customer who has orders with products from suppliers
        $customer = DB::table('customers')
            ->join('orders', 'customers.id', '=', 'orders.customer_id')
            ->join('product_orders', 'orders.id', '=', 'product_orders.order_id')
            ->join('products', 'product_orders.product_id', '=', 'products.id')
            ->join('suppliers', 'products.supplier_id', '=', 'suppliers.id')
            ->select('customers.id', 'customers.first_name', 'customers.last_name')
            ->groupBy('customers.id', 'customers.first_name', 'customers.last_name')
            ->havingRaw('COUNT(DISTINCT suppliers.id) > 0')
            ->first();
        
        if (!$customer) {
            return view('dashboard.suppliers_by_customer', ['suppliers' => collect()]);
        }

        // Get all product IDs ordered by this customer
        $productIds = Customer::where("customers.id", $customer->id)
            ->join("orders","customers.id","=","orders.customer_id")
            ->join("product_orders","orders.id","=","product_orders.order_id")
            ->select("product_id")
            ->pluck("product_id");

        // Get suppliers for these products
        $suppliers = Product::whereIn("products.id",$productIds)
            ->join("suppliers","products.supplier_id","=","suppliers.id")
            ->select("first_name","last_name","name")
            ->get();

        return view('dashboard.suppliers_by_customer', compact('suppliers', 'customer'));
    }

    // Find products that share warehouses with a reference product
    public function productsSameWarehouse(): View
    {
        // Find a product that has other products sharing its warehouses
        $referenceProduct = DB::table('products')
            ->join('stocks', 'products.id', '=', 'stocks.product_id')
            ->join('stores', 'stocks.store_id', '=', 'stores.id')
            ->select('products.id', 'products.name')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('stocks as s2')
                    ->join('products as p2', 's2.product_id', '=', 'p2.id')
                    ->whereRaw('s2.store_id = stocks.store_id')
                    ->whereRaw('p2.id != products.id');
            })
            ->first();

        if (!$referenceProduct) {
            return view('dashboard.products_same_warehouse', ['products' => collect(), 'referenceProduct' => null]);
        }

        // Get all store IDs where reference product is stored
        $storeIds = DB::table('stocks')
            ->where('product_id', $referenceProduct->id)
            ->pluck('store_id');

        // Get other products in these stores
        $products = Product::with(['category', 'supplier', 'stock.store'])
            ->whereHas('stock', function($query) use ($storeIds) {
                $query->whereIn('store_id', $storeIds);
            })
            ->where('id', '!=', $referenceProduct->id)
            ->get();

        return view('dashboard.products_same_warehouse', compact('products', 'referenceProduct'));
    }

    // Count products in each warehouse
    public function productsPerWarehouse(): View
    {
        $warehouses = Store::withCount('stocks')->get();
        return view('dashboard.products_per_warehouse', compact('warehouses'));
    }

    // Calculate total value of each warehouse
    public function warehouseValues(): View
    {
        // Calculate total value (price * quantity) for each warehouse
        $warehouses = Store::with('stocks.product')->get()
            ->map(function($store) {
                $totalValue = $store->stocks->sum(function($stock) {
                    return $stock->quantity_stock * $stock->product->price;
                });

                return [
                    'id' => $store->id,
                    'name' => $store->name,
                    'total_value' => $totalValue
                ];
            });

        return view('dashboard.warehouse_values', compact('warehouses'));
    }

    // Find warehouses with value greater than reference warehouse
    public function warehousesGreaterValue(): View
    {
        $referenceStore = Store::first();

        if (!$referenceStore) {
            return view('dashboard.warehouses_greater_value', ['warehouses' => collect(), 'referenceWarehouse' => null]);
        }

        // Calculate total value of reference store
        $referenceValue = Stock::join('products', 'stocks.product_id', '=', 'products.id')
            ->where('stocks.store_id', $referenceStore->id)
            ->selectRaw('SUM(products.price * stocks.quantity_stock) as total_value')
            ->value('total_value');

        // Find warehouses with greater total value
        $warehouses = Store::with(['stocks.product'])
            ->get()
            ->map(function ($store) {
                $totalValue = $store->stocks->sum(function ($stock) {
                    return $stock->product->price * $stock->quantity_stock;
                });
                return [
                    'id' => $store->id,
                    'name' => $store->name,
                    'total_value' => $totalValue
                ];
            })
            ->filter(function ($warehouse) use ($referenceValue) {
                return $warehouse['total_value'] > $referenceValue;
            })
            ->values();

        return view('dashboard.warehouses_greater_value', compact('warehouses', 'referenceStore', 'referenceValue'));
    }

    // Cookie management
    public function saveCookie()
    {
        // Save cookie for 6000000 minutes (about 11.4 years)
        $name = request()->input("txtCookie");
        Cookie::queue("UserName", $name, 6000000);
        return redirect()->back();
    }

    // Session management
    public function saveSession(Request $request)
    {
        $name = $request->input("txtSession");
        $request->session()->put('SessionName', $name);
        return redirect()->back();
    }

    // Chart data preparation
    public function chart(): View
    {
        // Get stores with more than 1 stock item
        $stores = Store::select('name')
            ->withCount('stocks')
            ->having('stocks_count', '>', 1)
            ->get();
        
        // Get categories with their product counts
        $categories = Category::select('categories.name', DB::raw('COUNT(products.id) as products_count'))
            ->leftJoin('products', 'categories.id', '=', 'products.category_id')
            ->groupBy('categories.name')
            ->get();

        // Prepare data for bar and doughnut charts
        $data = [
            'bar' => [
                'labels' => $stores->pluck('name'),
                'values' => $stores->pluck('stocks_count')
            ],
            'doughnut' => [
                'labels' => $categories->pluck('name'),
                'values' => $categories->pluck('products_count')
            ]
        ];

        return view('chart', compact('data'));
    }

    // Avatar management
    public function saveAvatar(Request $request)
    {
        // Validate that uploaded file is an image
        $request->validate([
            'avatarFile' => 'required|image',
        ]);

        $user = Auth::user();
        if ($user) {
            // Delete old avatar if it exists
            if ($user->avatar) {
                $oldAvatarPath = public_path('storage/avatars/' . $user->avatar);
                if (file_exists($oldAvatarPath)) {
                    unlink($oldAvatarPath);
                }
            }

            // Generate unique filename and save new avatar
            $ext = $request->avatarFile->getClientOriginalExtension();
            $name = Str::random(30) . time() . "." . $ext;

            $request->avatarFile->move(public_path('storage/avatars'), $name);

            // Update user's avatar in database
            $user->avatar = $name;
            $user->save();

            return redirect()->back()->with('pic', $name);
        }

        return redirect()->back()->withErrors(['error' => 'User not authenticated']);
    }
}
