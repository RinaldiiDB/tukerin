<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;

#[Fillable([
    'user_id',
    'points_used',
    'amount',
    'method',
    'bank_name',
    'recipient_account',
    'status',
    'rejection_note',
    'processed_at'
])]
class RedemptionRequest extends Model
{
    use HasUuids;

    public function newUniqueId(): string
    {
        return (string) Str::uuid7();
    }

    protected function casts(): array
    {
        return [
            'processed_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeAccessibleBy($query, User $user)
    {
        if ($user->isAdmin()) {
            return $query;
        }
        // Employees do not have redemption requests, but if they do query, they see nothing.
        // Users see their own.
        return $query->where('user_id', $user->id);
    }
}
