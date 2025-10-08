@extends('layouts.app')

@section('title', 'Import Assets')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-upload"></i> Import Assets</h3>
                <div class="box-tools pull-right">
                    <a href="{{ route('assets.download-template') }}" class="btn btn-success btn-sm">
                        <i class="fa fa-download"></i> Download Template
                    </a>
                    <a href="{{ route('assets.index') }}" class="btn btn-default btn-sm">
                        <i class="fa fa-arrow-left"></i> Back to Assets
                    </a>
                </div>
            </div>
            
            <form action="{{ route('assets.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="box-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <h4><i class="icon fa fa-ban"></i> Error!</h4>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="file">Choose Excel File</label>
                        <input type="file" 
                               class="form-control" 
                               id="file" 
                               name="file" 
                               accept=".xlsx,.xls,.csv"
                               required>
                        <small class="help-block">
                            Accepted formats: .xlsx, .xls, .csv (Max size: 2MB)
                        </small>
                    </div>

                    <div class="alert alert-info">
                        <h4><i class="icon fa fa-info"></i> Import Instructions:</h4>
                        <ol>
                            <li>Download the template file using the button above</li>
                            <li>Fill in your asset data following the template format</li>
                            <li>Make sure all required fields are filled</li>
                            <li>Asset tags must be unique</li>
                            <li>Dates should be in YYYY-MM-DD format</li>
                            <li>Upload the completed file using the form above</li>
                        </ol>
                    </div>
                </div>
                
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-upload"></i> Import Assets
                    </button>
                    <a href="{{ route('assets.index') }}" class="btn btn-default">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection