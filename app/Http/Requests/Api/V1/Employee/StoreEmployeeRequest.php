<?php

namespace App\Http\Requests\Api\V1\Employee;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use App\Models\Employee;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', [Employee::class, $this->input('position')]);
    }

    public function rules(): array
    {
        return [
            'phone' => ['required', 'string', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:6'],
            'image' => ['nullable', 'image', 'max:2048'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'gender' => ['required', 'in:male,female'],
            'passport_number' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'position' => ['required', 'string', 'in:manager,planner,employee'],
            'work_type' => ['required', 'in:hourly,fixed'],
            'hourly_rate' => ['required_if:work_type,hourly', 'nullable', 'numeric', 'min:0'],
            'monthly_salary' => ['required_if:work_type,fixed', 'nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:active,new,inactive'],
            'notes' => ['nullable', 'string']
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required' => 'Telefon raqami kiritilishi kerak',
            'phone.unique' => 'Bu telefon raqami allaqachon ro\'yxatdan o\'tgan',
            'password.required' => 'Parol kiritilishi kerak',
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
