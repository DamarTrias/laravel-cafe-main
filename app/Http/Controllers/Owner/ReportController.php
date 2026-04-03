<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->query('start_date', Carbon::today()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', Carbon::today()->toDateString());

        $orders = Order::with(['user', 'items.product'])
            ->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ])
            ->where('status', 'selesai')
            ->latest()
            ->get();

        $totalRevenue = $orders->sum('total_price');
        $totalOrders = $orders->count();
        
        $itemSales = [];
        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                $productId = $item->product_id;
                $productName = $item->product->name ?? 'Produk Dihapus';
                
                if (!isset($itemSales[$productId])) {
                    $itemSales[$productId] = [
                        'name' => $productName,
                        'qty' => 0,
                        'total' => 0
                    ];
                }
                
                $itemSales[$productId]['qty'] += $item->quantity;
                $itemSales[$productId]['total'] += $item->price * $item->quantity;
            }
        }
        
        // Sort by quantity descending
        uasort($itemSales, function($a, $b) {
            return $b['qty'] <=> $a['qty'];
        });

        return view('owner.reports.index', compact(
            'orders', 
            'totalRevenue', 
            'totalOrders', 
            'itemSales',
            'startDate', 
            'endDate'
        ));
    }

    public function exportCsv(Request $request)
    {
        $startDate = $request->query('start_date', Carbon::today()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', Carbon::today()->toDateString());

        $orders = Order::with(['user', 'items.product'])
            ->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ])
            ->where('status', 'selesai')
            ->latest()
            ->get();

        $filename = "laporan_penjualan_{$startDate}_ke_{$endDate}.csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($orders) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID Pesanan', 'Meja', 'Pelanggan', 'Tanggal', 'Metode', 'Total Harga']);

            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->id,
                    $order->table_number ?? 'Bawa Pulang',
                    $order->user->name,
                    $order->created_at->format('d/m/Y H:i'),
                    $order->payment_method,
                    $order->total_price
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
