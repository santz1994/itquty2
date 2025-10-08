<?php

namespace App\Imports;

use App\Asset;
use App\AssetModel;
use App\Division;
use App\Supplier;
use App\Status;
use App\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Carbon\Carbon;

class AssetsImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Find related models by name or create new ones
        $model = AssetModel::where('name', $row['model'])->first();
        $division = Division::where('name', $row['division'])->first();
        $supplier = Supplier::where('name', $row['supplier'])->first();
        $status = Status::where('name', $row['status'])->first() ?: Status::first();
        $assignedTo = null;
        
        if (!empty($row['assigned_to'])) {
            $assignedTo = User::where('name', $row['assigned_to'])->first();
        }

        return new Asset([
            'asset_tag' => $row['asset_tag'],
            'serial_number' => $row['serial_number'],
            'model_id' => $model ? $model->id : null,
            'division_id' => $division ? $division->id : null,
            'supplier_id' => $supplier ? $supplier->id : null,
            'purchase_date' => !empty($row['purchase_date']) ? Carbon::parse($row['purchase_date']) : null,
            'warranty_months' => $row['warranty_months'] ?? null,
            'ip_address' => $row['ip_address'] ?? null,
            'mac_address' => $row['mac_address'] ?? null,
            'status_id' => $status->id,
            'assigned_to' => $assignedTo ? $assignedTo->id : null,
            'notes' => $row['notes'] ?? null,
        ]);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'asset_tag' => 'required|string|max:255|unique:assets,asset_tag',
            'serial_number' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'division' => 'nullable|string|max:255',
            'supplier' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'warranty_months' => 'nullable|integer|min:0',
            'ip_address' => 'nullable|ip',
            'mac_address' => 'nullable|string|max:17',
            'status' => 'nullable|string|max:255',
            'assigned_to' => 'nullable|string|max:255',
        ];
    }
}