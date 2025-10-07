<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\TicketsPriority;
use App\TicketsStatus;
use App\TicketsType;
use App\User;
use App\Location;
use App\Asset;

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
            'ticketsPriorities' => TicketsPriority::pluck('name', 'id'),
            'ticketsStatuses' => TicketsStatus::pluck('name', 'id'),
            'ticketsTypes' => TicketsType::pluck('name', 'id'),
            'users' => User::pluck('name', 'id'),
            'locations' => Location::pluck('name', 'id'),
            'assets' => Asset::with('assetModel')->get()->pluck('assetModel.name', 'id'),
            'assignableUsers' => User::whereHas('roles', function($q) {
                $q->whereIn('name', ['admin', 'super-admin']);
            })->pluck('name', 'id'),
        ]);
    }
}