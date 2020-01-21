<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = [
        'shift_id', 'worker_id', 'status_id'
    ];
}
