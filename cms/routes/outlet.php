<?php

  Route::namespace('API')->name('api.')->group(function () {
    Route::namespace('Outlet')->name('outlet.')->group(function () {
      /**
       * Login and Profile 
       */
      Route::get('/', 'OutletController@index');
      Route::get('/fetch-country-cities', 'OutletController@fetchCities')->name('country.fetchCities');
      Route::post('/store', 'OutletController@store')->name('profile.store');
      Route::get('/profile/{outlet}', 'OutletController@show')->name('profile');
      Route::post('/update/{outlet}', 'OutletController@update')->name('profile.update');
      Route::get('/fetch-suppliers/{outlet}', 'OutletController@suppliers')->name('suppliers');
      /**
       * Orders
       */
      Route::get('/supplier-orders/{client}', 'OutletOrderController@index')->name('order.fetch');
      Route::post('/create-order/{outlet}', 'OutletOrderController@store')->name('order.store');
      Route::post('/update-order/{order}', 'OutletOrderController@update')->name('order.update');
      Route::post('/delete-order/{order}', 'OutletOrderController@destroy')->name('order.delete');
      /**
       * Accounting 
       */
      Route::get('/accounting-info/{client}', 'AccountController@index')->name('accounts.fetch');
      /**
       * Payments / Collection
       */
      Route::get('/payment-info/{client}', 'CollectionController@index')->name('payments.fetch');

      //schemes

        Route::post('/schemes-list', 'OutletController@schemesForRetailerApp');
    });
  });


