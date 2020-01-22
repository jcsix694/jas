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
            // validates if shift exists
            $request->validate([
                'id' => ['required', 'exists:applications,id'],
            ]);

            $isUsers = Application::where('id', $request->id)->where('worker_id', $request->user()->id)->count();

            if($isUsers == 0){
                // throw error no results
                $this->no_results();
            }

            Application::where('id', $request->id)->delete();

            return response()->json('deleted', 200);
        }
    }

    public function create(Request $request){
        // Get logged on user
        if($request->user()->group_id == config('db.values.groups.admin.id'))
        {
            // if user is an admin decline access
            return $this->decline_access();
        }

        if(!is_null($request->user()->shift_id))
        {
            // if user has a shift decline application
            return $this->decline_application();
        }

            // validates if shift exists
            $request->validate([
                'shift_id' => ['required', 'exists:shifts,id'],
            ]);

            $shiftId = $request->shift_id;

            // check if shift assigned to a worker
            $shiftHasOwner = User::where('shift_id', $shiftId)->get();

            if(sizeof($shiftHasOwner) == 0){
                // Check if user applied for this shift
                $applied = Application::where('shift_id', $shiftId)
                    ->where('worker_id', $request->user()->id)
                    ->count();

                if($applied > 0) {
                    // show already applied message
                    return $this->already_applied_application();
                }else {
                    $shift = Shift::with('job')->where('id', $shiftId)->get()[0];

                    // if number of workers on a job is bigger or the same as the number of shifts for job throw error
                    if (sizeof($shift->job->workers) >= $shift->job->no_shifts) {
                        return $this->max_shifts();
                    } else {
                        // send application
                        $application = Application::create([
                            'shift_id' => $shiftId,
                            'worker_id' => $request->user()->id,
                            'status_id' => config('db.values.statuses.pending.id'),
                        ])->load('shift', 'worker', 'status');

                        $application->shift->job;

                        return array($application);
                    }
                }
            }

                // show message shift belongs to another worker
                return $this->shift_belongs_user();

    }

    public function decline(Request $request){
        // If logged on users group is worker
        if($request->user()->group_id == config('db.values.groups.worker.id'))
        {
            // Return error - do not have access
            return $this->decline_access();
        }
        else{
            return $this->admin($request, config('db.values.statuses.rejected.id'));
        }
    }

    public function accept(Request $request){
        // If logged on users group is worker
        if($request->user()->group_id == config('db.values.groups.worker.id'))
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
            'id' => ['required', 'exists:applications,id'],
        ]);

        // gets the application id
        $id = $request->id;

        // gets the application by id where status is pending
        $application = Application::where('id', $id)->where('status_id', config('db.values.statuses.pending.id'))->get();

        if(sizeof($application) > 0)
        {
            $application = $application[0];

            // worker id
            $workerId = $application->worker_id;

            // switch to see if to approve or reject the application
            switch ($action)
            {
                case config('db.values.statuses.approved.id'):
                    $worker = User::find($workerId);
                    $worker->shift_id = $application->shift_id;
                    $worker->save();

                    Application::where('worker_id', $workerId)->delete();

                    $user = User::with(['group','shift'])->where('id', $workerId)->get();
                    $user[0]->shift->job;

                    return $user;
                    break;
                case config('db.values.statuses.rejected.id'):
                        $reject = Application::find($id);
                        $reject->status_id = $action;
                        $reject->save();

                        $return = Application::with('shift','status','worker')->where('id', $id)->get();
                        $return[0]->shift->job;
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
