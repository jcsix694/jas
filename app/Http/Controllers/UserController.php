<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{
    public function login(Request $request){
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
        return $this->post($request, config('db.values.groups.worker.id'));
    }

    public function create_admin(Request $request)
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
            // return created admin
            return $this->post($request, config('db.values.groups.admin.id'));
        }
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

    public function post($request, $groupId){
        $this->validate_user($request);

        // returns created user
        return User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'group_id' => $groupId,
        ])->load('group');
    }

    public function get(Request $request, $filterId = null)
    {
        // Get logged on user from token
        $user = $request->user();

        // If logged on users group is a worker
        if($user->{config('db.fields.group_id')} == config('db.values.groups.worker.id'))
        {
            // Set the variable id as that users id
            $filterId = $user->{config('db.fields.id')};
        }

        if(is_null($filterId))
        {
            $results = User::all();

            if(sizeof($results) == 0){
                return response()->json(config('messages.results.error.message'), config('messages.results.error.status'));
            }
        }
        else {
            $results = User::query()->where(config('db.fields.id'), $filterId)->get();

            // if no results after searching for user with specified id then return error
            if(sizeof($results) == 0){
                return response()->json($filterId.config('messages.exist.error.message'), config('messages.exist.error.status'));
            }
        }

        // foreach result
        foreach ($results as $result)
        {
           $result->group->id;
        }

        // return results
        return $results;
    }
}
