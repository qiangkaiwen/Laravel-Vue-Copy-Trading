<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Accounts;
use App\UserAccounts;
use App\Role\UserRole;
use Validator;

class AuthController extends Controller
{

    private $successStatus = ['api_status' => 1, 'code' => 200,];
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
            $email = $request->email;
            $eid = User::where('email', $email)->exists();
            $aid = null;
            if ($request->account_number)
                $aid = Accounts::where('account_number', $request->account_number)->exists();
            if ($eid == true) {
                return response()->json([
                    'response' => [
                        'api_status' => 0,
                        'code' => 400,
                        'message' => 'This email already exist.'
                    ]
                ], 400);
            } else if ($aid) {
                return response()->json([
                    'response' => [
                        'api_status' => 0,
                        'code' => 400,
                        'message' => 'This account number already exist.'
                    ]
                ], 400);
            } else {
                $input = $request->all();
                $input['password'] = bcrypt($input['password']);
                $user = User::create($input);
                $user->addRole(UserRole::ROLE_USER);
                if ($request->account_number) {
                    $account = Accounts::create(['account_number' => $input['account_number']]);
                    UserAccounts::create(['user_id' => $user['id'], 'account_id' => $account['id']]);
                }

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
            if (Auth::attempt(['email' => request('email'), 'password' => request('password'), 'active' => 1], true)) {
                $user = Auth::user();
                $api_token =  $user->createToken('Personal Access Token')->accessToken;
                $user->api_token = $api_token;
                $user->save();
                return response()->json([
                    'response' => [
                        'api_status' => 1,
                        'code' => 200,
                        'message' => 'Logged in Successfully.',
                        'access_token' => $api_token,
                        'name' => $user->name,
                        'email' => $user->email,
                    ]
                ], 200);
            } else {
                return response()->json([
                    'response' =>
                    [
                        'api_status' => 0,
                        'code' => 200,
                        'message' => 'Data is not in the proper format or check your email and password.'
                    ]
                ], 200);
            }
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
