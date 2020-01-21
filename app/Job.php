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

    public function admin()
    {
        return $this->belongsTo('App\User');
    }
}
