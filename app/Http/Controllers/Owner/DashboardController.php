<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->query('start_date', Carbon::today()->subDays(6)->toDateString());
        $endDate = $request->query('end_date', Carbon::today()->toDateString());

        $productCount = Product::count();
        $categoryCount = Category::count();

        // Stats for the selected range
        $ordersQuery = Order::with(['user', 'items.product'])
            ->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ])
            ->where('status', 'selesai');

        $totalPenjualan = (float) $ordersQuery->sum('total_price');
        $jumlahPesanan = $ordersQuery->count();
        $ordersDetail = $ordersQuery->latest()->get();

        // Data for Chart (Revenue by Date)
        $chartData = Order::where('status', 'selesai')
            ->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_price) as total_revenue')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $labels = [];
        $revenue = [];
        
        $current = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        while ($current <= $end) {
            $dateStr = $current->toDateString();
            $labels[] = $current->format('d M');
            
            $found = $chartData->firstWhere('date', $dateStr);
            $revenue[] = $found ? (float)$found->total_revenue : 0;
            
            $current->addDay();
        }

        // Low Stock Products Alert
        $lowStockProducts = Product::where('stock', '<', 10)->get();

        return view('owner.dashboard', compact(
            'productCount', 
            'categoryCount', 
            'totalPenjualan', 
            'jumlahPesanan', 
            'ordersDetail',
            'startDate',
            'endDate',
            'labels',
            'revenue',
            'lowStockProducts'
        ));
    }
}
