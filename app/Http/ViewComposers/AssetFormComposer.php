<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Division;
use App\Location;
use App\Status;
use App\AssetModel;
use App\Manufacturer;
use App\Supplier;
use App\AssetType;
use App\WarrantyType;

class AssetFormComposer
{
    /**
     * Bind asset-specific data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->with([
            'divisions' => Division::pluck('name', 'id'),
            'locations' => Location::pluck('name', 'id'),
            'statuses' => Status::pluck('name', 'id'),
            'assetModels' => AssetModel::with('manufacturer')->get()->mapWithKeys(function($model) {
                return [$model->id => $model->manufacturer->name . ' - ' . $model->name];
            }),
            'manufacturers' => Manufacturer::pluck('name', 'id'),
            'suppliers' => Supplier::pluck('name', 'id'),
            'assetTypes' => AssetType::pluck('name', 'id'),
            'warrantyTypes' => WarrantyType::pluck('name', 'id'),
        ]);
    }
}