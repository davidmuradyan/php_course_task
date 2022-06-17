<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ProductImageController extends Controller
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
     * @param Request $request
     * @param $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $product): JsonResponse
    {
        $product_user_id = Product::findOrFail($product)->shop->user_id;

        if (auth()->user()->isSeller() && auth()->id() === $product_user_id) {

            if (!$request->hasFile('path')) {
                return response()->json([
                    'status' => 'image.fail',
                    'message' => 'file_not_found'
                ]);
            }



            //find what is the picture's last image's order,
            //set the new picture's order
            //if the picture is the first set it as default
            $productImages = ProductImage::where('product_id', $product)->get();
            $orders = [];
            foreach($productImages as $item) {
               $orders[] = $item->order;
            }

            if (!empty($orders)) {
                $max_order = $orders[0];
                foreach ($orders as $order) {
                    if ($order > $max_order) {
                        $max_order = $order;
                    }
                }
                $request['order'] = $max_order + 1;
            } else {
                $request['default'] = 1;
                $request['order'] = 1;
            }


            $request['product_id'] = $product;
            ProductImage::create($request->all());

            return response()->json([
                'status' => trans('image.success'),
                'data' => 'Image added successfully'
            ]);
        }

        return response()->json([
            'status' => trans('image.fail'),
            'message' => "Can't add image"
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Change the order of pictures.
     *
     * @param int $product
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reorder(Request $request, int $product): JsonResponse
    {
        $ordering = $request->all();
        foreach ($ordering as $order => $id) {
            if ($order !== 'default') {
                ProductImage::findOrFail(intval($id))->update([
                    'order' => intval($order),
                    'default' => 0
                ]);
            }
        }
        ProductImage::findOrFail(intval($ordering['default']))->update([
            'default' => 1
        ]);

        return response()->json([
            'status' => trans('image.success'),
            'message' => 'Successfully reordered'
        ]);
    }
}
