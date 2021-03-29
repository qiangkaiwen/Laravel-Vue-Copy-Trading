<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Accounts;
use App\UserAccounts;
use App\Role\UserRole;
use Illuminate\Support\Facades\Mail;
use App\Mail\Registration;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    private $wrongHTTP = [
        'response' => [
            'api_status' => 0,
            'code' => 400,
            'message' => 'Your HTTP method is not correct.'
        ]
    ];

    public function signup(Request $request)
    {
        //$method = $request->method();         
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email',
                'password' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'response' => [
                        'code' => 400,
                        'api_status' => 0,
                        'message' => 'Data is not in the proper format.',
                    ]
                ]);
            }
            $email = $request['email'];
            $eid = User::where('email', $email)->exists();
            if ($eid == true) {
                return response()->json([
                    'response' => [
                        'api_status' => 0,
                        'code' => 400,
                        'message' => 'This email already exist.',
                    ]
                ], 400);
            } else {
                do {
                    $client_id = User::incrementalHash();
                    $cid_exist = User::where('client_id', $client_id)->exists();
                } while ($cid_exist);

                do {
                    $client_secret = User::incrementalHash();
                    $csec_exist = User::where('client_id', $client_secret)->exists();
                } while ($csec_exist);

                $input = $request->all();
                $input['password'] = bcrypt($input['password']);
                $user = User::create(array_merge($input, ['client_id' => $client_id, 'client_secret' => $client_secret]));
                $user->addRole(UserRole::ROLE_USER);
                $user->save();

                Mail::to($user->email)->send(new Registration($user['name']));

                return response()->json([
                    'response' => [
                        'api_status' => 1,
                        'code' => 201,
                        'message' => 'Successfully registered.',
                    ]
                ], 201);
            }
        }
        return response()->json($this->wrongHTTP, 400);
    }

    public function login(Request $request)
    {
        if ($request->isMethod('post')) {
            $email = $request['email'];
            $password = $request['password'];
            $account_number = $request['account_number'];
            $broker = $request['broker'];
            $client_id = $request['client_id'];
            $client_secret = $request['client_secret'];

            if ($email && $password) {
                if (Auth::attempt(['email' => $email, 'password' => $password, 'active' => 1], true)) {
                    $user = Auth::user();
                    $api_token =  $user->createToken('Personal Access Token')->accessToken;
                    $user->api_token = $api_token;
                    if (!$user->roles)
                        $user->roles = [UserRole::ROLE_USER];
                    $user->save();
                    if ($account_number && $broker) {
                        $aid = Accounts::where(['account_number' => $account_number, 'broker' => $broker])->exists();
                        if (!$aid) {
                            $account = Accounts::create(['account_number' => $account_number, 'broker' => $broker]);
                            UserAccounts::create(['user_id' => $user['id'], 'account_id' => $account['id']]);
                        }
                    }
                    return response()->json([
                        'response' => [
                            'api_status' => 1,
                            'code' => 200,
                            'message' => 'Logged in Successfully.',
                            'access_token' => $api_token,
                            'name' => $user->name,
                            'email' => $user->email,
                            'roles' => $user->roles,
                            'avatar' => $user->avatar,
                        ]
                    ], 200);
                } else {
                    return response()->json([
                        'response' =>
                        [
                            'api_status' => 0,
                            'code' => 400,
                            'message' => 'Data is not in the proper format or check your email and password.'
                        ]
                    ], 400);
                }
            }

            if ($client_id && $client_secret) {
                $user = User::where(['client_id' => $client_id, 'client_secret' => $client_secret, 'active' => '1'])->first();
                if (!$user) {
                    return response()->json([
                        'response' => [
                            'api_status' => 0,
                            'code' => 400,
                            'message' => 'Data is not in the proper format or check your Client Id and secure.'
                        ]
                    ]);
                }
                Auth::login($user, true);
                $api_token =  $user->createToken('Personal Access Token')->accessToken;
                $user->api_token = $api_token;
                if (!$user->roles)
                    $user->roles = [UserRole::ROLE_USER];
                $user->save();

                if ($account_number && $broker) {
                    $aid = Accounts::where(['account_number' => $account_number, 'broker' => $broker])->exists();
                    if (!$aid) {
                        $account = Accounts::create(['account_number' => $account_number, 'broker' => $broker]);
                        UserAccounts::create(['user_id' => $user['id'], 'account_id' => $account['id']]);
                    }
                }

                return response()->json([
                    'response' => [
                        'api_status' => 1,
                        'code' => 200,
                        'message' => 'Logged in Successfully.',
                        'access_token' => $api_token,
                        'name' => $user->name,
                        'email' => $user->email,
                        'roles' => $user->roles,
                        'avatar' => $user->avatar,
                    ]
                ], 200);
            }

            return response()->json([
                'response' => [
                    'api_status' => 0,
                    'code' => 400,
                    'message' => 'Data is not in the proper format or check your credentials.'
                ]
            ], 400);
        }
        return response()->json($this->wrongHTTP, 400);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $user->token()->revoke();

            return response()->json([
                'response' => [
                    'api_status' => 1,
                    'code' => 200,
                    'message' => 'Logged out.',
                ]
            ], 200);
        } else {
            return response()->json([
                'response' =>
                [
                    'api_status' => 0,
                    'code' => 403,
                    'message' => 'Authorization required.',
                ]
            ], 403);
        }
    }
}
