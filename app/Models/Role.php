<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['name', 'label'])]
class Role extends Model
{
    public $timestamps = false;

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
