<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Building extends Model
{
    protected $table = "buildings";

    public function wards()
    {
        return $this->hasMany(Ward::class, 'building', 'id');
    }
}