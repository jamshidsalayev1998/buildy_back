<?php

namespace App\Http\Requests\Api\V1\User;

use Illuminate\Foundation\Http\FormRequest;

class CreateAdminRequest extends BaseUserRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization will be handled by policy
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^998[0-9]{9}$/', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'gender' => ['required', 'string', 'in:male,female'],
            'passport_number' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'position' => ['required', 'string', 'max:255'],
            'work_type' => ['required', 'string', 'in:hourly,fixed'],
            'hourly_rate' => ['required_if:work_type,hourly', 'nullable', 'numeric', 'min:0'],
            'monthly_salary' => ['required_if:work_type,fixed', 'nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048'], // 2MB max
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'Ism kiritish majburiy',
            'last_name.required' => 'Familiya kiritish majburiy',
            'phone.required' => 'Telefon raqam kiritish majburiy',
            'phone.regex' => 'Telefon raqam formati noto\'g\'ri. Masalan: 998901234567',
            'phone.unique' => 'Bu telefon raqam allaqachon ro\'yxatdan o\'tgan',
            'password.required' => 'Parol kiritish majburiy',
            'password.min' => 'Parol kamida 6 ta belgidan iborat bo\'lishi kerak',
            'password.confirmed' => 'Parolni tasdiqlash mos kelmadi',
            'gender.required' => 'Jinsni tanlash majburiy',
            'gender.in' => 'Noto\'g\'ri jins tanlandi',
            'position.required' => 'Lavozim kiritish majburiy',
            'work_type.required' => 'Ish turini tanlash majburiy',
            'work_type.in' => 'Noto\'g\'ri ish turi tanlandi',
            'hourly_rate.required_if' => 'Soatlik stavka kiritish majburiy',
            'monthly_salary.required_if' => 'Oylik maosh kiritish majburiy',
            'image.image' => 'Fayl rasm formatida bo\'lishi kerak',
            'image.max' => 'Rasm hajmi 2MB dan oshmasligi kerak',
        ];
    }
}
