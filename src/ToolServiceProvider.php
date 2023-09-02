<?php

namespace OptimistDigital\NovaRedirects;

use Laravel\Nova\Nova;
use Laravel\Nova\Events\ServingNova;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use OptimistDigital\NovaRedirects\Nova\Redirect;
use OptimistDigital\NovaRedirects\Listeners\UpdateCache;
use OptimistDigital\NovaRedirects\Events\RedirectUpdated;
use OptimistDigital\NovaRedirects\Http\Middleware\Authorize;

class ToolServiceProvider extends ServiceProvider
{
  /**
   * Bootstrap any application services.
   *
   * @return void
   */
  public function boot()
  {
    $this->loadViewsFrom(__DIR__ . '/../resources/views', 'NovaRedirects');

    $this->publishes([
      __DIR__ . '/../database/migrations' => database_path('migrations'),
    ], 'migrations');

    $this->app->booted(function () {
      $this->routes();
    });

    Nova::resources([
      Redirect::class
    ]);

    // add event listener

    \Illuminate\Support\Facades\Event::listen(
      RedirectUpdated::class,
      UpdateCache::class
    );
  }

  /**
   * Register the tool's routes.
   *
   * @return void
   */
  protected function routes()
  {
    if ($this->app->routesAreCached()) {
      return;
    }

    Route::middleware(['nova', Authorize::class])
      ->prefix('nova-vendor/NovaRedirects')
      ->group(__DIR__ . '/../routes/api.php');

    $redirects = Cache::get('nova-redirects');
    if (is_null($redirects)) {
      event(new RedirectUpdated);
      $redirects = Cache::get('nova-redirects') ?? [];
    }
    foreach ($redirects as $from_url => $redirect) {
      Route::get($from_url, $this->redirect($redirect));
    }
  }

  /**
   * Register any application services.
   *
   * @return void
   */
  public function register()
  {
    //
  }

  protected function redirect($redirect)
  {
    return function () use ($redirect) {
      return redirect($redirect[0], $redirect[1]);
    };
  }
}
