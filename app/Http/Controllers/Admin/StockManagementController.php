<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class StockManagementController extends Controller
{
    /**
     * Display a listing of stock movements (stock logs).
     */
    public function index(Request $request)
    {
        $query = StockMovement::with('product', 'order')
            ->orderBy('created_at', 'desc');

        // Apply filters if provided
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('movement_type')) {
            $query->where('movement_type', $request->movement_type);
        }

        if ($request->filled('movement_reason')) {
            $query->where('movement_reason', $request->movement_reason);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $movements = $query->paginate(20);

        // Get unique product list for filter dropdown
        $products = Product::orderBy('name')->get();

        return view('admin.stock-management.index', compact('movements', 'products'));
    }

    /**
     * Show a summary of stock movements.
     */
    public function summary()
    {
        $totalIn = StockMovement::where('movement_type', 'in')->sum('quantity');
        $totalOut = StockMovement::where('movement_type', 'out')->sum('quantity');
        $netChange = $totalIn - $totalOut;

        $recentMovements = StockMovement::with('product', 'order')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $movementsByReason = StockMovement::selectRaw('movement_reason, movement_type, SUM(quantity) as total_quantity, COUNT(*) as count')
            ->groupBy('movement_reason', 'movement_type')
            ->get();

        return view('admin.stock-management.summary', compact(
            'totalIn',
            'totalOut',
            'netChange',
            'recentMovements',
            'movementsByReason'
        ));
    }

    /**
     * Show stock adjustments page.
     */
    public function adjustments()
    {
        $products = Product::orderBy('name')->get();
        return view('admin.stock-management.adjustments', compact('products'));
    }

    /**
     * Process a manual stock adjustment.
     */
    public function processAdjustment(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'adjustment_type' => 'required|in:increase,decrease',
            'reason' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($request->adjustment_type === 'increase') {
            $product->increaseStock(
                $request->quantity,
                $request->reason ?: 'manual_adjustment',
                null, // no order ID for manual adjustment
                $request->description ?: "Manual stock increase: {$request->quantity} units"
            );
        } else {
            $product->reduceStock(
                $request->quantity,
                $request->reason ?: 'manual_adjustment',
                null, // no order ID for manual adjustment
                $request->description ?: "Manual stock decrease: {$request->quantity} units"
            );
        }

        return redirect()->route('admin.stock-management.index')
                         ->with('success', 'Stock adjustment processed successfully.');
    }
}