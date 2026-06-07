<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['name', 'barcode', 'description', 'points_value'])]
class BottleType extends Model
{
    public function details()
    {
        return $this->hasMany(ExchangeTransactionDetail::class);
    }
}
