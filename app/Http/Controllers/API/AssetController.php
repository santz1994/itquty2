<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Asset;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AssetController extends Controller
{
    /**
     * Display a listing of assets
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Asset::with(['status', 'division', 'location', 'assetModel', 'assignedUser']);

        // Apply filters
        if ($request->has('status_id')) {
            $query->where('status_id', $request->status_id);
        }

        if ($request->has('division_id')) {
            $query->where('division_id', $request->division_id);
        }

        if ($request->has('assigned_to') && $request->assigned_to !== '') {
            if ($request->assigned_to === 'unassigned') {
                $query->whereNull('assigned_to');
            } else {
                $query->where('assigned_to', $request->assigned_to);
            }
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('asset_tag', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $assets = $query->paginate($perPage);

        // Transform data
        $assets->getCollection()->transform(function ($asset) {
            return [
                'id' => $asset->id,
                'asset_tag' => $asset->asset_tag,
                'name' => $asset->name,
                'serial_number' => $asset->serial_number,
                'mac_address' => $asset->mac_address,
                'ip_address' => $asset->ip_address,
                'purchase_date' => $asset->purchase_date,
                'warranty_expiry' => $asset->warranty_expiry,
                'location' => $asset->location->name ?? null,
                'division' => $asset->division->name ?? null,
                'status' => [
                    'id' => $asset->status_id,
                    'name' => $asset->status->name ?? null,
                    'badge' => $asset->status_badge
                ],
                'assigned_user' => $asset->assignedUser ? [
                    'id' => $asset->assignedUser->id,
                    'name' => $asset->assignedUser->name,
                    'email' => $asset->assignedUser->email
                ] : null,
                'asset_model' => $asset->assetModel ? [
                    'id' => $asset->assetModel->id,
                    'name' => $asset->assetModel->name,
                    'manufacturer' => $asset->assetModel->manufacturer->name ?? null
                ] : null,
                'depreciation_percentage' => $asset->depreciation_percentage,
                'warranty_expiry_date' => $asset->warranty_expiry_date,
                'formatted_mac_address' => $asset->formatted_mac_address,
                'is_warranty_expired' => $asset->is_warranty_expired,
                'warranty_status' => $asset->warranty_status,
                'created_at' => $asset->created_at,
                'updated_at' => $asset->updated_at
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $assets,
            'message' => 'Assets retrieved successfully'
        ]);
    }

    /**
     * Store a newly created asset
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('create-assets')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to create assets'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'asset_tag' => 'required|string|unique:assets',
            'name' => 'required|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'mac_address' => 'nullable|string|max:17',
            'ip_address' => 'nullable|ip',
            'purchase_date' => 'nullable|date',
            'warranty_expiry' => 'nullable|date',
            'location_id' => 'required|exists:locations,id',
            'division_id' => 'required|exists:divisions,id',
            'status_id' => 'required|exists:statuses,id',
            'model_id' => 'required|exists:asset_models,id',
            'assigned_to' => 'nullable|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $asset = Asset::create($request->all());
        $asset->load(['status', 'division', 'location', 'assetModel', 'assignedUser']);

        // Auto-assign if assigned_to is provided
        if ($request->assigned_to) {
            $user = User::find($request->assigned_to);
            $asset->assignTo($user, 'api');
        }

        return response()->json([
            'success' => true,
            'data' => $this->transformAsset($asset),
            'message' => 'Asset created successfully'
        ], 201);
    }

    /**
     * Display the specified asset
     *
     * @param Asset $asset
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Asset $asset)
    {
        $asset->load(['status', 'division', 'location', 'assetModel', 'assignedUser', 'tickets']);

        return response()->json([
            'success' => true,
            'data' => $this->transformAsset($asset, true),
            'message' => 'Asset retrieved successfully'
        ]);
    }

    /**
     * Update the specified asset
     *
     * @param Request $request
     * @param Asset $asset
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Asset $asset)
    {
        if (!auth()->user()->can('edit-assets')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to update assets'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'asset_tag' => 'sometimes|string|unique:assets,asset_tag,' . $asset->id,
            'name' => 'sometimes|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'mac_address' => 'nullable|string|max:17',
            'ip_address' => 'nullable|ip',
            'purchase_date' => 'nullable|date',
            'warranty_expiry' => 'nullable|date',
            'location_id' => 'sometimes|exists:locations,id',
            'division_id' => 'sometimes|exists:divisions,id',
            'status_id' => 'sometimes|exists:statuses,id',
            'model_id' => 'sometimes|exists:asset_models,id',
            'assigned_to' => 'nullable|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $asset->update($request->all());
        $asset->load(['status', 'division', 'location', 'assetModel', 'assignedUser']);

        return response()->json([
            'success' => true,
            'data' => $this->transformAsset($asset),
            'message' => 'Asset updated successfully'
        ]);
    }

    /**
     * Remove the specified asset
     *
     * @param Asset $asset
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Asset $asset)
    {
        if (!auth()->user()->can('delete-assets')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete assets'
            ], 403);
        }

        $asset->delete();

        return response()->json([
            'success' => true,
            'message' => 'Asset deleted successfully'
        ]);
    }

    /**
     * Assign asset to user
     *
     * @param Request $request
     * @param Asset $asset
     * @return \Illuminate\Http\JsonResponse
     */
    public function assign(Request $request, Asset $asset)
    {
        if (!auth()->user()->can('assign-assets')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to assign assets'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'notes' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::find($request->user_id);
        $result = $asset->assignTo($user, 'api', $request->notes);

        return response()->json([
            'success' => true,
            'data' => $this->transformAsset($asset->fresh(['assignedUser'])),
            'message' => 'Asset assigned successfully'
        ]);
    }

    /**
     * Unassign asset from user
     *
     * @param Request $request
     * @param Asset $asset
     * @return \Illuminate\Http\JsonResponse
     */
    public function unassign(Request $request, Asset $asset)
    {
        if (!auth()->user()->can('assign-assets')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to unassign assets'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'notes' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $asset->unassign('api', $request->notes);

        return response()->json([
            'success' => true,
            'data' => $this->transformAsset($asset->fresh(['assignedUser'])),
            'message' => 'Asset unassigned successfully'
        ]);
    }

    /**
     * Mark asset for maintenance
     *
     * @param Request $request
     * @param Asset $asset
     * @return \Illuminate\Http\JsonResponse
     */
    public function markForMaintenance(Request $request, Asset $asset)
    {
        if (!auth()->user()->can('edit-assets')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to update assets'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'notes' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $asset->markForMaintenance($request->notes);

        return response()->json([
            'success' => true,
            'data' => $this->transformAsset($asset->fresh(['status'])),
            'message' => 'Asset marked for maintenance successfully'
        ]);
    }

    /**
     * Get asset history
     *
     * @param Asset $asset
     * @return \Illuminate\Http\JsonResponse
     */
    public function getHistory(Asset $asset)
    {
        $movements = $asset->movements()->with(['user', 'status'])->orderBy('created_at', 'desc')->get();
        $tickets = $asset->tickets()->with(['user', 'status', 'priority'])->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'movements' => $movements,
                'tickets' => $tickets
            ],
            'message' => 'Asset history retrieved successfully'
        ]);
    }

    /**
     * Transform asset data
     *
     * @param Asset $asset
     * @param bool $detailed
     * @return array
     */
    private function transformAsset(Asset $asset, $detailed = false)
    {
        $data = [
            'id' => $asset->id,
            'asset_tag' => $asset->asset_tag,
            'name' => $asset->name,
            'serial_number' => $asset->serial_number,
            'mac_address' => $asset->mac_address,
            'ip_address' => $asset->ip_address,
            'purchase_date' => $asset->purchase_date,
            'warranty_expiry' => $asset->warranty_expiry,
            'location' => $asset->location->name ?? null,
            'division' => $asset->division->name ?? null,
            'status' => [
                'id' => $asset->status_id,
                'name' => $asset->status->name ?? null,
                'badge' => $asset->status_badge
            ],
            'assigned_user' => $asset->assignedUser ? [
                'id' => $asset->assignedUser->id,
                'name' => $asset->assignedUser->name,
                'email' => $asset->assignedUser->email
            ] : null,
            'asset_model' => $asset->assetModel ? [
                'id' => $asset->assetModel->id,
                'name' => $asset->assetModel->name,
                'manufacturer' => $asset->assetModel->manufacturer->name ?? null
            ] : null,
            'depreciation_percentage' => $asset->depreciation_percentage,
            'warranty_expiry_date' => $asset->warranty_expiry_date,
            'formatted_mac_address' => $asset->formatted_mac_address,
            'is_warranty_expired' => $asset->is_warranty_expired,
            'warranty_status' => $asset->warranty_status,
            'created_at' => $asset->created_at,
            'updated_at' => $asset->updated_at
        ];

        if ($detailed) {
            $data['tickets'] = $asset->tickets->map(function($ticket) {
                return [
                    'id' => $ticket->id,
                    'title' => $ticket->title,
                    'description' => $ticket->description,
                    'status' => $ticket->status->name ?? null,
                    'priority' => $ticket->priority->name ?? null,
                    'created_at' => $ticket->created_at
                ];
            });
        }

        return $data;
    }
}