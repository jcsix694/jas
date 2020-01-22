<?php

namespace App\Http\Controllers;

use App\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class JobController extends Controller
{
    public function create(Request $request)
    {
        if($request->user()->group_id == config('db.values.groups.worker.id'))
        {
            // if logged on user is a worker decline access
            return $this->decline_access();
        }
        else
        {
            // validates request
            $request->validate([
                'name' => ['required', 'string', 'max:100', 'unique:jobs'],
                'description' => ['required', 'string', 'max:255'],
                'no_shifts' => ['required', 'integer', 'min:1', 'max:12']
            ]);

            // returns created job
            return array(Job::create([
                'name' => $request->name,
                'description' => $request->description,
                'no_shifts' => $request->no_shifts,
                'admin_id' => $request->user()->id,
            ])->load(array('admin', 'shifts')));
        }
    }

    public function get(Request $request){
        if($request->user()->group_id == config('db.values.groups.worker.id')){
            if(is_null($request->user()->shift_id)) {
               // if user is a worker and has no shift show message
               return $this->no_job();
            }
            else{
                // if user is a worker and has a shift show job
                return $request->user()->shift->job;
            }
        }
        else{
            // gets id from request
            $id = $request->query("id");

            // checks if id exists
            $request->validate([
                'id' => ['exists:jobs,id'],
            ]);

            return $this->get_job($id);
        }
    }

    public function get_job($id){
        // Get jobs with shifts
        $results = Job::with(['shifts', 'admin']);

        if(!is_null($id)){
            // id searching for a specific job add id
            $results = $results->where('id', $id);
        }

        $results = $results->get();

        if(sizeof($results) == 0){
            return $this->no_results();
        }

        return $results;
    }
}
