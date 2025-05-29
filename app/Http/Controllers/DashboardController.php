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


 public function partie26(): View
    {
        $user = Auth::user();
        $pic = $user?->avatar ?? 'default-avatar.png';
        return view('partie26',compact('pic'));
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
        $productIds = Customer::where("customers.first_name","Harold")
        ->where("customers.last_name","Feeney")
        ->join("orders","customers.id","=","orders.customer_id")
        ->join("product_orders","orders.id","=","product_orders.order_id")
        ->select("product_id")
        ->pluck("product_id");


         $suppliers = Product::whereIn("products.id",$productIds)
        ->join("suppliers","products.supplier_id","=","suppliers.id")
        ->select("first_name","last_name","name")
        ->get();

        return view('dashboard.suppliers_by_customer', compact('suppliers'));
    }

    /**
     * Display products stored in the same warehouses as products supplied by 'Scottie Crona'
     */

    public function productsSameWarehouse(): View
    {
        $supplier = Supplier::where('first_name', 'Ruthie')->where('last_name', 'Will')->first();

        if (!$supplier) {
            return view('dashboard.products_same_warehouse', ['products' => collect(), 'supplier' => null]);
        }

        // Obtenir les identifiants des magasins où sont stockés les produits de Scottie Crona
        $storeIds = Store::whereHas('stocks.product.supplier', function($query) use ($supplier) {
            $query->where('id', $supplier->id);
        })->pluck('id');
        //dd($storeIds);

        // Obtenir des produits dans les magasins qui ne sont pas de Scottie Crona
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

    /*** Display warehouses with value greater than Lind-Gislason warehouse */
    public function warehousesGreaterValue(): View
    {
        // First, find the Lind-Gislason warehouse and calculate its value
        $lindGislasonWarehouse = Store::where('name', 'Watsica-Gutmann')->first();

        if (!$lindGislasonWarehouse) {
            return view('dashboard.warehouses_greater_value', [
                'warehouses' => collect(),
                'referenceWarehouse' => null,
                'referenceValue' => 0
            ]);
        }

        $referenceValue = $lindGislasonWarehouse->stocks->sum(function($stock) {
            return $stock->quantity_stock * $stock->product->price;
        });

        // Get all warehouses with their values
        $allWarehouses = Store::with('stocks.product')->get()
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

        // Filter warehouses with value greater than Lind-Gislason
        $warehousesGreaterValue = $allWarehouses->filter(function($warehouse) use ($referenceValue, $lindGislasonWarehouse) {
            return $warehouse['id'] != $lindGislasonWarehouse->id && $warehouse['total_value'] > $referenceValue;
        })->values();

        return view('dashboard.warehouses_greater_value', [
            'warehouses' => $warehousesGreaterValue,
            'referenceWarehouse' => $lindGislasonWarehouse->name,
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



   public function saveAvatar()
   {
    request()->validate([
        'avatarFile'=>'required|image',
            ]);
    $ext = request()->avatarFile->getClientOriginalExtension();
    $name = Str::random(30).time().".".$ext;
    request()->avatarFile->move(public_path('storage/avatars'),$name);
    $user =  Auth::user();
if ($user) {
    $user->avatar = $name;
    $user->save();
    return redirect()->back()->with('success', 'Avatar mis à jour.');
}

   }


}
