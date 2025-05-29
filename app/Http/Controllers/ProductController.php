<?php

namespace App\Http\Controllers;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductExport;
use App\Imports\ProductImport;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ProductRequest;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function export()
    {
        return Excel::download(new ProductExport, 'products.xlsx');
    }
    public function import(Request $request)
    {
      
        Excel::import(new ProductImport, $request->file('file'));

        return back()->with('success', 'Products imported successfully.');
    }


    public function index(Request $request)
    {
//dd(request('search'));
        $products = Product::with(['category', 'supplier', 'stock'])
            ->when(request('search'), function($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
                 //   ->orWhere('code', 'like', "%{$search}%");
            })
            ->paginate(10);

        $categories = Category::all();
        $suppliers = Supplier::all();

        if (request()->ajax()) {

            return response()->json([
                'products' => $products->items(),
                'pagination' => [
                    'total' => $products->total(),
                    'per_page' => $products->perPage(),
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                ]
            ]);
        }

        return view('products.index', compact('products', 'categories', 'suppliers'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(ProductRequest $request)
    {
        $validated = $request->validated();

        // Handle file upload if present
        if ($request->hasFile('picture')) {
            $picturePath = $request->file('picture')->store('products', 'public');
            $validated['picture'] = $picturePath;
        }

        $product = Product::create($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'product' => $product]);
        }

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        return response()->json($product);
    }

    /**
     * Update the specified product in storage.
     */
    public function update(ProductRequest $request, Product $product)
    {
        $validated = $request->validated();

        // Handle file upload if present
        if ($request->hasFile('picture')) {
            // Delete old picture if exists
            if ($product->picture) {
                Storage::disk('public')->delete($product->picture);
            }

            $picturePath = $request->file('picture')->store('products', 'public');
            $validated['picture'] = $picturePath;
        }

        $product->update($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'product' => $product]);
        }

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product)
    {
        // Delete product image if exists
        if ($product->picture) {
            Storage::disk('public')->delete($product->picture);
        }

        $product->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Display the number of orders per product.
     */
    public function ordersCount()
    {
        $products = Product::select('products.name')
            ->leftJoin('product_orders', 'products.id', '=', 'product_orders.product_id')
            ->groupBy('products.id', 'products.name')
            ->selectRaw('products.name, COUNT(product_orders.order_id) as orders_count')
            ->get();
        return view('products.orders_count', compact('products'));
    }

    /**
     * Display products with more than 6 orders.
     */
    public function productsMoreThan6Orders()
    {
        $products = Product::select('products.id', 'products.name')
            ->leftJoin('product_orders', 'products.id', '=', 'product_orders.product_id')
            ->groupBy('products.id', 'products.name')
            ->selectRaw('products.name, COUNT(product_orders.order_id) as orders_count')
            ->havingRaw('COUNT(product_orders.order_id) > 6')
            ->get();
        return view('products.products_more_than_6_orders', compact('products'));
    }
}
