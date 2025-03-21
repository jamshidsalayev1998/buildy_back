<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Manager extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_id',
        'first_name',
        'last_name',
        'middle_name',
        'gender',
        'passport_number',
        'birth_date',
        'position',
        'work_type',
        'hourly_rate',
        'monthly_salary',
        'status',
        'notes'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'hourly_rate' => 'decimal:2',
        'monthly_salary' => 'decimal:2'
    ];

    // Munosabatlar
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // To'liq ismni olish uchun accessor
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }

    public function scopeVisibleToUser($query)
    {
        if (auth()->user()->hasRole('admin')) {
            $query->where('company_id', auth()->user()->admin->company_id);
        }
        return $query;
    }
}
