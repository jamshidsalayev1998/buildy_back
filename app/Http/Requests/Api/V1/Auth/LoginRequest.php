<?php

namespace App\Http\Requests\Api\V1\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'phone' => ['required', 'string', 'regex:/^998[0-9]{9}$/'],
            'password' => ['required', 'string', 'min:6'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required' => 'Telefon raqam kiritish majburiy',
            'phone.regex' => 'Telefon raqam formati noto\'g\'ri. Masalan: 998901234567',
            'password.required' => 'Parol kiritish majburiy',
            'password.min' => 'Parol kamida 6 ta belgidan iborat bo\'lishi kerak',
        ];
    }
}
