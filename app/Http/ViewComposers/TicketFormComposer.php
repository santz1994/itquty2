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
            'ticketsPriorities' => Cache::remember('ticket_priorities_dropdown', 3600, function () {
                return TicketsPriority::orderBy('priority')->pluck('priority', 'id');
            }),
            'ticketsStatuses' => Cache::remember('ticket_statuses_dropdown', 3600, function () {
                return TicketsStatus::orderBy('status')->pluck('status', 'id');
            }),
            'ticketsTypes' => Cache::remember('ticket_types_dropdown', 3600, function () {
                return TicketsType::orderBy('type')->pluck('type', 'id');
            }),
            'users' => Cache::remember('users_dropdown', 1800, function () {
                return User::orderBy('name')->pluck('name', 'id');
            }),
            'locations' => Cache::remember('locations_dropdown', 3600, function () {
                return Location::orderBy('location_name')->pluck('location_name', 'id');
            }),
            'assets' => Cache::remember('assets_for_tickets_dropdown', 1800, function () {
                return Asset::with('assetModel')->get()->mapWithKeys(function($asset) {
                    return [$asset->id => ($asset->assetModel ? $asset->assetModel->name : 'Unknown Model') . ' - ' . $asset->asset_tag];
                });
            }),
            'assignableUsers' => Cache::remember('assignable_users_dropdown', 1800, function () {
                return User::whereHas('roles', function($q) {
                    $q->whereIn('name', ['admin', 'super-admin']);
                })->orderBy('name')->pluck('name', 'id');
            }),
        ]);
    }
}