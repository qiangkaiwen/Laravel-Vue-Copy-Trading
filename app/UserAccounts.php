<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserAccounts extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_idx', 'account_idx'
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

    public function user() {
        return $this->belongsTo(User::class, 'user_idx');
    }

    public function account() {
        return $this->belongsTo(Accounts::class, 'account_idx');
    }
}
