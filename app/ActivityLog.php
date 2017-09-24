<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table 	= 'activity_log';
    protected $fillable = ['user_id', 'activity','details', 'org_id'];

    public function User()
    {
    	return $this->belongsTo(User::class);
    }
}
