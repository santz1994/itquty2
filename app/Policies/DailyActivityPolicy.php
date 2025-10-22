<?php

namespace App\Policies;

use App\User;
use App\DailyActivity;

class DailyActivityPolicy
{
    /**
     * Determine whether the user can update the daily activity.
     */
    public function update(User $user, DailyActivity $dailyActivity)
    {
        // owners can update their own activities; admins (has role 'super_admin') can update any
        if ($user->hasRole('super-admin') || $user->hasRole('admin')) {
            return true;
        }

        return $dailyActivity->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the daily activity.
     */
    public function delete(User $user, DailyActivity $dailyActivity)
    {
        // same rule as update
        if ($user->hasRole('super-admin') || $user->hasRole('admin')) {
            return true;
        }

        return $dailyActivity->user_id === $user->id;
    }
}
