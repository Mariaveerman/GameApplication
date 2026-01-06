<?php

use Illuminate\Support\Facades\Route;
use function Laravel\Prompts\text;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/game', function () {

    return GameResource::find(1)

        ->toResource()

        ->response();

});

