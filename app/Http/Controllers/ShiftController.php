<?php

namespace App\Http\Controllers;

use App\Shift;
use App\Job;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function create(Request $request)
    {
        // If logged on users group is a worker
        if($request->user()->group_id == config('db.values.groups.worker.id'))
        {
            // if logged in user is a worker decline access
            return $this->decline_access();
        }
        else
        {
            // validates request
            $request->validate([
                'monday' => ['required', 'boolean'],
                'tuesday' => ['required', 'boolean'],
                'wednesday' => ['required', 'boolean'],
                'thursday' => ['required', 'boolean'],
                'friday' => ['required', 'boolean'],
                'saturday' => ['required', 'boolean'],
                'sunday' => ['required', 'boolean'],
                'start' => ['required', 'regex:/^\d+(\.\d{1,2})?$/'],
                'end' => ['required', 'regex:/^\d+(\.\d{1,2})?$/'],
                'pay_per_hour' => ['required', 'regex:/^\d+(\.\d{1,2})?$/'],
                'job_id' => ['required', 'exists:jobs,id'],
            ]);

            $jobId = $request->job_id;

            // gets total number of shifts allowed for job
            $jobNoShifts = Job::where('id', $jobId)->get()[0]->no_shifts;

            // gets current count of shifts for job
            $shiftsCount = Shift::where('job_id', $jobId)->count();

            if($shiftsCount >= $jobNoShifts)
            {
                // if current shifts is the same as or bigger than total number of shifts allowed for job then show error
               return $this->max_shifts();
            }

            // returns created shift
            return array(Shift::create([
                'monday' => $request->monday,
                'tuesday' => $request->tuesday,
                'wednesday' => $request->wednesday,
                'thursday' => $request->thursday,
                'friday' => $request->friday,
                'saturday' => $request->saturday,
                'sunday' => $request->sunday,
                'start' => $request->start,
                'end' => $request->end,
                'pay_per_hour' => $request->pay_per_hour,
                'job_id' => $jobId,
            ])->load(array('job')));
        }
    }

    public function get(Request $request){
        if($request->user()->group_id == config('db.values.groups.worker.id')){
            // If logged on users group is a worker
            if(is_null($request->user()->shift)){
                // if logged on user is a worker decline access
                return $this->decline_access();
            }
            else{
                $request->user()->shift->job;
                return $request->user()->shift;
            }
        }
        else{
            // gets id from request
            $id = $request->query("id");
            $jobId = $request->query("job_id");

            // checks if id exists
            $request->validate([
                'id' => ['exists:shifts,id'],
                'job_id' => ['exists:jobs,id'],
            ]);

            return $this->get_shift($id, $jobId);
        }
    }

    public function get_shift($id, $jobId){
        // Get jobs with shifts
        $results = Shift::with(['job', 'worker']);

        if(!is_null($id)){
            // id searching for a specific job add id
            $results = $results->where('id', $id);
        }

        if(!is_null($jobId)){
            // id searching for a specific job add id
            $results = $results->where('job_id', $jobId);
        }

        $results = $results->get();

        if(sizeof($results) == 0){
            return $this->no_results();
        }

        return $results;
    }

    public function available(){
        return Shift::with(['job'])
            ->leftjoin('users', 'shifts.id', '=', 'users.shift_id')
            ->select('shifts.*')
            ->whereNull('users.shift_id')
            ->get();
    }
}
