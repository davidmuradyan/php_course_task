<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class RegistrationRequest extends FormRequest
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
                'first_name' => 'required|string|min:3|max:255',
                'last_name' => 'required|string|min:3|max:255',
                'email' => 'required|email:rfc|unique:users,email',
                'type' => [
                    'required',
                    Rule::in([User::TYPE_BUYER, User::TYPE_SELLER])
                ],
                'gender' => 'required:in:male,female',
                'username' => 'required|string|unique:users,username|min:3|max:255',
                'password' => 'required|confirmed|string|min:3|max:255'
            ];
    }
}
