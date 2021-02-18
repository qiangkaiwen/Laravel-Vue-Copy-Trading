<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Accounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_account', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('account_number');
            $table->string('broker');
            $table->integer('authorization')->nullable();
            $table->timestamp('expiry')->nullable();
            $table->integer('online_status')->nullable();
            $table->double('lots_traded')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('tbl_account');
    }
}
