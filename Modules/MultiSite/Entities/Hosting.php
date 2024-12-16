<?php

namespace Modules\MultiSite\Entities;

use Illuminate\Database\Eloquent\Model;

class Hosting extends Model
{
  protected $fillable = ['name', 'db_host', 'max_capacity', 'current_capacity'];

  public function sites()
  {
    return $this->hasMany(Site::class);
  }
}
