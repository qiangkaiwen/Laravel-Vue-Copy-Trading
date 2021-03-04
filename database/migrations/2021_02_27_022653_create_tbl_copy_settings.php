<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblCopySettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_copy_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('account_id');
            $table->integer('isOpenTradesInDestination')->nullable()->default(1);
            $table->integer('isOpenPendingOrdersInDestination')->nullable()->default(1);
            $table->integer('copyDirection')->nullable()->default(2);
            $table->integer('isCopyTPToDestination')->nullable()->default(1);
            $table->double('overrideDestinationTP')->nullable()->default(0);
            $table->integer('isCopySLToDestination')->nullable()->default(1);
            $table->double('overrideDestinationSL')->nullable()->default(0);
            $table->integer('isCloseTradesInDestination')->nullable()->default(1);
            $table->integer('isDeletePendingOrdersInDestination')->nullable()->default(1);
            $table->integer('isInvertTradeCopyDirection')->nullable()->default(0);
            $table->integer('isCopyOpenTrades')->nullable()->default(0);
            $table->double('maxTime')->nullable()->default(10);
            $table->double('copyDelay')->nullable()->default(60);
            $table->integer('lotSizeType')->nullable()->default(0);
            $table->double('lotSizeRisk')->nullable()->default(0.5);
            $table->double('lotSizeMultipleOfSource')->nullable()->default(1);
            $table->double('fixedLotSize')->nullable()->default(0.01);
            $table->double('minimumLotSize')->nullable()->default(0.01);
            $table->double('maximumLotSize')->nullable()->default(100);
            $table->integer('maximumOrdersInDestination')->nullable()->default(0);
            $table->integer('maximumOpenPriceSlippage')->nullable()->default(0);
            $table->integer('maximumOpenPriceDeviationToCopy')->nullable()->default(0);
            $table->integer('maximumTimeAfterSourceOpen')->nullable()->default(0);
            $table->double('dailyProfitToStop')->nullable()->default(0);
            $table->integer('isCloseTradesWhenDailyProfitIsReached')->nullable()->default(0);
            $table->double('dailyLossToStop')->nullable()->default(0);
            $table->integer('isCloseTradesWhenDailyLossIsReached')->nullable()->default(0);
            $table->integer('isSendAlertForNewTrades')->nullable()->default(0);
            $table->integer('isSendAlertForClosedTrades')->nullable()->default(0);
            $table->integer('isSendAlertForPartiallyClosedTrades')->nullable()->default(0);
            $table->integer('isSendAlertForDailyProfitReached')->nullable()->default(0);
            $table->integer('isSendAlertForDailyLossReached')->nullable()->default(0);
            $table->integer('isAlertSound')->nullable()->default(0);
            $table->integer('isAlertPopup')->nullable()->default(0);
            $table->integer('isAlertEmail')->nullable()->default(0);
            $table->integer('isAlertMobile')->nullable()->default(0);
            $table->integer('brokerServerSummerTimeZone')->nullable()->default(1);
            $table->integer('brokerServerWinterTimeZone')->nullable()->default(2);
            $table->string('brokerSymbolPrefix')->nullable()->default('');
            $table->string('brokerSymbolSuffix')->nullable()->default('');
            $table->string('messageColor')->nullable()->default("#000000");
            $table->integer('applyTrailingStop')->nullable()->default(0);
            $table->integer('profitTrailing')->nullable()->default(1);
            $table->integer('trailingStop')->nullable()->default(8);
            $table->integer('trailingStep')->nullable()->default(2);
            $table->integer('applyOnOffTime')->nullable()->default(0);
            $table->string('onTime')->nullable()->default("02:00");
            $table->string('offTime')->nullable()->default("17:30");
            $table->integer('applyDestinationPair')->nullable()->default(0);
            $table->string('destinationPair')->nullable()->default('EURUSD');
            $table->integer('applyMessageLog')->nullable()->default(1);
            $table->integer('applyOrderCloseBy')->nullable()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_copy_settings');
    }
}
