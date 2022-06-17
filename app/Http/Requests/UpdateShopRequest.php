<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UpdateShopRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->shop->user_id === auth()->id();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $regex = '/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/';
        return
            [
                'name' => 'required|string|min:3|max:255',
                'description' => 'nullable|string',
                'address' => 'nullable|string|min:3|max:255',
                'phone_number' => [
                    'nullable',
                    'regex:' . $regex
                ],
                'email' => [
                    'required',
                    'email:rfc',
                    Rule::unique('shops')->ignore($this->id),
                ],
                'manager_name' => 'required|string|min:3|max:255'
            ];
    }

    /**
     * @throws HttpException
     */
    protected function failedAuthorization()
    {
        throw new HttpException(Response::HTTP_FORBIDDEN, 'You can update your shops');
    }
}
