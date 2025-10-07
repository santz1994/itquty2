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
use Illuminate\Support\Facades\Cache;

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
            'divisions' => Cache::remember('divisions_dropdown', 3600, function () {
                return Division::orderBy('name')->pluck('name', 'id');
            }),
            'locations' => Cache::remember('locations_dropdown', 3600, function () {
                return Location::orderBy('location_name')->pluck('location_name', 'id');
            }),
            'statuses' => Cache::remember('asset_statuses_dropdown', 3600, function () {
                return Status::orderBy('name')->pluck('name', 'id');
            }),
            'assetModels' => Cache::remember('asset_models_dropdown', 3600, function () {
                return AssetModel::with('manufacturer')->get()->mapWithKeys(function($model) {
                    return [$model->id => $model->manufacturer->name . ' - ' . $model->name];
                });
            }),
            'manufacturers' => Cache::remember('manufacturers_dropdown', 3600, function () {
                return Manufacturer::orderBy('name')->pluck('name', 'id');
            }),
            'suppliers' => Cache::remember('suppliers_dropdown', 3600, function () {
                return Supplier::orderBy('name')->pluck('name', 'id');
            }),
            'assetTypes' => Cache::remember('asset_types_dropdown', 3600, function () {
                return AssetType::orderBy('type_name')->pluck('type_name', 'id');
            }),
            'warrantyTypes' => Cache::remember('warranty_types_dropdown', 7200, function () {
                return WarrantyType::orderBy('name')->pluck('name', 'id');
            }),
        ]);
    }
}