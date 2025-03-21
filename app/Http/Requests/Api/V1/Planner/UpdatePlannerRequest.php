<?php

namespace App\Http\Requests\Api\V1\Planner;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePlannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone' => ['sometimes', 'required', 'string', 'unique:users,phone,' . $this->planner->user_id],
            'password' => ['nullable', 'string', 'min:6'],
            'image' => ['nullable', 'image', 'max:2048'],
            'first_name' => ['sometimes', 'required', 'string', 'max:255'],
            'last_name' => ['sometimes', 'required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'gender' => ['sometimes', 'required', 'in:male,female'],
            'passport_number' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'position' => ['sometimes', 'required', 'string', 'max:255'],
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
            'phone.required' => 'Telefon raqam kiritilishi shart',
            'phone.unique' => 'Bu telefon raqam band',
            'password.min' => 'Parol kamida 6 ta belgidan iborat bo\'lishi kerak',
            'first_name.required' => 'Ism kiritilishi shart',
            'last_name.required' => 'Familiya kiritilishi shart',
            'gender.required' => 'Jins tanlanishi shart',
            'gender.in' => 'Noto\'g\'ri jins tanlangan',
            'position.required' => 'Lavozim kiritilishi shart',
            'work_type.required' => 'Ish turi tanlanishi shart',
            'work_type.in' => 'Noto\'g\'ri ish turi tanlangan',
            'hourly_rate.required_if' => 'Soatlik ish turi tanlanganda stavka kiritilishi shart',
            'monthly_salary.required_if' => 'Oylik ish turi tanlanganda oylik maosh kiritilishi shart',
            'status.required' => 'Status tanlanishi shart',
            'status.in' => 'Noto\'g\'ri status tanlangan'
        ];
    }
}
