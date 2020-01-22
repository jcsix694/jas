<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $fillable = [
        'name', 'description', 'no_shifts', 'admin_id', 'list'
    ];

    protected $hidden = [
        'admin_id', 'created_at', 'updated_at'
    ];

    public function shifts()
    {
        return $this->hasMany('App\Shift');
    }

    public function admin()
    {
        return $this->belongsTo('App\User');
    }

    public function workers()
    {
        return $this->hasManyThrough('App\User', 'App\Shift', 'job_id', 'shift_id', 'id', 'id');
    }

    public function applications()
    {
        return $this->hasManyThrough('App\Application', 'App\Shift', 'job_id', 'shift_id', 'id', 'id');
    }
}
