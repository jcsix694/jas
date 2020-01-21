<?php

namespace App\Http\Controllers;

use App\Shift;
use App\Job;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function create(Request $request)
    {
        // Get logged on user from token
        $user = $request->user();

        // If logged on users group is a worker
        if($user->{config('db.fields.group_id')} == config('db.values.groups.worker.id'))
        {
            // Return error - do not have access
            return $this->decline_access();
        }
        else
        {
            // validates request
            $request->validate([
                config('db.fields.monday') => ['required', 'boolean'],
                config('db.fields.tuesday') => ['required', 'boolean'],
                config('db.fields.wednesday') => ['required', 'boolean'],
                config('db.fields.thursday') => ['required', 'boolean'],
                config('db.fields.friday') => ['required', 'boolean'],
                config('db.fields.saturday') => ['required', 'boolean'],
                config('db.fields.sunday') => ['required', 'boolean'],
                config('db.fields.start') => ['required', 'regex:/^\d+(\.\d{1,2})?$/'],
                config('db.fields.end') => ['required', 'regex:/^\d+(\.\d{1,2})?$/'],
                config('db.fields.pay_per_hour') => ['required', 'regex:/^\d+(\.\d{1,2})?$/'],
                config('db.fields.job_id') => ['required', 'exists:'.config('db.tables.jobs').','.config('db.fields.id')],
            ]);

            $job = Job::query()->where(config('db.fields.id'), $request->{config('db.fields.job_id')})->get()[0]->no_shifts;
            $shifts = Shift::query()->where(config('db.fields.job_id'), $request->{config('db.fields.job_id')})->get();

            if(sizeof($shifts) >= $job)
            {
                return response()->json(config('messages.max_shifts.error.message'), config('messages.max_shifts.error.status'));
            }

            // returns created job
            return Shift::create([
                config('db.fields.monday') => $request->{config('db.fields.monday')},
                config('db.fields.tuesday') => $request->{config('db.fields.tuesday')},
                config('db.fields.wednesday') => $request->{config('db.fields.wednesday')},
                config('db.fields.thursday') => $request->{config('db.fields.thursday')},
                config('db.fields.friday') => $request->{config('db.fields.friday')},
                config('db.fields.saturday') => $request->{config('db.fields.saturday')},
                config('db.fields.sunday') => $request->{config('db.fields.sunday')},
                config('db.fields.start') => $request->{config('db.fields.start')},
                config('db.fields.end') => $request->{config('db.fields.end')},
                config('db.fields.pay_per_hour') => $request->{config('db.fields.pay_per_hour')},
                config('db.fields.job_id') => $request->{config('db.fields.job_id')},
            ])->load(config('db.tables.job'));
        }
    }
}