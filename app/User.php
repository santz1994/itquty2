<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Traits\HasDualRoles;
// Make sure we're not directly using Spatie's HasRoles trait
// use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
  // Only use the HasDualRoles trait which already includes Spatie's HasRoles trait
  use HasDualRoles;
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
      'name', 'email', 'password',
  ];

  /**
   * The attributes excluded from the model's JSON form.
   *
   * @var array
   */
  protected $hidden = [
      'password', 'remember_token',
  ];

  public function movement()
  {
    return $this->hasOne(Movement::class);
  }

  public function ticket()
  {
    return $this->hasMany(Ticket::class);
  }
}
