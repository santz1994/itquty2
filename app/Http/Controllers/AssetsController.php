<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Assets\StoreAssetRequest;
use App\Services\AssetService;
use App\Asset;
use App\AssetType;
use App\Location;
use App\User;
use App\Manufacturer;
use App\Status;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use App\Traits\RoleBasedAccessTrait;

class AssetsController extends Controller
{
    use RoleBasedAccessTrait;
    
    protected $assetService;

    public function __construct(AssetService $assetService)
    {
        $this->middleware('auth');
        $this->assetService = $assetService;
    }

    /**
     * Display a listing of assets
     */
    public function index(Request $request)
    {
        // Use optimized scopes for better performance
        $query = Asset::withRelations();

        // Apply filters using scopes where possible
        if ($request->has('type') && $request->type !== '') {
            $query->where('asset_type_id', $request->type);
        }

        if ($request->has('location') && $request->location !== '') {
            $query->where('location_id', $request->location);
        }

        if ($request->has('status') && $request->status !== '') {
            $query->byStatus($request->status);
        }

        if ($request->has('assigned_to') && $request->assigned_to !== '') {
            $query->where('assigned_to', $request->assigned_to);
        }

        // Search functionality
        if ($request->has('search') && $request->search !== '') {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('asset_tag', 'like', '%' . $request->search . '%')
                  ->orWhere('serial', 'like', '%' . $request->search . '%');
            });
        }

        $assets = $query->orderBy('created_at', 'desc')->paginate(20);

        // Calculate asset statistics for dashboard
        $totalAssets = Asset::count();
        $deployed = Asset::byStatus('Deployed')->count();
        $readyToDeploy = Asset::byStatus('Ready to Deploy')->count();
        $repairs = Asset::byStatus('Out for Repair')->count();
        $writtenOff = Asset::byStatus('Written off')->count();

        // Get filter options - ViewComposer will handle dropdown data
        $types = AssetType::orderBy('type_name')->get();
        $locations = Location::orderBy('location_name')->get();
        $statuses = Status::orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return view('assets.index', compact('assets', 'types', 'locations', 'statuses', 'users', 
                                          'totalAssets', 'deployed', 'readyToDeploy', 'repairs', 'writtenOff'));
    }

    /**
     * Show the form for creating a new asset
     */
    public function create()
    {
        $pageTitle = 'Create New Asset';
        
        // ViewComposer will provide dropdown data
        return view('assets.create', compact('pageTitle'));
    }

    /**
     * Store a newly created asset
     */
    public function store(StoreAssetRequest $request)
    {
        try {
            $asset = $this->assetService->createAsset($request->validated());
            
            return redirect()->route('assets.show', $asset)
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
        // Use scope for consistent eager loading
        $asset->load(['assetType', 'location', 'user', 'manufacturer', 'status', 'tickets', 'movements']);
        
        return view('assets.show', compact('asset'));
    }

    /**
     * Show the form for editing the specified asset
     */
    public function edit(Asset $asset)
    {
        // ViewComposer will provide dropdown data
        return view('assets.edit', compact('asset'));
    }

    /**
     * Update the specified asset
     */
    public function update(StoreAssetRequest $request, Asset $asset)
    {
        try {
            $updatedAsset = $this->assetService->updateAsset($asset, $request->validated());
            
            return redirect()->route('assets.show', $updatedAsset)
                           ->with('success', 'Asset berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memperbarui asset: ' . $e->getMessage()])
                         ->withInput();
        }
    }

    /**
     * Remove the specified asset from storage
     */
    public function destroy(Asset $asset)
    {
        try {
            // Check if user has permission to delete assets
            if (!$this->hasRole('super-admin')) {
                return back()->with('error', 'Anda tidak memiliki izin untuk menghapus asset');
            }

            $asset->delete();
            
            return redirect()->route('assets.index')
                           ->with('success', 'Asset berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus asset: ' . $e->getMessage());
        }
    }

    /**
     * Generate QR Code for asset
     */
    public function generateQR(Asset $asset)
    {
        try {
            $qrPath = $this->assetService->generateQRCode($asset);
            
            return redirect()->route('assets.show', $asset)
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
     * Assign asset to user
     */
    public function assign(Request $request, Asset $asset)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        try {
            $this->assetService->assignAsset($asset, $request->user_id);
            
            return redirect()->route('assets.show', $asset)
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
            
            return redirect()->route('assets.show', $asset)
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
        $movements = $asset->movements()
                          ->with(['from_location', 'to_location', 'moved_by'])
                          ->orderBy('created_at', 'desc')
                          ->get();
        
        return view('assets.movements', compact('asset', 'movements'));
    }

    /**
     * Show asset ticket history
     */
    public function history(Asset $asset)
    {
        $ticketHistory = $asset->getTicketHistory();
        $recentIssues = $asset->getRecentIssues();
        
        return view('admin.assets.history', compact('asset', 'ticketHistory', 'recentIssues'));
    }

    /**
     * Show my assets (for regular users)
     */
    public function myAssets()
    {
        $assets = Asset::where('assigned_to', auth()->id())
                      ->withRelations()
                      ->orderBy('created_at', 'desc')
                      ->get();

        return view('assets.my-assets', compact('assets'));
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

        $asset = Asset::withRelations()->find($request->asset_id);

        return view('assets.scan-result', compact('asset'));
    }

    /**
     * Export assets to Excel
     */
    public function export()
    {
        if (!$this->hasAnyRole(['admin', 'super-admin', 'management'])) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $excel = app(\Maatwebsite\Excel\Excel::class);
            return $excel->download(new \App\Exports\AssetsExport, 'assets_' . now()->format('Y-m-d_H-i-s') . '.xlsx');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Export failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Show import form
     */
    public function importForm()
    {
        if (!$this->hasAnyRole(['admin', 'super-admin', 'management'])) {
            abort(403, 'Unauthorized action.');
        }

        return view('assets.import');
    }

    /**
     * Import assets from Excel
     */
    public function import(Request $request)
    {
        if (!$this->hasAnyRole(['admin', 'super-admin'])) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        try {
            $excel = app(\Maatwebsite\Excel\Excel::class);
            $excel->import(new \App\Imports\AssetsImport, $request->file('file'));
            
            return redirect()->route('assets.index')
                           ->with('success', 'Assets imported successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['file' => 'Error importing file: ' . $e->getMessage()]);
        }
    }

    /**
     * Print asset details to PDF
     */
    public function print($id)
    {
        $asset = Asset::withRelations()->findOrFail($id);
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('assets.print', compact('asset'));
        
        return $pdf->stream('asset_' . $asset->asset_tag . '.pdf');
    }

    /**
     * Download template for import
     */
    public function downloadTemplate()
    {
        if (!$this->hasAnyRole(['admin', 'super-admin'])) {
            abort(403, 'Unauthorized action.');
        }

        $headers = [
            'Asset Tag',
            'Serial Number', 
            'Model',
            'Division',
            'Supplier',
            'Purchase Date',
            'Warranty Months',
            'IP Address',
            'MAC Address',
            'Status',
            'Assigned To',
            'Notes'
        ];

        $callback = function() use ($headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            
            // Add sample data
            fputcsv($file, [
                'ASSET001',
                'SN123456',
                'Dell OptiPlex 7090',
                'IT Department',
                'Dell Inc',
                '2024-01-15',
                '36',
                '192.168.1.100',
                '00:11:22:33:44:55',
                'Active',
                'John Doe',
                'Sample asset note'
            ]);
            
            fclose($file);
        };

        return response()->stream($callback, 200, [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=assets_template.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ]);
    }

    // ========================================
    // USER ASSET MANAGEMENT METHODS
    // ========================================

    /**
     * Display user's assigned assets
     */
    public function userAssets(Request $request)
    {
        $user = auth()->user();
        
        $query = Asset::withRelations()->where('assigned_to', $user->id);

        // Search functionality
        if ($request->has('search') && $request->search !== '') {
            $query->where(function($q) use ($request) {
                $q->where('asset_tag', 'like', '%' . $request->search . '%')
                  ->orWhere('serial_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('model', function($model) use ($request) {
                      $model->where('model_name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->byStatus($request->status);
        }

        $assets = $query->orderBy('asset_tag')->paginate(20);
        $statuses = Status::orderBy('name')->get();
        $pageTitle = 'Aset Saya';

        return view('assets.user.index', compact('assets', 'statuses', 'pageTitle'));
    }

    /**
     * Show user's specific asset details
     */
    public function userShow(Asset $asset)
    {
        $user = auth()->user();
        
        // Ensure user can only view their own assets
        if ($asset->assigned_to !== $user->id) {
            abort(403, 'Anda hanya dapat melihat aset yang ditugaskan kepada Anda.');
        }

        $asset->load(['model', 'status', 'location', 'division', 'supplier', 'warranty_type']);
        
        // Get asset's ticket history
        $ticketHistory = \App\Ticket::where('asset_id', $asset->id)
                                   ->with(['ticket_status', 'ticket_priority', 'ticket_type'])
                                   ->orderBy('created_at', 'desc')
                                   ->take(10)
                                   ->get();
        
        $pageTitle = 'Detail Aset - ' . $asset->asset_tag;

        return view('assets.user.show', compact('asset', 'ticketHistory', 'pageTitle'));
    }
}
