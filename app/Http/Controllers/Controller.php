<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function decline_access()
    {
        return response()->json(config('messages.access.error.message'), config('messages.access.error.status'));
    }

    public function decline_application()
    {
        return response()->json(config('messages.application.error.message'), config('messages.application.error.status'));
    }

    public function already_applied_application()
    {
        return response()->json(config('messages.applied.error.message'), config('messages.applied.error.status'));
    }
}
