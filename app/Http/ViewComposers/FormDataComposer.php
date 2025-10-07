<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Division;
use App\Location;
use App\Status;
use App\AssetModel;
use App\Manufacturer;
use App\Supplier;
use App\TicketsPriority;
use App\TicketsStatus;
use App\TicketsType;
use App\User;
use App\Role;

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
        // Common dropdown data that's used across multiple forms
        $view->with([
            'divisions' => Division::pluck('name', 'id'),
            'locations' => Location::pluck('name', 'id'),
            'statuses' => Status::pluck('name', 'id'),
            'assetModels' => AssetModel::pluck('name', 'id'),
            'manufacturers' => Manufacturer::pluck('name', 'id'),
            'suppliers' => Supplier::pluck('name', 'id'),
            'ticketsPriorities' => TicketsPriority::pluck('name', 'id'),
            'ticketsStatuses' => TicketsStatus::pluck('name', 'id'),
            'ticketsTypes' => TicketsType::pluck('name', 'id'),
            'users' => User::pluck('name', 'id'),
            'roles' => Role::pluck('display_name', 'id'),
        ]);
    }
}