<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'string|max:255|regex:/^[A-Z][a-z]*$/',
            'last_name' => 'string|max:255|regex:/^[A-Z][a-z]*$/',
            'login' => 'string|min:4|max:255|unique:users',
            'email' => 'string|email|max:255|unique:users', 
        ];
    }
}
