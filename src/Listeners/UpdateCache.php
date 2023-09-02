<?php

namespace OptimistDigital\NovaRedirects\Listeners;

use Illuminate\Support\Facades\Cache;
use OptimistDigital\NovaRedirects\Models\Redirect;

class UpdateCache
{

  /**
   * Create the event listener.
   */
  public function __construct()
  {
    //
  }

  /**
   * Handle the event.
   */
  public function handle($event): void
  {
    $redirects = Redirect::all();
    $redirects = $redirects->mapWithKeys(function ($redirect) {
      return [$redirect->from_url => [$redirect->to_url, $redirect->status_code]];
    })->toArray();
    Cache::forever('nova-redirects', $redirects);
  }
}
