<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Accounts;
use App\UserAccounts;
use App\Copy;
use App\Source;
use App\Copy_Settings;
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
        $user_account = $account->user_account->first();
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

    public function checkAccount(Request $request)
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
        $user_account = $account->user_account->first();
        if (!$user_account) {
            return response()->json([
                'response' => [
                    'code' => 400,
                    'api_status' => 0,
                    'message' => "Account Number is not correct.",
                ]
            ], 400);
        }
        if ($user_account->user_id != $user->id) {
            return response()->json([
                'response' => [
                    'code' => 400,
                    'api_status' => 0,
                    'message' => "Account Number is not yours.",
                ]
            ], 400);
        }
        return response()->json([
            'response' => [
                'code' => 200,
                'api_status' => 1,
                'message' => $account->status,
            ]
        ], 200);
    }

    public function getCopySetting(Request $request)
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

        $account_number = $request['account_number'];
        $broker = $request['broker'];
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

        if ($account->status != Accounts::STATUS_COPY) {
            return response()->json([
                'response' => [
                    'code' => 400,
                    'api_status' => 0,
                    'message' => "Account Number is not for copy.",
                ]
            ], 400);
        }

        $copy_setting = Copy_Settings::where(['account_id' => $account['id']])->first();
        if (!$copy_setting) {
            return response()->json([
                'response' => [
                    'code' => 200,
                    'api_status' => 1,
                    'setting' => [
                        "isOpenTradesInDestination" => 1,
                        "isOpenPendingOrdersInDestination" => 1,
                        "copyDirection" => 2,
                        "isCopyTPToDestination" => 1,
                        "overrideDestinationTP" => 0,
                        "isCopySLToDestination" => 1,
                        "overrideDestinationSL" => 0,
                        "isCloseTradesInDestination" => 1,
                        "isDeletePendingOrdersInDestination" => 1,
                        "isInvertTradeCopyDirection" => 0,
                        'isCopyOpenTrades' => 0,
                        'maxTime' => 10,
                        'copyDelay' => 60,
                        "lotSizeType" => 1,
                        "lotSizeRisk" => 0.5,
                        "lotSizeMultipleOfSource" => 1,
                        "fixedLotSize" => 0.01,
                        "minimumLotSize" => 0.01,
                        "maximumLotSize" => 100,
                        "maximumOrdersInDestination" => 0,
                        "maximumOpenPriceSlippage" => 0,
                        "maximumOpenPriceDeviationToCopy" => 0,
                        "maximumTimeAfterSourceOpen" => 0,
                        "dailyProfitToStop" => 0,
                        "isCloseTradesWhenDailyProfitIsReached" => 0,
                        "dailyLossToStop" => 0,
                        "isCloseTradesWhenDailyLossIsReached" => 0,
                        "isSendAlertForNewTrades" => 0,
                        "isSendAlertForClosedTrades" => 0,
                        "isSendAlertForPartiallyClosedTrades" => 0,
                        "isSendAlertForDailyProfitReached" => 0,
                        "isSendAlertForDailyLossReached" => 0,
                        "isAlertSound" => 0,
                        "isAlertPopup" => 0,
                        "isAlertEmail" => 0,
                        "isAlertMobile" => 0,
                        "brokerServerSummerTimeZone" => 1,
                        "brokerServerWinterTimeZone" => 2,
                        "brokerSymbolPrefix" => null,
                        "brokerSymbolSuffix" => null,
                        "messageColor" => "#000000",
                        "applyTrailingStop" => 0,
                        "profitTrailing" => 1,
                        "trailingStop" => 8,
                        "trailingProfit" => 2,
                        'applyOnOffTime' => 0,
                        "onTime" => "02:00",
                        "onHour" => "02",
                        "onMinute" => "00",
                        "offHour" => "17",
                        "offMinute" => "30",
                        "offTime" => "17:30",
                        "applyDestinationPair" => 0,
                        "destinationPair" => "EURUSD",
                        "applyMessageLog" => 1,
                        "applyOrderCloseBy" => 0
                    ],
                ]
            ]);
        }

        unset($copy_setting['id']);
        unset($copy_setting['account_id']);
        unset($copy_setting['master_id']);
        unset($copy_setting['slave_id']);
        unset($copy_setting['created_at']);
        unset($copy_setting['updated_at']);

        $onTime = explode(":", $copy_setting['onTime']);
        $copy_setting['onHour'] = $onTime[0];
        $copy_setting['onMinute'] = $onTime[1];

        $offTime = explode(":", $copy_setting['offTime']);
        $copy_setting['offHour'] = $offTime[0];
        $copy_setting['offMinute'] = $offTime[1];

        return response()->json([
            'response' => [
                'code' => 200,
                'api_status' => 1,
                'setting' => $copy_setting,
            ]
        ]);
    }

    public function saveCopySetting(Request $request)
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

        $account_number = $request['account_number'];
        $broker = $request['broker'];
        $setting = $request->all();
        unset($setting['account_number']);
        unset($setting['broker']);
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

        if ($account->status != Accounts::STATUS_COPY) {
            return response()->json([
                'response' => [
                    'code' => 400,
                    'api_status' => 0,
                    'message' => "Account Number is not for copy.",
                ]
            ], 400);
        }

        $copy_setting = Copy_Settings::where(['account_id' => $account['id']])->first();
        if ($copy_setting) {
            $copy_setting->update($setting);
        } else {
            $setting['account_id'] = $account['id'];
            Copy_Settings::create($setting);
        }

        return response()->json([
            'response' => [
                'code' => 200,
                'api_status' => 1,
                'message' => "Setting Saved.",
            ]
        ]);
    }

    public function getProvideSetting(Request $request)
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

        $account_number = $request['account_number'];
        $broker = $request['broker'];
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

        if ($account->status != Accounts::STATUS_PROVIDE) {
            return response()->json([
                'response' => [
                    'code' => 400,
                    'api_status' => 0,
                    'message' => "Account Number is not for provide.",
                ]
            ], 400);
        }

        $provide_setting = Copy_Settings::where(['account_id' => $account['id']])
            ->get([
                "brokerServerSummerTimeZone",
                "brokerServerWinterTimeZone",
                "brokerSymbolPrefix",
                "brokerSymbolSuffix",
                "messageColor",
                "applyTrailingStop",
                "profitTrailing",
                "trailingStop",
                "trailingProfit",
                'applyOnOffTime',
                "onTime",
                "offTime",
                "applyMessageLog",
            ])
            ->first();
        if (!$provide_setting) {
            return response()->json([
                'response' => [
                    'code' => 200,
                    'api_status' => 1,
                    'setting' => [
                        "brokerServerSummerTimeZone" => 1,
                        "brokerServerWinterTimeZone" => 2,
                        "brokerSymbolPrefix" => null,
                        "brokerSymbolSuffix" => null,
                        "messageColor" => "#000000",
                        "applyTrailingStop" => 0,
                        "profitTrailing" => 1,
                        "trailingStop" => 8,
                        "trailingProfit" => 2,
                        'applyOnOffTime' => 0,
                        "onTime" => "02:00",
                        "onHour" => "02",
                        "onMinute" => "00",
                        "offHour" => "17",
                        "offMinute" => "30",
                        "offTime" => "17:30",
                        "applyMessageLog" => 1,
                    ],
                ]
            ]);
        }

        $onTime = explode(":", $provide_setting['onTime']);
        $provide_setting['onHour'] = $onTime[0];
        $provide_setting['onMinute'] = $onTime[1];

        $offTime = explode(":", $provide_setting['offTime']);
        $provide_setting['offHour'] = $offTime[0];
        $provide_setting['offMinute'] = $offTime[1];

        return response()->json([
            'response' => [
                'code' => 200,
                'api_status' => 1,
                'setting' => $provide_setting,
            ]
        ]);
    }

    public function saveProvideSetting(Request $request)
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

        $account_number = $request['account_number'];
        $broker = $request['broker'];
        $setting = $request->all();
        unset($setting['account_number']);
        unset($setting['broker']);
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

        if ($account->status != Accounts::STATUS_PROVIDE) {
            return response()->json([
                'response' => [
                    'code' => 400,
                    'api_status' => 0,
                    'message' => "Account Number is not for provide.",
                ]
            ], 400);
        }

        $provide_setting = Copy_Settings::where(['account_id' => $account['id']])->first();
        if ($provide_setting) {
            $provide_setting->update($setting);
        } else {
            $setting['account_id'] = $account['id'];
            Copy_Settings::create($setting);
        }

        return response()->json([
            'response' => [
                'code' => 200,
                'api_status' => 1,
                'message' => "Setting Saved.",
            ]
        ]);
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

        $accounts = $request['accounts'];
        $success = 0;
        $total = 0;
        foreach ($accounts as $index => $account_body) {
            $total++;
            $account_number = $account_body['account_number'];
            $broker = $account_body['broker'];
            $account = Accounts::where(['account_number' => $account_number, 'broker' => $broker])->first();

            if (!$account) {
                continue;
            }

            if ($account->status == Accounts::STATUS_PROVIDE) {
                continue;
            }

            $cid = Copy::where(['master_id' => $src_account->id, 'slave_id' => $account->id])->exists();
            if ($cid) {
                continue;
            }

            Copy::create(['master_id' => $src_account->id, 'slave_id' => $account->id]);

            $account->status = Accounts::STATUS_COPY;
            $account->save();
            $success++;
        }

        return response()->json([
            'response' => [
                'code' => 200,
                'api_status' => 1,
                'message' => "Copied $success / $total accounts",
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
                tbl_users.name AS `provider`,
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
                INNER JOIN tbl_users ON tbl_users.id = tbl_user_account.user_id
                WHERE
                tbl_account.`status` = '$status' 
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
                LEFT JOIN ( SELECT COUNT( 1 ) AS signal_number, account_id FROM tbl_source GROUP BY account_id ) AS sources ON sources.account_id = tbl_copy.master_id
                WHERE slave_user_account.user_id = $user_id
                ";

        if ($account) {
            $query .= "AND slave_account.account_number = $account ";
        }

        $total = DB::select("SELECT COUNT(1) as total from 
                            ( " . $query . ") as result");
        $total = $total[0]->total;
        $copyingSignal = DB::select($query . "LIMIT $offset, $perPage");
        return response()->json([
            'response' => [
                'code' => 200,
                'api_status' => 1,
                'total' => $total,
                'page' => $page,
                'perPage' => $perPage,
                'copyingSignal' => $copyingSignal,
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

        $copy = Copy::where('id', $id)->first();
        if (!$copy) {
            return response()->json([
                'response' => [
                    'code' => 400,
                    'api_status' => 0,
                    'message' => "Copy information doesn't exist.",
                ]
            ], 400);
        }

        $slave = $copy->slave;
        $copy->delete();

        $masters = $slave->masters;
        if (count($masters) == 0) {
            $slave['status'] = Accounts::STATUS_NONE;
            $slave->save();
            Copy_Settings::where('account_id', $slave['id'])->delete();
        }

        return response()->json([
            'response' => [
                'code' => 200,
                'api_status' => 1,
                'message' => "Deleted.",
            ]
        ], 200);
    }
}
