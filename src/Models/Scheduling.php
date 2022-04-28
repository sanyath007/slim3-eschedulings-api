<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Scheduling extends Model
{
    protected $table = "schedulings";

    public function shifts()
    {
        return $this->hasMany(SchedulingDetail::class, 'scheduling_id', 'id');
    }

    public function depart()
    {
        return $this->belongsTo(Depart::class, 'depart_id', 'depart_id');
    }

    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id', 'ward_id');
    }

    public function controller()
    {
        return $this->belongsTo(Person::class, 'controller_id', 'person_id');
    }
}