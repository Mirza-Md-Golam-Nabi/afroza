<?php

use Illuminate\Support\Facades\Route;

Route::get('test', 'TestController@test')->name('test');

// For All User Controller
Route::get('/brand/fetch','GeneralController@brandFetch')->name('general.brand.fetch');
Route::get('/category/fetch','GeneralController@categoryFetch')->name('general.category.fetch');
Route::get('/product/check','GeneralController@productCheck')->name('general.product.check');
Route::get('/stock/check', 'GeneralController@stockCheck')->name('general.stock.check');

// Admin Controller

Route::group(['prefix'=>'admin'], function(){
    Route::get('/dashboard','AdminController@index')->name('admin.dashboard');  

    Route::group(['prefix'=>'type'], function(){
        Route::get('/create','TypeController@typeCreate')->name('admin.type.create');
        Route::post('/store','TypeController@typeStore')->name('admin.type.store');
        Route::get('/list','TypeController@typeList')->name('admin.type.list');
        Route::get('/edit/{id}','TypeController@typeEdit')->name('admin.type.edit');
        Route::post('/update','TypeController@typeUpdate')->name('admin.type.update');
    });

    Route::group(['prefix'=>'brand'], function(){
        Route::get('/create','BrandController@brandCreate')->name('admin.brand.create');
        Route::post('/store','BrandController@brandStore')->name('admin.brand.store');
        Route::get('/list','BrandController@brandList')->name('admin.brand.list');
        Route::get('/edit/{id}','BrandController@brandEdit')->name('admin.brand.edit');
        Route::post('/update','BrandController@brandUpdate')->name('admin.brand.update');
    });

    Route::group(['prefix'=>'category'], function(){
        Route::get('/create','CategoryController@categoryCreate')->name('admin.category.create');
        Route::post('/store','CategoryController@categoryStore')->name('admin.category.store');
        Route::get('/list','CategoryController@categoryList')->name('admin.category.list');
        Route::get('/edit/{id}','CategoryController@categoryEdit')->name('admin.category.edit');
        Route::post('/update','CategoryController@categoryUpdate')->name('admin.category.update');
    });

    Route::group(['prefix'=>'product'], function(){
        Route::get('/create','ProductController@productCreate')->name('admin.product.create');
        Route::post('/store','ProductController@productStore')->name('admin.product.store');
        Route::get('/list','ProductController@productList')->name('admin.product.list');
        Route::get('/edit-{id}','ProductController@productEdit')->name('admin.product.edit');
        Route::post('/update','ProductController@productUpdate')->name('admin.product.update');
        Route::get('/status-{id}','ProductController@productStatus')->name('admin.product.status');
        Route::get('/del','ProductController@delete')->name('admin.product.more.image.delete');
    });

    Route::group(['prefix'=>'stockin'], function(){
        Route::get('/create','StockinController@stockinCreate')->name('admin.stockin.create');
        Route::post('/store','StockinController@stockinStore')->name('admin.stockin.store');
        Route::get('/list/{date}','StockinController@stockinList')->name('admin.stockin.list');
        Route::get('/edit-{date}/{id}','StockinController@stockinEdit')->name('admin.stockin.edit');
        Route::post('/update','StockinController@stockinUpdate')->name('admin.stockin.update');
        Route::get('/date', 'StockinController@stockinDate')->name('admin.stockin.date');
    });
 
    Route::group(['prefix'=>'stockout'], function(){
        Route::get('/create', 'StockoutController@stockoutCreate')->name('admin.stockout.create');
        Route::post('/store', 'StockoutController@stockoutStore')->name('admin.stockout.store');
        Route::get('/list/{date}', 'StockoutController@stockoutList')->name('admin.stockout.list');
        Route::get('/edit-{date}/{id}', 'StockoutController@stockoutEdit')->name('admin.stockout.edit');
        Route::post('/update', 'StockoutController@stockoutUpdate')->name('admin.stockout.update');
        Route::get('/date', 'StockoutController@stockoutDate')->name('admin.stockout.date');
    });
 
    Route::group(['prefix'=>'stock'], function(){
        Route::get('/current', 'StockController@stockCurrent')->name('admin.stock.current');        
        Route::get('/history-{id}', 'StockController@stockHistory')->name('admin.stock.history');
        Route::get('/add', 'StockController@add')->name('admin.stock.add');
        Route::post('/store', 'StockController@store')->name('admin.stock.store');
    });

    Route::group(['prefix'=>'report'], function(){
        Route::get('/date','ReportController@dateReport')->name('admin.report.date');
        Route::get('/date-{date}', 'ReportController@dateDetailsReport')->name('admin.report.date.details');
        Route::get('/weekly', 'ReportController@weeklyReport')->name('admin.report.weekly');
        Route::get('/last-3-month', 'ReportController@last3MonthReport')->name('admin.report.last.3.month');
        Route::get('/product', 'ReportController@productList')->name('admin.report.product.list');
        Route::get('/monthly-{productId}', 'ReportController@monthlyReport')->name('admin.report.monthly');
        Route::get('/yearly', 'ReportController@yearlyReport')->name('admin.report.yearly');
        Route::get('/company', 'ReportController@companyReport')->name('admin.report.company');
        Route::get('/ajax', 'ReportController@ajaxReport')->name('admin.report.ajax');
    });

    Route::group(['prefix'=>'export'], function(){
        Route::get('/company', 'ExportController@companyReport')->name('admin.export.report.company');
    });
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

// Front End Controller

Route::group(['prefix'=>'user'], function(){
    Route::post('/store', 'RegisterController@userStore')->name('user.store');
    Route::get('/forget', 'RegisterController@passForget')->name('user.password.forget');
    Route::get('/check', 'RegisterController@mobileCheck')->name('user.mobile.check');
    Route::get('/new-pass','RegisterController@newPassword')->name('user.new.password');
    Route::post('/new-pass','RegisterController@newPasswordStore')->name('user.new.password.store');
});

Route::get('/','FrontendController@index')->name('welcome');
