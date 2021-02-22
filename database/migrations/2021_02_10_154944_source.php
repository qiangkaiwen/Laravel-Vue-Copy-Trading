<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Source extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_source', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('account_id');
            $table->string('symbol');
            $table->double('lots');
            $table->bigInteger('ticket');
            $table->integer('direction');
            $table->string('type');
            $table->bigInteger('magic');
            $table->double('openPrice');
            $table->double('stopLossPrice');
            $table->double('takeProfitPrice');
            $table->bigInteger('openTime');
            $table->bigInteger('openTimeGMT');
            $table->bigInteger('expiration');
            $table->bigInteger('expirationGMT');
            $table->string('comment_str')->nullable();
            $table->bigInteger('sourceTicket');
            $table->double('sourceLots');
            $table->string('sourceType');
            $table->bigInteger('originalTicket');
            $table->double('originalLots');
            $table->bigInteger('sourceOriginalTicket');
            $table->double('sourceOriginalLots');

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
        Schema::dropIfExists('tbl_source');
    }
}
