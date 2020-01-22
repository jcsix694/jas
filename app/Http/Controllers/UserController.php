<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{
    public function get_token(Request $request){
        $client = new \GuzzleHttp\Client;

        // if data try below to login
        try {
            $response = $client->request('POST', config('url.base').config('services.passport.endpoint.token'), [
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => config('services.passport.client.id'),
                    'client_secret' => config('services.passport.client.secret'),
                    'username' => $request->username,
                    'password' => $request->password,
                ]
            ]);

            // return results
            return $response->getBody();
        } catch (\GuzzleHttp\Exception\BadResponseException $e){
            // throw error

            switch ($e->getCode()){
                case 400:
                    if(is_null($request->username) or is_null($request->password))
                    {
                        return response()->json(config('messages.login_empty.error.message'), $e->getCode());
                    }
                    else
                    {
                        return response()->json(config('messages.login.error.message'), config('messages.login.error.status'));
                    }

                    break;

                default:
                    return response()->json(config('messages.server.error.message'), $e->getCode());
            }
        }
    }

    public function create_worker(Request $request)
    {
        // returns created worker
        return $this->create($request, config('db.values.groups.worker.id'), array('shift', 'applications'));
    }

    public function create_admin(Request $request)
    {
        // Get logged on user from token
        $groupId = $request->user()->{config('db.fields.group_id')};

        // If logged on users group is a worker
        if($groupId == config('db.values.groups.worker.id'))
        {
            // Return error - do not have access
            return $this->decline_access();
        }
        else
        {
            // return created admin
            return $this->create($request, config('db.values.groups.admin.id'));
        }
    }

    public function create($request, $groupId, $relations = array()){
        array_push($relations, 'group');

        $this->validate_user($request);

        // returns created user
        return array(User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'group_id' => $groupId,
        ])->load($relations));
    }

    public function validate_user($request)
    {
        // validates request
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);
    }

    public function user(Request $request){
        if($request->user()->group_id == config('db.values.groups.worker.id')){
            // if user is a worker then get user
            return $this->get_worker($request->user()->id);
        }
        else{
            // if user is a admin then get admin
            return $this->get_admin($request->user()->id);
        }
    }

    public function admin(Request $request){
        if($request->user()->group_id == config('db.values.groups.worker.id')){
            // if user is a worker then decline access
            return $this->decline_access();
        }
        else{
            // gets id from request
            $id = $request->query("id");

            // checks if id exists
            $request->validate([
                'id' => ['exists:users,id'],
            ]);

            // if user is a admin then get admin
            return $this->get_admin($id);
        }
    }

    public function worker(Request $request){
        if($request->user()->group_id == config('db.values.groups.worker.id')){
            // if user is a worker then decline access
            return $this->decline_access();
        }
        else{
            // gets id from request
            $id = $request->query("id");

            // checks if id exists
            $request->validate([
                'id' => ['exists:users,id'],
            ]);

            // if user is a admin then get admin
            return $this->get_worker($id);
        }
    }

    public function get_admin($id){
        $results = $this->get_user(config('db.values.groups.admin.id'), $id)->get();

        if(sizeof($results) == 0){
            return $this->no_results();
        }

        return $results;
    }

    public function get_worker($id){
        $results = $this->get_user(config('db.values.groups.worker.id'), $id, array('shift','applications','status'))->get();

        if(sizeof($results) == 0){
            return $this->no_results();
        }

        foreach ($results as $result) {
            foreach ($result->applications as $application){
                $application->status;
                $application->shift->job;
            }

            if(!is_null($result->shift)) {
                $result->shift->job;
            }
        }

        return $results;
    }

    public function get_user($groupId, $id = null, $relations = array()){
        array_push($relations, 'group');
        $results = User::with($relations)->where('group_id', $groupId);

        if(!is_null($id)){
            $results = $results->where('id', $id);
        }

        return $results;
    }
}
