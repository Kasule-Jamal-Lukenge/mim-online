<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    // Orders Per Month
    public function ordersPerMonth(){
        $data = Order::select(
            DB::raw('MONTH(created_at) as month'),
            DB::RAW('COUNT(*) as total_orders')
        )
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        return response()->json($data);
    }

    //Orders Per Year
    public function ordersPerYear(){
        $data = Order::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('COUNT(*) as total_orders')
        )
        ->groupBy('year')
        ->orderBy('year')
        ->get();


        return response()->json($data);
    }

    //Orders By Status
    public function ordersByStatus(){
        $data = Order::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        return response()->json($data);
    }

    //New Users Per Month
     public function usersPerMonth(){
        $data = User::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as total_users')
        )
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        return response()->json($data);
    }

    // Total Sales (Delivered Orders)
    public function totalSales(){
        $totalSales = Order::where('status', 'Delivered')->sum('total_price');

        return response()->json([
            'total_sales' => $totalSales
        ]);
    }

       // Combined Summary for Dashboard Cards
    public function summary()
    {
        $totalOrders = Order::count();
        $delivered = Order::where('status', 'Delivered')->count();
        $inDelivery = Order::where('status', 'In-Delivery')->count();
        $received = Order::where('status', 'Received')->count();
        $totalUsers = User::count();
        $totalSales = Order::where('status', 'Delivered')->sum('total_price');

        return response()->json([
            'total_orders' => $totalOrders,
            'delivered_orders' => $delivered,
            'in_delivery_orders' => $inDelivery,
            'received_orders' => $received,
            'total_users' => $totalUsers,
            'total_sales' => $totalSales,
        ]);
    }
}
