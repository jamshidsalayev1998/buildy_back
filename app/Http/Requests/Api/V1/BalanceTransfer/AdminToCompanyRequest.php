<?php

namespace App\Http\Requests\Api\V1\BalanceTransfer;

use Illuminate\Foundation\Http\FormRequest;

class AdminToCompanyRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'admin_id' => 'required|exists:admins,id',
            'company_id' => 'required|exists:companies,id',
            'amount' => 'required|numeric|min:0.01'
        ];
    }

    public function messages()
    {
        return [
            'admin_id.required' => 'Admin ID si talab qilinadi',
            'admin_id.exists' => 'Bunday admin mavjud emas',
            'company_id.required' => 'Kompaniya ID si talab qilinadi',
            'company_id.exists' => 'Bunday kompaniya mavjud emas',
            'amount.required' => 'Summa talab qilinadi',
            'amount.numeric' => 'Summa raqam bo\'lishi kerak',
            'amount.min' => 'Summa 0.01 dan katta bo\'lishi kerak'
        ];
    }
}
