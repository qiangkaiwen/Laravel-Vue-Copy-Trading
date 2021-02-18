<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page = $request->get('page', 1);
        $page = intval($page);
        $perPage = $request->get('perPage', 10);
        $perPage = intval($perPage);
        $total = User::all()->count();
        if ($perPage == -1)
            $users = User::all();
        else
            $users = User::skip(($page - 1) * $perPage)->take($perPage)
                ->get(['id', 'name', 'email', 'phone', 'active', 'created_at']);
        $users->toArray();
        return response()->json([
            'response' => [
                'code' => 200,
                'api_status' => 1,
                'page' => $page,
                'perPage' => $perPage,
                'total' => $total,
                'users' => $users,
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::where('id', $id)->get(['id', 'name', 'email', 'phone', 'active', 'created_at'])->first();
        return response()->json([
            'response' => [
                'code' => 200,
                'api_status' => 1,
                'user' => $user,
            ]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::where('id', $id)->first();
        if (!$user) {
            return response()->json([
                'response' => [
                    'code' => 400,
                    'api_status' => 0,
                    'message' => "User not found",
                ]
            ], 400);
        }
        $name = $request->get('name');
        $email = $request->get('email');
        $phone = $request->get('phone');
        $password = $request->get('password');
        if ($name) {
            $user['name'] = $name;
        }
        if ($email) {
            $user['email'] = $email;
        }
        if ($phone) {
            $user['phone'] = $phone;
        }
        if ($password) {
            $user['password'] = bcrypt($password);
        }
        $user->save();

        return response()->json([
            'response' => [
                'code' => 200,
                'api_status' => 1,
                'message' => "Successfully updated.",
            ]
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::where('id', $id)->first();
        if ($user) {
            $user->delete();
            return response()->json([
                'response' => [
                    'code' => 200,
                    'api_status' => 1,
                    'message' => "Successfully deleted.",
                ]
            ]);
        }
        return response()->json([
            'response' => [
                'code' => 400,
                'api_status' => 0,
                'message' => "User doesn't exist.",
            ]
        ], 400);
    }
}
