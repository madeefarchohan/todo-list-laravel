<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Auth;


class AuthController extends Controller
{

    public function login(Request $request){
        $validation = Validator::make($request->all(), [
            'username' => 'required|string|email',
            'password' 		=> 'required',
            'remember_me' 	=> 'boolean'
        ]);
        if ($validation->fails()) {
        	$error = $validation->errors();
        	return response()->json([
            	$error
        	], 422);
            exit;
        }
        $login_bol = Auth::attempt(['email' => request('username'), 'password' => request('password')]);
        if($login_bol){
                $user = Auth::user();
                $response['access_token']     = $user->createToken('token')->accessToken;
                
                $response['message']   = 'Login Successfully...';

                return json_encode($response);
                exit;
                    }else{
            $response = [
                    'message'     => 'Unauthorized...',
                    'status'    => false,
                ];
            return json_encode($response);
            exit;
        }  
    }


    // public function login(Request $request)
    // {
    //     echo '<pre>';
    //     print_r($request->all());
    //     echo '</pre>';

     
    //     // $http = new \GuzzleHttp\Client;

    //     // try {
    //     //     $response = $http->post(config('services.passport.login_endpoint'), [
    //     //         'form_params' => [
    //     //             'grant_type' => 'password',
    //     //             'client_id' => config('services.passport.client_id'),
    //     //             'client_secret' => config('services.passport.client_secret'),
    //     //              'username' => $request->username,
    //     //             'password' => $request->password,
    //     //         ]
    //     //     ]);
    //     //     return $response->getBody();
    //     // } catch (\GuzzleHttp\Exception\BadResponseException $e) {
    //     //     if ($e->getCode() === 400) {
    //     //         return response()->json('Invalid Request. Please enter a username or a password.', $e->getCode());
    //     //     } else if ($e->getCode() === 401) {
    //     //         return response()->json('Your credentials are incorrect. Please try again', $e->getCode());
    //     //     }

    //     //     return response()->json('Something went wrong on the server.', $e->getCode());
    //     // }
    // }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        return User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
    }

    public function logout()
    {
        auth()->user()->tokens->each(function ($token, $key) {
            $token->delete();
        });

        return response()->json('Logged out successfully', 200);
    }
}
