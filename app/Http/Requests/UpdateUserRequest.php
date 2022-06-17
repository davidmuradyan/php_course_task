<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
     * @return array
     */
    public function rules(): array
    {
        return
            [
                'first_name' => 'required|string|min:3|max:255',
                'last_name' => 'required|string|min:3|max:255',
                'email' => [
                    'required',
                    'email:rfc,dns',
                    Rule::unique('users')->ignore(auth()->id()),
                ],
                'username' => [
                    'required',
                    'string',
                    'min:3',
                    'max:255',
                    Rule::unique('users')->ignore(auth()->id()),
                ],
                'gender' => 'required:in:male,female',
                'current_password' => 'required_with:new_password|current_password:api',
                'new_password' => 'nullable|different:current_password|confirmed|string|min:3|max:255'
            ];
    }

    public function messages()
    {
        return [
            'new_password.different' => ':attribute must be different than current one'
        ];
    }

    public function attributes()
    {
        return [
            'new_password' => 'your new password'
        ];
    }
}
