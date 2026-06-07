<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;

#[Fillable(['user_id', 'employee_id', 'total_points', 'transacted_at'])]
class ExchangeTransaction extends Model
{
    use HasUuids;

    public $timestamps = false; // transacted_at is used instead of standard timestamps

    public function newUniqueId(): string
    {
        return (string) Str::uuid7();
    }

    protected function casts(): array
    {
        return [
            'transacted_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function details()
    {
        return $this->hasMany(ExchangeTransactionDetail::class, 'transaction_id');
    }

    public function scopeAccessibleBy($query, User $user)
    {
        if ($user->isAdmin()) {
            return $query;
        }
        if ($user->isEmployee()) {
            return $query->where('employee_id', $user->id);
        }
        return $query->where('user_id', $user->id);
    }
}
