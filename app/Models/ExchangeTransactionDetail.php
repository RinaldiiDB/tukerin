<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['transaction_id', 'bottle_type_id', 'quantity', 'points_earned'])]
class ExchangeTransactionDetail extends Model
{
    public $timestamps = false;

    public function transaction()
    {
        return $this->belongsTo(ExchangeTransaction::class, 'transaction_id');
    }

    public function bottleType()
    {
        return $this->belongsTo(BottleType::class, 'bottle_type_id');
    }
}
