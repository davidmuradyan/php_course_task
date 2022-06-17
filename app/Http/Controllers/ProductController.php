<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use http\Env\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        /** @var User $user */
        /** @var Shop $shop */

//        search anel yst namei, descriptioni, shopi namei
        $user = auth()->user();
        $products = Product::query()
            ->when(request()->has('SearchText'), function ($query) {
                $searchText = request()->get('SearchText');
                $query->where('name', 'like', "%$searchText%");
            })
            ->when($user->isSeller(), function () use ($user) {
                return $user->products();
            })
            ->get();

        return response()->json([
            'status' => trans('shop.success'),
            'data' => $products
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreProductRequest $request
     * @param Shop $shop
     * @return JsonResponse
     */
    public function store(StoreProductRequest $request, Shop $shop): JsonResponse
    {
        if (auth()->id() === Shop::find($shop->id)->user_id) {
            $product = $shop->products()->create($request->validated());
            return response()->json([
                'status' => trans('product.success'),
                'data' => $product
            ], Response::HTTP_CREATED);
        } else {
            return response()->json([
                'status' => trans('product.fail'),
                'data' => 'null'
            ], Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Product $product
     * @return JsonResponse
     */
    public function show(Shop $shop, Product $product): JsonResponse
    {
        return response()->json([
            'status' => trans('product.success'),
            'data' => $product
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateProductRequest $request
     * @param Shop $shop
     * @param Product $product
     * @return JsonResponse
     */
    public function update(UpdateProductRequest $request, Shop $shop, Product $product): JsonResponse
    {
        if (auth()->id() === $shop->user_id) {
            $product->update($request->validated());
            return response()->json([
                'status' => trans('product.success'),
                'data' => $product
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'status' => trans('product.fail'),
                'data' => $product
            ], Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Shop $shop
     * @param Product $product
     * @return JsonResponse
     */
    public function destroy(Shop $shop, Product $product): JsonResponse
    {
        if (auth()->id() === $shop->user_id) {
            $product->delete();
            return response()->json([
                'status' => trans('product.success')
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'status' => trans('product.fail')
            ]);
        }
    }
}
