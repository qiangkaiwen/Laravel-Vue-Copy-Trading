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
            $table->integer('isOpenTradesInDestination')->default(0);
            $table->integer('isOpenPendingOrdersInDestination')->default(0);
            $table->integer('copyDirection')->default(2);
            $table->integer('isCopyTPToDestination')->default(1);
            $table->double('overrideDestinationTP')->default(0);
            $table->integer('isCopySLToDestination')->default(1);
            $table->double('overrideDestinationSL')->default(0);
            $table->integer('isCloseTradesInDestination')->default(1);
            $table->integer('isDeletePendingOrdersInDestination')->default(1);
            $table->integer('isInvertTradeCopyDirection')->default(0);
            $table->integer('lotSizeType')->default(0);
            $table->double('lotSizeRisk')->default(0.5);
            $table->double('lotSizeMultipleOfSource')->default(1);
            $table->double('fixedLotSize')->default(0.01);
            $table->double('minimumLotSize')->default(0.01);
            $table->double('maximumLotSize')->default(100);
            $table->integer('maximumOrdersInDestination')->default(0);
            $table->integer('maximumOpenPriceSlippage')->default(0);
            $table->integer('maximumOpenPriceDeviationToCopy')->default(0);
            $table->integer('maximumTimeAfterSourceOpen')->default(30);
            $table->double('dailyProfitToStop')->default(0);
            $table->integer('isCloseTradesWhenDailyProfitIsReached')->default(0);
            $table->double('dailyLossToStop')->default(0);
            $table->integer('isCloseTradesWhenDailyLossIsReached')->default(0);
            $table->integer('isSendAlertForNewTrades')->default(0);
            $table->integer('isSendAlertForClosedTrades')->default(0);
            $table->integer('isSendAlertForPartiallyClosedTrades')->default(0);
            $table->integer('isSendAlertForDailyProfitReached')->default(0);
            $table->integer('isSendAlertForDailyLossReached')->default(0);
            $table->integer('isAlertSound')->default(0);
            $table->integer('isAlertPopup')->default(0);
            $table->integer('isAlertEmail')->default(0);
            $table->integer('isAlertMobile')->default(0);
            $table->integer('brokerServerSummerTimeZone')->default(1);
            $table->integer('brokerServerWinterTimeZone')->default(2);
            $table->string('brokerSymbolPrefix')->nullable()->default('');
            $table->string('brokerSymbolSuffix')->nullable()->default('');
            $table->string('messageColor')->default("#000000");
            $table->integer('applyTrailingStop')->default(0);
            $table->integer('profitTrailing')->default(1);
            $table->integer('trailingStop')->default(8);
            $table->integer('trailingStep')->default(2);
            $table->integer('applyOnOffTime')->default(0);
            $table->string('onTime')->default("02:00");
            $table->string('offTime')->default("17:30");
            $table->integer('applyDestinationPair')->default(0);
            $table->string('destinationPair')->default('EURUSD');
            $table->integer('applyMessageLog')->default(1);
            $table->integer('applyOrderCloseBy')->default(0);
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
