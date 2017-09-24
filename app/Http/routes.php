<?php
/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/
// Route::get('welcome/{locale}', function ($locale) {
//     App::setLocale($locale);
// });
define('OAUTH_REQUEST_URL', 'https://oauth.intuit.com/oauth/v1/get_request_token');
define('OAUTH_ACCESS_URL', 'https://oauth.intuit.com/oauth/v1/get_access_token');
define('OAUTH_AUTHORISE_URL', 'https://appcenter.intuit.com/Connect/Begin');
use Illuminate\Http\Request;
use App\QuickBooks as QB;
use App\Organization;
use Myleshyson\LaravelQuickBooks\Facades\Connection;
//use Auth;

function loadSDK(){
    require_once('v3-php-sdk-2.6.0/config.php');
    require_once(PATH_SDK_ROOT . 'Core/ServiceContext.php');
    require_once(PATH_SDK_ROOT . 'DataService/DataService.php');
    require_once(PATH_SDK_ROOT . 'PlatformService/PlatformService.php');
    require_once(PATH_SDK_ROOT . 'Utility/Configuration/ConfigurationManager.php');
    require_once(PATH_SDK_ROOT . 'Core/OperationControlList.php');
    require_once(PATH_SDK_ROOT . 'XSD2PHP/src/com/mikebevz/xsd2php/Php2Xml.php');
    require_once(PATH_SDK_ROOT . 'XSD2PHP/src/com/mikebevz/xsd2php/Bind.php');
};


// Route::group(['middleware' => 'csrf'], function () {
    Route::group(['middleware' => 'web'], function () {
//        Route::group(["prefix" => "qbo"], function () {
//            Route::get('/oauth', ['as' => 'qbo_oauth', 'uses' => 'QuickBooksController@oauth']);
//            Route::get('/success', ['as' => 'qbo_success', 'uses' => 'QuickBooksController@success']);
//            Route::get('/connect', ['as' => 'qbo_connect', 'uses' => 'QuickBooksController@connect']);
//            Route::get('/check', ['as' => 'qbo_check', 'uses' => 'QuickBooksController@checkConnection']);
//            Route::get('/disconnect/{slug}', ['as' => 'qbo_disconnect', 'uses' => 'QuickBooksController@disconnect']);
//        });


    Route::auth();
    Route::get('/',                     ['as' => 'home_page', 'uses' => 'MainController@index']);
    Route::post('/get/{id}',                     ['as' => 'get', 'uses' => 'MainController@get']);
    #Route::get('/{locale}',                 ['as' => 'home_page', 'uses' => 'MainController@index']);
    Route::get('/about-us',             ['as' => 'about_us', 'uses' => 'MainController@about']);
    Route::get('/permission',           ['as' => 'permission', 'uses' => 'PermissionListController@index']);
    Route::get('/features',             ['as' => 'features','uses' => 'MainController@features']);
    Route::get('/activity-log',         ['as' => 'activity-log', 'uses' => 'ActivityLogController@index']);

    // Reset Password Routes
    // -- send email link
    Route::get('/password/send-reset-password-link', 'Auth\NewPasswordController@email');
    Route::get('/reset-password/{token}', 'Auth\NewPasswordController@resetLink');
    Route::post('/password/send_email', 'Auth\NewPasswordController@sendEmail');
    // -- reset form endpoint (NewPasswordController)
    Route::post('/password/reset-password/{token}', 'Auth\NewPasswordController@resetPassword');

    //superadmin
    Route::post('register',['middleware' => 'auth','uses' =>'OrganizationController@store']);
    Route::get('register-superadmin',['middleware' => 'auth','uses' =>'OrganizationController@create']);
    Route::get('/dashboard', ['middleware' => 'auth','uses' =>'OrganizationController@dashboard']);
    Route::get('/send/{id}', ['middleware' => 'auth','as' => 'send', 'uses' => 'OrganizationController@sendEmailNotification']);
    Route::get('/donations', 'DonationController@index');
    Route::get('/organizations', ['middleware' => 'auth','uses' =>'OrganizationController@index']);
    Route::post('/store',               ['as' => 'pending_organization_store', 'uses' => 'PendingOrganizationUsersController@save']);
    Route::post('/superadmin-login',               ['as' => 'superadmin_login', 'uses' => 'UserAuthController@superadminLogin']);
    Route::post('/savelanguage/{id}',           ['as' => 'save_language', 'uses' => 'UserController@saveLanguage']);
    Route::post('/superadmin/store',             ['as' => 'superadmin_store',                'uses' => 'UserController@superadminStore']);
    Route::post('/superadmin/update/{id}',             ['as' => 'superadmin_update',                'uses' => 'UserController@superadminUpdate']);
    Route::any('/get-occurence', ['as' => 'get_occurence',  'uses' => 'EventController@getOccurence']);
    Route::group(['prefix' => 'user', 'middleware'],function(){
        Route::get('/',             ['as' => 'users',                'uses' => 'UserController@index']);
        Route::get('/create',             ['as' => 'user_create',                'uses' => 'UserController@create']);
        Route::post('/store',             ['as' => 'user_store',                'uses' => 'UserController@store']);
        Route::post('/update/{id}',             ['as' => 'user_update',                'uses' => 'UserController@update']);
        Route::get('/edit/{id}',             ['as' => 'user_edit',                'uses' => 'UserController@edit']);
        Route::get('/delete/{id}',             ['as' => 'user_delete',                'uses' => 'UserController@destroy']);
    });

    Route::group(['prefix' => 'superadmin', 'middleware'],function(){
        Route::get('/organization-list-of-administrators/{id}',['as' => 'org_list_admins',                'uses' => 'AdministratorController@index']);
        Route::get('/pending-organization',             ['as' => 'pending_organization',                'uses' => 'PendingOrganizationUsersController@index']);
        Route::get('/status/{status}',                  ['as' => 'pending_organization_status',         'uses' => 'PendingOrganizationUsersController@indexStatus']);
        Route::get('/create',                           ['as' => 'pending_organization_create',         'uses' => 'PendingOrganizationUsersController@create']);
        Route::get('/review-organization/{id}',         ['as' => 'pending_organization_review',         'uses' => 'PendingOrganizationUsersController@reviewPending']);
        Route::get('/review-pending-organization/{id}', ['as' => 'pending_organization_review_pending', 'uses' => 'PendingOrganizationUsersController@reviewPendingToActive']);
        Route::get('/edit/{id}',                        ['as' => 'pending_organization_edit',           'uses' => 'PendingOrganizationUsersController@edit']);
        Route::get('/update-pending/{id}',              ['as' => 'pending_organization_update_pending', 'uses' => 'PendingOrganizationUsersController@updatePending']);
        Route::get('/update-declined/{id}',             ['as' => 'pending_organization_update_declined','uses' => 'PendingOrganizationUsersController@updateDeclined']);
        Route::get('/update-deactivate/{id}',           ['as' => 'pending_organization_update_inactive','uses' => 'PendingOrganizationUsersController@updateInactive']);
        Route::get('/update-active/{id}',               ['as' => 'pending_organization_update_active',  'uses' => 'PendingOrganizationUsersController@updateActive']);
        Route::post('/update-pending-active/{id}',      ['as' => 'pending_organization_pending_store',  'uses' => 'PendingOrganizationUsersController@updateApprove']);
        Route::post('/update-review/{id}',              ['as' => 'pending_organization_update_review',  'uses' => 'PendingOrganizationUsersController@updateReview']);
        Route::get('/update-approve/{id}',              ['as' => 'pending_organization_update_approve', 'uses' => 'PendingOrganizationUsersController@updateApprove']);
        Route::get('/delete/{id}',                      ['as' => 'pending_organization_destroy',        'uses' => 'PendingOrganizationUsersController@destroy']);
    });

    Route::group(['prefix' => 'page','middleware'],function(){
        Route::get('/',                 ['as' => 'page', 'uses' => 'PageController@index']);
        Route::get('/create',           ['as' => 'page_create', 'uses' => 'PageController@create']);
        Route::post('/store',           ['as' => 'page_store', 'uses' => 'PageController@store']);
        Route::get('/edit/{id}',        ['as' => 'page_edit', 'uses' => 'PageController@edit']);
        Route::post('/update/{id}',     ['as' => 'page_update', 'uses' => 'PageController@update']);
        Route::get('/delete/{id}',      ['as' => 'page_delete', 'uses' => 'PageController@destroy']);
    });

    Route::group(['prefix' => 'donation', 'middleware'],function(){
        Route::get('/',                 ['as' => 'donation', 'uses' => 'DonationController@index']);
        Route::post('/store',           ['as' => 'donation_store', 'uses' => 'DonationController@save']);
        Route::get('/create',           ['as' => 'donation_create', 'uses' => 'DonationController@create']);
        Route::get('/edit/{id}',        ['as' => 'donation_edit', 'uses' => 'DonationController@edit']);
        Route::post('/update/{id}',     ['as' => 'donation_update', 'uses' => 'DonationController@save']);
        Route::get('/delete/{id}',      ['as' => 'donation_destroy', 'uses' => 'DonationController@destroy']);
    });

    Route::group(['prefix' => 'event', 'middleware'],function(){
        Route::get('/',                 ['as' => 'event', 'uses' => 'EventController@index']);
        Route::post('/store',           ['as' => 'event_store', 'uses' => 'EventController@save']);
        Route::get('/create',           ['as' => 'event_create', 'uses' => 'EventController@create']);
        Route::get('/edit/{id}',        ['as' => 'event_edit', 'uses' => 'EventController@edit']);
        Route::post('/update/{id}',     ['as' => 'event_update', 'uses' => 'EventController@save']);
        //Route::post('/update_json/{id}',     ['as' => 'event_update_json', 'uses' => 'EventController@update_json']);
        Route::get('/delete/{id}',      ['as' => 'event_destroy', 'uses' => 'EventController@destroy']);
    });

    Route::group(['prefix' => 'frequency', 'middleware'],function(){
        Route::get('/',                     ['as' => 'frequency', 'uses' => 'FrequencyController@index']);
        Route::get('/create',               ['as' => 'frequency_create', 'uses' => 'FrequencyController@create']);
        Route::post('/store',               ['as' => 'frequency_store', 'uses' => 'FrequencyController@save']);
        Route::get('/edit/{id}',            ['as' => 'frequency_edit', 'uses' => 'FrequencyController@edit']);
        Route::post('/update/{id}',         ['as' => 'frequency_update', 'uses' => 'FrequencyController@update']);
        Route::get('/delete/{id}',          ['as' => 'frequency_destroy', 'uses' => 'FrequencyController@destroy']);
    });

    Route::group(['prefix' => 'organization','middleware' => 'web'],function(){
        // superadmin
        Route::get('/',                     ['middleware' => 'auth','as' => 'organization',                        'uses' => 'OrganizationController@index']);
        Route::get('/create',               ['middleware' => 'auth','as' => 'organization_create',                 'uses' => 'OrganizationController@create']);
        Route::post('/store',               ['middleware' => 'auth','as' => 'organization_store',                  'uses' => 'OrganizationController@save']);
        Route::post('/storeorganization',   ['middleware' => 'auth','as' => 'organization_storeorg',               'uses' => 'OrganizationController@saveOrganization']);
        Route::post('/administrator_store', ['as' => 'administrator_store','uses' => 'AdministratorController@store']);
        Route::get('/edit/{id}',            ['middleware' => 'auth','as' => 'organization_edit',                   'uses' => 'OrganizationController@edit']);
        Route::post('/admin-update/{id}',   ['middleware' => 'auth','as' => 'organization_update',                 'uses' => 'OrganizationController@update']);
        Route::get('/delete/{id}',          ['middleware' => 'auth','as' => 'organization_destroy',                'uses' => 'OrganizationController@destroy']);
        Route::get('/fetch/{id}/{needing_volunteers?}',           ['as' => 'fetch_events', 'uses' => 'OrganizationController@fetchEvents']);


        //user outside middleware
        Route::post('/admin-login',             ['as' => 'post_login',      'uses' => 'ChurchController@postlogin']);
        Route::post('/update/{id}',             ['as' => 'post_user_update', 'uses' => 'UserController@save']);
        Route::post('/register',                ['as' => 'post_user_register', 'uses' => 'UserAuthController@save']);
        Route::post('/register-admin',                ['as' => 'post_user_register_admin', 'uses' => 'UserAuthController@save_admin']);
        Route::post('/login',                   ['as' => 'post_user_login', 'uses' => 'UserAuthController@postLogin']);
        Route::post('/post-join',               ['as' => 'post_join', 'uses' => 'ChurchController@postJoin']);
        Route::post('/{slug}/family/{id}/addFamilyMember/{count}',             ['as' => 'add_family_member', 'uses' => 'FamilyController@addFamilyMember'])->where('slug', '([A-Za-z0-9\-\/]+)');
        // Route::post('/family-members/store',         ['as' => 'post_family_store', 'uses' => 'FamilyController@save_family_member']);
        // Route::post('/family-members/update/{id}',   ['as' => 'post_family_update', 'uses' => 'FamilyController@update_family_member']);
        // Route::get('/family-members/delete/{id}',    ['as' => 'get_family_delete', 'uses' => 'FamilyController@destroy']);

        Route::group(['prefix' => 'administrator'], function ()
        {
            Route::post('events/store/',                    ['as' => 'store_event','uses' =>'EventController@save']);
            Route::post('role/store/',                      ['as' => 'store_role','uses' => 'RoleController@store']);
            Route::post('role/update/{id}',                 ['as' => 'update_role','uses' => 'RoleController@update']);
            Route::post('events-update/{id}',               ['as' => 'church_event_update','uses' =>'EventController@update']);
            Route::post('donation-list/store/',             ['as' => 'store_donation_list','uses' =>'DonationListController@store']);
            Route::post('donation-list/update/{id}',        ['as' => 'update_donation_list','uses' =>'DonationListController@store']);
            Route::post('donation-category/update/{id}',    ['as' => 'update_donation_category','uses' =>'DonationCategoryController@store']);
            Route::post('members/update/{id}',              ['as' => 'update_member_details','uses' =>'MembersController@store']);
            Route::post('donation-category/store/',         ['as' => 'store_donation_category','uses' =>'DonationCategoryController@store']);
            Route::post('events/update_duplicate/{id}',     ['as' => 'event_update_duplicate', 'uses' => 'EventController@saveDuplicate']);

        });

        Route::post('/assign_role/{id}',['as' => 'user_assign_role', 'uses' => 'UserController@assign_role']);
        Route::post('/delete-user-role/',['as' => 'delete_user_role', 'uses' => 'RoleController@deleteUserRole']);

        Route::post('family/store',                 ['as' => 'family_store', 'uses' => 'FamilyController@family_store']);
        Route::post('family/update/{id}',           ['as' => 'family_update', 'uses' => 'FamilyController@family_update']);
        Route::post('family-member/{id}/store',     ['as' => 'family_member_store', 'uses' => 'FamilyController@family_member_store']);
        Route::post('family-member/update/{id}',    ['as' => 'family_member_update', 'uses' => 'FamilyController@family_member_update']);
        Route::post('member/assign-family/{id}',    ['as' => 'assign_to_family', 'uses' => 'FamilyController@assignFamily']);

        Route::group(['prefix' => 'email-group'], function ()
        {
            Route::post('members/update/{id}',  ['as' => 'update_email_group_member', 'uses' => 'EmailController@update_email_group_member']);
            Route::post('members/store',        ['as' => 'store_email_group_member', 'uses' => 'EmailController@store_email_group_member']);
            Route::post('update/{id}',          ['as' => 'update_email_group', 'uses' => 'EmailController@update_email_group']);
            Route::post('store',                ['as' => 'store_email_group', 'uses' => 'EmailController@store_email_group']);
        });

        //for administrator users
        Route::get('/organization_admin_users', ['as' => 'organization_admin_users','uses' => 'AdministratorController@index']);
        #Route::get('/organization_admin_users?type=json', ['middleware' => 'web','uses' => 'AdministratorController@index']);
        Route::get('/administrator_create/{id}', ['as' => 'administrator_create','uses' => 'AdministratorController@create']);
        Route::get('/administrator_edit/{id}', ['as' => 'administrator_edit','uses' => 'AdministratorController@edit']);
        Route::post('/administrator_update/{id}', ['as' => 'administrator_update','uses' => 'AdministratorController@update']);
        Route::get('/administrator_delete/{id}', ['as' => 'administrator_delete','uses' => 'AdministratorController@destroy']);

        Route::group(['prefix' => '{slug}'],function()
        {
            //Reset Password Link------
            Route::get('/request-resetpassword-link',['as' => 'request-resetpassword-link','uses' => 'UserAuthController@requestResetLink']);
            Route::get('/reset-password/{token}',['as' => 'reset-password','uses' => 'UserAuthController@resetPassword']);
            //--------------------------
            Route::post('/send-email-transaction',['as' => 'send_email_transaction','uses' => 'BillingController@sendEmailTransaction']);
            Route::post('/send-email-transaction-zero',['as' => 'send_email_transaction_zero','uses' => 'BillingController@sendEmailTransactionZero']);
            Route::post('/transaction-receipt-pdf', ['uses' => 'BillingController@transactionReceiptPDF']);
            Route::post('/transaction-receipt-pdf-zero', ['uses' => 'BillingController@transactionReceiptPDFZero']);
            Route::post('administrator/reports/generate',     ['as' => 'generate_report',        'uses' => 'OrganizationController@generateReport'])      ->where('slug', '([A-Za-z0-9\-\/]+)');
            Route::post('administrator/reports/generate/sort',     ['as' => 'generate_report_sort',        'uses' => 'OrganizationController@sort_member_list'])      ->where('slug', '([A-Za-z0-9\-\/]+)');
            Route::post('/donation/add-donation',              ['uses' =>'DonationController@addtocart'])->where('slug', '([A-Za-z0-9\-\/]+)');
            Route::post('/donation/payment-info/proceed-to-payment',          ['uses' => 'ChurchController@proceedtopayment'])           ->where('slug', '([A-Za-z0-9\-\/]+)');
            Route::post('/one_time_add', ['uses' => 'DonationController@oneTimeAdd'])->where('slug', '([A-Za-z0-9\-\/]+)');
            Route::post('/event/add-to-cart',              ['uses' =>'EventController@addtocart'])->where('slug', '([A-Za-z0-9\-\/]+)');
            Route::get('/volunteer_listing',['as' => 'volunteer_listing','uses' => 'VolunteerController@index'])->where('slug', '([A-Za-z0-9\-\/]+)');
            Route::get('/family-member/delete/{id}',        ['as' => 'family_member_delete', 'uses' => 'FamilyController@family_member_delete'])->where('slug', '([A-Za-z0-9\-\/]+)');
            Route::get('/transaction',['uses' => 'BillingController@transaction']);

            //API Routes
            Route::group(['prefix' => 'api', 'middleware' => 'auth:api'],function(){
                Route::get('/cart',                   ['uses' =>'ChurchController@cart'])->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::group(['prefix' => 'user'],function(){

                    Route::post('update/{id}',             ['uses' => 'UserController@save']);
                    
                    Route::group(['prefix' => 'family'],function(){
                        Route::get('/{id}/family-member/create',    ['as' => 'user_family_member_create', 'uses' => 'FamilyController@user_family_member_create'])    ->where('slug', '([A-Za-z0-9\-\/]+)');
                        Route::get('/{id}',                         ['uses' => 'FamilyController@user_family_member_index'])                                ->where('slug', '([A-Za-z0-9\-\/]+)');
                        Route::get('/',                             ['uses' => 'FamilyController@user_family_index'])                                       ->where('slug', '([A-Za-z0-9\-\/]+)');

                        Route::group(['prefix' => 'family-member'],function(){
                            // Route::get('/delete/{id}',        ['as' => 'family_member_delete', 'uses' => 'FamilyController@family_member_delete']);
                            Route::get('/edit/{id}',          ['as' => 'user_family_member_edit', 'uses' => 'FamilyController@user_family_member_edit'])         ->where('slug', '([A-Za-z0-9\-\/]+)');
                            Route::get('/',                   ['uses' => 'FamilyController@user_family_member_index'])          ->where('slug', '([A-Za-z0-9\-\/]+)');
                        });
                    });

                    
                    Route::group(['prefix' => 'family-member'],function(){
                            Route::get('/edit/{id}',          ['as' => 'user_family_member_edit', 'uses' => 'FamilyController@user_family_member_edit'])         ->where('slug', '([A-Za-z0-9\-\/]+)');
                            Route::post('/store/{id}', ['uses' => 'FamilyController@family_member_store']);
                            Route::post('/update/{id}', ['uses' => 'FamilyController@family_member_update']);
                            Route::get('/delete/{id}',        ['as' => 'family_member_delete', 'uses' => 'FamilyController@family_member_delete'])->where('slug', '([A-Za-z0-9\-\/]+)');
                    });
                    
                    
                    Route::group(['prefix' => 'family-group'],function(){
                        Route::get('/edit/{id}',                     ['uses' => 'FamilyController@user_family_edit'])->where('slug', '([A-Za-z0-9\-\/]+)');
                        Route::post('/create', ['uses' => 'FamilyController@family_store'])          ->where('slug', '([A-Za-z0-9\-\/]+)');
                        Route::get('/delete/{id}',                 ['as' => 'post_family_delete', 'uses' => 'FamilyController@family_delete']);
                        Route::post('/update/{id}',           ['uses' => 'FamilyController@family_update'])->where('slug', '([A-Za-z0-9\-\/]+)');
                    });

                    Route::get('/donation/cancel-donation/{id}',                 ['uses' => 'DonationController@cancelDonation'])             ->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::post('/donation/add-recurring-donation',              ['uses' =>'DonationController@addtocart'])->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::get('/volunteer/cancel-volunteer/{id}',                   ['uses' =>'UserController@cancelVolunteer'])->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::get('/events/create',            ['uses' => 'UserController@create_event'])                          ->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::get('/edit-profile/{id}',        ['as' => 'edit_profile','uses' => 'UserController@editprofile'])    ->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::get('/dashboard',                ['uses' => 'UserController@dashboard'])                             ->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::get('/donate',                 ['uses' => 'UserDashboardController@donate'])                     ->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::get('/donate/donateonetime/{id}',                 ['uses' => 'UserDashboardController@donateonetime'])                     ->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::get('/donate/donaterecurring/{id}',                 ['uses' => 'UserDashboardController@donaterecurring'])                     ->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::post('/donate/add-donation',              ['uses' =>'UserDashboardController@addtocartuser'])->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::post('/event/add-to-cart',              ['uses' =>'EventController@addtocart'])->where('slug', '([A-Za-z0-9\-\/]+)');

                    Route::get('/calendar',                 ['uses' => 'UserDashboardController@calendar'])                     ->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::get('/event/volunteer',                 ['uses' => 'UserDashboardController@volunteer'])                     ->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::get('/donation',                 ['uses' => 'UserController@donation'])                              ->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::get('/profile',                  ['as' => 'user_profile','uses' => 'UserController@profile'])        ->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::get('/events',                   ['uses' => 'UserController@event'])                                 ->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::post('/events/export/{id}',       ['as' => 'export_event_excel', 'uses' => 'UserController@getEventTransaction'])        ->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::post('/donation/export/{id}',     ['as' => 'export_donation_excel', 'uses' => 'UserController@getDonationTransaction'])  ->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::get('/volunteer/export/{id}',     ['as' => 'export_volunteer_excel', 'uses' => 'UserController@getVolunteerHistory'])  ->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::get('/volunteer',                ['uses' => 'UserController@volunteer'])                             ->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::get('/volunteergroups', ['uses' => 'VolunteerController@volunteerGroupTable']);
                    Route::post('/volunteer_apply', ['uses' => 'VolunteerController@apply']);
                });

                Route::get('/login', ['uses' => 'UserAuthController@userLogin'])->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::post('/login', ['uses' => 'UserAuthController@postLogin'])->where('slug', '([A-Za-z0-9\-\/]+)');

            });

            Route::group(['prefix' => 'api'],function(){
                Route::resource('billing','BillingController');

                Route::group(['prefix' => 'user'],function(){
                     Route::post('/register/create',            ['uses' => 'UserAuthController@save'])   ->where('slug', '([A-Za-z0-9\-\/]+)');
                
                });

            });

            Route::group(['prefix' => 'user'],function(){
                Route::group(['prefix' => 'family', 'middleware'],function(){
                    Route::get('/{id}/family-member/create',    ['as' => 'user_family_member_create', 'uses' => 'FamilyController@user_family_member_create'])    ->where('slug', '([A-Za-z0-9\-\/]+)');

                    Route::get('/view/{id}',                         ['uses' => 'FamilyController@family_view'])                                ->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::get('edit/{id}',                     ['uses' => 'FamilyController@user_family_edit'])                                ->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::get('/create',                             ['uses' => 'FamilyController@user_family_create'])                                       ->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::get('/{id}',                         ['uses' => 'FamilyController@user_family_member_index'])                                ->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::get('/',                             ['uses' => 'FamilyController@user_family_index'])                                       ->where('slug', '([A-Za-z0-9\-\/]+)');

                    Route::group(['prefix' => 'family-member', 'middleware'],function(){
                        // Route::get('/delete/{id}',        ['as' => 'family_member_delete', 'uses' => 'FamilyController@family_member_delete']);
                        Route::get('/edit/{id}',          ['as' => 'user_family_member_edit', 'uses' => 'FamilyController@user_family_member_edit'])         ->where('slug', '([A-Za-z0-9\-\/]+)');
                        Route::get('/{id}',               ['as' => 'family_member_view', 'uses' => 'FamilyController@family_member_view'])                  ->where('slug', '([A-Za-z0-9\-\/]+)');
                        Route::get('/',                   ['uses' => 'FamilyController@user_family_member_index'])          ->where('slug', '([A-Za-z0-9\-\/]+)');
                    });
                });
                Route::post('/donation/add-donation',              ['uses' =>'DonationController@addtocart'])->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::get('/volunteer/cancel-volunteer/{id}',                   ['uses' =>'UserController@cancelVolunteer'])->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::get('/events/create',            ['uses' => 'UserController@create_event'])                          ->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::get('/edit-profile/{id}',        ['as' => 'edit_profile','uses' => 'UserController@editprofile'])    ->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::get('/login-as',                ['uses' => 'UserController@multipleUserRole'])                             ->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::get('/dashboard',                ['uses' => 'UserController@dashboard'])                             ->where('slug', '([A-Za-z0-9\-\/]+)');
                #Route::get('/donation',                 ['uses' => 'DonationController@user_donation'])                     ->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::get('/donate',                 ['uses' => 'UserDashboardController@donate'])                     ->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::get('/donate/donateonetime/{id}',                 ['uses' => 'UserDashboardController@donateonetime'])                     ->where('slug', '([A-Za-z0-9\-\/]+)');
                #Route::get('/donate/donaterecurring/{id}',                 ['uses' => 'UserDashboardController@donaterecurring'])                     ->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::get('/donate/fund/{id}',                 ['uses' => 'UserDashboardController@donaterecurring'])                     ->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::post('/donate/add-donation',              ['uses' =>'UserDashboardController@addtocartuser'])->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::get('/calendar',                 ['uses' => 'UserDashboardController@calendar'])                     ->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::get('/event/volunteer',                 ['as' =>'user_volunteer', 'uses' => 'UserDashboardController@volunteer'])                     ->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::get('/donation',                 ['uses' => 'UserController@donation'])                              ->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::get('/donation/cancel-donation/{id}',                 ['uses' => 'DonationController@cancelDonation'])             ->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::get('/profile',                  ['as' => 'user_profile','uses' => 'UserController@profile'])        ->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::get('/events',                   ['uses' => 'UserController@event'])                                 ->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::post('/events/export/{id}',       ['as' => 'export_event_excel', 'uses' => 'UserController@getEventTransaction'])        ->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::post('/donation/export/{id}',     ['as' => 'export_donation_excel', 'uses' => 'UserController@getDonationTransaction'])  ->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::post('/volunteer/export/{id}',     ['as' => 'export_volunteer_excel', 'uses' => 'UserController@getVolunteerHistory'])  ->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::get('/volunteer',                ['uses' => 'UserController@volunteer'])                             ->where('slug', '([A-Za-z0-9\-\/]+)');
            });

            //Administrator
            Route::group(['prefix' => 'administrator', 'middleware' => 'web'],function()
            {
                Route::get('/quickbooks',            ['as' => 'qb','uses' => 'ChurchController@quickbooks'])          ->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::get('/events/edit/{id}/instance/{instance?}',     ['as' => 'church_event',           'uses' => 'EventController@edit']) ->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::get('/events/edit/{id}/{date}',     ['as' => 'church_event_edit',           'uses' => 'EventController@edit']) ->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::get('/events/edit/{id}',     ['as' => 'church_event',           'uses' => 'EventController@edit']) ->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::get('/events/duplicate/{id}',     ['as' => 'church_event_duplicate',           'uses' => 'EventController@duplicate']) ->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::get('/events/duplicate/{id}/instance/{instance?}',     ['as' => 'church_event_duplicate_with_instance',           'uses' => 'EventController@duplicate']) ->where('slug', '([A-Za-z0-9\-\/]+)');
                //Route::get('/events/delete/{id}',     ['as' => 'church_event_delete',           'uses' => 'EventController@destroy']) ->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::get('/events/delete',     ['as' => 'church_event_delete',           'uses' => 'EventController@destroy']) ->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::get('/events/view-details/{id}',     ['as' => 'church_event_view_details',           'uses' => 'EventController@viewdetails'])->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::get('/events/view-volunteers/{id}',  ['as' => 'church_event_view_volunteers',   'uses' =>'EventController@viewvolunteers'])->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::get('/members/view-details/{id}',     ['as' => 'church_member_view_details',           'uses' => 'MembersController@viewdetails']) ->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::get('/role/edit/{id}',       ['as' => 'role_edit',        'uses' => 'RoleController@edit'])      ->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::get('/role/delete/{id}',     ['as' => 'role_delete',        'uses' => 'RoleController@destroy'])      ->where('slug', '([A-Za-z0-9\-\/]+)');

                Route::post('/event/send-email-to-participant',               ['as' => 'send_email_to_participant',         'uses' => 'EventController@sendEmailToParticipants'])        ->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::post('/volunteer/send-email-to-individual-volunteer/{id}',               ['as' => 'send_email_to_individual_volunteer',         'uses' => 'VolunteerController@sendEmailIndividualVolunteer'])        ->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::post('/volunteer/send-email-to-volunteer-group/{id}',               ['as' => 'send_email_to_volunteer_group',         'uses' => 'VolunteerController@sendEmailToVolunteerGroup'])        ->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::post('/volunteer/send-email-to-multiple-volunteer',               ['as' => 'send_email_to_multiple_volunteer',         'uses' => 'VolunteerController@sendEmailToMultipleVolunteer'])        ->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::post('/volunteer/send-reminder-message-to-volunteer-group/{id}',               ['as' => 'send_reminder_message_to_volunteer_group',         'uses' => 'VolunteerController@sendReminderMessageToVolunteerGroup'])        ->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::get('/email-group/{id}/members/create/filter/add-member/{user_id}',            ['as' => 'save_filter_email_group_member', 'uses' => 'EmailController@save_email_group_member_filter_single']) ->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::post('/email-group/{id}/members/create/filter/add-member',            ['as' => 'ssave_filter_email_group_member', 'uses' => 'EmailController@save_email_group_member_filter_single']) ->where('slug', '([A-Za-z0-9\-\/]+)');

               


                 //Route::any('/get-occurence', ['as' => 'get_occurence',  'uses' => 'EventController@getOccurence'])        ->where('slug', '([A-Za-z0-9\-\/]+)');






                Route::group(['prefix' => 'donation'],function()
                {
                    Route::get('add',                           ['as' => 'add_donation','uses' => 'DonationController@create'])->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::get('create-donation-list',          ['as' => 'create_donation_list','uses' => 'DonationListController@create'])->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::get('create-donation-category',      ['as' => 'create_donation_category','uses' => 'DonationCategoryController@create'])->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::get('edit-donation-list/{id}',       ['as' => 'edit_donation_list','uses' => 'DonationListController@edit'])->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::get('edit-donation-category/{id}',   ['as' => 'edit_donation_category','uses' => 'DonationCategoryController@edit'])->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::get('delete-donation-list/{id}',     ['as' => 'delete_donation_list/{id}','uses' => 'DonationListController@delete'])->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::get('delete-donation-category/{id}', ['as' => 'delete_donation_category/{id}','uses' => 'DonationCategoryController@delete'])->where('slug', '([A-Za-z0-9\-\/]+)');
                });

                Route::group(['prefix' => 'members'],function()
                {
                    Route::get('add-member',            ['as' => 'create_member','uses' => 'MembersController@create'])->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::get('edit-member/{id}',      ['as' => 'edit_member_details','uses' => 'MembersController@edit'])->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::get('delete-member/{id}',    ['as' => 'deletemember/{id}','uses' => 'MembersController@delete'])->where('slug', '([A-Za-z0-9\-\/]+)');
                });

                Route::get('/events/create',        ['uses' => 'EventController@eventform'])->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::get('/events/deactivate/{id}',        ['uses' => 'EventController@inactive'])->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::get('/volunteer/create',     ['as' => 'create_volunteer',      'uses' => 'VolunteerController@admin'])->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::get('/role/create',          ['as' => 'create_role',           'uses' => 'RoleController@create'])->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::get('/dashboard',            ['as' => 'church_dashboard',      'uses' => 'ChurchController@dashboard'])->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::get('/login-as',            ['as' => 'church_login_as',      'uses' => 'ChurchController@multipleUserRole'])->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::any('/events',               ['as' => 'church_events',         'uses' => 'EventController@index'])->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::post('/event_modal',          ['as' => 'event_modal','uses' => 'EventController@modal'])->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::any('/add_event_modal',          ['as' => 'add_event_modal','uses' => 'EventController@add_event_modal'])->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::get('/volunteer',            ['as' => 'church_volunteer',      'uses' => 'VolunteerController@admin'])->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::get('/volunteer-list',            ['as' => 'church_volunteer_list',      'uses' => 'VolunteerController@volunteer_list'])->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::get('/volunteer-main-list',            ['as' => 'church_volunteer_main_list',      'uses' => 'VolunteerController@volunteer_main_list'])->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::get('/volunteer/filter-by-event/',            ['as' => 'filter_church_volunteer',      'uses' => 'VolunteerController@eventFilter'])->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::get('/volunteer/filter-by-event/{eid}/view-volunteers-by-event/{id}',            ['as' => 'view_filter_church_volunteer',      'uses' => 'VolunteerController@viewVolunteersPerEvent'])->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::post('/volunteer/filter-by-event/view-volunteers-by-event/Approve/{id}',            ['as' => 'approve_view_filter_church_volunteer',      'uses' => 'VolunteerController@actionApproveVolunteersPerEvent'])->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::post('/volunteer/filter-by-event/view-volunteers-by-event/Reject/{id}',            ['as' => 'reject_view_filter_church_volunteer',      'uses' => 'VolunteerController@actionRejectedVolunteersPerEvent'])->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::get('/volunteer/add-volunteer',            ['as' => 'add_church_volunteer',      'uses' => 'VolunteerController@addvolunteer'])->where('slug', '([A-Za-z0-9\-\/]+)');

                Route::group(['prefix' => 'users'],function(){
                    Route::get('/delete/{id}',             ['as' => 'back_office_delete', 'uses' => 'UserController@back_office_delete']);
                    Route::get('/edit/{id}',      ['as' => 'back_office_edit', 'uses' => 'UserController@back_office_edit'])              ->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::get('/create',         ['as' => 'back_office_create', 'uses' => 'UserController@back_office_create'])         ->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::get('/',               ['as' => 'back_office_index', 'uses' => 'UserController@back_office_index'])           ->where('slug', '([A-Za-z0-9\-\/]+)');
                });
                Route::group(['prefix' => 'staff'],function(){
                    Route::get('/role-modal-delete/{id}',   ['as' => 'role_modal_delete',      'uses' => 'StaffController@roleModalDelete'])->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::get('/',                ['as' => 'church_staff',      'uses' => 'StaffController@index'])        ->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::get('/create',         ['as' => 'church_staff_create',      'uses' => 'StaffController@create'])        ->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::post('/store',         ['as' => 'church_staff_store',      'uses' => 'StaffController@store'])        ->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::get('/edit/{id}',         ['as' => 'church_staff_edit',      'uses' => 'StaffController@edit'])        ->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::post('/update/{id}',         ['as' => 'church_staff_update',      'uses' => 'StaffController@update'])        ->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::get('/destroy/{id}',         ['as' => 'church_staff_destroy',      'uses' => 'StaffController@destroy'])        ->where('slug', '([A-Za-z0-9\-\/]+)');
                });


                Route::group(['prefix' => 'family'],function(){
                    Route::get('/{id}/family-member/create',   ['as' => 'family_member_create', 'uses' => 'FamilyController@family_member_create']) ->where('slug', '([A-Za-z0-9\-\/]+)');

                    Route::get('/delete/{id}',                 ['as' => 'post_family_delete', 'uses' => 'FamilyController@family_delete']);
                    Route::get('/edit/{id}',                   ['as' => 'family_edit', 'uses' => 'FamilyController@family_edit'])                   ->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::get('/view/{id}',                   ['as' => 'family_view', 'uses' => 'FamilyController@family_view'])   ->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::get('/create',                      ['as' => 'family_create', 'uses' => 'FamilyController@family_create'])               ->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::get('/{id}',                        ['as' => 'family_member_index', 'uses' => 'FamilyController@family_member_index'])   ->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::get('/',                            ['as' => 'family_index', 'uses' => 'FamilyController@family_index'])                 ->where('slug', '([A-Za-z0-9\-\/]+)');

                    Route::group(['prefix' => 'family-member'],function(){
                        Route::get('/searchajax',          ['as' => 'searchajax','uses'=>'FamilyController@autoComplete'])                           ->where('slug', '([A-Za-z0-9\-\/]+)');
                        Route::get('/edit/{id}',          ['as' => 'family_member_edit', 'uses' => 'FamilyController@family_member_edit'])         ->where('slug', '([A-Za-z0-9\-\/]+)');
                        Route::get('/{id}',               ['as' => 'family_member_view', 'uses' => 'FamilyController@family_member_view'])         ->where('slug', '([A-Za-z0-9\-\/]+)');
                    });
                });
                Route::group(['prefix' => 'email-group'],function(){

                    Route::get('/{id}/members/create',            ['as' => 'create_email_group_member', 'uses' => 'EmailController@create_email_group_member']) ->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::get('/{id}/members/create/filter',            ['as' => 'create_email_group_member', 'uses' => 'EmailController@create_email_group_member_filter']) ->where('slug', '([A-Za-z0-9\-\/]+)');
                    //Route::get('/{id}/members/create/filter/add-member/{user_id}',            ['as' => 'save_filter_email_group_member', 'uses' => 'EmailController@save_email_group_member_filter_single']) ->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::get('/delete/{id}',                    ['as' => 'delete_email_group', 'uses' => 'EmailController@delete_email_group']);
                    Route::get('/edit/{id}',                      ['as' => 'edit_email_group', 'uses' => 'EmailController@edit_email_group'])                   ->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::get('/create',                         ['as' => 'create_email_group', 'uses' => 'EmailController@create_email_group'])    ->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::post('/send-to-group/{id}',                           ['as' => 'send_email_group', 'uses' => 'EmailController@sendEmailGroup'])->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::post('/send-to-individual/{id}',                           ['as' => 'send_email_individual', 'uses' => 'EmailController@sendEmailIndividual'])->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::post('/send-to-multiple/{id}',                           ['as' => 'send_email_multiple', 'uses' => 'EmailController@sendEmailMultiple'])->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::get('/{id}',                           ['as' => 'index_email_group_member', 'uses' => 'EmailController@index_email_group_member'])   ->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::get('/',                               ['as' => 'index_email_group', 'uses' => 'EmailController@index_email_group'])       ->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::group(['prefix' => '{id}/members'],function(){
                        // Route::get('/{id}/family-member/create',   ['as' => 'family_member_create', 'uses' => 'FamilyController@family_member_create']) ->where('slug', '([A-Za-z0-9\-\/]+)');
                        Route::get('/delete/{mid}',                    ['as' => 'delete_email_group_member', 'uses' => 'EmailController@delete_email_group_member']);
                        Route::get('/edit/{mid}',                      ['as' => 'edit_email_group_member', 'uses' => 'EmailController@edit_email_group_member'])                   ->where('slug', '([A-Za-z0-9\-\/]+)');
                        // Route::get('/create',                         ['as' => 'create_email_group', 'uses' => 'EmailController@create_email_group'])    ->where('slug', '([A-Za-z0-9\-\/]+)');
                        // // Route::get('/{id}',                        ['as' => 'family_member_index', 'uses' => 'FamilyController@family_member_index'])   ->where('slug', '([A-Za-z0-9\-\/]+)');
                        // Route::get('/',                               ['as' => 'index_email_group', 'uses' => 'EmailController@index_email_group'])       ->where('slug', '([A-Za-z0-9\-\/]+)');
                    });
                });
                    Route::get('/restore/{id}',             ['middleware' => 'auth','as' => 'restore_defaults',        'uses' => 'OrganizationController@restoreDefault'])      ->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::get('/donation-list',             ['as' => 'donation_list', 'uses' => 'DonationListController@admin_donationlist'])     ->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::get('/donation-category',             ['as' => 'donation_category', 'uses' => 'DonationCategoryController@admin_donationcat'])     ->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::get('/members',              ['as' => 'church_members',        'uses' => 'MembersController@index'])      ->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::get('/role',                 ['as' => 'role',        'uses' => 'RoleController@index'])      ->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::any('/logs',                 ['as' => 'admin_logs',        'uses' => 'ActivityLogController@adminlogs'])      ->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::any('/logs/timezone',                 ['as' => 'admin_logs_post',        'uses' => 'ActivityLogController@adminlogspost'])      ->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::get('/reports',              ['middleware' => 'auth','as' => '',        'uses' => 'OrganizationController@reports'])      ->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::get('/backup',               ['middleware' => 'auth','as' => '',        'uses' => 'OrganizationController@backupIndex'])      ->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::get('/backup/download',               ['middleware' => 'auth','as' => '',        'uses' => 'OrganizationController@backup'])      ->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::get('/settings',             ['middleware' => 'auth','as' => 'settings',        'uses' => 'OrganizationController@settings'])      ->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::post('/settings/update',     ['middleware' => 'auth','as' => 'settings_update',        'uses' => 'OrganizationController@settingsUpdate'])      ->where('slug', '([A-Za-z0-9\-\/]+)');
                    Route::get('/',                     ['as' => 'church_get_login',      'uses' => 'ChurchController@login'])       ->where('slug', '([A-Za-z0-9\-\/]+)');
            });

            //Church
            Route::group(['prefix' => 'donations'],function()
            {
                // Route::get('payment-info',          ['uses' => 'ChurchController@insertcreditcard'])->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::get('remove-cart-item/{id}', ['uses' => 'DonationController@removeCartitem'])->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::get('edit-cart-item/{id}',   ['uses' => 'DonationController@editCartitem'])->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::get('update-cart-item/{id}', ['uses' => 'DonationController@updateCartitem'])->where('slug', '([A-Za-z0-9\-\/]+)');
                Route::get('clear-cart-item',       ['uses' => 'DonationController@clearCart'])->where('slug', '([A-Za-z0-9\-\/]+)');
            });

            Route::get('/volunteer/view-details/{id}',              ['as' => 'church_event_view_details',           'uses' => 'EventController@viewdetails']) ->where('slug', '([A-Za-z0-9\-\/]+)');
            /* Route::get('/volunteer',                                ['uses' => 'ChurchController@volunteer'])          ->where('slug', '([A-Za-z0-9\-\/]+)');*/
            Route::get('/logout',            ['uses' => 'ChurchController@logout'])            ->where('slug', '([A-Za-z0-9\-\/]+)');
            Route::get('/donations',         ['uses' => 'ChurchController@donate'])           ->where('slug', '([A-Za-z0-9\-\/]+)');
            Route::get('/donation',         ['uses' => 'ChurchController@donationnew'])           ->where('slug', '([A-Za-z0-9\-\/]+)');
            Route::get('/guest/dashboard',         ['uses' => 'ChurchController@guest_dashboard'])           ->where('slug', '([A-Za-z0-9\-\/]+)');
            Route::post('/donationonetime/{id}',         ['uses' => 'ChurchController@onetimedonation'])           ->where('slug', '([A-Za-z0-9\-\/]+)');
            Route::get('/donationrecurring/{id}',         ['uses' => 'ChurchController@recurringdonation'])           ->where('slug', '([A-Za-z0-9\-\/]+)');
            Route::get('/events',            ['uses' => 'ChurchController@calendar'])          ->where('slug', '([A-Za-z0-9\-\/]+)');
            Route::get('/events-list',       ['as' => 'event_list', 'uses' => 'ChurchController@eventList'])          ->where('slug', '([A-Za-z0-9\-\/]+)');
            // Route::get('/home',              ['as' => 'church', 'uses' => 'ChurchController@index'])->where('slug', '([A-Za-z0-9\-\/]+)');
            Route::get('/logout',            ['uses' => 'UserAuthController@logout'])                                         ->where('slug', '([A-Za-z0-9\-\/]+)');
            Route::get('/register',          ['as' => 'get_user_register', 'uses' => 'UserAuthController@create'])            ->where('slug', '([A-Za-z0-9\-\/]+)');
            Route::get('/login',             ['uses' => 'UserAuthController@userLogin'])                                      ->where('slug', '([A-Za-z0-9\-\/]+)');
            Route::get('/home',              ['as' => 'church', 'uses' => 'ChurchController@indexHome'])->where('slug', '([A-Za-z0-9\-\/]+)');
            Route::get('/',              ['as' => 'church', 'uses' => 'ChurchController@index'])->where('slug', '([A-Za-z0-9\-\/]+)');
            //Church
        });
    });
    #Route::post('/one_time_add', ['uses' => 'DonationController@oneTimeAdd']);

    // For Volunteers
    Route::post('/volunteer_apply', ['as' => 'volunteer_apply','uses' => 'VolunteerController@apply']);
    Route::post('/volunteer_add', ['as' => 'volunteer_add','uses' => 'VolunteerController@add']);
    Route::get('/volunteer_table', ['as' => 'volunteer_table','uses' => 'VolunteerController@volunteerGroupTable']);
    Route::get('/volunteer_group_list', ['as' => 'volunteer_group_list','uses' => 'VolunteerController@volunteerGroupList']);
    Route::get('/volunteer_main_table', ['as' => 'volunteer_main_table','uses' => 'VolunteerController@volunteerGroupMainTable']);
    Route::get('/volunteer_detail_table', ['as' => 'volunteer_detail_table','uses' => 'VolunteerController@volunteerDetailTable']);
    Route::get('/volunteer_delete/{id}', ['as' => 'volunteer_delete','uses' => 'VolunteerController@delete']);
    Route::post('/vg_per_occurrence/', ['as' => 'vg_per_occurrence','uses' => 'VolunteerController@vg_per_occurrence']);
    
    Route::post('/event_modal_details/', ['as' => 'event_modal_details','uses' => 'ChurchController@eventModalDetails']);
    Route::post('/volunteer_group_modal_details/', ['as' => 'volunteer_group_modal_details','uses' => 'ChurchController@volunteerGroupModalDetails']);
    // Route::post('/generate_volunteer_group/{id}', ['as' => 'generate_volunteer_group','uses' => 'EventController@generateVolunteerGroup']);
    Route::post('/generate_volunteer_group/{id}', ['as' => 'generate_volunteer_group','uses' => 'EventController@generateVolunteerGroup']);
    /*Route::post('/filter_events_by_role/{needs_volunteers?}/{filter?}/{display_table?}',['as' => 'filter_events_by_role','uses' => 'ChurchController@filterEventsByRole']);
    Route::post('/filter_events_by_slots/{needs_volunteers?}/{filter?}/{display_table?}',['as' => 'filter_events_by_slots','uses' => 'ChurchController@filterEventsBySlots']);
    */


    Route::post('/filter_events/{display_table?}/{slug?}',['as' => 'filter_events','uses' => 'ChurchController@filterEvents']);
    Route::post('/get_volunteer_form',['as' => 'get_volunteer_form','uses' => 'ChurchController@getVolunteerForm']);
    Route::post('/volunteer_unique_email/{id?}',['as' => 'volunteer_unique_email','uses' => 'ChurchController@checkUniqueVolunteerEmail']);
    Route::post('/filter-pages/',['as' => 'filter_pages','uses' => 'PageController@filterPages']);
    Route::post('/all-pages/',['as' => 'all_pages','uses' => 'PageController@allPages']);
    // volunteer listing
    Route::get('/events_table',['as' => 'events_table', 'uses' => 'VolunteerController@events_table']);

    //staff
    Route::post('/change_status',['as' => 'change_status', 'uses' => 'StaffController@changeStaffStatus']);
    Route::post('/change_volunteer_status',['as' => 'change_volunteer_status', 'uses' => 'VolunteerController@changeVolunteerStatus']);
    Route::post('/change_volunteer_group_status',['as' => 'change_volunteer_group_status', 'uses' => 'VolunteerController@changeVolunteerGroupStatus']);
    Route::get('/permission',['as' => 'permission', 'uses' => 'PermissionListController@index']);
    Route::get('{slug}', ['uses' => 'MainController@getPage'])->where('slug', '([A-Za-z0-9\-\/]+)');


                /*
                    Temporary order displacement
                    -- Form HERE
                */
Route::get("/qb_connect/{organization_id}",['as' => 'qb_connect',function($organization_id){
    $oauth = QB::where("organization_id",$organization_id)->first();
    $oauth_url = route("qb_connect",$organization_id);
    $success_url = route("qb_success",$organization_id);
    Connection::start($organization_id,$oauth->qb_consumer_key,$oauth->qb_consumer_secret,$oauth_url,$success_url);
    //Connection::start();
}]);

Route::get("qb_success/{organization_id?}",['as' => 'qb_success',function($organization_id = null){
    return view("auth.quickbooks.success");
}]);


Route::get('qb_disconnect', function () {
    Connection::stop();
});


Route::post("create_customer",['as' => 'create_customer',function(Request $request){
    loadSDK();

//    $requestValidator = new OAuthRequestValidator(ConfigurationManager::AppSettings('AccessToken'),
//        ConfigurationManager::AppSettings('AccessTokenSecret'),
//        ConfigurationManager::AppSettings('ConsumerKey'),
//        ConfigurationManager::AppSettings('ConsumerSecret'));

    $oauth = QB::where("organization_id",$request->organization_id)->first();
    //$serviceType = $oauth->qb_company_id?IntuitServicesType::QBD:IntuitServicesType::QBO;
    $serviceType = IntuitServicesType::QBO;
    //$realmId = $oauth->qb_company_id?$oauth->qb_company_id:ConfigurationManager::AppSettings('RealmID');
    //123145751746754
    $realmId = $oauth->qb_company_id?$oauth->qb_company_id:$oauth->qb_sandbox_company_id;
    $requestValidator = new OAuthRequestValidator($oauth->oauth_access_token,
        $oauth->oauth_access_token_secret,
        $oauth->qb_consumer_key,
        $oauth->qb_consumer_secret);


    $serviceContext = new ServiceContext($realmId, $serviceType, $requestValidator);
    $dataService = new DataService($serviceContext);
    $entities = $dataService->Query("Select * From Customer");
    if(isset($entities)){
        foreach($entities as $customer){
            if($request->user_id != "" && ($customer->UserId == $request->user_id)){
                echo $customer->Id;
                exit();
            }
        }
    }

    $customerObj = new IPPCustomer();
    $customerObj->Name = $request->name;
    $customerObj->CompanyName = $request->company_name;
    $customerObj->GivenName = $request->given_name;
    $customerObj->DisplayName = $request->display_name;
    $customerObj->UserId = isset($request->user_id)?$request->user_id:null;
    $resultingCustomerObj = $dataService->Add($customerObj);
    echo $resultingCustomerObj->Id;

}]);


Route::post("create_invoice",['as' => 'create_invoice',function(Request $request){

    loadSDK();
    $oauth = QB::where("organization_id",$request->organization_id)->first();
    $serviceType = IntuitServicesType::QBO;
    $realmId = $oauth->qb_company_id?$oauth->qb_company_id:$oauth->qb_sandbox_company_id;
    $requestValidator = new OAuthRequestValidator($oauth->oauth_access_token,
        $oauth->oauth_access_token_secret,
        $oauth->qb_consumer_key,
        $oauth->qb_consumer_secret);
    $serviceContext = new ServiceContext($realmId, $serviceType, $requestValidator);
    $dataService = new DataService($serviceContext);

//    $invoice = new IPPInvoice();
//    $invoice->DocNumber=$request->doc_number;
//    $IPPLine = new IPPLine();
//    $IPPId = new IPPReferenceType();
//    $IPPId->value = $request->id;
//    $IPPLine->Id = $IPPId;
//    $IPPLine->LineNum = 1;
//    $IPPLine->Description = $request->description;
//    $IPPLine->Amount = $request->amount;
//    $enum = new IPPLineDetailTypeEnum();
//    $enum->value = "SalesItemLineDetail";
//    $IPPLine->DetailType = $enum;
//    $SalesItemLineDetail = new IPPSalesItemLineDetail();
////    $itemRef = new IPPReferenceType();
////    $itemRef->name = "Services";
////    $itemRef->value = $id;
//    $SalesItemLineDetail->ItemRef = '1';
//    $IPPLine->SalesItemLineDetail = $SalesItemLineDetail;
//    $invoice->Line = $IPPLine;
//    $customerRef = new IPPReferenceType();
//    $customerRef->value = $request->customer_id;
//    $invoice->CustomerRef = $customerRef;

    $invoice = new IPPInvoice();
    $IPPLine = new IPPLine();
    $IPPLine->Amount = $request->amount;
    $IPPLine->DetailType = "SalesItemLineDetail";
    $SalesItemLineDetail = new IPPSalesItemLineDetail();
    $itemRef = new IPPReferenceType();
    $itemRef->name = "Services";
    $itemRef->value = '1';
    $SalesItemLineDetail->ItemRef = '1';
    $IPPLine->SalesItemLineDetail = $SalesItemLineDetail;
    $invoice->Line = $IPPLine;
    $invoice->CustomerRef = $request->customer_id;
    $resultingInvoiceObj = $dataService->Add($invoice);

    $payment = new IPPPayment();
    $payment->PaymentRefNum = rand(1,99).rand(1,999).rand(1,9999);
    //$payment->PaymentTypeSpecified = true;
    $payment->UnappliedAmt = 0;
    $payment->TotalAmt = $request->amount;
    $payment->ProcessPayment = false;
    $payment->ProcessPaymentSpecified = true;
    $payment->TxnDateSpecified = true;

    $payment_line = new IppLine();
    $payment_line->Amount = $request->amount;
    $payment_line->AmountSpecified = true;
    $payment_line->DetailType = "PaymentLineDetail";

    $line_txn = new IPPLinkedTxn();
    $line_txn->TxnId = $resultingInvoiceObj->Id;
    $line_txn->TxnType = "Invoice";
    $payment_line->LinkedTxn = $line_txn;

    $payment->Line = $payment_line;
    $payment->CustomerRef = $request->customer_id;
    $resultingPaymentObj = $dataService->Add($payment);

}]);

Route::post('/qb_save',     ['as' => 'qb_save','uses' => 'QuickBooksController@save']);


Route::get('/session',['uses' => 'MainController@session']);
Route::group(['prefix' => 'api'],function(){
    Route::get('/organizations', ['uses' => 'OrganizationController@index']);

    Route::group(["prefix" => "organization"], function(){
        Route::post('login', ['as' => 'post_user_login', 'uses' => 'UserAuthController@postLogin']);
        Route::post('multiple-login', ['as' => 'post_multiple_user_login', 'uses' => 'UserAuthController@multipleLogin']);
        Route::post('family-member/update/{id}', ['uses' => 'FamilyController@family_member_update']);
    });
});
                /*
                    Temporary order displacement
                    -- Until HERE
                */
});
