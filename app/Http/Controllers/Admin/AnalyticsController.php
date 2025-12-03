<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    /**
     * Display the main analytics dashboard.
     */
    public function index()
    {
        // Get overall sales and profit data - exclude cancelled orders
        $totalOrders = Order::where('status', '!=', 'cancelled')->count();
        $totalRevenue = Order::where('status', '!=', 'cancelled')->sum('total');
        
        // Calculate total profit - only count orders that haven't been cancelled
        $totalProfit = 0;
        $orderItems = OrderItem::with('product', 'order')->get();
        foreach ($orderItems as $item) {
            // Only count profit for items that belong to non-cancelled orders
            if ($item->product && $item->product->buy_price !== null && $item->order && $item->order->status !== 'cancelled') {
                $profitPerUnit = $item->product->price - $item->product->buy_price;
                $totalProfit += $profitPerUnit * $item->quantity;
            }
        }

        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // Get data for the last 30 days - exclude cancelled orders
        $salesData = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as total_sales'),
                DB::raw('COUNT(*) as order_count')
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->where('status', '!=', 'cancelled')  // Exclude cancelled orders
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date', 'asc')
            ->get();
            
        // Profit by product category - exclude cancelled orders
        $profitByCategory = OrderItem::select(
                'products.category_id',
                'categories.name as category_name',
                DB::raw('SUM((order_items.price - products.buy_price) * order_items.quantity) as profit'),
                DB::raw('SUM(order_items.quantity) as total_sold')
            )
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereNotNull('products.buy_price')
            ->where('orders.status', '!=', 'cancelled')  // Exclude cancelled orders
            ->groupBy('products.category_id', 'categories.name')
            ->get();
        
        // Top selling products by profit - exclude cancelled orders
        $topProfitProducts = OrderItem::select(
                'products.name as product_name',
                'products.buy_price',
                'products.price',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM((order_items.price - products.buy_price) * order_items.quantity) as profit')
            )
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereNotNull('products.buy_price')
            ->where('orders.status', '!=', 'cancelled')  // Exclude cancelled orders
            ->groupBy('order_items.product_id', 'products.name', 'products.buy_price', 'products.price')
            ->orderBy('profit', 'desc')
            ->limit(10)
            ->get();
            
        // Get recent orders for the dashboard
        $recentOrders = Order::with('items.product')->latest()->limit(5)->get();

        return view('admin.analytics.index', compact(
            'totalOrders',
            'totalRevenue', 
            'totalProfit',
            'avgOrderValue',
            'salesData',
            'profitByCategory',
            'topProfitProducts',
            'recentOrders'
        ));
    }

    /**
     * Show profit analytics for a specific period.
     */
    public function profit(Request $request)
    {
        $period = $request->input('period', 'monthly');

        if ($period === 'yearly') {
            // Yearly analysis - exclude cancelled orders
            $profitData = OrderItem::select(
                    DB::raw('YEAR(orders.created_at) as year'),
                    DB::raw('SUM(orders.total) as revenue'),
                    DB::raw('SUM((order_items.price - products.buy_price) * order_items.quantity) as profit'),
                    DB::raw('SUM(orders.total - ((order_items.price - products.buy_price) * order_items.quantity)) as cost_of_goods_sold')
                )
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->whereNotNull('products.buy_price')
                ->where('orders.status', '!=', 'cancelled')  // Exclude cancelled orders
                ->groupBy(DB::raw('YEAR(orders.created_at)'))
                ->orderBy('year', 'desc')
                ->get();
        } else {
            // Monthly analysis (default) - exclude cancelled orders
            $profitData = OrderItem::select(
                    DB::raw('DATE_FORMAT(orders.created_at, "%Y-%m") as month'),
                    DB::raw('SUM(orders.total) as revenue'),
                    DB::raw('SUM((order_items.price - products.buy_price) * order_items.quantity) as profit'),
                    DB::raw('SUM(orders.total - ((order_items.price - products.buy_price) * order_items.quantity)) as cost_of_goods_sold')
                )
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->whereNotNull('products.buy_price')
                ->where('orders.status', '!=', 'cancelled')  // Exclude cancelled orders
                ->groupBy(DB::raw('DATE_FORMAT(orders.created_at, "%Y-%m")'))
                ->orderBy('month', 'desc')
                ->get();
        }

        return view('admin.analytics.profit', compact('profitData', 'period'));
    }

    /**
     * Show detailed product analytics.
     */
    public function products()
    {
        $bestSelling = OrderItem::select(
                'products.id',
                'products.name',
                'products.buy_price',
                'products.price',
                DB::raw('SUM(order_items.quantity) as total_quantity_sold'),
                DB::raw('SUM(orders.total) as total_revenue'),
                DB::raw('SUM((order_items.price - products.buy_price) * order_items.quantity) as total_profit')
            )
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereNotNull('products.buy_price')
            ->where('orders.status', '!=', 'cancelled')  // Exclude cancelled orders
            ->groupBy('order_items.product_id', 'products.id', 'products.name', 'products.buy_price', 'products.price')
            ->orderBy('total_quantity_sold', 'desc')
            ->get();

        $mostProfitable = OrderItem::select(
                'products.id',
                'products.name',
                'products.buy_price',
                'products.price',
                DB::raw('SUM(order_items.quantity) as total_quantity_sold'),
                DB::raw('SUM(orders.total) as total_revenue'),
                DB::raw('SUM((order_items.price - products.buy_price) * order_items.quantity) as total_profit')
            )
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereNotNull('products.buy_price')
            ->where('orders.status', '!=', 'cancelled')  // Exclude cancelled orders
            ->groupBy('order_items.product_id', 'products.id', 'products.name', 'products.buy_price', 'products.price')
            ->orderBy('total_profit', 'desc')
            ->get();

        return view('admin.analytics.products', compact('bestSelling', 'mostProfitable'));
    }
}