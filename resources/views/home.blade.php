@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard-widgets.css') }}">
@endpush

@section('main-content')
@include('components.loading-overlay')
	@role(['super-admin', 'admin'])
		<!-- Quick summary cards -->
	<section class="content">
		<div class="row mb-3">
			<div class="col-md-12">
				<div class="card">
					<div class="card-body d-flex justify-content-between align-items-center">
						<div>
							<strong>Total Assets:</strong>
							<span class="ml-2">{{ \App\Asset::count() }}</span>
						<div>
							<strong>Open Tickets:</strong>
							<span class="ml-2">{{ \App\Ticket::where('ticket_status_id', '!=', \App\TicketsStatus::where('status', 'Closed')->value('id'))->count() ?? '\N/A' }}</span>
						</div>
						</div>
						<div>
							<strong>Recent Movements:</strong>
							<span class="ml-2">{{ isset($movements) ? $movements->count() : 0 }}</span>
						</div>
						<div class="text-muted small text-right">
							Server time: {{ now()->format('Y-m-d H:i:s') }}
							<br>
							<a href="{{ Route::has('reports.index') ? route('reports.index') : url('/reports') }}" class="btn btn-sm btn-outline-primary mt-1">Reports</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	
		<div class="row">
			<div class="col-md-5 col-xs-12">
	      <div class="box box-primary">
	        <div class="box-header with-border">
	          <h3 class="box-title">Latest Movement Activity</h3>
	        </div>
	        <div class="box-body">
						<ul class="timeline">
					    <!-- timeline time label -->
							@foreach($movements as $movement)
								<?php $createdDate = \Carbon\Carbon::parse($movement->created_at);
								$asset = App\Asset::find($movement->asset_id); ?>
								<li class="time-label">
					        <span class="bg-aqua">
				            {{$createdDate->format('l, j F Y')}}
					        </span>
						    </li>
						    <!-- /.timeline-label -->

						    <!-- timeline item -->
						    <li>
					        <!-- timeline icon -->
								<span class="ml-2">{{ \App\Ticket::where('ticket_status_id', '!=', \App\TicketsStatus::where('status', 'Closed')->value('id'))->count() ?? '\N/A' }}</span>
					        <div class="timeline-item">
				            <span class="time"><i class="fa fa-clock-o"></i> {{$createdDate->format('H:i')}}</span>

				            <h3 class="timeline-header">{{$movement->user->name}}</h3>

				            <div class="timeline-body">
											<dl class="dl-horizontal">
					              <dt>Asset:</dt><dd>{{$asset->asset_tag}}</dd>
					              <dt>Model:</dt><dd>{{$asset->model->manufacturer->name}} {{$asset->model->asset_model}}</dd>
					              <dt>Location:</dt><dd>{{$movement->location->location_name}}</dd>
					              <dt>Status Applied:</dt><dd>{{$movement->status->name}}</dd>
											</dl>
				            </div>
				            <div class="timeline-footer">
				            </div>
					        </div>
					    	</li>
						    <!-- END timeline item -->
							@endforeach
						</ul>
					</div>
				</div>
			</div>
		</div>
		</section>
	@endrole
@endsection


