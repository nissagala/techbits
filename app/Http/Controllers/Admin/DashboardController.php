<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function show()
    {
        $totalProducts  = Product::count();
        $totalOrders    = Order::count();
        $totalCustomers = User::where('role', 'customer')->count();
        $unreadMessages = ContactMessage::where('is_read', false)->count();

        $ordersByStatus = Order::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $recentOrders = Order::with('user')
            ->latest('placed_at')
            ->limit(5)
            ->get();

        $recentMessages = ContactMessage::latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalProducts', 'totalOrders', 'totalCustomers', 'unreadMessages',
            'ordersByStatus', 'recentOrders', 'recentMessages'
        ));
    }
}
