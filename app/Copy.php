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
