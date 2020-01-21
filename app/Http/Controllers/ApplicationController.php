<?php

namespace App\Http\Controllers;

use App\Job;
use App\Shift;
use App\Application;
use App\User;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function create(Request $request){
        // Get logged on user
        $userId = $request->user()->{config('db.fields.id')};
        $userGroupId = $request->user()->{config('db.fields.group_id')};
        $userShiftId = $request->user()->{config('db.fields.shift_id')};

        // If logged on users group is admin
        if($userGroupId == config('db.values.groups.admin.id'))
        {
            // Return error - do not have access
            return $this->decline_access();
        }
        else
        {
            if(!is_null($userShiftId))
            {
                // Return error - already have shift
                return $this->decline_application();
            }
            else
            {
                // validates if shift exists
                $request->validate([
                    config('db.fields.shift_id') => ['required', 'exists:'.config('db.tables.shifts').','.config('db.fields.id')],
                ]);

                // shift id
                $shiftId = $request->{config('db.fields.shift_id')};

                // Check if user applied for this shift
                $applied = Application::where(config('db.fields.shift_id'), $shiftId)
                    ->where(config('db.fields.worker_id'), $userId)
                    ->count();

                if($applied > 0)
                {
                    // show already applied message
                    return $this->already_applied_application();
                }
                else
                {
                    // gets job id from shift id
                    $jobId = Shift::where(config('db.fields.id'), $shiftId)->get()[0]->{config('db.fields.job_id')};

                    // gets number of shifts for job
                    $jobNoShifts = Job::where(config('db.fields.id'), $jobId)->get()[0]->no_shifts;

                    // gets the number of workers assigned to a job
                    $shiftsAssigned = sizeof(Job::with('workers')->where(config('db.fields.id'), $jobId)->get()[0]->{config('db.fields.workers')});

                    // if number of workers on a job is bigger or equle to number of shifts for job throw error
                    if($shiftsAssigned >= $jobNoShifts)
                    {
                        return response()->json(config('messages.max_shifts.error.message'), config('messages.max_shifts.error.status'));
                    }
                    else
                    {
                        // returns created application
                        return Application::create([
                            config('db.fields.shift_id') => $shiftId,
                            config('db.fields.worker_id') => $userId,
                            config('db.fields.status_id') => config('db.values.statuses.pending.id')
                        ]);
                    }
                }

            }
        }
    }

    public function accept(Request $request){
        // Get logged on user
        $userId = $request->user()->{config('db.fields.id')};
        $userGroupId = $request->user()->{config('db.fields.group_id')};

        // If logged on users group is worker
        if($userGroupId == config('db.values.groups.worker.id'))
        {
            // Return error - do not have access
            return $this->decline_access();
        }
        else{
           return $this->admin($request, config('db.values.statuses.approved.id'));
        }
    }

    public function admin($request, $action)
    {
        // validates if shift exists
        $request->validate([
            config('db.fields.id') => ['required', 'exists:'.config('db.tables.applications').','.config('db.fields.id')],
        ]);

        // gets the application id
        $id = $request->{config('db.fields.id')};

        // gets the application by id where status is pending
        $application = Application::where(config('db.fields.id'), $id)->where(config('db.fields.status_id'), config('db.values.statuses.pending.id'))->get()[0];

        // worker id
        $workerId = $application->{config('db.fields.worker_id')};

        // switch to see if to approve or reject the application
        switch ($action)
        {
            case config('db.values.statuses.approved.id'):
                $worker = User::find($workerId);
                $worker->{config('db.fields.shift_id')} = $application->{config('db.fields.shift_id')};
                $worker->save();

                Application::where(config('db.fields.worker_id'), $workerId)->delete();

                return User::with(['group','shift'])->where(config('db.fields.id'), $workerId)->get();
                break;
        }
    }
}
