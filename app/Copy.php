<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Copy extends Model
{
    protected $table = "tbl_copy";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'master_id', 'slave_id',
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
        'trailingStep',
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
    ];

    public function master()
    {
        return $this->belongsTo(Accounts::class, 'master_id');
    }

    public function slave()
    {
        return $this->belongsTo(Accounts::class, 'slave_id');
    }
}
