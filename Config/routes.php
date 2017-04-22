<?php
use NoahBuscher\Macaw\Macaw as Route;

//Route::get('/', 'HomeController@home');

Route::get('/foo', function() {
    echo "Foo!";
});

Route::get('/home', "App\\Controller\\HomeController@home");



Route::dispatch();