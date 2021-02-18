<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Accounts;
use App\UserAccounts;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        $page = $request->get('page', 1);
        $page = intval($page);
        $perPage = $request->get('perPage', 10);
        $perPage = intval($perPage);
        $skip = ($page - 1) * $perPage;

        $query = "SELECT
                tbl_account.id,
                tbl_account.account_number,
                tbl_account.broker,
                tbl_account.created_at,
                ( CASE WHEN ISNULL( qryProvider.account_id ) = FALSE THEN 'success' WHEN ISNULL( qryCopy.slave_id ) = FALSE THEN 'danger' ELSE 'secondary' END ) AS `statusColor`, 
                ( CASE WHEN ISNULL( qryProvider.account_id ) = FALSE THEN 'PROVIDE' WHEN ISNULL( qryCopy.slave_id ) = FALSE THEN 'COPY' ELSE 'NONE' END ) AS `status` 
                FROM
                tbl_user_account
                INNER JOIN tbl_account ON tbl_user_account.account_id = tbl_account.id
                LEFT JOIN ( SELECT account_id FROM tbl_source GROUP BY tbl_source.account_id ) AS qryProvider ON qryProvider.account_id = tbl_account.id
                LEFT JOIN ( SELECT slave_id FROM tbl_copy GROUP BY tbl_copy.slave_id ) AS qryCopy ON qryCopy.slave_id = tbl_account.id 
                WHERE
                tbl_user_account.user_id = $user_id ";

        $total = DB::select("SELECT COUNT(1) as total from ( " . $query . ") as result");
        $total = $total[0]->total;

        $accounts = DB::select($query . "LIMIT $skip, $perPage");

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
        $broker = $request->broker;
        $aid = Accounts::where(['account_number' => $account_number, 'broker' => $broker])->exists();
        if ($aid) {
            return response()->json([
                'response' => [
                    'code' => 400,
                    'api_status' => 0,
                    'message' => "Account Number Already exist.",
                ]
            ], 400);
        }
        $account = Accounts::create(['account_number' => $account_number, 'broker' => $broker]);
        UserAccounts::create(['user_id' => $user['id'], 'account_id' => $account['id']]);

        return response()->json([
            'response' => [
                'code' => 200,
                'api_status' => 1,
                'message' => "Account Created",
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
        $user_id = $user->id;

        $page = $request->get('page', 1);
        $page = intval($page);
        $perPage = $request->get('perPage', 10);
        $perPage = intval($perPage);
        $skip = ($page - 1) * $perPage;

        $query = "SELECT
                tbl_account.id,
                tbl_account.account_number,
                tbl_account.broker,
                tbl_account.created_at,
                ( CASE WHEN ISNULL( qryProvider.account_id ) = FALSE THEN 'success' WHEN ISNULL( qryCopy.slave_id ) = FALSE THEN 'danger' ELSE 'secondary' END ) AS `statusColor`, 
                ( CASE WHEN ISNULL( qryProvider.account_id ) = FALSE THEN 'PROVIDE' WHEN ISNULL( qryCopy.slave_id ) = FALSE THEN 'COPY' ELSE 'NONE' END ) AS `status` 
                FROM
                tbl_user_account
                INNER JOIN tbl_account ON tbl_user_account.account_id = tbl_account.id
                LEFT JOIN ( SELECT account_id FROM tbl_source GROUP BY tbl_source.account_id ) AS qryProvider ON qryProvider.account_id = tbl_account.id
                LEFT JOIN ( SELECT slave_id FROM tbl_copy GROUP BY tbl_copy.slave_id ) AS qryCopy ON qryCopy.slave_id = tbl_account.id 
                WHERE
                tbl_user_account.user_id = $user_id ";

        $total = DB::select("SELECT COUNT(1) as total from ( " . $query . ") as result");
        $total = $total[0]->total;

        $accounts = DB::select($query . "LIMIT $skip, $perPage");

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
        $broker = $request->broker;
        $aid = Accounts::where(['account_number' => $account_number, 'broker' => $broker])->exists();
        if ($aid) {
            return response()->json([
                'response' => [
                    'code' => 400,
                    'api_status' => 0,
                    'message' => "Account Number Already exist.",
                ]
            ], 400);
        }
        $account = Accounts::create(['account_number' => $account_number, 'broker' => $broker]);
        UserAccounts::create(['user_id' => $user['id'], 'account_id' => $account['id']]);

        return response()->json([
            'response' => [
                'code' => 200,
                'api_status' => 1,
                'message' => "Account Created",
            ]
        ]);
    }
}
