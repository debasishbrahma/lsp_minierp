<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Quotation;
use App\Notifications\QuotationStatusUpdated;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class QuotationController extends Controller
{
    public function index()
    {
        $quotations = auth()->user()->isAdmin()
            ? Quotation::with('user')->withTrashed()->get()
            : auth()->user()->quotations()->withTrashed()->get();
        return view('quotations.index', compact('quotations'));
    }

    public function create()
    {
        $products = Product::all();
        return view('quotations.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        $totalPrice = 0;
        $items = [];

        foreach ($request->products as $item) {
            $product = Product::getCached($item['product_id']);
            if ($item['quantity'] > $product->quantity_available) {
                return back()->withErrors(['products' => "Insufficient stock for {$product->name}."]);
            }
            $subtotal = $product->unit_price * $item['quantity'];
            $totalPrice += $subtotal;
            $items[] = [
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $product->unit_price,
                'subtotal' => $subtotal,
            ];
        }

        $quotation = Quotation::create([
            'customer_name' => $request->customer_name,
            'user_id' => auth()->id(),
            'total_price' => $totalPrice,
        ]);

        $quotation->items()->createMany($items);
        Cache::store('redis')->forget('quotations_user_' . auth()->id());

        Log::info('Quotation created', ['quotation_id' => $quotation->id]);

        return redirect()->route('dashboard')->with('success', 'Quotation created successfully.');
    }

    public function show(Quotation $quotation)
    {
        $this->authorizeQuotation($quotation);
        return view('quotations.show', compact('quotation'));
    }

    public function edit(Quotation $quotation)
    {
        if ($quotation->status !== 'pending' || (!auth()->user()->isAdmin() && $quotation->user_id !== auth()->id())) {
            return redirect()->route('quotations.index');
        }
        $products = Product::all();
        return view('quotations.edit', compact('quotation', 'products'));
    }

    public function update(Request $request, Quotation $quotation)
    {

        if ($quotation->status !== 'pending' && !auth()->user()->isAdmin()) {

            return redirect()->route('quotations.index');
        }

        $request->validate([
            'customer_name' => 'required|string|max:255',
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        $totalPrice = 0;
        $items = [];

        foreach ($request->products as $item) {
            /*  $product = Product::getCached($item['product_id']);
            if ($item['quantity'] > $product->quantity_available) { */
            $product = Product::find($item['product_id']);
            if ($product->quantity_available < $item['quantity']) {

                return back()->withErrors(['products' => "Insufficient stock for {$product->name}."]);
            }
            $subtotal = $product->unit_price * $item['quantity'];
            $totalPrice += $subtotal;
            $items[] = [
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $product->unit_price,
                'subtotal' => $subtotal,
            ];
        }

        $quotation->update([
            'customer_name' => $request->customer_name,
            'total_price' => $totalPrice,
        ]);

        $quotation->items()->delete();
        $quotation->items()->createMany($items);

        //  Cache::store('redis')->forget('quotations_user_' . auth()->id());

        Cache::store('redis')->forget('quotations_user_' . $quotation->user_id);
        Cache::store('redis')->forget('quotation_' . $quotation->id);


        Log::info('Quotation updated', ['quotation_id' => $quotation->id]);

        return redirect()->route('dashboard')->with('success', 'Quotation updated successfully.');
    }

    public function updateStatus(Request $request, Quotation $quotation)
    {
        $this->middleware('role:admin');
        $request->validate([
            'status' => 'required|in:approved,rejected,pending',
        ]);

        $quotation->update(['status' => $request->status]);
        Cache::store('redis')->forget('quotations_user_' . $quotation->user_id);
        Log::info('Quotation status updated, sending notification', [
            'quotation_id' => $quotation->id,
            'status' => $request->status,
            'user_id' => $quotation->user_id,
        ]);
        try {
            Notification::send($quotation->user, new QuotationStatusUpdated($quotation));
            Cache::store('redis')->forget('notifications_user_' . $quotation->user_id);
        } catch (\Exception $e) {
            Log::error('Failed to send notification', [
                'error' => $e->getMessage(),
                'quotation_id' => $quotation->id,
                'user_id' => $quotation->user_id,
            ]);

            throw $e;
        }

        return redirect()->route('dashboard')->with('success', 'Quotation status updated.');
    }

    public function destroy(Quotation $quotation)
    {
        $this->authorizeQuotation($quotation);
        $quotation->delete();
        Cache::store('redis')->forget('quotations_user_' . auth()->id());

        Log::info('Quotation soft deleted', ['quotation_id' => $quotation->id]);

        return redirect()->route('quotations.index')->with('success', 'Quotation deleted successfully.');
    }

    public function downloadPdf(Quotation $quotation)
    {
        $this->authorizeQuotation($quotation);
        $pdf = Pdf::loadView('quotations.report', compact('quotation'));
        return $pdf->download('quotation_' . $quotation->id . '.pdf');
    }

    private function authorizeQuotation(Quotation $quotation)
    {
        if (!auth()->user()->isAdmin() && $quotation->user_id !== auth()->id()) {

            abort(403, 'Unauthorized');
        }
    }
}
