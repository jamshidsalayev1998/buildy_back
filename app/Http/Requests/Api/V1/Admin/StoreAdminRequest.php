<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone' => ['required', 'string', 'size:12', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:6'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'gender' => ['required', 'string', 'in:male,female'],
            'passport_number' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'position' => ['required', 'string', 'max:255'],
            'work_type' => ['required', 'string', 'in:hourly,fixed'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'monthly_salary' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'string', 'in:active,new,inactive'],
            'notes' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048']
        ];
    }
}
