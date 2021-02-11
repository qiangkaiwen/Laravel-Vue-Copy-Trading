<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Accounts;
use App\UserAccounts;
use App\Source;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    public function getProvideSource(Request $request)
    {
        $page = $request->get('page', 1);
        $page = intval($page);
        $perPage = $request->get('perPage', 10);
        $perPage = intval($perPage);
        $offset = ($page - 1) * $perPage;

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
        $user_id = $user->id;
        $query = "SELECT
                    tbl_source.account_number,
                    tbl_source.symbol,
                    MIN( tbl_source.openTime ) AS openTime,
                    COUNT( 1 ) AS signal_number
                FROM
                    tbl_source
                    LEFT JOIN tbl_account ON tbl_account.account_number = tbl_source.account_number
                    LEFT JOIN tbl_user_account ON tbl_account.id = tbl_user_account.account_id
                WHERE
                    tbl_user_account.user_id = $user_id
                GROUP BY
                    tbl_source.account_number,
                    tbl_source.symbol ";
        $total = DB::select("SELECT COUNT(1) as total from 
                            ( " . $query . ") as result");
        $total = $total[0]->total;
        $provideSignal = DB::select($query . "LIMIT $offset, $perPage");
        return response()->json([
            'response' => [
                'code' => 200,
                'api_status' => 1,
                'total' => $total,
                'page' => $page,
                'perPage' => $perPage,
                'provideSignal' => $provideSignal,
            ]
        ], 200);
    }

    public function getProvideSourceDetail(Request $request, $account_number)
    {
        $page = $request->get('page', 1);
        $page = intval($page);
        $perPage = $request->get('perPage', 10);
        $perPage = intval($perPage);
        $offset = ($page - 1) * $perPage;

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
        $user_id = $user->id;
        $query = "SELECT
                    tbl_source.account_number,
                    tbl_source.symbol,
                    tbl_source.openTime,
                    tbl_source.type,
                    tbl_source.openPrice,
                    tbl_source.takeProfitPrice,
                    tbl_source.stopLossPrice 
                FROM
                    tbl_source
                    LEFT JOIN tbl_account ON tbl_account.account_number = tbl_source.account_number
                    LEFT JOIN tbl_user_account ON tbl_account.id = tbl_user_account.account_id 
                WHERE
                    tbl_user_account.user_id = $user_id 
                    AND tbl_source.account_number = '$account_number' ";
        $total = DB::select("SELECT COUNT(1) as total from 
                            ( " . $query . ") as result");
        $total = $total[0]->total;
        $provideSignalDetail = DB::select($query . "LIMIT $offset, $perPage");
        return response()->json([
            'response' => [
                'code' => 200,
                'api_status' => 1,
                'total' => $total,
                'page' => $page,
                'perPage' => $perPage,
                'provideSignalDetail' => $provideSignalDetail,
            ]
        ], 200);
    }
}
