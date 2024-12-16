<?php

namespace Modules\MultiSite\Entities;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
  protected $fillable = ['domain', 'type', 'theme', 'db_name', 'db_host', 'db_user', 'db_password'];

  public function hosting()
  {
    return $this->belongsTo(Hosting::class, 'hosting_id');
  }

  public function orders()
  {
    return $this->hasMany(Order::class, 'site_id');
  }
}
