<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Accounts;
use App\UserAccounts;
use App\Source;
use App\Copy;
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
        $account_id = null;
        $account_status = Accounts::STATUS_NONE;
        for ($i = 0; $i < count($user_accounts); $i++) {
            $account = $user_accounts[$i]->account;
            if ($account['account_number'] == $input['account_number'] && $account['broker'] == $input['broker']) {
                $account_id = $account['id'];
                $account_status = $account['status'];
                break;
            }
        }
        if (!$account_id) {
            return response()->json([
                'response' => [
                    'code' => 400,
                    'api_status' => 0,
                    'message' => "Account Number doesn't match.",
                ]
            ], 400);
        }
        if ($account_status != Accounts::STATUS_PROVIDE) {
            return response()->json([
                'response' => [
                    'code' => 400,
                    'api_status' => 0,
                    'message' => "Account isn't for provide.",
                ]
            ], 400);
        }

        $input['account_id'] = $account_id;
        unset($input['account_number']);
        unset($input['broker']);

        Source::create($input);
        return response()->json([
            'response' => [
                'code' => 201,
                'api_status' => 1,
                'message' => "success.",
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

    public function getProvideSourceDetail(Request $request)
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

        $account_number = $request->get('account_number');
        $broker = $request->get('broker');
        if (!$account_number || !$broker) {
            return response()->json([
                'response' => [
                    'code' => 400,
                    'api_status' => 0,
                    'message' => "Account number and Broker are required.",
                ]
            ], 400);
        }
        $account = Accounts::where(['account_number' => $account_number, 'broker' => $broker])->first();
        if (!$account) {
            return response()->json([
                'response' => [
                    'code' => 400,
                    'api_status' => 0,
                    'message' => "Account doesn't exist.",
                ]
            ], 400);
        }

        if ($account->status == Accounts::STATUS_NONE) {
            return response()->json([
                'response' => [
                    'code' => 400,
                    'api_status' => 0,
                    'message' => "Signal doesn't exist.",
                ]
            ], 400);
        }

        $account_id = $account->id;
        $user_account = $account->user_account->first();
        if (!$user_account) {
            return response()->json([
                'response' => [
                    'code' => 400,
                    'api_status' => 0,
                    'message' => "Account doesn't exist.",
                ]
            ], 400);
        }

        $user = $user_account->user->first();
        if (!$user) {
            return response()->json([
                'response' => [
                    'code' => 400,
                    'api_status' => 0,
                    'message' => "User doesn't exist.",
                ]
            ], 400);
        }

        $detail = DB::select("SELECT * FROM
                            (SELECT COUNT( 1 ) AS copier_number FROM tbl_copy WHERE master_id = $account_id) AS copier,
                            (SELECT MIN(openTime) as openTime FROM tbl_source WHERE account_id = $account_id) AS source");
        $detail = $detail[0];

        $page = $request->get('page', 1);
        $page = intval($page);
        $perPage = $request->get('perPage', 10);
        $perPage = intval($perPage);
        $offset = ($page - 1) * $perPage;

        $query = "SELECT
                tbl_source.account_id,
                tbl_source.symbol,
                tbl_source.openTime,
                tbl_source.lots,
                tbl_source.type,
                tbl_source.openPrice,
                tbl_source.takeProfitPrice,
                tbl_source.stopLossPrice,
                tbl_source.ticket 
                FROM
                tbl_source
                WHERE
                tbl_source.account_id = $account_id 
                ORDER BY openTime DESC ";
        $total = DB::select("SELECT COUNT(1) as total from 
                            ( " . $query . ") as result");
        $total = $total[0]->total;
        $provideSignalDetail = DB::select($query . "LIMIT $offset, $perPage");
        return response()->json([
            'response' => [
                'code' => 200,
                'api_status' => 1,
                'information' => [
                    'provider' => $user->name,
                    'account_number' => $account->account_number,
                    'broker' => $account->broker,
                    'status' => $account->status,
                    'openTime' => $detail->openTime,
                    'copier_number' => $detail->copier_number,
                ],
                'total' => $total,
                'page' => $page,
                'perPage' => $perPage,
                'signalDetail' => $provideSignalDetail,
            ]
        ], 200);
    }

    public function deleteProvideSource(Request $request, $id)
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
        return response()->json([
            'response' => [
                'code' => 200,
                'api_status' => 1,
                'message' => "Deleted.",
            ]
        ], 200);
    }

    public function deleteCopySource(Request $request, $id)
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

    public function getAvailableSource(Request $request)
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

    public function getCopySource(Request $request)
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
}
