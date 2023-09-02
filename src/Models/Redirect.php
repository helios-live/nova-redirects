<?php

namespace OptimistDigital\NovaRedirects\Models;

use OptimistDigital\NovaRedirects\Events\RedirectUpdated;
use Illuminate\Database\Eloquent\Model;

class Redirect extends Model
{
  protected $table = 'nova_redirects';

  protected $fillable = [
    'from_url',
    'to_url',
    'status_code',
  ];
  // emits
  protected $dispatchesEvents = [
    'created' => RedirectUpdated::class,
    'updated' => RedirectUpdated::class,
    'deleted' => RedirectUpdated::class,
  ];
}
