<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\Discord\DiscordExtendSocialite;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        \App\Models\LeaveRequest::observe(\App\Observers\LeaveRequestObserver::class);
        \App\Models\Holiday::observe(\App\Observers\HolidayObserver::class);
        \App\Models\User::observe(\App\Observers\UserObserver::class);

        Event::listen(SocialiteWasCalled::class, DiscordExtendSocialite::class);
    }
}
