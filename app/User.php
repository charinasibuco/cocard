<?php

namespace App;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Role;
use Carbon\Carbon;
use App\Volunteer;

class User extends Authenticatable
{
    //use HasApiTokens, Notifiable;
    use HasRoles;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['organization_id', 'first_name', 'last_name', 'middle_name','address','city',
                            'state','zipcode', 'birthdate', 'gender', 'marital_status', 'phone', 'image', 'email','password','status','locale', 'api_token'
                            ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getFillable(){
        return $this->fillable;
    }

    public function getFullNameAttribute(){
        return $this->attributes["first_name"]." ".substr($this->attributes["middle_name"],0,1)."."." ".$this->attributes["last_name"];
    }

    public function Organization(){
        return $this->belongsTo(Organization::class);
    }
    public function Role(){
        return $this->belongsToMany('App\Role', 'user_roles');
    }

    public function UserRoles(){
        return $this->hasOne('App\UserRole');
    }

    public function changeRole($id){
        $this->UserRoles()->delete();
        $this->role()->save(Role::find($id));
    }

    public function UserGroup()
    {
        return $this->belongsToMany(UserGroup::class);
    }
   /* public function Donation()
    {
        return $this->belongsToMany(Donation::class);
    }*/
     public function Transaction()
    {
        return $this->belongsToMany(Transaction::class);
    }
    public function CreditCard()
    {
        return $this->belongsToMany(CreditCard::class);
    }

    public function EvenParticipants()
    {
        return $this->belongsToMany(EvenParticipants::class);
    }
    public function Family()
    {
        return $this->belongsTo(Family::class);
    }
    public function FamilyMember()
    {
        return $this->belongsTo(FamilyMember::class);
    }
    public function Volunteer()
    {
        return $this->hasMany(Volunteer::class);
    }

    public function getVolunteeredEventsAttribute()
    {
        $volunteered = Volunteer::where("user_id",$this->id)->get();
        $volunteered_events = [];
        foreach($volunteered as $volunteer){
            $volunteered_events[] = $volunteer->event;
        }
        return $volunteered_events;
    }

    public function assignedRole($id)
    {
        return $this->roles()->save(
            Role::where('id',$id)->firstOrFail()
        );
    }
    public function attachOrganization($organization){
        return $this->organization()->attach($organization);
    }

    public function getFormatBirthdateAttribute(){
		return Carbon::parse($this->birthdate);
	}
    /*public function getFullNameAttribute(){
        return $this->first_name." ".$this->last_name;
    }*/
}

