<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Organization extends Model
{
   	protected $table  		= 'organizations';
    protected $fillable		= ['id','name', 'contact_person','position','contact_number',
    						'email','password','url','approved_by', 'language',
    						'scheme', 'logo', 'pending_organization_user_id', 'status'];

    public function User(){
    	return $this->belongsToMany(User::class);
    }


    public function Users(){
        return $this->hasMany('App\User');
    }

    public function Event(){
        return $this->hasMany(Event::class)->where('status','=','Active');
    }

    public function events(){
        return $this->hasMany(Event::class)->where('status','=','Active');
    }


    public function getEventsNeedingVolunteersAttribute(){
       $needs_volunteers = [];
       foreach($this->event as $event){
            // if($event->volunteer_slots > 0 && ($event->start_date > Carbon::now()->toDateTimeString())){
          if($event->volunteer_slots){
                $needs_volunteers[] = $event;
            }
       }
       return $needs_volunteers;
    }
}
