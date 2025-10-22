<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CreateAssetRequestRequest;
use App\AssetRequest;
use App\AssetType;
use App\User;
use Illuminate\Support\Facades\Auth;

class AssetRequestController extends Controller
{
    /**
     * Get the authenticated user
     * 
     * @return \App\User
     */
    protected function user(): User
    {
        /** @var User $user */
        $user = Auth::user();
        return $user;
    }
    /**
     * Display a listing of asset requests
     */
    public function index(Request $request)
    {
        $query = AssetRequest::with(['requestedBy', 'assetType', 'approvedBy']);

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by asset type
        if ($request->has('asset_type') && $request->asset_type !== '') {
            $query->where('asset_type_id', $request->asset_type);
        }

        // Filter by priority
        if ($request->has('priority') && $request->priority !== '') {
            $query->where('priority', $request->priority);
        }

        // If regular user, only show their requests
        if (!$this->user()->hasRole(['admin', 'super-admin'])) {
            $query->where('requested_by', Auth::id());
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get filter options
        $assetTypes = AssetType::all();
        $statuses = ['pending', 'approved', 'rejected', 'fulfilled'];
        $priorities = ['low', 'medium', 'high', 'urgent'];

        return view('asset-requests.index', compact('requests', 'assetTypes', 'statuses', 'priorities'));
    }

    /**
     * Show the form for creating a new asset request
     */
    public function create()
    {
        $assetTypes = AssetType::all();
        $priorities = ['low', 'medium', 'high', 'urgent'];

        return view('asset-requests.create', compact('assetTypes', 'priorities'));
    }

    /**
     * Store a newly created asset request
     */
    public function store(CreateAssetRequestRequest $request)
    {
        try {
            $data = $request->validated();
            $data['requested_by'] = Auth::id();
            $data['status'] = 'pending';

            AssetRequest::create($data);
            
            return redirect()->route('asset-requests.index')
                           ->with('success', 'Permintaan asset berhasil diajukan');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal mengajukan permintaan: ' . $e->getMessage()])
                         ->withInput();
        }
    }

    /**
     * Display the specified asset request
     */
    public function show(AssetRequest $assetRequest)
    {
        // Check if user can view this request
        if (!$this->user()->hasRole(['admin', 'super-admin']) && $assetRequest->requested_by !== Auth::id()) {
            abort(403, 'Tidak memiliki akses untuk melihat permintaan ini');
        }

        $assetRequest->load(['requestedBy', 'assetType', 'approvedBy']);
        
        return view('asset-requests.show', compact('assetRequest'));
    }

    /**
     * Approve asset request
     */
    public function approve(Request $request, AssetRequest $assetRequest)
    {
        // Only admin can approve
        if (!$this->user()->hasRole(['admin', 'super-admin'])) {
            abort(403, 'Tidak memiliki akses untuk menyetujui permintaan');
        }

        $request->validate([
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        try {
            $assetRequest->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'admin_notes' => $request->admin_notes
            ]);
            
            return redirect()->route('asset-requests.show', $assetRequest->id)
                           ->with('success', 'Permintaan asset berhasil disetujui');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyetujui permintaan: ' . $e->getMessage());
        }
    }

    /**
     * Reject asset request
     */
    public function reject(Request $request, AssetRequest $assetRequest)
    {
        // Only admin can reject
        if (!$this->user()->hasRole(['admin', 'super-admin'])) {
            abort(403, 'Tidak memiliki akses untuk menolak permintaan');
        }

        $request->validate([
            'admin_notes' => 'required|string|max:1000'
        ]);

        try {
            $assetRequest->update([
                'status' => 'rejected',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'admin_notes' => $request->admin_notes
            ]);
            
            return redirect()->route('asset-requests.show', $assetRequest->id)
                           ->with('success', 'Permintaan asset berhasil ditolak');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menolak permintaan: ' . $e->getMessage());
        }
    }

    /**
     * Mark request as fulfilled
     */
    public function fulfill(Request $request, AssetRequest $assetRequest)
    {
        // Only admin can fulfill
        if (!$this->user()->hasRole(['admin', 'super-admin'])) {
            abort(403, 'Tidak memiliki akses untuk memenuhi permintaan');
        }

        $request->validate([
            'fulfillment_notes' => 'nullable|string|max:1000'
        ]);

        try {
            $assetRequest->update([
                'status' => 'fulfilled',
                'fulfilled_at' => now(),
                'fulfillment_notes' => $request->fulfillment_notes
            ]);
            
            return redirect()->route('asset-requests.show', $assetRequest->id)
                           ->with('success', 'Permintaan asset berhasil dipenuhi');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memenuhi permintaan: ' . $e->getMessage());
        }
    }

    /**
     * Show pending requests (for admin)
     */
    public function pending()
    {
        // Only admin can view
        if (!$this->user()->hasRole(['admin', 'super-admin'])) {
            abort(403, 'Tidak memiliki akses');
        }

        $requests = AssetRequest::with(['requestedBy', 'assetType'])
                              ->where('status', 'pending')
                              ->orderBy('created_at', 'desc')
                              ->get();

        return view('asset-requests.pending', compact('requests'));
    }

    /**
     * Show my requests (for regular users)
     */
    public function myRequests()
    {
        $requests = AssetRequest::with(['assetType', 'approvedBy'])
                              ->where('requested_by', Auth::id())
                              ->orderBy('created_at', 'desc')
                              ->get();

        return view('asset-requests.my-requests', compact('requests'));
    }

    /**
     * Cancel asset request (user can cancel their own pending requests)
     */
    public function cancel(AssetRequest $assetRequest)
    {
        // Check if user can cancel this request
        if ($assetRequest->requested_by !== Auth::id()) {
            abort(403, 'Tidak memiliki akses untuk membatalkan permintaan ini');
        }

        // Can only cancel pending requests
        if ($assetRequest->status !== 'pending') {
            return back()->with('error', 'Hanya dapat membatalkan permintaan yang masih pending');
        }

        try {
            $assetRequest->delete();
            
            return redirect()->route('asset-requests.index')
                           ->with('success', 'Permintaan asset berhasil dibatalkan');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membatalkan permintaan: ' . $e->getMessage());
        }
    }

    /**
     * Export requests to CSV
     */
    public function export(Request $request)
    {
        $query = AssetRequest::with(['requestedBy', 'assetType', 'approvedBy']);

        // Apply filters
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('asset_type') && $request->asset_type !== '') {
            $query->where('asset_type_id', $request->asset_type);
        }

        // If regular user, only export their requests
        if (!$this->user()->hasRole(['admin', 'super-admin'])) {
            $query->where('requested_by', Auth::id());
        }

        $requests = $query->orderBy('created_at', 'desc')->get();

        // Generate CSV
        $filename = 'asset-requests-' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($requests) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, ['Tanggal Request', 'User', 'Tipe Asset', 'Deskripsi', 'Prioritas', 'Status', 'Disetujui Oleh', 'Tanggal Disetujui']);
            
            foreach ($requests as $req) {
                fputcsv($file, [
                    $req->created_at->format('Y-m-d H:i:s'),
                    $req->requestedBy->name,
                    $req->assetType->name,
                    $req->description,
                    $req->priority,
                    $req->status,
                    $req->approvedBy ? $req->approvedBy->name : '',
                    $req->approved_at ? $req->approved_at->format('Y-m-d H:i:s') : ''
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}