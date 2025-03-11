<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'middle_name' => $this->middle_name,
            'phone' => $this->phone,
            'gender' => $this->gender,
            'passport_number' => $this->passport_number,
            'birth_date' => $this->birth_date?->format('Y-m-d'),
            'position' => $this->position,
            'work_type' => $this->work_type,
            'hourly_rate' => $this->when($this->work_type === 'hourly', $this->hourly_rate),
            'monthly_salary' => $this->when($this->work_type === 'fixed', $this->monthly_salary),
            'status' => $this->status,
            'notes' => $this->notes,
            'image_url' => $this->when($this->image_path, fn() => url('storage/' . $this->image_path)),
            'role' => $this->roles->first()?->name,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
