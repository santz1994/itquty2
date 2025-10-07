<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\TicketsPriority;
use App\TicketsStatus;
use App\TicketsType;
use App\User;
use App\Location;
use App\Asset;
use Illuminate\Support\Facades\Cache;

class TicketFormComposer
{
    /**
     * Bind ticket-specific data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->with([
            'ticketsPriorities' => Cache::remember('ticket_priorities_objects', 3600, function () {
                return TicketsPriority::select('id', 'priority')->orderBy('priority')->get();
            }),
            'ticketsStatuses' => Cache::remember('ticket_statuses_objects', 3600, function () {
                return TicketsStatus::select('id', 'status')->orderBy('status')->get();
            }),
            'ticketsTypes' => Cache::remember('ticket_types_objects', 3600, function () {
                return TicketsType::select('id', 'type')->orderBy('type')->get();
            }),
            'users' => Cache::remember('users_objects', 1800, function () {
                return User::select('id', 'name')->orderBy('name')->get();
            }),
            'locations' => Cache::remember('locations_objects', 3600, function () {
                return Location::select('id', 'location_name')->orderBy('location_name')->get();
            }),
            'assets' => Cache::remember('assets_for_tickets_objects', 1800, function () {
                return Asset::select('id', 'asset_tag')->with('assetModel:id,name')->get();
            }),
            'assignableUsers' => Cache::remember('assignable_users_objects', 1800, function () {
                return User::select('id', 'name')->whereHas('roles', function($q) {
                    $q->whereIn('name', ['admin', 'super-admin']);
                })->orderBy('name')->get();
            }),
        ]);
    }
}