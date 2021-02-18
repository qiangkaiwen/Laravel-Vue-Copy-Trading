<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Accounts;

class AddStatusTblAccount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_account', function (Blueprint $table) {
            $table->enum('status', [Accounts::STATUS_NONE, Accounts::STATUS_COPY, Accounts::STATUS_PROVIDE])
                ->default(Accounts::STATUS_NONE);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_account', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
