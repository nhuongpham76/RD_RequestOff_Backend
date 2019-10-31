<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            \App\Repositories\UserRepositoryInterface::class,
            \App\Repositories\Eloquent\UserRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /**
         * Validate phone
         */
        app('validator')->extend('phone_number', function ($attribute, $value, $parameters, $validator) {
            $regex = '/^([0-9\(\)\+ .-]{0,20})$/';
            $result = preg_match($regex, $value);
            if ($result === 1 || is_null($value)) {
                return true;
            }

            return false;
        });
    }
}
