<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Accounts;
use App\UserAccounts;
use App\Copy;
use App\Source;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
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
                tbl_account.`status`,
                ( CASE WHEN tbl_account.`status` = 'PROVIDE' THEN 'success' WHEN tbl_account.`status` = 'COPY' THEN 'red' ELSE 'secondary' END ) AS `statusColor` 
                FROM
                tbl_user_account
                INNER JOIN tbl_account ON tbl_user_account.account_id = tbl_account.id
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

    public function getAccountsForProvide(Request $request)
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
        $accounts = DB::select("SELECT
                                tbl_account.account_number,
                                tbl_account.broker 
                                FROM
                                tbl_user_account
                                INNER JOIN tbl_account ON tbl_account.id = tbl_user_account.account_id 
                                WHERE
                                tbl_user_account.user_id = $user_id 
                                AND tbl_account.`status` = 'NONE'");
        return response()->json([
            'response' => [
                'code' => 200,
                'api_status' => 1,
                'accounts' => $accounts,
            ]
        ]);
    }

    public function getAccountsForCopy(Request $request)
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
        $accounts = DB::select("SELECT
                                tbl_account.account_number,
                                tbl_account.broker 
                                FROM
                                tbl_user_account
                                INNER JOIN tbl_account ON tbl_account.id = tbl_user_account.account_id 
                                WHERE
                                tbl_user_account.user_id = $user_id 
                                AND (tbl_account.`status` = 'NONE' OR tbl_account.`status` = 'COPY')
                                ");
        return response()->json([
            'response' => [
                'code' => 200,
                'api_status' => 1,
                'accounts' => $accounts,
            ]
        ]);
    }

    public function getProvideAccount(Request $request)
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
        $status = Accounts::STATUS_PROVIDE;
        $query = "SELECT
                tbl_account.id,
                tbl_account.account_number,
                tbl_account.broker,
                sources.openTime,
                IFNULL( sources.signal_number, 0 ) AS signal_number,
                IFNULL( copiers.copier_number, 0 ) AS copier_number 
                FROM
                tbl_account
                LEFT JOIN (
                SELECT
                    tbl_source.account_id,
                    MIN( tbl_source.openTime ) AS openTime,
                    COUNT( 1 ) AS signal_number 
                FROM
                    tbl_source
                    INNER JOIN tbl_user_account ON tbl_source.account_id = tbl_user_account.account_id 
                WHERE
                    tbl_user_account.user_id = $user_id 
                GROUP BY
                    tbl_source.account_id
                ) AS sources ON tbl_account.id = sources.account_id
                LEFT JOIN ( SELECT COUNT( 1 ) AS copier_number, tbl_copy.master_id FROM tbl_copy GROUP BY tbl_copy.master_id ) AS copiers ON copiers.master_id = tbl_account.id 
                INNER JOIN tbl_user_account ON tbl_account.id = tbl_user_account.account_id 
                WHERE
                tbl_account.`status` = '$status' AND 
                tbl_user_account.user_id = $user_id
                ";

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

    public function provideAccount(Request $request)
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
        $account = Accounts::where(['account_number' => $account_number, 'broker' => $broker])->first();
        if (!$account) {
            return response()->json([
                'response' => [
                    'code' => 400,
                    'api_status' => 0,
                    'message' => "Account Number doesn't exist.",
                ]
            ], 400);
        }
        $user_account = $account->user_account;
        if ($user_account->user_id != $user->id) {
            return response()->json([
                'response' => [
                    'code' => 400,
                    'api_status' => 0,
                    'message' => "Account Number is not yours.",
                ]
            ], 400);
        }
        if ($account->status != Accounts::STATUS_NONE) {
            return response()->json([
                'response' => [
                    'code' => 400,
                    'api_status' => 0,
                    'message' => "Account Number is already in use.",
                ]
            ], 400);
        }
        $account->status = Accounts::STATUS_PROVIDE;
        $account->save();
        return response()->json([
            'response' => [
                'code' => 200,
                'api_status' => 1,
                'message' => "Provided",
            ]
        ], 200);
    }

    public function copyAccount(Request $request)
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

        $src_account_body = $request->source_account;
        $src_account_number = $src_account_body['account_number'];
        $src_broker = $src_account_body['broker'];
        $src_account = Accounts::where(['account_number' => $src_account_number, 'broker' => $src_broker])->first();

        if (!$src_account) {
            return response()->json([
                'response' => [
                    'code' => 400,
                    'api_status' => 0,
                    'message' => "Source Account Number doesn't exist.",
                ]
            ], 400);
        }

        if ($src_account->status != Accounts::STATUS_PROVIDE) {
            return response()->json([
                'response' => [
                    'code' => 400,
                    'api_status' => 0,
                    'message' => "Source Account Number is not for provide.",
                ]
            ], 400);
        }

        $account_body = $request->account;
        $account_number = $account_body['account_number'];
        $broker = $account_body['broker'];
        $account = Accounts::where(['account_number' => $account_number, 'broker' => $broker])->first();

        if (!$account) {
            return response()->json([
                'response' => [
                    'code' => 400,
                    'api_status' => 0,
                    'message' => "Account Number doesn't exist.",
                ]
            ], 400);
        }

        if ($account->status == Accounts::STATUS_PROVIDE) {
            return response()->json([
                'response' => [
                    'code' => 400,
                    'api_status' => 0,
                    'message' => "Account Number is already in use.",
                ]
            ], 400);
        }


        $cid = Copy::where(['master_id' => $src_account->id, 'slave_id' => $account->id])->exists();
        if ($cid) {
            return response()->json([
                'response' => [
                    'code' => 400,
                    'api_status' => 0,
                    'message' => "Account Number is already copying source.",
                ]
            ], 400);
        }

        Copy::create(['master_id' => $src_account->id, 'slave_id' => $account->id]);

        $account->status = Accounts::STATUS_COPY;
        $account->save();

        return response()->json([
            'response' => [
                'code' => 200,
                'api_status' => 1,
                'message' => "Copied",
            ]
        ], 200);
    }

    public function getAvailableSignal(Request $request)
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
        $status = Accounts::STATUS_PROVIDE;
        $query = "SELECT
                tbl_account.id,
                tbl_account.account_number,
                tbl_account.broker,
                sources.openTime,
                IFNULL( sources.signal_number, 0 ) AS signal_number,
                IFNULL( copiers.copier_number, 0 ) AS copier_number 
                FROM
                tbl_account
                LEFT JOIN (
                SELECT
                    tbl_source.account_id,
                    MIN( tbl_source.openTime ) AS openTime,
                    COUNT( 1 ) AS signal_number 
                FROM
                    tbl_source
                    INNER JOIN tbl_user_account ON tbl_source.account_id = tbl_user_account.account_id 
                WHERE
                    tbl_user_account.user_id != $user_id 
                GROUP BY
                    tbl_source.account_id
                ) AS sources ON tbl_account.id = sources.account_id
                LEFT JOIN ( SELECT COUNT( 1 ) AS copier_number, tbl_copy.master_id FROM tbl_copy GROUP BY tbl_copy.master_id ) AS copiers ON copiers.master_id = tbl_account.id 
                INNER JOIN tbl_user_account ON tbl_account.id = tbl_user_account.account_id 
                WHERE
                tbl_account.`status` = '$status' AND 
                tbl_user_account.user_id != $user_id
                ";

        $total = DB::select("SELECT COUNT(1) as total from 
                            ( " . $query . ") as result");
        $total = $total[0]->total;
        $availableSignal = DB::select($query . "LIMIT $offset, $perPage");
        return response()->json([
            'response' => [
                'code' => 200,
                'api_status' => 1,
                'total' => $total,
                'page' => $page,
                'perPage' => $perPage,
                'availableSignal' => $availableSignal,
            ]
        ], 200);
    }

    public function getCopyAccount(Request $request)
    {
        $page = $request->get('page', 1);
        $page = intval($page);
        $perPage = $request->get('perPage', 10);
        $perPage = intval($perPage);
        $offset = ($page - 1) * $perPage;
        $account = $request->get('account');

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
                tbl_copy.id,
                slave_account.broker AS broker,
                slave_account.account_number AS account_number,
                master_user.`name` AS provider_name,
                master_account.broker AS src_broker,
                master_account.account_number AS src_account_number,
                tbl_copy.created_at,
                IFNULL( sources.signal_number, 0 ) AS signal_number 
                FROM
                tbl_copy
                INNER JOIN tbl_account AS master_account ON master_account.id = tbl_copy.master_id
                INNER JOIN tbl_user_account AS master_user_account ON master_user_account.account_id = tbl_copy.master_id
                INNER JOIN tbl_users AS master_user ON master_user_account.user_id = master_user.id
                INNER JOIN tbl_account AS slave_account ON slave_account.id = tbl_copy.slave_id
                INNER JOIN tbl_user_account AS slave_user_account ON slave_user_account.account_id = tbl_copy.slave_id
                LEFT JOIN ( SELECT COUNT( 1 ) AS signal_number, account_id FROM tbl_source WHERE deleted_at IS NULL GROUP BY account_id ) AS sources ON sources.account_id = tbl_copy.master_id
                WHERE slave_user_account.user_id = $user_id
                AND tbl_copy.deleted_at IS NULL
                ";

        if ($account) {
            $query .= "AND slave_account.account_number = $account ";
        }

        $total = DB::select("SELECT COUNT(1) as total from 
                            ( " . $query . ") as result");
        $total = $total[0]->total;
        $copySignal = DB::select($query . "LIMIT $offset, $perPage");
        return response()->json([
            'response' => [
                'code' => 200,
                'api_status' => 1,
                'total' => $total,
                'page' => $page,
                'perPage' => $perPage,
                'copySignal' => $copySignal,
            ]
        ], 200);
    }

    public function deleteProvideAccount(Request $request, $id)
    {
        $me = Auth::user();
        if (!$me) {
            return response()->json([
                'response' => [
                    'code' => 400,
                    'api_status' => 0,
                    'message' => "User not found.",
                ]
            ], 400);
        }

        $account = Accounts::where('id', $id)->first();
        if (!$account) {
            return response()->json([
                'response' => [
                    'code' => 400,
                    'api_status' => 0,
                    'message' => "Account doesn't exist.",
                ]
            ], 400);
        }

        $account->status = Accounts::STATUS_NONE;
        $account->save();

        Source::where('account_id', $account->id)->delete();
        Copy::where('master_id', $account->id)->delete();

        return response()->json([
            'response' => [
                'code' => 200,
                'api_status' => 1,
                'message' => "Deleted.",
            ]
        ], 200);
    }

    public function deleteCopyAccount(Request $request, $id)
    {
        $me = Auth::user();
        if (!$me) {
            return response()->json([
                'response' => [
                    'code' => 400,
                    'api_status' => 0,
                    'message' => "User not found.",
                ]
            ], 400);
        }

        $account = Copy::where('id', $id)->first();
        if (!$account) {
            return response()->json([
                'response' => [
                    'code' => 400,
                    'api_status' => 0,
                    'message' => "Copy information doesn't exist.",
                ]
            ], 400);
        }

        $account->delete();
        return response()->json([
            'response' => [
                'code' => 200,
                'api_status' => 1,
                'message' => "Deleted.",
            ]
        ], 200);
    }
}
