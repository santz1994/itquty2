<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MasterDataService;

class MasterDataController extends Controller
{
    protected $service;

    public function __construct(MasterDataService $service)
    {
        $this->middleware('auth');
        $this->middleware('role:admin|super-admin');
        $this->service = $service;
    }

    // Landing page - exports overview
    public function index()
    {
        return view('admin.masterdata.index');
    }

    // Imports landing / upload form
    public function imports()
    {
        return view('admin.masterdata.import');
    }

    // Handle posted import file (CSV or zip)
    public function handleImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,zip|max:10240'
        ]);

        $file = $request->file('file');
        $path = $file->store('imports');

        // Dispatch processing to service (which will queue a job)
        $jobId = $this->service->dispatchImportJob($path, auth()->user());

        return redirect()->route('masterdata.imports')->with('status', 'Import queued')->with('import_job_id', $jobId);
    }

    // Templates listing
    public function templates()
    {
        // Map template names to existing routes; use Route::has to check existence
        $templates = [];
        if (\Illuminate\Support\Facades\Route::has('assets.download-template')) {
            $templates['assets_template'] = route('assets.download-template');
        } else {
            $templates['assets_template'] = url('/assets/download-template');
        }

        // Add other templates here when available

        return view('admin.masterdata.templates', compact('templates'));
    }

    // Show recent import result JSON files
    public function results()
    {
        $files = [];
        $all = \Illuminate\Support\Facades\Storage::files('imports');
        // Filter result files
        foreach ($all as $f) {
            if (strpos($f, 'results_') !== false && str_ends_with($f, '.json')) {
                $files[] = $f;
            }
        }
        // Sort by modified time desc
        usort($files, function ($a, $b) {
            return filemtime(storage_path('app/' . $b)) <=> filemtime(storage_path('app/' . $a));
        });

        return view('admin.masterdata.results', compact('files'));
    }

    // Download a specific result file (sanitized)
    public function downloadResult($file)
    {
        // Prevent path traversal - allow only filenames without directory separators
        if (strpos($file, '/') !== false || strpos($file, '..') !== false) {
            abort(400);
        }

        $path = 'imports/' . $file;
        if (!\Illuminate\Support\Facades\Storage::exists($path)) {
            abort(404);
        }

        return response()->download(storage_path('app/' . $path));
    }
}
