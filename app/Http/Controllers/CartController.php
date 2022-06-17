<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function userCartItems(): JsonResponse
    {
        $user_products = Cart::where('user_id', auth()->id())->paginate(2);
        return response()->json([
            'status' => trans('cart.success'),
            'data' => $user_products
        ], Response::HTTP_OK);
    }

    /**
     * Add item to cart.
     *
     * @param Request $request
     * @param $product
     * @return JsonResponse
     */
    public function addToCart(Request $request, $product): JsonResponse
    {
        $request['user_id'] = auth()->id();
        $request['product_id'] = $product;
        if ($request['count'] <= Product::find($product)->count) {
            Cart::create($request->all());
            return response()->json([
                'status' => trans('cart.success'),
                'data' => $request->all()
            ], Response::HTTP_OK);
        }
        return response()->json([
            'status' => trans('cart.fail'),
            'data' => 'Not enough products.'
        ]);
    }

    /**
     * Update the count of a product in cart.
     */
    public function update(Request $request, $product): JsonResponse
    {
        $item = Cart::find($product);
        $item->update([
            'count' => $request->count
        ]);
        return response()->json([
            'status' => trans('cart.success'),
            'data' => $item
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from cart.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function removeFromCart(int $id): JsonResponse
    {
        $item = Cart::findOrFail($id);
        $item->delete();
        return response()->json([
            'status' => trans('cart.success'),
            'data' => $item
        ], Response::HTTP_OK);
    }

    /**
     * Remove all bought items from cart, add to orders.
     *
     * @return JsonResponse
     */
    public function buyAll(): JsonResponse
    {
        $user_cart_items = Cart::where('user_id', auth()->id())->with('product')->get();
        if (!$user_cart_items->isEmpty()) {

            $failed = $user_cart_items->map(function ($item) {
                if ($item->product->count < $item->count) {
                    return 'Not enough';
                }
            });

            if ($failed[0] !== null) {
                return response()->json([
                    'status' => trans('cart.fail'),
                    'data' => $failed
                ]);
            }

            $sum = $user_cart_items->map(function ($item) {
                return $item->count * $item->product->price;
            })->sum();

            try {
                DB::beginTransaction();
                $products = $user_cart_items->map(function ($item) {
                    $product = $item->product->id;
                    $bought_item_count = $item->count;
                    $product_count = $item->product->count;
                    $product_price = $item->product->price;

                    $item->delete();
                    $item->product->update([
                        'count' => $product_count - $bought_item_count
                    ]);
                    return [
                        'id' => $product,
                        'name' => $item->product->name,
                        'count' => $bought_item_count,
                        'price' => $product_price,
                        'sum' => $bought_item_count * $product_price
                    ];
                });

                Order::create([
                    'user_id' => auth()->id(),
                    'products' => $products,
                    'sum' => $sum
                ]);

                DB::commit();

                return response()->json([
                    'status' => trans('cart.success'),
                    'data' => 'Looking forward to welcome you again'
                ], Response::HTTP_OK);

            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status' => trans('cart.fail'),
                    'data' => 'Something is wrong'
                ]);
            }
        }

        return response()->json([
            'status' => trans('cart.fail'),
            'data' => 'Cart is empty'
        ]);
    }
}
