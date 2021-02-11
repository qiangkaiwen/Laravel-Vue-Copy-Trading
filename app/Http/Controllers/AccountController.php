<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Accounts;
use App\UserAccounts;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function getAccounts(Request $request, $user_id)
    {
        $user = User::where('id', $user_id)->first();
        if (!$user) {
            return response()->json([
                'response' => [
                    'code' => 400,
                    'api_status' => 0,
                    'message' => "User not found",
                ]
            ], 400);
        }
        $user_accounts = $user->user_accounts;

        $page = $request->get('page', 1);
        $page = intval($page);
        $perPage = $request->get('perPage', 10);
        $perPage = intval($perPage);

        $total = count($user_accounts);

        $user_accounts = $user_accounts->skip(($page - 1) * $perPage)->take($perPage);
        $accounts = array();

        foreach ($user_accounts as $key => $value) {
            $accounts[] = $value->account;
        }

        return response()->json([
            'response' => [
                'code' => 200,
                'api_status' => 1,
                'total' => $total,
                'perPage' => $perPage,
                'page' => $page,
                'accounts' => $accounts,
            ]
        ]);
    }

    public function addAccounts(Request $request, $user_id)
    {
        $user = User::where('id', $user_id)->first();
        if (!$user) {
            return response()->json([
                'response' => [
                    'code' => 400,
                    'api_status' => 0,
                    'message' => "User not found",
                ]
            ], 400);
        }

        $account_number = $request->account_number;
        $aid = Accounts::where('account_number', $account_number)->exists();
        if ($aid) {
            return response()->json([
                'response' => [
                    'code' => 400,
                    'api_status' => 0,
                    'message' => "Account Number Already exist.",
                ]
            ], 400);
        }
        $account = Accounts::create(['account_number' => $account_number]);
        UserAccounts::create(['user_id' => $user['id'], 'account_id' => $account['id']]);

        return response()->json([
            'response' => [
                'code' => 200,
                'api_status' => 1,
                'message' => "Account Created",
                'account' => $account,
            ]
        ]);
    }

    public function getMyAccounts(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'response' => [
                    'code' => 400,
                    'api_status' => 0,
                    'message' => "User not found",
                ]
            ], 400);
        }
        $user_accounts = $user->user_accounts;

        $page = $request->get('page', 1);
        $page = intval($page);
        $perPage = $request->get('perPage', 10);
        $perPage = intval($perPage);

        $total = count($user_accounts);

        $user_accounts = $user_accounts->skip(($page - 1) * $perPage)->take($perPage);
        $accounts = array();

        foreach ($user_accounts as $key => $value) {
            $accounts[] = $value->account;
        }

        return response()->json([
            'response' => [
                'code' => 200,
                'api_status' => 1,
                'page' => $page,
                'perPage' => $perPage,
                'total' => $total,
                'accounts' => $accounts,
            ]
        ]);
    }

    public function addMyAccounts(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'response' => [
                    'code' => 400,
                    'api_status' => 0,
                    'message' => "User not found",
                ]
            ], 400);
        }

        $account_number = $request->account_number;
        $aid = Accounts::where('account_number', $account_number)->exists();
        if ($aid) {
            return response()->json([
                'response' => [
                    'code' => 400,
                    'api_status' => 0,
                    'message' => "Account Number Already exist.",
                ]
            ], 400);
        }
        $account = Accounts::create(['account_number' => $account_number]);
        UserAccounts::create(['user_id' => $user['id'], 'account_id' => $account['id']]);

        return response()->json([
            'response' => [
                'code' => 200,
                'api_status' => 1,
                'message' => "Account Created",
                'account' => $account,
            ]
        ]);
    }
}
