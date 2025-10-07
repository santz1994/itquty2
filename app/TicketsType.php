<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TicketsType extends Model
{
  protected $fillable = ['type'];
  public $timestamps = false;

  public function ticket()
  {
    return $this->hasMany(Ticket::class);
  }

  // Accessor to provide consistent 'name' attribute
  public function getNameAttribute()
  {
      return $this->type;
  }
}
