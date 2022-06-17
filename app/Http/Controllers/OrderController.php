<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function orders(): JsonResponse
    {
        $orders = Order::where('user_id', auth()->id())->get();
        return response()->json([
            'status' => trans('cart.success'),
            'data' => $orders
        ], Response::HTTP_OK);
    }
}
