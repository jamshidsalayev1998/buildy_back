<?php

namespace App\Http\Requests\Api\V1\Transaction;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
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
            'amount' => ['required', 'numeric', 'min:0'],
            'type' => ['required', 'in:income,expense'],
            'transaction_category_id' => ['required', 'exists:transaction_categories,id'],
            'description' => ['nullable', 'string', 'max:500'],
            'receipt_image' => ['nullable', 'image', 'max:5120']
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required' => 'Summa kiritilishi shart',
            'amount.numeric' => 'Summa son bo\'lishi kerak',
            'amount.min' => 'Summa 0 dan katta bo\'lishi kerak',
            'type.required' => 'Turi kiritilishi shart',
            'type.in' => 'Noto\'g\'ri tur tanlangan',
            'transaction_category_id.required' => 'Kategoriya tanlanishi shart',
            'transaction_category_id.exists' => 'Bunday kategoriya mavjud emas',
            'description.max' => 'Izoh 500 ta belgidan oshmasligi kerak',
            'receipt_image.image' => 'Fayl rasm formatida bo\'lishi kerak',
            'receipt_image.max' => 'Rasm hajmi 5MB dan oshmasligi kerak'
        ];
    }
}
