<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchedulingDetail extends Model
{
    protected $table = "scheduling_details";

    public function person()
    {
        return $this->belongsTo(Person::class, 'person_id', 'person_id');
    }

    public function scheduling()
    {
        return $this->belongsTo(Scheduling::class, 'scheduling_id', 'id');
    }
}