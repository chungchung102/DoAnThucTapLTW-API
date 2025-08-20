<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('*', function ($view) {
            $cart = session('cart', []);
            $count = 0;
            foreach ($cart as $item) {
                $count += $item['quantity'] ?? 1;
            }
            $view->with('cartCount', $count);
        });
    }
}
