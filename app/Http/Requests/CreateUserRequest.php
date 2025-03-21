<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
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
            'first_name' => 'required|string|max:255|regex:/^[A-Z][a-z]*$/',
            'last_name' => 'required|string|max:255|regex:/^[A-Z][a-z]*$/',
            'login' => 'required|string|min:4|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|regex:/^(?=.*[A-Za-z])(?=.*\d)(?=.*[_\-,.])[A-Za-z\d_\-,.]{6,}$/',
        ];
    }

    public function messages()
    {
        return [
            'password.regex' => 'The password must contain at least one letter, one digit, and one special character (_-,.).',
        ];
    }
}
