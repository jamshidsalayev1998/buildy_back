<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if (!$this->resource) {
            return [];
        }

        $data = [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'company_id' => $this->company_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'middle_name' => $this->middle_name,
            'gender' => $this->gender,
            'passport_number' => $this->passport_number,
            'birth_date' => $this->birth_date,
            'position' => $this->position,
            'work_type' => $this->work_type,
            'hourly_rate' => $this->hourly_rate,
            'monthly_salary' => $this->monthly_salary,
            'status' => $this->status,
            'notes' => $this->notes,
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'phone' => $this->user->phone,
                    'image_path' => $this->user->image_path,
                ];
            }),
            'company' => $this->whenLoaded('company'),
            'balance' => $this->balance,
            'image_path' => $this->image_path ? asset('storage/' . $this->image_path) : null,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];

        if($this->relationLoaded('company')) {
            $data['company'] = $this->company;
        }

        return $data;
    }
}
