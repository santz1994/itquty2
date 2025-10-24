@extends('layouts.app')

@section('main-content')

@include('components.page-header', ['title' => 'Import/Export Templates', 'subtitle' => 'Download templates for master data'])

<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title">Templates</h3></div>
            <div class="box-body">
                <ul>
                    @foreach($templates as $label => $link)
                        <li><a href="{{ $link }}">{{ $label }}</a></li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection
