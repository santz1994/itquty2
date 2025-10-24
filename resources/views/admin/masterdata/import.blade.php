@extends('layouts.app')

@section('main-content')

@include('components.page-header', ['title' => 'Master Imports', 'subtitle' => 'Upload master data CSV(s) or a ZIP containing CSVs'])

<div class="row">
    <div class="col-md-8">
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title">Upload</h3></div>
            <div class="box-body">
                @if(session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif

                <form action="{{ route('masterdata.import.post') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="file">CSV or ZIP</label>
                        <input type="file" name="file" id="file" class="form-control" accept=".csv,.zip" required>
                    </div>
                    <button class="btn btn-primary">Upload and Queue Import</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="box box-info">
            <div class="box-header with-border"><h3 class="box-title">Notes</h3></div>
            <div class="box-body">
                <ul>
                    <li>Supported: CSV, ZIP (containing CSVs).</li>
                    <li>Files are processed in background. Results will be written to storage/imports.</li>
                    <li>Only admins/super-admins may use this tool.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection
