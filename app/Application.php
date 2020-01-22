<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = [
        'shift_id', 'worker_id', 'status_id'
    ];

    protected $hidden = [
        'worker_id', 'admin_id', 'status_id', 'shift_id', 'created_at', 'updated_at'
    ];

    public function shift(){
        return $this->belongsTo('App\Shift');
    }

    public function worker(){
        return $this->belongsTo('App\User');
    }

    public function status(){
        return $this->belongsTo('App\Status');
    }
}
