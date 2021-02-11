<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Accounts;
use App\UserAccounts;
use App\Source;
use Illuminate\Support\Facades\Auth;

class SourceController extends Controller
{
    public function addSource(Request $request)
    {
        $input = $request->all();
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'response' => [
                    'code' => 400,
                    'api_status' => 0,
                    'message' => "User not found.",
                ]
            ], 400);
        }
        $user_accounts = $user->user_accounts;
        $confirmed = false;
        for ($i = 0; $i < count($user_accounts); $i++) {
            $account = $user_accounts[$i]->account;
            if ($account['account_number'] == $input['account_number']) {
                $confirmed = true;
                break;
            }
        }
        if (!$confirmed) {
            return response()->json([
                'response' => [
                    'code' => 400,
                    'api_status' => 0,
                    'message' => "Account Number doesn't match.",
                ]
            ], 400);
        }
        $source = Source::create($input);
        return response()->json([
            'response' => [
                'code' => 201,
                'api_status' => 1,
                'message' => "Source Created.",
                'source' => $source
            ]
        ], 201);
    }
}
