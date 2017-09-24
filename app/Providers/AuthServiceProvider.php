<?php

namespace App\Providers;
use App\Permission;
//use Laravel\Passport\Passport;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any application authentication / authorization services.
     *
     * @param  \Illuminate\Contracts\Auth\Access\Gate  $gate
     * @return void
     */
    public function boot(GateContract $gate)
    {
        $this->registerPolicies($gate);

        if(Schema::hasTable('permissions')){
            foreach ($this->getPermissions() as $permission) {

                $gate->define($permission->title, function ($user) use ($permission){
                    return $user->hasRole($permission->roles);
                });
            }
        }

       // $this->registerPolicies();

      //  Passport::routes();
    }

    protected function getPermissions()
    {
        return Permission::with('roles')->get();
    }
}
