<?php

namespace App\Http\Controllers;

use App\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class JobController extends Controller
{
    public function create(Request $request)
    {
        // Get logged on user
        $userId = $request->user()->{config('db.fields.id')};
        $userGroupId = $request->user()->{config('db.fields.group_id')};

        // If logged on users group is a worker
        if($userGroupId == config('db.values.groups.worker.id'))
        {
            // Return error - do not have access
            return $this->decline_access();
        }
        else
        {
            $this->validate_job($request);

            // returns created job
            return Job::create([
                config('db.fields.name') => $request->{config('db.fields.name')},
                config('db.fields.description') => $request->{config('db.fields.description')},
                config('db.fields.no_shifts') => $request->{config('db.fields.no_shifts')},
                config('db.fields.admin_id') => $userId,
            ])->load(config('db.tables.admin'));
        }
    }

    public function validate_job($request)
    {
        // validates request
        $request->validate([
            config('db.fields.name') => ['required', 'string', 'max:100'],
            config('db.fields.description') => ['required', 'string', 'max:255'],
            config('db.fields.no_shifts') => ['required', 'integer', 'min:1', 'max:12']
        ]);
    }

    public function get(Request $request, $id = null){
        // Get logged on user
        $userGroupId = $request->user()->{config('db.fields.group_id')};

        // If logged on users group is a worker
        if($userGroupId == config('db.values.groups.worker.id'))
        {
           // decline access if no shift else return job
            if(is_null($request->user()->{config('db.fields.shift_id')})) {
                return $this->decline_access();
            }
            else
            {
                return $request->user()->shift->job;
            }
        }
        else {
            if (is_null($id)) {
                return Job::all();
            } else {
                $job = Job::where(config('db.fields.id'), $id)->get();

                if (is_null($job)) {
                    return $this->no_results();
                }

                return $job;
            }
        }
    }
}
