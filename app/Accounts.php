<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Accounts extends Model
{
    use SoftDeletes;
    const STATUS_NONE = "NONE";
    const STATUS_PROVIDE = "PROVIDE";
    const STATUS_COPY = "COPY";

    protected $table = "tbl_account";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_number', 'authorization', 'online_status', 'lots_traded'
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
        'expiry' => 'datetime',
    ];

    public function user_account()
    {
        return $this->hasOne(UserAccounts::class, 'account_id');
    }

    public function sources()
    {
        return $this->hasOne(Source::class, 'account_number', 'account_number');
    }
}
