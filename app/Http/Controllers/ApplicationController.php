<?php

namespace App\Http\Controllers;

use App\Job;
use App\Shift;
use App\Application;
use App\Status;
use App\User;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function delete(Request $request){
        // if user from token is an admin or a worker who has a shift decline access
        if($request->user()->group_id == config('db.values.groups.admin.id') OR !is_null($request->user()->shift_id)){
            return $this->decline_access();
        }
        else{

        }
    }

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
                        $application = Application::create([
                            config('db.fields.shift_id') => $shiftId,
                            config('db.fields.worker_id') => $userId,
                            config('db.fields.status_id') => config('db.values.statuses.pending.id'),
                        ])->load('shift', 'worker', 'status');

                        $application->shift->job;

                        return $application;

                    }
                }

            }
        }
    }

    public function decline(Request $request){
        // Get logged on user
        $userGroupId = $request->user()->{config('db.fields.group_id')};

        // If logged on users group is worker
        if($userGroupId == config('db.values.groups.worker.id'))
        {
            // Return error - do not have access
            return $this->decline_access();
        }
        else{
            return $this->admin($request, config('db.values.statuses.rejected.id'));
        }
    }

    public function accept(Request $request){
        // Get logged on user
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
        $application = Application::where(config('db.fields.id'), $id)->where(config('db.fields.status_id'), config('db.values.statuses.pending.id'))->get();


        if(sizeof($application))
        {
            $application = $application[0];

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
                case config('db.values.statuses.rejected.id'):
                        $reject = Application::find($id);
                        $reject->{config('db.fields.status_id')} = $action;
                        $reject->save();

                        $return = Application::with('shift','status','worker')->where('id', $id)->get()[0];
                        $return->shift->job;
                        return $return;
                    break;
            }
        }
        else{
            return $this->application_status();
        }

    }

    public function statuses()
    {
        return Status::all();
    }

    public function get(Request $request)
    {
        $id = $request->query("id");
        $statusId = $request->query("status_id");

        // validates filters
        $request->validate([
            'id' => ['exists:applications,id'],
            'status_id' => ['exists:statuses,id']
        ]);

        // if worker then show their applications
        if($request->user()->group_id == config('db.values.groups.worker.id')){

            // Throw error if user has a shift already
            if(!is_null($request->user()->shift_id)){
                return $this->already_have_shift();
            }
            else
            {
                $results = Application::with('shift','status','worker')->where('worker_id', $request->user()->id);
            }
        }
        else
        {
            $results = Application::with('shift','status','worker');
        }

        if(!is_null($id)) {
            $results = $results->where('id', $id);
        }

        if(!is_null($statusId)) {
            $results = $results->where('status_id', $statusId);
        }

        $results = $results->get();

        if(sizeof($results) == 0)
        {
            return $this->no_results();
        }
        else
        {
            foreach ($results as $result){
                $result->shift->job;
            }
        }

        return $results;
    }
}
