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
        return $this->create($request, config('db.values.groups.worker.id'));
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

    public function create($request, $groupId){
        $this->validate_user($request);

        // returns created user
        return User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'group_id' => $groupId,
        ])->load('group');
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

    public function get_user(Request $request)
    {
        $id = $request->user()->{config('db.fields.id')};
        $groupId = $request->user()->{config('db.fields.group_id')};

        // return logged on user
        return $this->get($id, $groupId);
    }

    public function get_admin(Request $request, $id = null){
        $groupId = $request->user()->{config('db.fields.group_id')};

        // If logged on users group is a worker
        if($groupId == config('db.values.groups.worker.id'))
        {
            // decline access
            return $this->decline_access();
        }
        else
        {
            // get admin by id
            return $this->get($id, config('db.values.groups.admin.id'));
        }
    }

    public function get_worker(Request $request, $id = null){
        $groupId = $request->user()->{config('db.fields.group_id')};

        // If logged on users group is a worker
        if($groupId == config('db.values.groups.worker.id'))
        {
            // decline access
            return $this->decline_access();
        }
        else
        {
            // get worker by id
            return $this->get($id, config('db.values.groups.worker.id'));
        }
    }

    public function get($id, $groupId){
        $fields = ['group'];

        switch ($groupId){
            case config('db.values.groups.admin.id'):
                break;
            case config('db.values.groups.worker.id'):
                array_push($fields, 'shift');
                break;
        }

        $results = User::with($fields)->where(config('db.fields.group_id'), $groupId);

        if(!is_null($id)){
            $results = $results->where(config('db.fields.id'), $id);
        }

        $results = $results->get();

        if(sizeof($results) == 0)
        {
            return $this->no_results();
        }

        // if searching for workers then loop through each worker and get the job if they have a shift
        if($groupId == config('db.values.groups.worker.id')){
            foreach ($results as $result)
            {
                if(!is_null($result->shift))
                {
                    $result->shift->job;
                }
            }
        }

        return $results;
    }
}
