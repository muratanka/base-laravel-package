<?php

namespace Modules\MultiSite\Entities;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Order extends Model
{
  protected $fillable = ['site_id', 'start_date', 'end_date', 'status'];

  public function site()
  {
    return $this->belongsTo(Site::class);
  }

  public function isExpired(): bool
  {
    return Carbon::now()->greaterThan($this->end_date);
  }
}
