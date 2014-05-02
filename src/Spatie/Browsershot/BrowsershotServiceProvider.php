<?php namespace Spatie\Browsershot;

use Illuminate\Support\ServiceProvider;

class BrowsershotServiceProvider extends ServiceProvider {

    public function register()
    {
        $this->app->bind(
            'browsershot',
            'Spatie\Browsershot'
        );
    }

}