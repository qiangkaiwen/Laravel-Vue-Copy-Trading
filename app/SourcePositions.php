<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SourcePositions extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_number', 'symbol', 'lots', 'ticket', 'direction',
        'type', 'magic', 'openPrice', 'stopLossPrice', 'takeProfitPrice',
        'openTime', 'openTimeGMT', 'expiration', 'expirationGMT', 'comment_str',
        'sourceTicket', 'sourceLots', 'sourceType', 
        'originalLots', 'originalTickets', 
        'sourceOriginalLots', 'sourceOriginalTickets'
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
    protected $casts = [];
}
