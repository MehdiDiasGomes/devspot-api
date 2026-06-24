<?php

use App\Providers\AppServiceProvider;
use App\Providers\JobApplicationServiceProvider;
use App\Providers\JobOfferServiceProvider;

return [
    AppServiceProvider::class,
    JobOfferServiceProvider::class,
    JobApplicationServiceProvider::class,
];
