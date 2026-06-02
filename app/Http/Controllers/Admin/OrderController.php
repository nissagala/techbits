<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\OrderStatusUpdateMail;
use App\Models\Order;
use App\Models\OrderStatusLog;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user')
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->search, function ($q) use ($request) {
                $q->where('order_number', 'like', '%' . $request->search . '%')
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', '%' . $request->search . '%'));
            })
            ->latest('placed_at');

        $orders = $query->paginate(20)->withQueryString();
        $currentStatus = $request->status ?? 'pending';

        return view('admin.orders.index', compact('orders', 'currentStatus'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items', 'statusLogs']);
        return view('admin.orders.show', compact('order'));
    }

    public function advance(Order $order)
    {
        if (! $order->canAdvance()) {
            return back()->withErrors(['order' => 'Cannot advance order from current status.']);
        }

        $from = $order->status;
        $to   = $order->nextStatus();

        DB::transaction(function () use ($order, $from, $to) {
            $order->update(['status' => $to]);
            OrderStatusLog::create([
                'order_id'   => $order->id,
                'from_status' => $from,
                'to_status'  => $to,
                'created_at' => now(),
            ]);
        });

        Mail::to($order->user->email)->send(new OrderStatusUpdateMail($order, $to));

        return back()->with('success', 'Order status updated to ' . ucfirst($to) . '.');
    }

    public function cancel(Order $order)
    {
        if (! $order->canCancel()) {
            return back()->withErrors(['order' => 'This order cannot be cancelled.']);
        }

        DB::transaction(function () use ($order) {
            $from = $order->status;
            $order->update(['status' => 'cancelled']);

            OrderStatusLog::create([
                'order_id'   => $order->id,
                'from_status' => $from,
                'to_status'  => 'cancelled',
                'created_at' => now(),
            ]);

            foreach ($order->items as $item) {
                if ($item->product_id) {
                    Product::withTrashed()
                        ->where('id', $item->product_id)
                        ->increment('stock', $item->quantity);
                }
            }
        });

        Mail::to($order->user->email)->send(new OrderStatusUpdateMail($order, 'cancelled'));

        return back()->with('success', 'Order cancelled and stock restored.');
    }
}
