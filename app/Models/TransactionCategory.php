<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'company_id'
    ];

    protected $casts = [
        'type' => 'string',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeVisibleToUser($query)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        if($user->hasRole('admin') || $user->hasRole('manager')) {
            return $query->where('company_id', $user->admin->company_id);
        }
        if($user->hasRole('superadmin')) {
            return $query;
        }
        return $query->where('company_id', $user->planner->company_id);
    }

}
