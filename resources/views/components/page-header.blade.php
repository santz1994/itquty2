{{-- Page Header Component --}}
{{-- Usage: @include('components.page-header', ['title' => 'Page Title', 'breadcrumbs' => [...], 'actions' => '...']) --}}

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">{{ $title }}</h1>
                @if(isset($subtitle))
                <p class="text-muted">{{ $subtitle }}</p>
                @endif
                
                @if(isset($breadcrumbs) && count($breadcrumbs) > 0)
                <ol class="breadcrumb mt-2">
                    @foreach($breadcrumbs as $crumb)
                    <li class="breadcrumb-item {{ isset($crumb['url']) ? '' : 'active' }}">
                        @if(isset($crumb['url']))
                        <a href="{{ $crumb['url'] }}">
                            @if(isset($crumb['icon']))
                            <i class="fa fa-{{ $crumb['icon'] }}"></i>
                            @endif
                            {{ $crumb['label'] }}
                        </a>
                        @else
                        @if(isset($crumb['icon']))
                        <i class="fa fa-{{ $crumb['icon'] }}"></i>
                        @endif
                        {{ $crumb['label'] }}
                        @endif
                    </li>
                    @endforeach
                </ol>
                @endif
            </div>
            
            @if(isset($actions))
            <div class="col-sm-6">
                <div class="float-sm-right">
                    {!! $actions !!}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
