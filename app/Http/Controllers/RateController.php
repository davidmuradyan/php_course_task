<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Rate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class RateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $data = Order::where('user_id', auth()->id())->where('id', $request->order_id)->get();
        $order = $data[0];
        if ($order['is_rated'] === 0) {
            $products = json_decode($order['products']);
            if (count($products) == count($request->all()) - 1) {
                $rate['user_id'] = auth()->id();
                $rate['order_id'] = $request->order_id;
                foreach ($products as $product) {
                    $rate['product_id'] = $product->id;
                    $rate['rate'] = $request[$product->id];
                    Rate::create($rate);
                }
                $order->update([
                    'is_rated' => 1
                ]);
                return response()->json([
                    'status' => trans('rate.success'),
                    'data' => 'Rated successfully'
                ], Response::HTTP_OK);
            }
            return response()->json([
                'status' => trans('rate.fail'),
                'data' => 'You must rate all'
            ]);
        }
        return response()->json([
            'status' => trans('rate.fail'),
            'data' => 'Already rated'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id)
    {
        $rate = Rate::findOrFail($id);
        if ($rate->user_id == auth()->id()) {
            $rate->update([
                'rate' => $request->rate
            ]);
            return response()->json([
                'status' => trans('rate.success'),
                'data' => 'Rate updated successfully'
            ], Response::HTTP_OK);
        }
        return response()->json([
            'status' => trans('rate.fail'),
            'data' => 'Not allowed to update rate'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Show product's average rate.
     *
     * @param Product $product
     * @return JsonResponse
     */
    public function avgRate(Product $product): JsonResponse
    {
        $avgRate = DB::table('rates')
            ->where('product_id', '=', $product->id)
            ->avg("rate");

        return response()->json([
            'message' => 'success',
            'data' => round(floatval($avgRate), 1)
        ], Response::HTTP_OK);
    }
}
