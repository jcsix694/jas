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

    public function no_results()
    {
        return response()->json(config('messages.results.error.message'), config('messages.results.error.status'));
    }

    public function already_have_shift()
    {
        return response()->json(config('messages.shift.error.message'), config('messages.shift.error.status'));
    }

    public function application_status(){
        return response()->json(config('messages.status.error.message'), config('messages.status.error.status'));
    }

    public function no_job(){
        return response()->json(config('messages.job.error.message'), config('messages.job.error.status'));
    }

    public function max_shifts(){
        return response()->json(config('messages.max_shifts.error.message'), config('messages.max_shifts.error.status'));
    }
}
