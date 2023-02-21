<?php

declare(strict_types=1);

use Dreitier\Streamline\Authentication\Facades\StreamlineAuthentication;
use Illuminate\Support\Facades\Route;

// put everything in the web middleware so that we have our session. otherwise, the user would not be logged in
Route::middleware(['web'])
    ->prefix('/sign-in')
    ->group(function () {
        StreamlineAuthentication::defaultRoutes();
    });
