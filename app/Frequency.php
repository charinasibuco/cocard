<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Frequency extends Model
{
    protected $table    = 'frequency';
    protected $fillable = ['title','description','status'];

    public function Donation()
    {
        return $this->hasMany(Donation::class);
    }
}
