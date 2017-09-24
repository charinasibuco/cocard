<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Acme\Repositories\Cart\EasyCart;
use App\Validator\CustomValidator;
use DB;
use Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if(!session('cart'))
        {
            session(['cart' => new EasyCart()]);
        }

        Validator::extend('unique_custom', function ($attribute, $value, $parameters)
        {
            // Get the parameters passed to the rule
            list($table, $field, $field2, $field2Value, $field3, $field3Value) = $parameters;

            // Check the table and return true only if there are no entries matching
            // both the first field name and the user input value as well as
            // the second field name and the second field value
            return DB::table($table)->where($field, $value)->where($field2, $field2Value)->where($field3, $field3Value)->count() == 0;
        });

        Validator::extend('unique_custom_update', function ($attribute, $value, $parameters)
        {
            // Get the parameters passed to the rule
            list($table, $field, $field2, $field2Value, $field3, $field3Value, $field4, $field4Value) = $parameters;

            // Check the table and return true only if there are no entries matching
            // both the first field name and the user input value as well as
            // the second field name and the second field value
            return DB::table($table)->where($field, $value)->where($field2, $field2Value)->where($field3, $field3Value)->where($field4, '!=', $field4Value)->count() == 0;
        });

        Validator::extend('unique_custom_update_organization', function ($attribute, $value, $parameters)
        {
            // Get the parameters passed to the rule
            list($table, $field, $field1, $field1Value) = $parameters;

            // Check the table and return true only if there are no entries matching
            // both the first field name and the user input value as well as
            // the second field name and the second field value
            return DB::table($table)->where($field, $value)->where($field1, '!=', $field1Value)->count() == 0;
        });

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('Acme\Repositories\RepositoryInterface', 'Acme\Repositories\Repository');
    }
}
