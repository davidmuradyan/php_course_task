<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreShopRequest;
use App\Http\Requests\UpdateShopRequest;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ShopController extends Controller
{
    /**
     * Display all shops.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();
        $shops = Shop::query()
            ->when($user->isSeller(), function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });

        return response()->json([
            'status' => trans('shop.success'),
            'data' => $shops->paginate(10)
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreShopRequest $request
     * @return JsonResponse
     */
    public function store(StoreShopRequest $request): JsonResponse
    {
        $shop = Shop::create($request->validated());
        return response()->json(
            $shop
        );
    }

    /**
     * Display shop data and shop's products.
     *
     * @param Shop $shop
     * @return JsonResponse
     */
    public function show(Shop $shop): JsonResponse
    {
        $user = auth()->user();
        $shop = Shop::query()
            ->when($user->isSeller(), function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->findOrFail($shop->id);
        return response()->json([
            'data' => $shop
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateShopRequest $request
     * @param Shop $shop
     * @return JsonResponse
     */
    public function update(UpdateShopRequest $request, Shop $shop): JsonResponse
    {
        if (auth()->id() === $shop->user_id) {
            $shop->update($request->validated());
            return response()->json([
                'status' => trans('shop.success'),
                'data' => $shop
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'status' => trans('shop.fail'),
                'data' => 'Not allowed'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Shop $shop
     * @return JsonResponse
     */
    public function destroy(Shop $shop): JsonResponse
    {
        if ($shop->user_id === auth()->id()) {
            $shop->delete();
            return response()->json([
                'status' => trans('shop.success'),
                'data' => "Deleted shop with id $shop->id"
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'status' => trans('shop.success'),
                'data' => "You do not have shop with id $shop->id"
            ], Response::HTTP_NOT_FOUND);
        }
    }
}
