@extends('layouts.app')

@section('main-content')

@include('components.page-header', ['title' => 'Master Import/Export', 'subtitle' => 'Consolidated master data import and export'])

<div class="row">
    <div class="col-md-6">
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title">Exports</h3></div>
            <div class="box-body">
                <p>Feature-specific exports:</p>
                <ul>
                    <li><a href="{{ route('assets.export') }}">Export Assets</a></li>
                    <li><a href="{{ route('tickets.export') }}">Export Tickets</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="box box-success">
            <div class="box-header with-border"><h3 class="box-title">Imports</h3></div>
            <div class="box-body">
                <p>Master imports let you upload CSVs for lookup/master data (locations, manufacturers, types).</p>
                <a href="{{ route('masterdata.imports') }}" class="btn btn-primary">Go to Imports</a>
            </div>
        </div>
    </div>
</div>

@endsection
