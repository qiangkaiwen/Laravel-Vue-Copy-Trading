<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class History extends Model
{
    protected $table = "tbl_history";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "account_id", "symbol", "lots", "ticket", "direction",
        "type", "magic", "openPrice", "stopLossPrice", "takeProfitPrice",
        "openTime", "openTimeGMT", "expiration", "expirationGMT", "comment_str",
        "sourceTicket", "sourceLots", "sourceType", "originalLots", "originalTicket",
        "sourceOriginalLots", "sourceOriginalTicket"
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
        "lots" => "double",
        "ticket" => "bigint",
        "magic" => "bigint",
        "openPrice" => "double",
        "stopLossPrice" => "double",
        "takeProfitPrice" => "double",
        "openTime" => "bigint",
        "openTimeGMT" => "bigint",
        "expiration" => "bigint",
        "expirationGMT" => "bigint",
        "sourceTicket" => "bigint",
        "sourceLots" => "double",
        "originalLots" => "double",
        "originalTicket" => "bigint",
        "sourceOriginalLots" => "double",
        "sourceOriginalTicket" => "bigint"
    ];

    public function account()
    {
        $this->belongsTo(Accounts::class, 'account_id');
    }
}
