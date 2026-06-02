<?php

namespace App\Http\Controllers;

use App\Mail\OrderConfirmationMail;
use App\Models\Address;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusLog;
use App\Rules\SriLankanPhone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class CheckoutController extends Controller
{
    public function shipping()
    {
        if (CartItem::where('user_id', Auth::id())->doesntExist()) {
            return redirect()->route('cart.show')->with('error', 'Your cart is empty.');
        }

        $addresses = Auth::user()->addresses()->get();
        $default   = $addresses->where('is_default', true)->first() ?? $addresses->first();

        return view('checkout.shipping', compact('addresses', 'default'));
    }

    public function saveShipping(Request $request)
    {
        if ($request->filled('address_id')) {
            $address = Address::where('id', $request->address_id)
                ->where('user_id', Auth::id())
                ->firstOrFail();
        } else {
            $request->validate([
                'new_address.recipient'   => 'required|string|min:2|max:100',
                'new_address.line1'       => 'required|string|min:3|max:200',
                'new_address.line2'       => 'nullable|string|max:200',
                'new_address.city'        => 'required|string|min:2|max:50',
                'new_address.district'    => 'required|string|in:' . implode(',', config('districts')),
                'new_address.postal_code' => 'required|digits:5',
                'new_address.phone'       => ['required', new SriLankanPhone],
                'new_address.label'       => 'nullable|string|max:30',
            ]);

            if (Auth::user()->addresses()->count() >= 10) {
                return back()->withErrors(['address' => 'Address book is full (max 10). Please remove an address first.']);
            }

            $isFirst = Auth::user()->addresses()->count() === 0;
            if ($isFirst || $request->boolean('new_address.is_default')) {
                Auth::user()->addresses()->update(['is_default' => false]);
            }

            $na    = $request->input('new_address');
            $phone = SriLankanPhone::normalize($na['phone']);

            $address = Auth::user()->addresses()->create([
                'label'       => $na['label'] ?? null,
                'recipient'   => $na['recipient'],
                'line1'       => $na['line1'],
                'line2'       => $na['line2'] ?? null,
                'city'        => $na['city'],
                'district'    => $na['district'],
                'postal_code' => $na['postal_code'],
                'phone'       => $phone,
                'is_default'  => $isFirst || $request->boolean('new_address.is_default'),
            ]);
        }

        session(['checkout.address_id' => $address->id]);

        return redirect()->route('checkout.payment');
    }

    public function payment()
    {
        if (! session('checkout.address_id')) {
            return redirect()->route('checkout.shipping');
        }
        return view('checkout.payment');
    }

    public function savePayment(Request $request)
    {
        $request->validate([
            'cardholder'  => 'required|string|min:2|max:100',
            'card_number' => ['required', function ($attr, $val, $fail) {
                $digits = preg_replace('/\D/', '', $val);
                if (strlen($digits) < 13 || strlen($digits) > 19) {
                    $fail('Card number must be 13–19 digits.');
                } elseif (! \App\Helpers\LuhnValidator::check($digits)) {
                    $fail('Card number is invalid.');
                }
            }],
            'expiry' => ['required', 'regex:/^\d{2}\/\d{2}$/', function ($attr, $val, $fail) {
                [$m, $y] = explode('/', $val);
                $expYear  = 2000 + (int) $y;
                $expMonth = (int) $m;
                if ($expMonth < 1 || $expMonth > 12) {
                    $fail('Expiry month must be 01–12.');
                } elseif ($expYear < now()->year || ($expYear === now()->year && $expMonth < now()->month)) {
                    $fail('Card has expired.');
                }
            }],
            'cvv' => 'required|digits_between:3,4',
        ]);

        $digits = preg_replace('/\D/', '', $request->card_number);

        session(['checkout.payment' => [
            'last4'       => substr($digits, -4),
            'cardholder'  => $request->cardholder,
            'expiry'      => $request->expiry,
        ]]);

        return redirect()->route('checkout.review');
    }

    public function review()
    {
        if (! session('checkout.address_id') || ! session('checkout.payment')) {
            return redirect()->route('checkout.shipping');
        }

        $address  = Address::find(session('checkout.address_id'));
        $payment  = session('checkout.payment');
        $cartItems = CartItem::with(['product.images'])->where('user_id', Auth::id())->get();

        $subtotal = $cartItems->sum(fn ($i) => $i->product->price * $i->quantity);
        $shipping = 500;
        $total    = $subtotal + $shipping;

        return view('checkout.review', compact('address', 'payment', 'cartItems', 'subtotal', 'shipping', 'total'));
    }

    public function place(Request $request)
    {
        $cartItems = CartItem::with('product')->where('user_id', Auth::id())->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.show')->with('error', 'Your cart is empty.');
        }

        $address = Address::where('id', session('checkout.address_id'))
            ->where('user_id', Auth::id())
            ->first();

        $payment = session('checkout.payment');

        if (! $address || ! $payment) {
            return redirect()->route('checkout.shipping');
        }

        // Stock validation
        $conflicts = [];
        foreach ($cartItems as $item) {
            if (! $item->product || ! $item->product->is_active || $item->product->trashed()) {
                $conflicts[] = $item->product?->name ?? 'Unknown product';
            } elseif ($item->quantity > $item->product->stock) {
                $conflicts[] = $item->product->name;
            }
        }

        if ($conflicts) {
            return redirect()->route('cart.show')
                ->with('error', 'Some items are unavailable or out of stock: ' . implode(', ', $conflicts));
        }

        $subtotal = $cartItems->sum(fn ($i) => $i->product->price * $i->quantity);
        $shipping = 500;
        $total    = $subtotal + $shipping;

        $order = DB::transaction(function () use ($cartItems, $address, $payment, $subtotal, $shipping, $total) {
            $order = Order::create([
                'user_id'            => Auth::id(),
                'status'             => 'pending',
                'subtotal'           => $subtotal,
                'shipping_fee'       => $shipping,
                'total'              => $total,
                'shipping_address'   => $address->toSnapshot(),
                'payment_cardholder' => $payment['cardholder'],
                'payment_last4'      => $payment['last4'],
                'payment_expiry'     => $payment['expiry'],
                'placed_at'          => now(),
            ]);

            $order->setOrderNumber();

            foreach ($cartItems as $item) {
                $primary = $item->product->primaryImage();
                OrderItem::create([
                    'order_id'          => $order->id,
                    'product_id'        => $item->product_id,
                    'product_name'      => $item->product->name,
                    'product_sku'       => $item->product->sku,
                    'unit_price'        => $item->product->price,
                    'quantity'          => $item->quantity,
                    'line_total'        => $item->product->price * $item->quantity,
                    'product_image_path' => $primary?->path,
                ]);

                $item->product->decrement('stock', $item->quantity);
            }

            OrderStatusLog::create([
                'order_id'   => $order->id,
                'from_status' => null,
                'to_status'  => 'pending',
                'created_at' => now(),
            ]);

            CartItem::where('user_id', Auth::id())->delete();

            return $order;
        });

        session()->forget(['checkout.address_id', 'checkout.payment']);

        Mail::to(Auth::user()->email)->send(new OrderConfirmationMail($order));

        return redirect()->route('checkout.confirmation', $order);
    }

    public function confirmation(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }
        $order->load('items');
        return view('checkout.confirmation', compact('order'));
    }
}
