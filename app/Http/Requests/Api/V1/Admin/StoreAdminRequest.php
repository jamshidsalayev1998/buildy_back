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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'gender' => 'nullable|string|in:male,female',
            'passport_number' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'position' => 'nullable|string|max:255',
            'work_type' => 'nullable|string|in:fixed,hourly',
            'hourly_rate' => 'nullable|numeric|min:0',
            'monthly_salary' => 'nullable|numeric|min:0',
            'status' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'phone' => 'required|string|unique:users,phone',
            'password' => 'required|string|min:6',
            'image' => 'nullable|image|max:2048',
            'company_id' => 'required|exists:companies,id',
            'status' => 'nullable|string|in:active,inactive'
        ];
    }
}
