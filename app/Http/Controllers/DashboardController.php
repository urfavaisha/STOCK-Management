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
    public function index(): View
    {
        return view('dashboard');
    }

    public function customers(): View
    {
        $customers = Customer::paginate(10); // dd($customers); //dump and dey;
        return view('customers.index', compact('customers'));
    }

    public function suppliers(): View
    {
        return view('suppliers.index', ['suppliers' => Supplier::all()]);
    }

    public function products(): View
    {
        return view('products.index', [
            'products' => Product::with(['category', 'supplier', 'stock'])->get()
        ]);
    }


 public function cooksess(): View
    {
        $user = Auth::user();
        $pic = $user?->avatar ?? 'default-avatar.png';
        return view('cooksess', compact('pic'));
    }




    public function productsBySupplier(): View
    {
        $suppliers = Supplier::all();
        return view('products.by-supplier', compact('suppliers'));

    }

    public function getProductsBySupplier(Supplier $supplier)
    {
        $products = Product::with(['stock','category'])
        ->where('supplier_id', $supplier->id)

        ->get();
        return view('products._products_by_supplier', compact('products'));
    }

    public function productsByStore(): View
    {
        $stores = Store::all();
        return view('products.by-store', compact('stores'));
    }

    public function getProductsByStore(Store $store)
    {
        $products = Product::with(['category', 'stock'])
            ->whereHas('stock', function($query) use ($store) {
                $query->where('store_id', $store->id);
            })
            ->get();

        return response()->json($products);
    }

    public function orders()
    {
        return view("orders.index");
    }

    /**
     * Display customer names for each order
     */
    public function customerOrders(): View
    //{ $orders = Order::with('customer')->get();
    //  return view('dashboard.customer_orders', compact('orders'));}

    {
        $orders = Order::join("customers","orders.customer_id","=","customers.id")
        ->select(DB::raw("concat(customers.first_name,' ', customers.last_name) as customer_name"),"orders.id as order_id", "orders.order_date as order_date")
        ->get();
        return view('dashboard.customer_orders', compact('orders'));
    }


    public function suppliersByCustomer(): View
    {
        // Get the first customer from the database
        $customer = Customer::first();
        
        if (!$customer) {
            return view('dashboard.suppliers_by_customer', ['suppliers' => collect()]);
        }

        $productIds = Customer::where("customers.id", $customer->id)
            ->join("orders","customers.id","=","orders.customer_id")
            ->join("product_orders","orders.id","=","product_orders.order_id")
            ->select("product_id")
            ->pluck("product_id");

        $suppliers = Product::whereIn("products.id",$productIds)
            ->join("suppliers","products.supplier_id","=","suppliers.id")
            ->select("first_name","last_name","name")
            ->get();

        return view('dashboard.suppliers_by_customer', compact('suppliers', 'customer'));
    }

    /**
     * Display products stored in the same warehouses as products supplied by a specific supplier
     */
    public function productsSameWarehouse(): View
    {
        // Get Premium Supplier as reference
        $supplier = Supplier::where('first_name', 'Premium')
            ->where('last_name', 'Supplier')
            ->first();

        if (!$supplier) {
            return view('dashboard.products_same_warehouse', ['products' => collect(), 'supplier' => null]);
        }

        // Get store IDs where the supplier's products are stored
        $storeIds = Store::whereHas('stocks.product.supplier', function($query) use ($supplier) {
            $query->where('id', $supplier->id);
        })->pluck('id');

        // Get products in those stores that are not from this supplier
        $products = Product::with(['category', 'supplier', 'stock.store'])
            ->whereHas('stock', function($query) use ($storeIds) {
                $query->whereIn('store_id', $storeIds);
            })
            ->whereDoesntHave('supplier', function($query) use ($supplier) {
                $query->where('id', $supplier->id);
            })
            ->get();

        return view('dashboard.products_same_warehouse', compact('products', 'supplier'));
    }

    /**
     * Display the number of products per warehouse
     */
    public function productsPerWarehouse(): View
    {
        $warehouses = Store::withCount('stocks')->get();
        return view('dashboard.products_per_warehouse', compact('warehouses'));
    }

    /**
     * Display the value of each warehouse (sum of product values)
     */
    public function warehouseValues(): View
    {
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

    /**
     * Display warehouses with value greater than a reference warehouse
     */
    public function warehousesGreaterValue(): View
    {
        // Get Lind-Gislason as reference warehouse
        $referenceStore = Store::where('name', 'Lind-Gislason')->first();
        
        if (!$referenceStore) {
            return view('dashboard.warehouses_greater_value', [
                'warehouses' => collect(),
                'referenceWarehouse' => 'Lind-Gislason',
                'referenceValue' => 0
            ]);
        }

        // Calculate total value for reference warehouse
        $referenceValue = Stock::where('store_id', $referenceStore->id)
            ->join('products', 'stocks.product_id', '=', 'products.id')
            ->selectRaw('SUM(products.price * stocks.quantity_stock) as total_value')
            ->value('total_value') ?? 0;

        // Get all warehouses with their total values
        $warehouses = Store::withCount('stocks')
            ->withSum(['stocks as total_value' => function($query) {
                $query->join('products', 'stocks.product_id', '=', 'products.id')
                    ->selectRaw('SUM(products.price * stocks.quantity_stock)');
            }], 'stocks.quantity_stock')
            ->where('id', '!=', $referenceStore->id) // Exclude reference warehouse
            ->having('total_value', '>', $referenceValue)
            ->orderBy('total_value', 'desc')
            ->get();

        return view('dashboard.warehouses_greater_value', [
            'warehouses' => $warehouses,
            'referenceWarehouse' => 'Lind-Gislason',
            'referenceValue' => $referenceValue
        ]);
    }


    public function saveCookie()
   {
      $name = request()->input("txtCookie");
      //Cookie::put("UserName",$name,6000000);
      Cookie::queue("UserName",$name,6000000);
      return redirect()->back();
   }


      public function saveSession(Request $request)
   {
            $name = $request->input("txtSession");
            $request->session()->put('SessionName', $name);
            return redirect()->back();
   }

    public function chart(): View
    {
        // Simple counts for stores and categories
        $stores = Store::select('name')
            ->withCount('stocks')
            ->having('stocks_count', '>', 1)
            ->get();
        
        // Get unique categories with their product counts
        $categories = Category::select('categories.name', DB::raw('COUNT(products.id) as products_count'))
            ->leftJoin('products', 'categories.id', '=', 'products.category_id')
            ->groupBy('categories.name')
            ->get();

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

    public function saveAvatar(Request $request)
{
    $request->validate([
        'avatarFile' => 'required|image',
    ]);

    $ext = $request->avatarFile->getClientOriginalExtension();
    $name = Str::random(30) . time() . "." . $ext;

    $request->avatarFile->move(public_path('storage/avatars'), $name);

    $user = Auth::user();
    if ($user) {
        $user->avatar = $name;
        $user->save();
        dd($name); // Debugging statement to check if the file name is set correctly
        return redirect()->back()->with('success', 'Avatar mis à jour.')->with('pic', $name);
    }

    return redirect()->back()->withErrors(['error' => 'Utilisateur non authentifié']);
}



}
