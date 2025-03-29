<?php

namespace App\Http\Requests\Api\V1\Employee;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone' => ['sometimes', 'required', 'string', 'unique:users,phone,' . $this->employee->user_id],
            'password' => ['nullable', 'string', 'min:6'],
            'image' => ['nullable', 'image', 'max:2048'],
            'first_name' => ['sometimes', 'required', 'string', 'max:255'],
            'last_name' => ['sometimes', 'required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'gender' => ['sometimes', 'required', 'in:male,female'],
            'passport_number' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'position' => ['sometimes', 'required', 'in:manager,planner,employee'],
            'work_type' => ['sometimes', 'required', 'in:hourly,fixed'],
            'hourly_rate' => ['required_if:work_type,hourly', 'nullable', 'numeric', 'min:0'],
            'monthly_salary' => ['required_if:work_type,fixed', 'nullable', 'numeric', 'min:0'],
            'status' => ['sometimes', 'required', 'in:active,new,inactive'],
            'notes' => ['nullable', 'string']
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required' => 'Telefon raqami kiritilishi kerak',
            'phone.unique' => 'Bu telefon raqami allaqachon ro\'yxatdan o\'tgan',
            'password.min' => 'Parol kamida 6 ta beli bo\'lishi kerak',
            'first_name.required' => 'Ism kiritilishi kerak',
            'last_name.required' => 'Familiya kiritilishi kerak',
            'gender.required' => 'Jinsi kiritilishi kerak',
            'gender.in' => 'Jinsi noto\'g\'ri kiritilgan',
            'position.required' => 'Lavozim kiritilishi kerak',
            'position.in' => 'Lavozim noto\'g\'ri kiritilgan',
            'work_type.required' => 'Ish turi kiritilishi kerak',
            'work_type.in' => 'Ish turi noto\'g\'ri kiritilgan',
            'hourly_rate.required_if' => 'Soatlik stavka kiritilishi kerak',
            'monthly_salary.required_if' => 'Oylik maosh kiritilishi kerak',
            'status.required' => 'Holat kiritilishi kerak',
            'status.in' => 'Holat noto\'g\'ri kiritilgan'
        ];
    }
}
