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
use Validator;

class SourceController extends Controller
{
    public function addSource(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'account_number' => 'required',
            'broker' => 'required',
            'symbol' => 'required',
            'lots' => 'required',
            'ticket' => 'required',
            'direction' => 'required',
            'type' => 'required',
            'magic' => 'required',
            'openPrice' => 'required',
            'stopLossPrice' => 'required',
            'takeProfitPrice' => 'required',
            'openTime' => 'required',
            'openTimeGMT' => 'required',
            'expiration' => 'required',
            'expirationGMT' => 'required',
            'sourceTicket' => 'required',
            'sourceLots' => 'required',
            'sourceType' => 'required',
            'originalTicket' => 'required',
            'originalLots' => 'required',
            'sourceOriginalTicket' => 'required',
            'sourceOriginalLots' => 'required',
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

        $account_number = $input['account_number'];
        $broker = $input['broker'];
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

        $user_account = $account->user_account->first();
        if ($user_account['user_id'] != $user['id']) {
            return response()->json([
                'response' => [
                    'code' => 400,
                    'api_status' => 0,
                    'message' => "Account is not yours.",
                ]
            ], 400);
        }
        $account_status = $account['status'];

        if ($account_status != Accounts::STATUS_PROVIDE) {
            return response()->json([
                'response' => [
                    'code' => 400,
                    'api_status' => 0,
                    'message' => "Account isn't for provide.",
                ]
            ], 400);
        }

        $input['account_id'] = $account['id'];
        unset($input['account_number']);
        unset($input['broker']);

        //Source::where('account_id', $account['id'])->delete();
        $source = Source::where([
            'account_id' => $account['id'],
            'symbol' => $input['symbol'],
            'ticket' => $input['ticket'],
            'magic' => $input['magic'],
        ])->delete();
        Source::create($input);

        return response()->json([
            'response' => [
                'code' => 201,
                'api_status' => 1,
                'message' => "success.",
            ]
        ], 201);
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
                tbl_source.ticket,
                tbl_source.created_at 
                FROM
                tbl_source
                WHERE
                tbl_source.account_id = $account_id 
                ORDER BY created_at DESC ";
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

    public function getProvideSourceDetailWithTime(Request $request)
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
        $delaytime = $request->get('delaytime');
        if (!$account_number || !$broker || !$delaytime) {
            return response()->json([
                'response' => [
                    'code' => 400,
                    'api_status' => 0,
                    'message' => "Account number, Broker and Delay time are required.",
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

        if ($account->status == Accounts::STATUS_PROVIDE) {
            $account_id = $account->id;
            $user_account = $account->user_account->first();
            //check account
            if (!$user_account) {
                return response()->json([
                    'response' => [
                        'code' => 400,
                        'api_status' => 0,
                        'message' => "Account doesn't exist.",
                    ]
                ], 400);
            }
            //check user
            $me = Auth::user();
            if ($me['id'] != $user_account['user_id']) {
                return response()->json([
                    'response' => [
                        'code' => 400,
                        'api_status' => 0,
                        'message' => "Account is not yours.",
                    ]
                ], 400);
            }

            $date = date_create();
            $date_stamp = date_timestamp_get($date);
            $date_stamp -= $delaytime;
            $date_str = gmdate("Y-m-d H:i:s", $date_stamp);

            $provideSignalDetailWithTime = Source::where([
                ['account_id', '=', $account_id],
                ['created_at', '>=', $date_str]
            ])
                ->orderBy('created_at', 'DESC')
                ->get();

            return response()->json([
                'response' => [
                    'code' => 200,
                    'api_status' => 1,
                    'signalDetail' => $provideSignalDetailWithTime,
                ]
            ], 200);
        } else if ($account->status == Accounts::STATUS_COPY) {
            $account_id = $account->id;
            $user_account = $account->user_account->first();
            //check account
            if (!$user_account) {
                return response()->json([
                    'response' => [
                        'code' => 400,
                        'api_status' => 0,
                        'message' => "Account doesn't exist.",
                    ]
                ], 400);
            }

            //check user
            $me = Auth::user();
            if ($me['id'] != $user_account['user_id']) {
                return response()->json([
                    'response' => [
                        'code' => 400,
                        'api_status' => 0,
                        'message' => "Account is not yours.",
                    ]
                ], 400);
            }

            $copies = Copy::where('slave_id', $account_id)->get('master_id');
            $account_ids = array();
            for ($i = 0; $i < count($copies); $i++) {
                $account_ids[] = $copies[$i]['master_id'];
            }

            $date = date_create();
            $date_stamp = date_timestamp_get($date);
            $date_stamp -= $delaytime;
            $date_str = gmdate("Y-m-d H:i:s", $date_stamp);

            $copySignalDetailWithTime = Source::where([
                ['created_at', '>=', $date_str]
            ])
                ->whereIn('account_id', $account_ids)
                ->orderBy('created_at', 'DESC')
                ->get();

            return response()->json([
                'response' => [
                    'code' => 200,
                    'api_status' => 1,
                    'signalDetail' => $copySignalDetailWithTime,
                ]
            ], 200);
        } else {
            return response()->json([
                'response' => [
                    'code' => 400,
                    'api_status' => 0,
                    'message' => "Signal doesn't exist.",
                ]
            ], 400);
        }
    }

    public function deleteSources(Request $request)
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

        $user_account = $account->user_account->first();
        if ($user_account['user_id'] != $me['id']) {
            return response()->json([
                'response' => [
                    'code' => 400,
                    'api_status' => 0,
                    'message' => "Account is not yours.",
                ]
            ], 400);
        }

        Source::where('account_id', $account['id'])->delete();
        return response()->json([
            'response' => [
                'code' => 200,
                'api_status' => 1,
                'message' => "success.",
            ]
        ], 200);
    }
}
