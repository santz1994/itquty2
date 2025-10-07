<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Division;
use App\Location;
use App\Status;
use App\User;
use App\Role;
use Illuminate\Support\Facades\Cache;

class FormDataComposer
{
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        // Only bind basic user management form data
        // Use caching to improve performance
        $view->with([
            'divisions' => Cache::remember('divisions_dropdown', 3600, function () {
                return Division::orderBy('name')->pluck('name', 'id');
            }),
            'locations' => Cache::remember('locations_dropdown', 3600, function () {
                return Location::orderBy('location_name')->pluck('location_name', 'id');
            }),
            'statuses' => Cache::remember('statuses_dropdown', 3600, function () {
                return Status::orderBy('name')->pluck('name', 'id');
            }),
            'users' => Cache::remember('users_dropdown', 1800, function () {
                return User::orderBy('name')->pluck('name', 'id');
            }),
            'roles' => Cache::remember('roles_dropdown', 7200, function () {
                return Role::orderBy('display_name')->pluck('display_name', 'id');
            }),
        ]);
    }
}