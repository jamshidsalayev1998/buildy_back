<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ManagerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => [
                'id' => $this->user->id,
                'phone' => $this->user->phone,
                'image_url' => $this->user->image_path ? asset('storage/' . $this->user->image_path) : null,
            ],
            'company' => [
                'id' => $this->company->id,
                'name' => $this->company->name,
            ],
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'middle_name' => $this->middle_name,
            'full_name' => $this->full_name,
            'gender' => $this->gender,
            'passport_number' => $this->passport_number,
            'birth_date' => $this->birth_date?->format('Y-m-d'),
            'position' => $this->position,
            'work_type' => $this->work_type,
            'hourly_rate' => $this->hourly_rate,
            'monthly_salary' => $this->monthly_salary,
            'status' => $this->status,
            'notes' => $this->notes,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
