<?php

namespace App\Providers;

use Laravel\Horizon\Horizon;
use Laravel\Horizon\HorizonApplicationServiceProvider;

/**
 * Custom Horizon service provider that overrides the default authorization logic.
 *
 * This implementation disables access control to the Horizon dashboard by always allowing access.
 * **Note:** This should not be used in production without proper access restrictions.
 */
class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    /**
     * Boot the Horizon service provider.
     *
     * Registers a closure to bypass Horizon dashboard authentication, allowing unrestricted access.
     *
     * @return void
     */
    public function boot()
    {
        Horizon::auth(function ($request) {
            return true;
        });
    }
}
