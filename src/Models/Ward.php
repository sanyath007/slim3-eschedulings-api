<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ward extends Model
{
    protected $connection = "person";
    protected $table = "ward";
    protected $primaryKey = 'ward_id';
    
    public function building()
    {
        return $this->belongsTo(Building::class, 'building', 'id');
    }
}