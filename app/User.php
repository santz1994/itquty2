<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

/**
 * App\User
 * 
 * @method bool hasRole($roles)
 * @method bool hasAnyRole($roles)
 * @method bool hasAllRoles($roles)
 * @method \Illuminate\Support\Collection getRoleNames()
 * @method mixed assignRole($roles)
 * @method mixed removeRole($roles)
 * @method mixed syncRoles($roles)
 * @method bool hasPermissionTo($permission)
 * @method bool hasAnyPermission($permissions)
 * @method bool hasAllPermissions($permissions)
 * @method mixed givePermissionTo($permissions)
 * @method mixed revokePermissionTo($permissions)
 * @method mixed syncPermissions($permissions)
 */
class User extends Authenticatable
{
  use HasRoles;
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

  public function assignedTickets()
  {
    return $this->hasMany(Ticket::class, 'assigned_to');
  }

  public function assets()
  {
    return $this->hasMany(Asset::class, 'assigned_to');
  }

  public function dailyActivities()
  {
    return $this->hasMany(DailyActivity::class);
  }

  // Scopes
  public function scopeWithRoles($query)
  {
    return $query->with('roles');
  }

  public function scopeAdmins($query)
  {
    return $query->whereHas('roles', function($q) {
      $q->whereIn('name', ['admin', 'super-admin']);
    });
  }

  public function scopeActiveUsers($query)
  {
    return $query->where('active', true);
  }

  public function scopeByRole($query, $roleName)
  {
    return $query->whereHas('roles', function($q) use ($roleName) {
      $q->where('name', $roleName);
    });
  }
}
