<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $fillable = [
        'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday', 'pay_per_hour', 'start', 'end', 'job_id'
    ];

    protected $hidden = [
        'job_id', 'created_at', 'updated_at'
    ];

    public function job()
    {
        return $this->belongsTo('App\Job');
    }
}
