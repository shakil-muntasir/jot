<?php

Auth::routes();

Route::get('/lgout', function () {
    request()->session()->invalidate();
});

Route::get('/{any}', 'AppController@index')->where('any', '.*');

