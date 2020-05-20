<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/register', 'Api\AuthController@register');
Route::post('/login', 'Api\AuthController@login');

Route::post('/password/email', 'Api\ForgotPasswordController@sendResetLinkEmail');
Route::post('/password/reset', 'Api\ResetPasswordController@reset');


/*
|
| Resources controllers
|
*/
Route::namespace('Api')->group(function () {

    /**
     * EVENTS table management routes
     */
    // no auth req'd
    Route::get(   '/events',                 'EventController@index'   );
    Route::get(   '/events/{event}',         'EventController@show'    );
    Route::get(   '/events/status/{status}', 'EventController@byStatus');

    // only with authentication
    Route::post(  '/events',                 'EventController@store'  );
    Route::put(   '/events/{event}',         'EventController@update' );
    Route::patch( '/events/{event}',         'EventController@update' );
    Route::delete('/events/{event}',         'EventController@destroy');
    // set nextdate of a certain event (once an event is over)
    Route::patch( '/events/{event}/nextdate/{date}', 'EventController@updateNextdate');
    Route::patch( '/events/{event}/status/{status}', 'EventController@updateStatus'  );



    /**
     * SETTINGS table management routes
     */
    // only with authentication
    Route::get(   '/settings',                   'SettingController@index'  );
    Route::get(   '/settings/{setting}',         'SettingController@show'   );
    Route::post(  '/settings',                   'SettingController@store'  );
    Route::put(   '/settings/{id}',              'SettingController@update' );
    Route::patch( '/settings/{id}',              'SettingController@update' );
    Route::patch( '/settings/status/{status}',   'SettingController@updateStatus' );
    Route::delete('/settings/{id}',              'SettingController@destroy');




    /**
     * POWER logging routes
     */
    // get data for a certain time period (default: 1 hour)
    Route::get(   '/powerlog',                 'PowerLogController@index' );
    // get latest power data (no auth req'd)
    Route::get(   '/powerlog/latest',          'PowerLogController@latest' );
    // only with authentication
    Route::post(  '/powerlog',                 'PowerLogController@store'  );


    /**
     * TEMPerature logging routes
     */
    // get data for a certain time period (default: 1 hour)
    Route::get(   '/templog',                 'TempLogController@index' );
    // get latest power data (no auth req'd)
    Route::get(   '/templog/latest',          'TempLogController@latest' );
    // ADD a new record (only with authentication)
    Route::post(  '/templog',                 'TempLogController@store'  );



    /**
     * EVENT logging routes
     */
    // get data for a certain time period (default: 1 hour)
    Route::get(   '/eventlog',                 'EventLogController@index' );
    // get latest power data (no auth req'd)
    Route::get(   '/eventlog/latest',          'EventLogController@latest' );
    // only with authentication
    Route::post(  '/eventlog',                 'EventLogController@store'  );



    /**
     * BUILDING logging routes
     */
    // get data for a certain time period (default: 1 hour)
    Route::get(   '/buildinglog',                 'BuildingLogController@index' );
    // get latest Building data (no auth req'd)
    Route::get(   '/buildinglog/latest',          'BuildingLogController@latest');
    // only with authentication
    Route::post(  '/buildinglog',                 'BuildingLogController@store' );

});
