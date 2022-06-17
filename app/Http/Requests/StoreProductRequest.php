<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return
            [
                'name' => 'required|string|min:3|max:255',
                'price' => 'required|numeric',
                'description' => 'nullable|string',
                'count' => 'required|numeric',
                'category_id' => 'required|exists:categories,id',
//                'shop_id' => 'required|exists:shops,id'
            ];
    }
}