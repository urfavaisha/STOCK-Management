<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function productsByCategory()
    {
        $categories = Category::all();
        $products = collect();
        return view('products.by-category', compact('categories', 'products'));
    }

    public function getProductsByCategory(Category $category)
    {
        $categories = Category::all();
        $products = $category->products;
        return view('products.by-category', compact('categories', 'products'));
    }
}
