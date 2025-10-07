{{-- Page Header Partial --}}
@php
    $title = $title ?? $pageTitle ?? 'Page Title';
    $breadcrumbs = $breadcrumbs ?? [];
    $showAddButton = $showAddButton ?? false;
    $addButtonRoute = $addButtonRoute ?? '';
    $addButtonText = $addButtonText ?? 'Add New';
    $addButtonPermission = $addButtonPermission ?? '';
@endphp

<section class="content-header">
    <h1>
        {{ $title }}
        @if(isset($subtitle))
            <small>{{ $subtitle }}</small>
        @endif
    </h1>
    
    @if(!empty($breadcrumbs))
        <ol class="breadcrumb">
            <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
            @foreach($breadcrumbs as $breadcrumb)
                @if(isset($breadcrumb['url']))
                    <li><a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['title'] }}</a></li>
                @else
                    <li class="active">{{ $breadcrumb['title'] }}</li>
                @endif
            @endforeach
        </ol>
    @endif
    
    @if($showAddButton && $addButtonRoute)
        @if(!$addButtonPermission || auth()->user()->can($addButtonPermission))
            <div class="pull-right" style="margin-top: -25px;">
                <a href="{{ route($addButtonRoute) }}" class="btn btn-primary">
                    <i class="fa fa-plus"></i> {{ $addButtonText }}
                </a>
            </div>
        @endif
    @endif
</section>