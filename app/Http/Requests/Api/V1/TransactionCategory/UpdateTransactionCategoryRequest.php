<?php

namespace App\Http\Requests\Api\V1\TransactionCategory;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTransactionCategoryRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:INCOME,EXPENSE'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Kategoriya nomi kiritilishi shart',
            'name.string' => 'Kategoriya nomi matn bo\'lishi kerak',
            'name.max' => 'Kategoriya nomi 255 ta belgidan oshmasligi kerak',
            'type.required' => 'Kategoriya turi kiritilishi shart',
            'type.string' => 'Kategoriya turi matn bo\'lishi kerak',
            'type.in' => 'Kategoriya turi faqat INCOME yoki EXPENSE bo\'lishi mumkin',
        ];
    }
}
