<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Copy_Settings extends Model
{
    protected $table = "tbl_copy_settings";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_id',
        'isOpenTradesInDestination',
        'isOpenPendingOrdersInDestination',
        'copyDirection',
        'isCopyTPToDestination',
        'overrideDestinationTP',
        'isCopySLToDestination',
        'overrideDestinationSL',
        'isCloseTradesInDestination',
        'isDeletePendingOrdersInDestination',
        'isInvertTradeCopyDirection',
        'isCopyOpenTrades',
        'maxTime',
        'copyDelay',
        'lotSizeType',
        'lotSizeRisk',
        'lotSizeMultipleOfSource',
        'fixedLotSize',
        'minimumLotSize',
        'maximumLotSize',
        'maximumOrdersInDestination',
        'maximumOpenPriceSlippage',
        'maximumOpenPriceDeviationToCopy',
        'maximumTimeAfterSourceOpen',
        'dailyProfitToStop',
        'isCloseTradesWhenDailyProfitIsReached',
        'dailyLossToStop',
        'isCloseTradesWhenDailyLossIsReached',
        'isSendAlertForNewTrades',
        'isSendAlertForClosedTrades',
        'isSendAlertForPartiallyClosedTrades',
        'isSendAlertForDailyProfitReached',
        'isSendAlertForDailyLossReached',
        'isAlertSound',
        'isAlertPopup',
        'isAlertEmail',
        'isAlertMobile',
        'brokerServerSummerTimeZone',
        'brokerServerWinterTimeZone',
        'brokerSymbolPrefix',
        'brokerSymbolSuffix',
        'messageColor',
        'applyTrailingStop',
        'profitTrailing',
        'trailingStop',
        'trailingProfit',
        'applyOnOffTime',
        'onTime',
        'offTime',
        'applyDestinationPair',
        'destinationPair',
        'applyMessageLog',
        'applyOrderCloseBy',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'isOpenTradesInDestination' => 'integer',
        'isOpenPendingOrdersInDestination' => 'integer',
        'isCopyTPToDestination' => 'integer',
        'isCopySLToDestination' => 'integer',
        'isCloseTradesInDestination' => 'integer',
        'isDeletePendingOrdersInDestination' => 'integer',
        'isInvertTradeCopyDirection' => 'integer',
        'isCloseTradesWhenDailyProfitIsReached' => 'integer',
        'isCloseTradesWhenDailyLossIsReached' => 'integer',
        'isSendAlertForNewTrades' => 'integer',
        'isSendAlertForClosedTrades' => 'integer',
        'isSendAlertForPartiallyClosedTrades' => 'integer',
        'isSendAlertForDailyProfitReached' => 'integer',
        'isSendAlertForDailyLossReached' => 'integer',
        'isAlertSound' => 'integer',
        'isAlertPopup' => 'integer',
        'isAlertEmail' => 'integer',
        'isAlertMobile' => 'integer',
        'applyTrailingStop' => 'integer',
        'profitTrailing' => 'integer',
        'applyDestinationPair' => 'integer',
        'applyMessageLog' => 'integer',
        'applyOrderCloseBy' => 'integer',
        'brokerSymbolPrefix' => 'string',
        'brokerSymbolSuffix' => 'string',
        'applyOnOffTime' => 'integer',
        'copyDelay' => 'integer',
        'copyDirection' => 'integer',
        'dailyLossToStop' => 'double',
        'dailyProfitToStop' => 'double',
        'fixedLotSize' => 'double',
        'isCopyOpenTrades' => 'integer',
        'lotSizeMultipleOfSource' => 'double',
        'lotSizeRisk' => 'double',
        'lotSizeType' => 'integer',
        'maxTime' => 'double',
        'maximumLotSize' => 'double',
        'maximumOpenPriceDeviationToCopy' => 'integer',
        'maximumOpenPriceSlippage' => 'integer',
        'maximumOrdersInDestination' => 'integer',
        'maximumTimeAfterSourceOpen' => 'integer',
        'minimumLotSize' => 'double',
        'offHour' => 'integer',
        'offMinute' => 'integer',
        'onHour' => 'integer',
        'onMinute' => 'integer',
        'overrideDestinationSL' => 'double',
        'overrideDestinationTP' => 'double',
        'trailingStop' => 'integer',
        'trailingProfit' => 'integer'
    ];

    public function account()
    {
        return $this->belongsTo(Accounts::class, 'account_id');
    }
}
