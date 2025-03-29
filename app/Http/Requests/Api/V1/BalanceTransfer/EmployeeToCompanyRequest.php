<?php

namespace App\Http\Requests\Api\V1\BalanceTransfer;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeToCompanyRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'employee_id' => 'required|exists:employees,id',
            'company_id' => 'required|exists:companies,id',
            'amount' => 'required|numeric|min:0.01'
        ];
    }

    public function messages()
    {
        return [
            'employee_id.required' => 'Xodim ID si talab qilinadi',
            'employee_id.exists' => 'Bunday xodim mavjud emas',
            'company_id.required' => 'Kompaniya ID si talab qilinadi',
            'company_id.exists' => 'Bunday kompaniya mavjud emas',
            'amount.required' => 'Summa talab qilinadi',
            'amount.numeric' => 'Summa raqam bo\'lishi kerak',
            'amount.min' => 'Summa 0.01 dan katta bo\'lishi kerak'
        ];
    }
}
