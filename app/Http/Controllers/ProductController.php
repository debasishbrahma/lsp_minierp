<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{

    public function __construct()
    {
        //$this->middleware('role:admin')->except(['index']);
        // Apply admin-only middleware to edit, update, and destroy
        $this->middleware('role:admin')->only(['edit', 'update', 'destroy']);
    }

    public function index()
    {
        // $products = Product::all();
        $products = auth()->user()->isAdmin() ? Product::withTrashed()->get() : Product::all();
        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit_price' => 'required|numeric|min:0',
            'quantity_available' => 'required|integer|min:0',
        ]);

        Product::create($request->all());
        Log::info('Product created', ['user_id' => auth()->id()]);

        return redirect()->route('dashboard')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        /*  if (!auth()->user()->isAdmin()) {
            //  Livewire::dispatch('showToast', 'Unauthorized to edit products.', 'error');
            return redirect()->route('products.index');
        } */
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        /*  if (!auth()->user()->isAdmin()) {
            //  Livewire::dispatch('showToast', 'Unauthorized to edit products.', 'error');
            return redirect()->route('dashboard');
        } */

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit_price' => 'required|numeric|min:0',
            'quantity_available' => 'required|integer|min:0',
        ]);

        $product->update($request->all());
        Product::clearCache($product->id);

        Log::info('Product updated', ['product_id' => $product->id]);
        //product.index
        return redirect()->route('dashboard')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        /* if (!auth()->user()->isAdmin()) {
            //  Livewire::dispatch('showToast', 'Unauthorized to delete products.', 'error');
            return redirect()->route('dashboard');
        } */

        $product->delete();
        Product::clearCache($product->id);

        Log::info('Product soft deleted', ['product_id' => $product->id]);

        return redirect()->route('dashboard')->with('success', 'Product deleted successfully.');
    }
}
