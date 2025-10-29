<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Assets\StoreAssetRequest;
use App\Services\AssetService;
use App\Asset;
use App\AssetType;
use App\Location;
use App\User;
use App\Status;
use App\AssetModel;
use Illuminate\Support\Facades\Storage;
use App\Division;
use App\Supplier;
use App\Invoice;
use App\WarrantyType;
use App\PurchaseOrder;
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
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('asset_tag', 'like', $searchTerm)
                  ->orWhere('serial', 'like', $searchTerm);
            });
        }

        // Allow optional request to return all results for client-side DataTables
        // or control per-page size via `per_page` query parameter.
        // Support multiple ways to request 'all' rows:
        // - ?all=1
        // - ?per_page=all or ?per_page=-1
        $perPageInput = $request->input('per_page', null);
        $wantsAll = $request->boolean('all') || $perPageInput === 'all' || $perPageInput === '-1';

        if ($wantsAll) {
            $assets = $query->orderBy('created_at', 'desc')->get();
        } else {
            $perPage = is_numeric($perPageInput) ? (int) $perPageInput : 25;
            $assets = $query->orderBy('created_at', 'desc')->paginate($perPage);
        }

    // Get KPI statistics from service
    $stats = $this->assetService->getAssetStatistics();
    $totalAssets = $stats['total'] ?? 0;
    $deployed = $stats['deployed'] ?? 0;
    $readyToDeploy = $stats['in_stock'] ?? 0;
    $repairs = $stats['in_repair'] ?? 0;
    $writtenOff = $stats['disposed'] ?? 0;

    // Filter options are now provided by AssetFormComposer
    // Get users for assigned_to filter
    $users = \App\User::select('id', 'name')->where('is_active', 1)->orderBy('name')->get();
    
    // Assets by location for KPI widget
    $assetsByLocation = $this->assetService->getAssetsByLocation();
    $assetsByStatus = $this->assetService->assetsByStatusBreakdown();
    $monthlyNewAssets = $this->assetService->monthlyNewAssets(6);

    return view('assets.index', compact('assets', 'users', 
                      'totalAssets', 'deployed', 'readyToDeploy', 'repairs', 'writtenOff', 'assetsByLocation', 'assetsByStatus', 'monthlyNewAssets'));
    }

    /**
     * Show the form for creating a new asset
     */
    public function create()
    {
    $asset_models = AssetModel::all(); // Fetch all asset models
    $asset_types = AssetType::orderBy('type_name')->get(); // Fetch asset types
        $divisions = Division::all(); // Fetch all divisions
        $suppliers = Supplier::all(); // Fetch all suppliers
        $invoices = Invoice::all(); // Fetch all invoices
        $warranty_types = WarrantyType::all(); // Fetch all warranty types
        $statuses = Status::all(); // Fetch all statuses
        $locations = Location::all(); // Fetch all locations

    $purchaseOrders = PurchaseOrder::orderBy('order_date', 'desc')->get();

    $pageTitle = 'Create Asset';
    return view('assets.create', compact('asset_types','asset_models', 'divisions', 'suppliers', 'invoices', 'warranty_types', 'statuses', 'locations', 'purchaseOrders', 'pageTitle'));
    }

    /**
     * Store a newly created asset
     */
    public function store(StoreAssetRequest $request)
    {
        $validated = $request->validated();

        // Normalize model id (tests may send `model_id` while request uses `asset_model_id`)
        $modelId = $validated['model_id'] ?? $validated['asset_model_id'] ?? null;

        $assetData = [
            'model_id' => $modelId,
            'asset_tag' => $validated['asset_tag'] ?? null,
            'name' => $validated['name'] ?? ($validated['asset_tag'] ?? null),
            'serial_number' => $validated['serial_number'] ?? null,
            'division_id' => $validated['division_id'] ?? null,
            'supplier_id' => $validated['supplier_id'] ?? null,
            'warranty_type_id' => $validated['warranty_type_id'] ?? null,
            'status_id' => $validated['status_id'] ?? null,
            'purchase_date' => $validated['purchase_date'] ?? null,
            'purchase_cost' => $validated['purchase_cost'] ?? null,
            'location_id' => $validated['location_id'] ?? null,
            'assigned_to' => $validated['assigned_to'] ?? null,
            'purchase_order_id' => $validated['purchase_order_id'] ?? null,
        ];

        Asset::create($assetData);

        return redirect()->route('assets.index')->with('success', 'Asset created successfully.');
    }

    /**
     * Display the specified asset
     */
    public function show(Asset $asset)
    {
    // Use scope for consistent eager loading. AssetType and Manufacturer are reached via the related model,
    // so eager-load the nested relations `model.asset_type` and `model.manufacturer` instead of non-existent direct relations.
    // Also eager-load `assignedTo` (alias for the user the asset is assigned to) and movements history.
    $asset->load(['model.asset_type', 'model.manufacturer', 'location', 'assignedTo', 'status', 'tickets', 'movements']);
        
        // Load ticket history and recent issues
        $ticketHistory = $asset->getTicketHistory();
        $recentIssues = $asset->getRecentIssues();
        
        return view('assets.show', compact('asset', 'ticketHistory', 'recentIssues'));
    }

    /**
     * Show the form for editing the specified asset
     */
    public function edit(Asset $asset)
    {
        // Provide dropdown data explicitly so the view does not rely on a ViewComposer
    $asset_models = AssetModel::orderBy('asset_model')->get();
    $asset_types = AssetType::orderBy('type_name')->get();
        $divisions = Division::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();
    $invoices = Invoice::orderBy('invoiced_date', 'desc')->get();
    $warranty_types = WarrantyType::orderBy('name')->get();
    $statuses = Status::orderBy('name')->get();
    $locations = Location::orderBy('location_name')->get();
    $purchaseOrders = PurchaseOrder::orderBy('order_date', 'desc')->get();

    return view('assets.edit', compact('asset', 'asset_types', 'asset_models', 'divisions', 'suppliers', 'invoices', 'warranty_types', 'statuses', 'locations', 'purchaseOrders'));
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
        $this->authorize('delete', $asset);

        try {
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
            'user_id' => 'required|exists:users,id',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            $this->assetService->assignAsset($asset, $request->user_id);
            
            // Redirect to movements page to show the assignment
            return redirect()->route('assets.movements', $asset)
                           ->with('success', 'Asset berhasil ditugaskan ke pengguna');
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
            
            // Check if request expects JSON (AJAX call)
            if (request()->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Asset unassigned successfully']);
            }
            
            return redirect()->route('assets.show', $asset)
                           ->with('success', 'Asset berhasil dibatalkan penugasannya');
        } catch (\Exception $e) {
            $message = 'Gagal membatalkan penugasan asset: ' . $e->getMessage();
            
            if (request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            
            return back()->with('error', $message);
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
        
        // Get active users for assignment
        $users = User::select('id', 'name')
                    ->where('is_active', 1)
                    ->orderBy('name')
                    ->get();
        
        return view('assets.movements', compact('asset', 'movements', 'users'));
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
     * Download import errors as CSV when import_summary exists in session
     */
    public function downloadImportErrors()
    {
        if (!session()->has('import_summary')) {
            // Better user experience: redirect back to import form with a message
            return redirect()->route('assets.import-form')
                             ->with('error', 'No import summary available for download.');
        }

        $summary = session('import_summary');
        $errors = $summary['errors'] ?? [];

        $callback = function() use ($errors) {
            $out = fopen('php://output', 'w');
            // Header
            fputcsv($out, ['row', 'messages', 'data']);

            foreach ($errors as $err) {
                $row = $err['row'] ?? '';
                $messages = '';
                if (!empty($err['errors'])) {
                    $messages = implode('; ', $err['errors']);
                } else {
                    $messages = $err['error'] ?? '';
                }
                $data = isset($err['data']) ? json_encode($err['data']) : '';

                fputcsv($out, [$row, $messages, $data]);
            }

            fclose($out);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="import_errors.csv"',
        ]);
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
            $file = $request->file('file');

            // If CSV, use fallback importer with row-level validation
            if ($file->getClientOriginalExtension() === 'csv') {
                $importer = new \App\Imports\AssetsCsvImport($file);
                $result = $importer->import();

                if (!empty($result['errors'])) {
                    return redirect()->route('assets.import-form')
                                     ->with('import_summary', $result);
                }

                return redirect()->route('assets.index')
                                 ->with('success', 'Assets imported successfully!')
                                 ->with('import_summary', $result);
            }

            // For Excel files, use the maatwebsite importer if available
            $excel = app(\Maatwebsite\Excel\Excel::class);
            $excel->import(new \App\Imports\AssetsImport, $file);

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
