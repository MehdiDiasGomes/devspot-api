<?php

use App\Providers\AppServiceProvider;
use App\Providers\AuthServiceProvider;
use App\Providers\JobApplicationServiceProvider;
use App\Providers\JobOfferServiceProvider;

return [
    AppServiceProvider::class,
    AuthServiceProvider::class,
    JobOfferServiceProvider::class,
    JobApplicationServiceProvider::class,
];
