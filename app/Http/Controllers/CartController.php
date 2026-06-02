<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    private function getCartLines(): array
    {
        if (Auth::check() && Auth::user()->isCustomer()) {
            $items = CartItem::with(['product.images'])->where('user_id', Auth::id())->get();
            $lines = [];
            foreach ($items as $item) {
                $product = $item->product;
                $warning = null;
                $blocked = false;

                if (! $product || $product->trashed() || ! $product->is_active) {
                    $warning = 'unavailable';
                    $blocked = true;
                } elseif ($item->quantity > $product->stock) {
                    if ($product->stock === 0) {
                        $warning = 'out_of_stock';
                        $blocked = true;
                    } else {
                        $warning = 'partial';
                    }
                }

                $lines[] = [
                    'id'       => $item->id,
                    'product'  => $product,
                    'quantity' => $item->quantity,
                    'warning'  => $warning,
                    'blocked'  => $blocked,
                    'db_item'  => $item,
                ];
            }
            return $lines;
        }

        // Guest session cart
        $sessionCart = session('cart', []);
        $lines = [];
        foreach ($sessionCart as $productId => $qty) {
            $product = Product::withTrashed()->with('images')->find($productId);
            $warning = null;
            $blocked = false;

            if (! $product || $product->trashed() || ! $product->is_active) {
                $warning = 'unavailable';
                $blocked = true;
            } elseif ($qty > $product->stock) {
                if ($product->stock === 0) {
                    $warning = 'out_of_stock';
                    $blocked = true;
                } else {
                    $warning = 'partial';
                }
            }

            $lines[] = [
                'id'      => 'session_' . $productId,
                'product' => $product,
                'quantity'=> $qty,
                'warning' => $warning,
                'blocked' => $blocked,
            ];
        }
        return $lines;
    }

    public function show()
    {
        $lines   = $this->getCartLines();
        $blocked = collect($lines)->contains('blocked', true);

        $subtotal = collect($lines)->filter(fn ($l) => ! $l['blocked'] && $l['product'])
            ->sum(fn ($l) => $l['product']->price * $l['quantity']);

        $shipping  = $subtotal > 0 ? 500 : 0;
        $total     = $subtotal + $shipping;
        $cartCount = $this->cartCount();

        return view('storefront.cart', compact('lines', 'blocked', 'subtotal', 'shipping', 'total', 'cartCount'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'quantity'   => 'required|integer|min:1',
        ]);

        $product = Product::active()->findOrFail($request->product_id);
        $qty     = min((int) $request->quantity, $product->maxCartQty());

        if (Auth::check() && Auth::user()->isCustomer()) {
            $item = CartItem::firstOrNew([
                'user_id'    => Auth::id(),
                'product_id' => $product->id,
            ]);
            $item->quantity = min($item->quantity + $qty, $product->maxCartQty());
            $item->save();
        } else {
            $cart = session('cart', []);
            $existing = $cart[$product->id] ?? 0;
            $cart[$product->id] = min($existing + $qty, $product->maxCartQty());
            session(['cart' => $cart]);
        }

        return response()->json([
            'success'    => true,
            'cart_count' => $this->cartCount(),
            'message'    => 'Added to cart.',
        ]);
    }

    public function update(Request $request, $item)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);

        if (str_starts_with((string)$item, 'session_')) {
            $productId = (int) str_replace('session_', '', $item);
            $product = Product::active()->find($productId);
            if ($product) {
                $cart = session('cart', []);
                $cart[$productId] = min($request->quantity, $product->maxCartQty());
                session(['cart' => $cart]);
            }
        } else {
            $cartItem = CartItem::where('id', $item)
                ->where('user_id', Auth::id())
                ->firstOrFail();
            $product = $cartItem->product;
            $cartItem->update(['quantity' => min($request->quantity, $product->maxCartQty())]);
        }

        return redirect()->route('cart.show');
    }

    public function remove($item)
    {
        if (str_starts_with((string)$item, 'session_')) {
            $productId = (int) str_replace('session_', '', $item);
            $cart = session('cart', []);
            unset($cart[$productId]);
            session(['cart' => $cart]);
        } else {
            CartItem::where('id', $item)->where('user_id', Auth::id())->delete();
        }

        return redirect()->route('cart.show');
    }

    public static function cartCount(): int
    {
        if (Auth::check() && Auth::user()->isCustomer()) {
            return CartItem::where('user_id', Auth::id())->sum('quantity');
        }
        return array_sum(session('cart', []));
    }

    public static function mergeGuestCart(): array
    {
        $sessionCart = session('cart', []);
        if (empty($sessionCart) || ! Auth::check()) {
            return [];
        }

        $capped = [];
        foreach ($sessionCart as $productId => $qty) {
            $product = Product::active()->find($productId);
            if (! $product) {
                continue;
            }

            $existing = CartItem::firstOrNew([
                'user_id'    => Auth::id(),
                'product_id' => $productId,
            ]);

            $newQty = $existing->quantity + $qty;
            $cap    = $product->maxCartQty();

            if ($newQty > $cap) {
                $capped[] = $product->name;
                $newQty   = $cap;
            }

            $existing->quantity = $newQty;
            $existing->save();
        }

        session()->forget('cart');

        return $capped;
    }
}
