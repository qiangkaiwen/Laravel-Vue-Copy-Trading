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
        $accounts = array();
        for ($i = 0; $i < count($user_accounts); $i++) {
            $accounts[] = $user_accounts[$i]->account;
        }
        return response()->json([
            'response' => [
                'code' => 200,
                'api_status' => 1,
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
        $accounts = array();
        for ($i = 0; $i < count($user_accounts); $i++) {
            $accounts[] = $user_accounts[$i]->account;
        }
        return response()->json([
            'response' => [
                'code' => 200,
                'api_status' => 1,
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
