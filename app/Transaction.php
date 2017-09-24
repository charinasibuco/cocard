<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table    = 'transaction';
    protected $fillable = ['user_id','transaction_key','token','total_amount','status'];

    public function Users()
    {
    	return $this->hasOne(Users::class);
    } 
    public function TransactionDetails()
    {
        return $this->hasMany(TransactionDetails::class);
    }
    public function Donation()
    {
        return $this->belongsToMany(Donation::class);
    }
}
