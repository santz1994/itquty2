<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CreateAssetRequest;
use App\Services\AssetService;
use App\Asset;
use App\AssetType;
use App\Location;
use App\User;
use App\Manufacturer;
use App\Status;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class AssetController extends Controller
{
    protected $assetService;

    public function __construct(AssetService $assetService)
    {
        $this->assetService = $assetService;
    }

    /**
     * Display a listing of assets
     */
    public function index(Request $request)
    {
        $query = Asset::with(['assetType', 'location', 'user', 'manufacturer', 'status']);

        // Filter by type
        if ($request->has('type') && $request->type !== '') {
            $query->where('asset_type_id', $request->type);
        }

        // Filter by location
        if ($request->has('location') && $request->location !== '') {
            $query->where('location_id', $request->location);
        }

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status_id', $request->status);
        }

        // Filter by assigned user
        if ($request->has('assigned_to') && $request->assigned_to !== '') {
            $query->where('assigned_to', $request->assigned_to);
        }

        // Search by name, tag, or serial
        if ($request->has('search') && $request->search !== '') {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('asset_tag', 'like', '%' . $request->search . '%')
                  ->orWhere('serial', 'like', '%' . $request->search . '%');
            });
        }

        $assets = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get filter options
        $types = AssetType::all();
        $locations = Location::all();
        $statuses = Status::all();
        $users = User::all();

        return view('assets.index', compact('assets', 'types', 'locations', 'statuses', 'users'));
    }

    /**
     * Show the form for creating a new asset
     */
    public function create()
    {
        $types = AssetType::all();
        $locations = Location::all();
        $manufacturers = Manufacturer::all();
        $statuses = Status::all();
        $users = User::all();

        return view('assets.create', compact('types', 'locations', 'manufacturers', 'statuses', 'users'));
    }

    /**
     * Store a newly created asset
     */
    public function store(CreateAssetRequest $request)
    {
        try {
            $asset = $this->assetService->createAsset($request->validated());
            
            return redirect()->route('assets.show', $asset->id)
                           ->with('success', 'Asset berhasil dibuat dengan tag: ' . $asset->asset_tag);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal membuat asset: ' . $e->getMessage()])
                         ->withInput();
        }
    }

    /**
     * Display the specified asset
     */
    public function show(Asset $asset)
    {
        $asset->load(['assetType', 'location', 'user', 'manufacturer', 'status', 'tickets', 'movements']);
        
        return view('assets.show', compact('asset'));
    }

    /**
     * Show the form for editing the specified asset
     */
    public function edit(Asset $asset)
    {
        $types = AssetType::all();
        $locations = Location::all();
        $manufacturers = Manufacturer::all();
        $statuses = Status::all();
        $users = User::all();

        return view('assets.edit', compact('asset', 'types', 'locations', 'manufacturers', 'statuses', 'users'));
    }

    /**
     * Update the specified asset
     */
    public function update(CreateAssetRequest $request, Asset $asset)
    {
        try {
            $asset = $this->assetService->updateAsset($asset, $request->validated());
            
            return redirect()->route('assets.show', $asset->id)
                           ->with('success', 'Asset berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memperbarui asset: ' . $e->getMessage()])
                         ->withInput();
        }
    }

    /**
     * Generate QR Code for asset
     */
    public function generateQR(Asset $asset)
    {
        try {
            $qrPath = $this->assetService->generateQRCode($asset);
            
            return redirect()->route('assets.show', $asset->id)
                           ->with('success', 'QR Code berhasil dibuat');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membuat QR Code: ' . $e->getMessage());
        }
    }

    /**
     * Download QR Code
     */
    public function downloadQR(Asset $asset)
    {
        if (!$asset->qr_code || !Storage::exists($asset->qr_code)) {
            return back()->with('error', 'QR Code tidak ditemukan');
        }

        $filePath = storage_path('app/' . $asset->qr_code);
        return Response::download($filePath, 'qr-' . $asset->asset_tag . '.svg');
    }

    /**
     * Scan QR Code page
     */
    public function scanQR()
    {
        return view('assets.scan-qr');
    }

    /**
     * Process QR Code scan result
     */
    public function processScan(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id'
        ]);

        $asset = Asset::with(['assetType', 'location', 'user', 'status'])->find($request->asset_id);

        return view('assets.scan-result', compact('asset'));
    }

    /**
     * Assign asset to user
     */
    public function assign(Request $request, Asset $asset)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        try {
            $this->assetService->assignAsset($asset, $request->user_id);
            
            return redirect()->route('assets.show', $asset->id)
                           ->with('success', 'Asset berhasil ditugaskan');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menugaskan asset: ' . $e->getMessage());
        }
    }

    /**
     * Unassign asset from user
     */
    public function unassign(Asset $asset)
    {
        try {
            $this->assetService->unassignAsset($asset);
            
            return redirect()->route('assets.show', $asset->id)
                           ->with('success', 'Asset berhasil dibatalkan penugasannya');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membatalkan penugasan asset: ' . $e->getMessage());
        }
    }

    /**
     * Show asset movements/history
     */
    public function movements(Asset $asset)
    {
        $movements = $asset->movements()->with(['from_location', 'to_location', 'moved_by'])->orderBy('created_at', 'desc')->get();
        
        return view('assets.movements', compact('asset', 'movements'));
    }

    /**
     * Show my assets (for regular users)
     */
    public function myAssets()
    {
        $assets = Asset::where('assigned_to', auth()->id())
                      ->with(['assetType', 'location', 'status'])
                      ->orderBy('created_at', 'desc')
                      ->get();

        return view('assets.my-assets', compact('assets'));
    }
}