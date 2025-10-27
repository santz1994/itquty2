@extends('layouts.app')

@section('main-content')

@include('components.page-header', [
    'title' => 'Assets',
    'subtitle' => 'Manage all IT assets and equipment',
    'breadcrumbs' => [
        ['label' => 'Home', 'url' => route('admin.dashboard'), 'icon' => 'home'],
        ['label' => 'Assets']
    ],
    'actions' => '<a href="'.route('assets.create').'" class="btn btn-primary">
        <i class="fa fa-plus"></i> Create New Asset
    </a>
    <a href="'.route('assets.import-form').'" class="btn btn-info">
        <i class="fa fa-upload"></i> Import
    </a>
    <a href="'.route('assets.export').'" class="btn btn-success">
        <i class="fa fa-download"></i> Export
    </a>'
])

  <div class="row">
    <div class="col-md-2 col-sm-6 col-xs-12">
      <div class="small-box bg-purple">
        <div class="inner">
          <h3>{{$totalAssets}}</h3>
          <p>Total</p>
        </div>
        <div class="icon">
          <i class="fa fa-tags"></i>
        </div>
      </div>
      <!-- /.info-box -->
    </div>
    <div class="col-md-2 col-sm-6 col-xs-12">
      <div class="small-box bg-aqua">
        <div class="inner">
          <h3>{{$deployed}}</h3>
          <p>Deployed</p>
        </div>
        <div class="icon">
          <i class="fa fa-check-circle"></i>
        </div>
      </div>
      <!-- /.info-box -->
    </div>
    <div class="col-md-2 col-sm-6 col-xs-12">
      <div class="small-box bg-green">
        <div class="inner">
          <h3>{{$readyToDeploy}}</h3>
          <p>Ready</p>
        </div>
        <div class="icon">
          <i class="fa fa-plus-circle"></i>
        </div>
      </div>
      <!-- /.info-box -->
    </div>
    <div class="col-md-2 col-sm-6 col-xs-12">
      <div class="small-box bg-yellow">
        <div class="inner">
          <h3>{{$repairs}}</h3>
          <p>Repairs</p>
        </div>
        <div class="icon">
          <i class="fa fa-question-circle"></i>
        </div>
      </div>
      <!-- /.info-box -->
    </div>
    <div class="col-md-2 col-sm-6 col-xs-12">
      <div class="small-box bg-red">
        <div class="inner">
          <h3>{{$writtenOff}}</h3>
          <p>Written Off</p>
        </div>
        <div class="icon">
          <i class="fa fa-times-circle"></i>
        </div>
      </div>
      <!-- /.info-box -->
    </div>
  </div>
  <div class="row">
    <div class="col-md-4">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">Assets by Status</h3>
        </div>
        <div class="box-body">
          {{-- Per-page selector: let user choose how many rows to show, or show all --}}
          <form method="get" class="form-inline mb-2">
            <div class="form-group">
              <label for="per_page" class="mr-2">Show</label>
              <select name="per_page" id="per_page" class="form-control input-sm">
                <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10</option>
                <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25</option>
                <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
                <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100</option>
                <option value="all" {{ request('per_page') == 'all' || request()->boolean('all') ? 'selected' : '' }}>All</option>
              </select>
            </div>
            <div class="form-group ml-2">
              <button class="btn btn-default btn-sm">Apply</button>
            </div>
            {{-- Preserve other filters in querystring --}}
            @foreach(request()->except(['per_page', 'page']) as $k => $v)
              <input type="hidden" name="{{ $k }}" value="{{ $v }}" />
            @endforeach
          </form>

          @if(!empty($assetsByStatus) && count($assetsByStatus) > 0)
            <table class="table table-condensed">
              <thead>
                <tr><th>Status</th><th class="text-right">Count</th></tr>
              </thead>
              <tbody>
                @foreach($assetsByStatus as $s)
                  <tr>
                    <td>{{ $s->status_name }}</td>
                    <td class="text-right">{{ $s->count }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          @else
            <p>No data available</p>
          @endif
        </div>
      </div>
    </div>
    <div class="col-md-8">
      <div class="box box-warning">
        <div class="box-header with-border">
          <h3 class="box-title">New Assets (Last 6 months)</h3>
        </div>
        <div class="box-body">
          @if(!empty($monthlyNewAssets) && count($monthlyNewAssets) > 0)
            <div class="table-responsive">
              <table class="table table-condensed">
                <thead>
                  <tr><th>Month</th><th class="text-right">New Assets</th></tr>
                </thead>
                <tbody>
                  @foreach($monthlyNewAssets as $m)
                    <tr>
                      <td>{{ $m['month'] ?? $m->month ?? 'N/A' }}</td>
                      <td class="text-right">{{ $m['count'] ?? $m->count ?? 0 }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <p>No recent assets</p>
          @endif
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12 col-xs-12 col-lg-12">
      <div class="box box-primary">
        <div class="box-body">
          <table id="table" class="table table-enhanced table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th class="sortable" data-column="asset_tag">Tag</th>
                <th class="sortable" data-column="asset_type">Asset Type</th>
                <th class="sortable" data-column="serial_number">S/N</th>
                <th class="sortable" data-column="age">Age</th>
                <th class="sortable" data-column="model">Model</th>
                <th class="sortable" data-column="location">Location</th>
                <th class="sortable" data-column="division">Division</th>
                <th class="sortable" data-column="status">Status</th>
                <th class="actions">Actions</th>
                <th class="sortable" data-column="supplier">Supplier</th>
                <th class="sortable" data-column="purchase_date">Purchase Date</th>
                <th class="sortable" data-column="warranty_months">Warranty Months</th>
                <th class="sortable" data-column="warranty_type">Warranty Type</th>
              </tr>
            </thead>
            <tbody>
              <?php $now = new \Carbon\Carbon(); ?>
              @foreach($assets as $asset)
                @if($asset->purchase_date != '0000-00-00')
                  <?php $purchasedDate = \Carbon\Carbon::parse($asset->purchase_date);
                  $age = $purchasedDate->diffInMonths($now); ?>
                @endif
                <tr
                  @if(isset($age))
                    @if($age > 59)
                      class="danger"
                    @elseif($age > 47 && $age < 60)
                      class="warning"
                    @endif
                  @endif
                >
                  <div>
                    <td>{{$asset->asset_tag}}</td>
                    <td>{{ $asset->model && $asset->model->asset_type ? $asset->model->asset_type->type_name : 'N/A' }}</td>
                    <td>{{$asset->serial_number or ''}}</td>
                    <td>
                      @if (isset($age))
                        <?php $years = $age / 12;
                              $months = $age % 12;
                               ?>
                        {{floor($years)}}Y, {{$months}}M
                      @endif
                    </td>
                    <td>
                      <div id="model{{$asset->id}}" class="hover-pointer">
                        {{ $asset->model ? (($asset->model->manufacturer ? $asset->model->manufacturer->name : 'N/A') . ' - ' . $asset->model->asset_model) : 'N/A' }}
                      </div>
                    </td>
                    <td>
                      <div id="location{{$asset->id}}" class="hover-pointer">
                        {{ $asset->movement ? ($asset->movement->location ? $asset->movement->location->location_name : 'N/A') : 'N/A' }}
                      </div>
                    </td>
                    <td>
                      <div id="division{{$asset->id}}" class="hover-pointer">
                        {{ $asset->division ? $asset->division->name : 'N/A' }}
                      </div>
                    </td>
                    <td>
                      <div id="status{{$asset->id}}" class="hover-pointer">
                        @if($asset->status)
                          @if($asset->status->id == 1)
                            <span class="label label-success">
                          @elseif($asset->status->id == 2)
                            <span class="label label-info">
                          @elseif($asset->status->id == 3 || $asset->status->id == 4)
                            <span class="label label-warning">
                          @elseif($asset->status->id == 5 || $asset->status->id == 6)
                            <span class="label label-danger">
                          @else
                            <span class="label label-default">
                          @endif
                          {{$asset->status->name}}</span>
                        @else
                          <span class="label label-default">No Status</span>
                        @endif
                      </div>
                    </td>
                    <td>
                      <div class="btn-group">
                        <a href="/assets/{{ $asset->id }}/move" class="btn btn-primary"><span class="fa fa-send" aria-hidden="true"></span> <b>Move</b></a>
                        <a href="/assets/{{ $asset->id }}/history" class="btn btn-primary"><span class="fa fa-calendar" aria-hidden="true"></span> <b>History</b></a>
                        <a href="{{ route('tickets.create', ['asset_id' => $asset->id]) }}" class="btn btn-warning"><span class="fa fa-ticket" aria-hidden="true"></span> <b>Tickets</b></a>
                        <a href="/assets/{{ $asset->id }}/edit" class="btn btn-primary"><span class="fa fa-pencil" aria-hidden="true"></span> <b>Edit</b></a>
                        <form method="POST" action="{{ url('assets/' . $asset->id) }}" style="display:inline-block; margin-left:6px;" onsubmit="return confirm('Are you sure you want to delete this asset?');">
                          {{ csrf_field() }}
                          {{ method_field('DELETE') }}
                          <button type="submit" class="btn btn-danger"><span class="fa fa-trash" aria-hidden="true"></span> <b>Delete</b></button>
                        </form>
                      </div>
                    </td>
                    <td>{{ $asset->supplier ? $asset->supplier->name : 'N/A' }}</td>
                    <td>{{$asset->purchase_date}}</td>
                    <td>{{$asset->warranty_months}}</td>
                    <td>{{ $asset->warranty_type ? $asset->warranty_type->name : 'N/A' }}</td>
                  </div>
                </tr>
                <?php $age = null; ?>
              @endforeach
            </tbody>
          </table>
          {{-- If $assets is a paginator, show Laravel pagination controls so users can navigate pages --}}
          @if(method_exists($assets, 'links'))
            <div class="mt-2">
              {{ $assets->links() }}
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
  <script>
  $(document).ready(function() {
  var table = $('#table').DataTable( {
    responsive: true,
    dom: 'l<"clear">Bfrtip',
    // Default page length and length menu. Note: DataTables paginates client-side
    // only the rows present in the HTML. To let DataTables handle all rows client-side,
    // request all rows by adding `?all=1` to the assets index URL (controller supports this).
    pageLength: 25,
    lengthMenu: [[10,25,50,100,-1],[10,25,50,100,'All']],
        buttons: [
            {
              extend: 'excel',
              exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 8, 9, 10, 11, 12]
              }
            },
            {
              extend: 'csv',
              exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 8, 9, 10, 11, 12]
              }
            },
            {
              extend: 'copy',
              exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 8, 9, 10, 11, 12]
              }
            }
        ],
        columns: [
          null,
          { "visible": false },
          null,
          null,
          null,
          null,
          null,
          null,
          null,
          { "visible": false },
          { "visible": false },
          { "visible": false },
          { "visible": false }
        ], columnDefs: [{
          orderable: false, targets: 8
        }]
    } );
    // Get the model, location, division and status columns' div IDs for each row.
    // If it is clicked on, then the datatable will filter that.
    @foreach($assets as $asset)
      // Model
      var model = (function() {
        var x = '#model' + {{$asset->id}};
        return x;
      });
      $(model()).click(function () {
        @if($asset->model && $asset->model->manufacturer)
        table.search( "{{$asset->model->manufacturer->name}} - {{$asset->model->asset_model}}" ).draw();
        @elseif($asset->model)
        table.search( "{{$asset->model->asset_model}}" ).draw();
        @else
        table.search( "N/A" ).draw();
        @endif
      });

      // Location
      var location = (function() {
        var x = '#location' + {{$asset->id}};
        return x;
      });
      $(location()).click(function () {
        @if($asset->movement && $asset->movement->location)
        table.search( "{{$asset->movement->location->location_name}}" ).draw();
        @else
        table.search( "N/A" ).draw();
        @endif
      });

      // Division
      var division = (function() {
        var x = '#division' + {{$asset->id}};
        return x;
      });
      $(division()).click(function () {
        @if($asset->division)
        table.search( "{{$asset->division->name}}" ).draw();
        @else
        table.search( "N/A" ).draw();
        @endif
      });

      // Status
      var status = (function() {
        var x = '#status' + {{$asset->id}};
        return x;
      });
      $(status()).click(function () {
        @if($asset->movement && $asset->movement->status)
        table.search( "{{$asset->movement->status->name}}" ).draw();
        @else
        table.search( "N/A" ).draw();
        @endif
      });
    @endforeach
  } );
  </script>
  @if(Session::has('status'))
    <script>
      $(document).ready(function() {
        Command: toastr["{{Session::get('status')}}"]("{{Session::get('message')}}", "{{Session::get('title')}}");
      });
    </script>
  @endif

@include('components.loading-overlay')

@endsection


