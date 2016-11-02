<?php
use Illuminate\Session\TokenMismatchException;

/*
  |--------------------------------------------------------------------------
  | Public side (no auth required)
  |--------------------------------------------------------------------------
  |
*/

/**
 * User login and logout
 */
Route::group(['middleware' => ['web']], function ()
{

    Route::get('/admin/login', [
            "as"   => "user.admin.login",
            "uses" => 'LaravelAcl\Authentication\Controllers\AuthController@getAdminLogin'
    ]);

    Route::get('/admin', function () {
        return redirect()->route('user.admin.login');
    });

    Route::get('/login', [
            "as"   => "user.login",
            "uses" => 'LaravelAcl\Authentication\Controllers\AuthController@getClientLogin'
    ]);
    Route::get('/user/logout', [
            "as"   => "user.logout",
            "uses" => 'LaravelAcl\Authentication\Controllers\AuthController@getLogout'
    ]);
    Route::post('/user/login', [
            "uses" => 'LaravelAcl\Authentication\Controllers\AuthController@postAdminLogin',
            "as"   => "user.login.process"
    ]);
    Route::post('/login', [
            "uses" => 'LaravelAcl\Authentication\Controllers\AuthController@postClientLogin',
            "as"   => "user.login"
    ]);

    /**
     * Password recovery
     */
    Route::get('/user/change-password', [
            "as"   => "user.change-password",
            "uses" => 'LaravelAcl\Authentication\Controllers\AuthController@getChangePassword'
    ]);
    Route::get('/user/recovery-password', [
            "as"   => "user.recovery-password",
            "uses" => 'LaravelAcl\Authentication\Controllers\AuthController@getReminder'
    ]);
    Route::post('/user/change-password/', [
            'uses' => 'LaravelAcl\Authentication\Controllers\AuthController@postChangePassword',
            "as"   => "user.reminder.process"
    ]);

    Route::get('/user/change-password-success', [
                    "uses" => function ()
                    {
                        return view('laravel-authentication-acl::client.auth.change-password-success');
                    },
                    "as"   => "user.change-password-success"
            ]
    );
    Route::post('/user/reminder', [
            'uses' => 'LaravelAcl\Authentication\Controllers\AuthController@postReminder',
            "as"   => "user.reminder"
    ]);
    Route::get('/user/reminder-success', [
            "uses" => function ()
            {
                return view('laravel-authentication-acl::client.auth.reminder-success');
            },
            "as"   => "user.reminder-success"
    ]);

    /**
     * User signup
     */
    Route::post('/user/signup', [
            'uses' => 'LaravelAcl\Authentication\Controllers\UserController@postSignup',
            "as"   => "user.signup.process"
    ]);
    Route::get('/user/signup', [
            'uses' => 'LaravelAcl\Authentication\Controllers\UserController@signup',
            "as"   => "user.signup"
    ]);
    Route::post('captcha-ajax', [
            "before" => "captcha-ajax",
            'uses'   => 'LaravelAcl\Authentication\Controllers\UserController@refreshCaptcha',
            "as"     => "user.captcha-ajax.process"
    ]);
    Route::get('/user/email-confirmation', [
            'uses' => 'LaravelAcl\Authentication\Controllers\UserController@emailConfirmation',
            "as"   => "user.email-confirmation"
    ]);
    Route::get('/user/signup-success', [
            "uses" => 'LaravelAcl\Authentication\Controllers\UserController@signupSuccess',
            "as"   => "user.signup-success"
    ]);

    /*
      |--------------------------------------------------------------------------
      | Admin side (auth required)
      |--------------------------------------------------------------------------
      |
      */
    Route::group(['middleware' => ['admin_logged', 'can_see']], function ()
    {
        /**
         * dashboard
         */
        Route::get('/admin/users/dashboard', [
                'as'   => 'dashboard.default',
                'uses' => 'LaravelAcl\Authentication\Controllers\DashboardController@base'
        ]);

        /**
         * user
         */
        Route::get('/admin/users/list', [
                'as'   => 'users.list',
                'uses' => 'LaravelAcl\Authentication\Controllers\UserController@getList'
        ]);
        Route::get('/admin/users/edit', [
                'as'   => 'users.edit',
                'uses' => 'LaravelAcl\Authentication\Controllers\UserController@editUser'
        ]);
        Route::post('/admin/users/edit', [
                'as'   => 'users.edit',
                'uses' => 'LaravelAcl\Authentication\Controllers\UserController@postEditUser'
        ]);
        Route::get('/admin/users/delete', [
                'as'   => 'users.delete',
                'uses' => 'LaravelAcl\Authentication\Controllers\UserController@deleteUser'
        ]);
        Route::post('/admin/users/groups/add', [
                'as'   => 'users.groups.add',
                'uses' => 'LaravelAcl\Authentication\Controllers\UserController@addGroup'
        ]);
        Route::post('/admin/users/groups/delete', [
                'as'   => 'users.groups.delete',
                'uses' => 'LaravelAcl\Authentication\Controllers\UserController@deleteGroup'
        ]);
        Route::post('/admin/users/editpermission', [
                'as'   => 'users.edit.permission',
                'uses' => 'LaravelAcl\Authentication\Controllers\UserController@editPermission'
        ]);
        Route::get('/admin/users/profile/edit', [
                'as'   => 'users.profile.edit',
                'uses' => 'LaravelAcl\Authentication\Controllers\UserController@editProfile'
        ]);
        Route::post('/admin/users/profile/edit', [
                'as'   => 'users.profile.edit',
                'uses' => 'LaravelAcl\Authentication\Controllers\UserController@postEditProfile'
        ]);
        Route::post('/admin/users/profile/addField', [
                'as'   => 'users.profile.addfield',
                'uses' => 'LaravelAcl\Authentication\Controllers\UserController@addCustomFieldType'
        ]);
        Route::post('/admin/users/profile/deleteField', [
                'as'   => 'users.profile.deletefield',
                'uses' => 'LaravelAcl\Authentication\Controllers\UserController@deleteCustomFieldType'
        ]);
        Route::post('/admin/users/profile/avatar', [
                'as'   => 'users.profile.changeavatar',
                'uses' => 'LaravelAcl\Authentication\Controllers\UserController@changeAvatar'
        ]);
        Route::get('/admin/users/profile/self', [
                'as'   => 'users.selfprofile.edit',
                'uses' => 'LaravelAcl\Authentication\Controllers\UserController@editOwnProfile'
        ]);

        /**
         * groups
         */
        Route::get('/admin/roles', [
                'as'   => 'groups.list',
                'uses' => 'LaravelAcl\Authentication\Controllers\GroupController@getList'
        ]);
     /*   Route::get('/admin/groups/edit', [
                'as'   => 'groups.edit',
                'uses' => 'LaravelAcl\Authentication\Controllers\GroupController@editGroup'
        ]);*/
        Route::post('/admin/groups/edit', [
                'as'   => 'groups.edit',
                'uses' => 'LaravelAcl\Authentication\Controllers\GroupController@postEditGroup'
        ]);
      /*  Route::get('/admin/groups/delete', [
                'as'   => 'groups.delete',
                'uses' => 'LaravelAcl\Authentication\Controllers\GroupController@deleteGroup'
        ]);*/
        Route::post('/admin/groups/editpermission', [
                'as'   => 'groups.edit.permission',
                'uses' => 'LaravelAcl\Authentication\Controllers\GroupController@editPermission'
        ]);
        Route::post('/admin/groups/getpermissions', [
                'as'   => 'groups.get_permissions',
                'uses' => 'LaravelAcl\Authentication\Controllers\GroupController@getPermissions'
        ]);

        /**
         * permissions
         */
        Route::get('/admin/permissions/list', [
                'as'   => 'permission.list',
                'uses' => 'LaravelAcl\Authentication\Controllers\PermissionController@getList'
        ]);
        Route::get('/admin/permissions/edit', [
                'as'   => 'permission.edit',
                'uses' => 'LaravelAcl\Authentication\Controllers\PermissionController@editPermission'
        ]);
        Route::post('/admin/permissions/edit', [
                'as'   => 'permission.edit',
                'uses' => 'LaravelAcl\Authentication\Controllers\PermissionController@postEditPermission'
        ]);
        Route::get('/admin/permissions/delete', [
                'as'   => 'permission.delete',
                'uses' => 'LaravelAcl\Authentication\Controllers\PermissionController@deletePermission'
        ]);

         /**
         * Cms Pages
         */
        Route::get('/admin/pages/list', [
                'as'   => 'pages.list',
                'uses' => 'LaravelAcl\Authentication\Controllers\PagesController@getList'
        ]);
        Route::get('/admin/pages/edit', [
                'as'   => 'pages.edit',
                'uses' => 'LaravelAcl\Authentication\Controllers\PagesController@editPage'
        ]);
        Route::post('/admin/pages/edit', [
                'as'   => 'pages.edit',
                'uses' => 'LaravelAcl\Authentication\Controllers\PagesController@postEditPage'
        ]);
        Route::get('/admin/pages/delete', [
                'as'   => 'pages.delete',
                'uses' => 'LaravelAcl\Authentication\Controllers\PagesController@deletePage'
        ]);

        /**
         * Plans
         */
        Route::get('/admin/plans/', [
                'as'   => 'plans.index',
                'uses' => 'LaravelAcl\Authentication\Controllers\PlansController@getList'
        ]);
        Route::get('/admin/plans/create', [
                'as'   => 'plans.create',
                'uses' => 'LaravelAcl\Authentication\Controllers\PlansController@createPlan'
        ]);
        Route::post('/admin/plans/create', [
                'as'   => 'plans.create',
                'uses' => 'LaravelAcl\Authentication\Controllers\PlansController@storePlan'
        ]);
        Route::get('/admin/plans/edit', [
                'as'   => 'plans.edit',
                'uses' => 'LaravelAcl\Authentication\Controllers\PlansController@editPlan'
        ]);
        Route::post('/admin/plans/edit', [
                'as'   => 'plans.edit',
                'uses' => 'LaravelAcl\Authentication\Controllers\PlansController@postEditPlan'
        ]);
        Route::get('/admin/plans/delete', [
                'as'   => 'plans.delete',
                'uses' => 'LaravelAcl\Authentication\Controllers\PlansController@deletePlan'
        ]);

        /**
         * Currency Prices
         */
        Route::get('/admin/currency/', [
                'as'   => 'currency.index',
                'uses' => 'LaravelAcl\Authentication\Controllers\CurrencyController@getList'
        ]);
        Route::get('/admin/currency/create', [
                'as'   => 'currency.create',
                'uses' => 'LaravelAcl\Authentication\Controllers\CurrencyController@createCurrencyRate'
        ]);
        Route::post('/admin/currency/create', [
                'as'   => 'currency.create',
                'uses' => 'LaravelAcl\Authentication\Controllers\CurrencyController@storeCurrencyRate'
        ]);
        Route::get('/admin/currency/edit', [
                'as'   => 'currency.edit',
                'uses' => 'LaravelAcl\Authentication\Controllers\CurrencyController@editCurrencyRate'
        ]);
        Route::post('/admin/currency/edit', [
                'as'   => 'currency.edit',
                'uses' => 'LaravelAcl\Authentication\Controllers\CurrencyController@postEditCurrencyRate'
        ]);
        Route::get('/admin/currency/delete', [
                'as'   => 'currency.delete',
                'uses' => 'LaravelAcl\Authentication\Controllers\CurrencyController@deleteCurrencyRate'
        ]);
		
		/**
		 * School Routes
		 *
		 **/
		
		Route::get('/admin/schools/list/', [
                'as'   => 'schools.list',
                'uses' => 'LaravelAcl\Authentication\Controllers\SchoolsController@getList'
        ]);
		
		Route::get('/admin/schools/view/', [
                'as'   => 'schools.view',
                'uses' => 'LaravelAcl\Authentication\Controllers\SchoolsController@viewSchool'
        ]);
		
		Route::get('/admin/schools/edit', [
                'as'   => 'schools.edit',
                'uses' => 'LaravelAcl\Authentication\Controllers\SchoolsController@editSchool'
        ]);
		
		Route::get('/admin/schools/create', [
                'as'   => 'admin.schools.create',
                'uses' => 'LaravelAcl\Authentication\Controllers\SchoolsController@createSchool'
        ]);
		
		Route::post('/admin/schools/create', [
                'as'   => 'admin.schools.store',
                'uses' => 'LaravelAcl\Authentication\Controllers\SchoolsController@postCreateSchool'
        ]);
        Route::post('/admin/schools/edit', [
                'as'   => 'schools.edit',
                'uses' => 'LaravelAcl\Authentication\Controllers\SchoolsController@postEditSchool'
        ]);
        Route::get('/admin/schools/delete', [
                'as'   => 'schools.delete',
                'uses' => 'LaravelAcl\Authentication\Controllers\SchoolsController@deleteSchool'
        ]);


        /**
         * Subscription Routes
         *
         **/
        
        Route::get('/admin/subscription/list/', [
                'as'   => 'subscription.list',
                'uses' => 'LaravelAcl\Authentication\Controllers\SubscriptionController@getList'
        ]);
        
        Route::get('/admin/subscription/view/', [
                'as'   => 'subscription.view',
                'uses' => 'LaravelAcl\Authentication\Controllers\SubscriptionController@viewSubscription'
        ]);


        Route::get('/admin/subscription/edit', [
                'as'   => 'subscription.edit',
                'uses' => 'LaravelAcl\Authentication\Controllers\SubscriptionController@editSubscription'
        ]);
      
        Route::post('/admin/subscription/edit', [
                'as'   => 'subscription.edit',
                'uses' => 'LaravelAcl\Authentication\Controllers\SubscriptionController@postEditSubscription'
        ]);
        
        Route::get('/admin/subscription/delete', [
                'as'   => 'subscription.delete',
                'uses' => 'LaravelAcl\Authentication\Controllers\SubscriptionController@deleteSubscription'
        ]);
        

        // Manage Location by admin
        Route::get('/admin/schools/locations/list', [
                'as'   => 'schools.manage_location',
                'uses' => 'LaravelAcl\Authentication\Controllers\LocationsController@getList'
        ]);

        Route::get('/admin/schools/add_location', [
                'as'   => 'schools.add_location',
                'uses' => 'LaravelAcl\Authentication\Controllers\LocationsController@editLocation'
        ]);

        Route::post('/admin/schools/add_location', [
                'as'   => 'post.add_location',
                'uses' => 'LaravelAcl\Authentication\Controllers\LocationsController@postEditLocation'
        ]);

        Route::get('/admin/schools/delete_location', [
                'as'   => 'schools.delete_location',
                'uses' => 'LaravelAcl\Authentication\Controllers\LocationsController@deleteLocation'
        ]);

        /**
         * Age Categories
         */
        Route::get('/admin/category/', [
                'as'   => 'category.index',
                'uses' => 'LaravelAcl\Authentication\Controllers\CategoryController@getList'
        ]);
        Route::get('/admin/category/create', [
                'as'   => 'category.create',
                'uses' => 'LaravelAcl\Authentication\Controllers\CategoryController@createCategory'
        ]);
        Route::post('/admin/category/create', [
                'as'   => 'category.create',
                'uses' => 'LaravelAcl\Authentication\Controllers\CategoryController@storeCategory'
        ]);
        Route::get('/admin/category/edit', [
                'as'   => 'category.edit',
                'uses' => 'LaravelAcl\Authentication\Controllers\CategoryController@editCategory'
        ]);
        Route::post('/admin/category/edit', [
                'as'   => 'category.edit',
                'uses' => 'LaravelAcl\Authentication\Controllers\CategoryController@postEditCategory'
        ]);
        Route::get('/admin/category/delete', [
                'as'   => 'category.delete',
                'uses' => 'LaravelAcl\Authentication\Controllers\CategoryController@deleteCategory'
        ]);

        /**
         * Coupons
         */
        Route::get('/admin/coupons/', [
                'as'   => 'coupons.index',
                'uses' => 'LaravelAcl\Authentication\Controllers\CouponsController@getList'
        ]);
        Route::get('/admin/coupons/create', [
                'as'   => 'coupons.create',
                'uses' => 'LaravelAcl\Authentication\Controllers\CouponsController@createCoupon'
        ]);
        Route::post('/admin/coupons/create', [
                'as'   => 'coupons.create',
                'uses' => 'LaravelAcl\Authentication\Controllers\CouponsController@storeCoupon'
        ]);
        Route::get('/admin/coupons/edit', [
                'as'   => 'coupons.edit',
                'uses' => 'LaravelAcl\Authentication\Controllers\CouponsController@editCoupon'
        ]);
        Route::post('/admin/coupons/edit', [
                'as'   => 'coupons.edit',
                'uses' => 'LaravelAcl\Authentication\Controllers\CouponsController@postCoupon'
        ]);
        Route::get('/admin/coupons/delete', [
                'as'   => 'coupons.delete',
                'uses' => 'LaravelAcl\Authentication\Controllers\CouponsController@deleteCoupon'
        ]);


        Route::get('/admin/setting', [
                'as'   => 'setting.edit',
                'uses' => 'LaravelAcl\Authentication\Controllers\SettingController@editOptions'
        ]);

        Route::post('/admin/setting', [
                'as'   => 'setting.edit',
                'uses' => 'LaravelAcl\Authentication\Controllers\SettingController@postEditOptions'
        ]);

          /**
         * Slider
         */
        Route::get('/admin/slides/', [
                'as'   => 'slides.index',
                'uses' => 'LaravelAcl\Authentication\Controllers\SlidesController@getList'
        ]);
        Route::get('/admin/slides/create', [
                'as'   => 'slides.create',
                'uses' => 'LaravelAcl\Authentication\Controllers\SlidesController@createSlide'
        ]);
        Route::post('/admin/slides/create', [
                'as'   => 'slides.create',
                'uses' => 'LaravelAcl\Authentication\Controllers\SlidesController@storeSlide'
        ]);
        Route::get('/admin/slides/edit', [
                'as'   => 'slides.edit',
                'uses' => 'LaravelAcl\Authentication\Controllers\SlidesController@editSlide'
        ]);
        Route::post('/admin/slides/edit', [
                'as'   => 'slides.edit',
                'uses' => 'LaravelAcl\Authentication\Controllers\SlidesController@postEditSlide'
        ]);
        Route::get('/admin/slides/delete', [
                'as'   => 'slides.delete',
                'uses' => 'LaravelAcl\Authentication\Controllers\SlidesController@deleteSlide'
        ]);

    });
});
//////////////////// Automatic error handling //////////////////////////

if(Config::get('acl_base.handle_errors'))
{
    App::error(function (RuntimeException $exception, $code)
    {
        switch($code)
        {
            case '404':
                return view('laravel-authentication-acl::client.exceptions.404');
                break;
            case '401':
                return view('laravel-authentication-acl::client.exceptions.401');
                break;
            case '500':
                return view('laravel-authentication-acl::client.exceptions.500');
                break;
        }
    });

    App::error(function (TokenMismatchException $exception)
    {
        return view('laravel-authentication-acl::client.exceptions.500');
    });
}