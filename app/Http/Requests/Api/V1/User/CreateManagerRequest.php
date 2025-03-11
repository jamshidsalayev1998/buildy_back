<?php

namespace App\Http\Requests\Api\V1\User;

use Illuminate\Foundation\Http\FormRequest;

class CreateManagerRequest extends BaseUserRequest
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
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^998[0-9]{9}$/', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'gender' => ['required', 'string', 'in:male,female'],
            'image' => ['nullable', 'image', 'max:2048'],
            'birth_date' => ['nullable', 'date'],
            'position' => ['required', 'string', 'max:255'],
            'work_type' => ['required', 'string', 'in:hourly,fixed'],
            'hourly_rate' => ['required_if:work_type,hourly', 'nullable', 'numeric', 'min:0'],
            'monthly_salary' => ['required_if:work_type,fixed', 'nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'Telefon raqam formati noto\'g\'ri. Masalan: 998901234567',
            'password.confirmed' => 'Parol tasdiqlash noto\'g\'ri',
            'image.image' => 'Rasim formati noto\'g\'ri',
            'image.max' => 'Rasim hajmi noto\'g\'ri',
            'gender.in' => 'Jins noto\'g\'ri',
            'first_name.required' => 'Ism kiritish majburiy',
            'last_name.required' => 'Familiya kiritish majburiy',
            'phone.required' => 'Telefon raqam kiritish majburiy',
            'password.required' => 'Parol kiritish majburiy',
            'gender.required' => 'Jins kiritish majburiy',
            'position.required' => 'Lavozim kiritish majburiy',
            'work_type.required' => 'Ish turini tanlash majburiy',
            'hourly_rate.required_if' => 'Soatlik stavka kiritish majburiy',
            'monthly_salary.required_if' => 'Oylik maosh kiritish majburiy',
            'status.required' => 'Status kiritish majburiy',
            'status.in' => 'Status noto\'g\'ri',
        ];
    }
}
