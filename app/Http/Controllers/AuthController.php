<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;
use DB;
use Validator;
use Lcobucci\JWT\Parser;

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
            $eid = DB::table('users')->where('email', $email)->exists();
            if ($eid == true) {
                return response()->json([
                    'response' => [
                        'api_status' => 0,
                        'code' => 200,
                        'message' => 'This email already exist.'
                    ]
                ], 200);
            } else {
                $input = $request->all();
                $input['password'] = bcrypt($input['password']);
                $user = User::create($input);
                $token =  $user->createToken('Personal Access Token')->accessToken;
                $name =  $user->name;
                $email = $user->email;
                return response()->json([
                    'response' => [
                        'api_status' => 1,
                        'code' => 201,
                        'message' => 'Successfully registered.',
                        'access_token' => $token
                    ]
                ], 201);
            }
        }
        return response()->json($this->wrongHTTP, 400);
    }

    public function login(Request $request)
    {
        if ($request->isMethod('post')) {
            if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
                $user = Auth::user();
                $name = $user->name;
                $api_token =  $user->createToken('Personal Access Token')->accessToken;
                $user->api_token = $api_token;
                $user->save();
                return response()->json([
                    'response' => [
                        'api_status' => 1,
                        'code' => 200,
                        'message' => 'Logged in Successfully.',
                        'access_token' => $api_token
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
            return response()->json([
                'response' => [
                    'api_status' => 1,
                    'code' => 200,
                    'user' => $user,
                ]
            ], 200);
        } else {
            return response()->json([
                'response' =>
                [
                    'api_status' => 0,
                    'code' => 403,
                    'message' => 'Authorization required.',
                    'user' => $user
                ]
            ], 403);
        }
        // $value = $request->bearerToken();
        // $id = (new Parser())->parse($value);
        // $token = Auth::user()->tokens->find($id);
        // $token->revoke();
        // return response('You have been successfully logged out!', 200);
    }
}
