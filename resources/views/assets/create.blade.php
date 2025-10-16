@extends('layouts.app')

@section('main-content')

{{-- Page Header --}}
@include('components.page-header', [
    'title' => $pageTitle ?? 'Create New Asset',
    'subtitle' => 'Add a new asset to the inventory',
    'breadcrumbs' => [
        ['label' => 'Home', 'url' => route('home'), 'icon' => 'home'],
        ['label' => 'Assets', 'url' => route('assets.index')],
        ['label' => 'Create']
    ],
    'actions' => '<a href="'.route('assets.index').'" class="btn btn-secondary">
        <i class="fa fa-arrow-left"></i> Back to List
    </a>'
])

  <div class="row">
    <div class="col-md-6">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Asset Information</h3>
        </div>
        <div class="box-body">
          <form method="POST" action="{{ url('assets') }}" id="asset-create-form">
            {{csrf_field()}}
            <div class="form-group">
              <label for="serial_number">Serial Number</label>
              <input type="text" name="serial_number" id="serial_number" class="form-control" value="{{old('serial_number')}}" autofocus>
            </div>
            <div class="form-group">
                <label for="model_id">Asset Model</label>
                <select name="model_id" id="model_id" class="form-control" required>
                    <option value="">-- Select Asset Model --</option>
            @php
              // Prefer composer-provided `asset_models` (cached) but fall back to controller's $models
              $modelsList = isset($asset_models) ? $asset_models : (isset($models) ? $models : collect());
            @endphp
            @foreach ($modelsList as $model)
              <option value="{{ $model->id }}">{{ $model->manufacturer->name ?? '' }} - {{ $model->name }}</option>
            @endforeach
                </select>
            </div>
            <div class="form-group">
              <label for="division_id">Division</label>
              <select class="form-control division_id" name="division_id" id="division_id">
                <option value = ""></option>
                @foreach($divisions as $division)
                    <option value="{{$division->id}}">{{$division->name}}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="supplier_id">Supplier</label>
              <select class="form-control supplier_id" name="supplier_id" id="supplier_id">
                <option value = ""></option>
                @foreach($suppliers as $supplier)
                    <option value="{{$supplier->id}}">{{$supplier->name}}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="invoice_id">Invoice (Optional)</label>
              <select class="form-control invoice_id" name="invoice_id" id="invoice_id">
                <option value="">No Invoice</option>
                @foreach($invoices as $invoice)
                    <option value="{{$invoice->id}}">{{$invoice->invoice_number}} - {{$invoice->invoiced_date}} - {{$invoice->supplier->name}} - R{{$invoice->total}}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="purchase_date">Purchase Date</label>
              <input type="date" name="purchase_date" id="purchase_date" class="form-control" value="{{old('purchase_date')}}">
            </div>
            <div class="form-group">
              <label for="warranty_months">Warranty Months</label>
              <input type="number" name="warranty_months" id="warranty_months" class="form-control" value="{{old('warranty_months')}}">
            </div>
            <div class="form-group">
              <label for="warranty_type_id">Warranty Type</label>
              <select class="form-control warranty_type_id" name="warranty_type_id" id="warranty_type_id">
                <option value = ""></option>
                @foreach($warranty_types as $warranty_type)
                    <option value="{{$warranty_type->id}}">{{$warranty_type->name}}</option>
                @endforeach
              </select>
            </div>
            <!-- Computer-specific fields -->
            <div class="pc-laptop-fields" style="display: none;">
              <fieldset style="border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 4px;">
                <legend style="font-size: 14px; font-weight: bold; color: #337ab7;">Computer Specifications</legend>
                <div class="form-group">
                  <label for="ip_address">IP Address</label>
                  <input type="text" name="ip_address" id="ip_address" class="form-control" value="{{old('ip_address')}}" placeholder="e.g., 192.168.1.100">
                </div>
                <div class="form-group">
                  <label for="mac_address">MAC Address</label>
                  <input type="text" name="mac_address" id="mac_address" class="form-control" value="{{old('mac_address')}}" placeholder="e.g., 00:1B:44:11:3A:B7">
                </div>
              </fieldset>
            </div>
            <div class="form-group">
              <label for="asset_tag">Asset Tag <span class="text-red">*</span></label>
              <input type="text" name="asset_tag" id="asset_tag" class="form-control" value="{{old('asset_tag')}}" required maxlength="10" placeholder="e.g., AST-001">
              <small class="text-muted">Maximum 10 characters, must be unique</small>
            </div>
            
            <div class="form-group">
              <label for="status_id">Status <span class="text-red">*</span></label>
              <select class="form-control status_id" name="status_id" id="status_id" required>
                <option value="">Select Status</option>
                @if(isset($statuses))
                  @foreach($statuses as $status)
                      <option value="{{$status->id}}" {{ old('status_id') == $status->id ? 'selected' : '' }}>{{$status->name}}</option>
                  @endforeach
                @else
                  <option value="1">In Stock</option>
                  <option value="2">In Use</option>
                  <option value="3">In Repair</option>
                  <option value="4">Disposed</option>
                @endif
              </select>
            </div>
            
            <div class="form-group">
              <label for="location">Deploy to a Location</label>
              <select class="form-control location" name="location" id="location">
                <option value = "">No</option>
                @foreach($locations as $location)
                    <option value="{{$location->id}}">{{$location->location_name}} - {{$location->building}}, {{$location->office}}</option>
                @endforeach
              </select>
            </div>
            
            <div class="form-group">
              <label for="notes">Notes</label>
              <textarea name="notes" id="notes" class="form-control" rows="3" maxlength="1000" placeholder="Additional notes about this asset...">{{old('notes')}}</textarea>
            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-primary btn-lg">
                <i class="fa fa-save"></i> <b>Add New Asset</b>
              </button>
              <a href="{{ route('assets.index') }}" class="btn btn-secondary btn-lg">
                <i class="fa fa-times"></i> Cancel
              </a>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Useful Links</h3>
        </div>
        <div class="box-body">
          <ul>
            <li><a href="http://h20564.www2.hp.com/hpsc/wc/public/home" target="_blank">HP Warranty Check</a></li>
            <li><a href="http://customercare.acer-euro.com/customerselfservice/CaseBooking.aspx?CID=ZA&LID=ENG&OP=1#_ga=1.185835882.214577358.1416317708" target="_blank">Acer Warranty Check</a></li>
          </ul>
        </div>
      </div>

      @if(count($errors))
        <ul>
          @foreach($errors->all() as $error)
            <li>{{$error}}</li>
          @endforeach
        </ul>
      @endif
    </div>
  </div>

{{-- Loading Overlay --}}
@include('components.loading-overlay')

@endsection

@push('scripts')
<script type="text/javascript">
  // Form loading state
  $('#asset-create-form').on('submit', function() {
    showLoading('Creating asset...');
  });
</script>
@endpush

@section('footer')
  <script type="text/javascript">
    $(document).ready(function() {
      $(".model_id").select2();
      $(".division_id").select2();
      $(".supplier_id").select2();
      $(".location").select2();
      $(".warranty_type_id").select2();
      $(".invoice_id").select2();
      $(".status_id").select2();

      // Handle asset model change to show/hide conditional fields
      $('#model_id').on('change', function() {
        var selectedOption = $(this).find('option:selected');
        var assetType = selectedOption.data('asset-type');
        var assetTypeInfo = $('#asset-type-info');
        
        // Hide all conditional fields first
        $('.pc-laptop-fields').hide();
        
        if (assetType) {
          // Show asset type information
          assetTypeInfo.text('Asset Type: ' + assetType).show();
          
          // Show relevant fields based on asset type
          if (assetType.toLowerCase().includes('pc') || assetType.toLowerCase().includes('laptop')) {
            $('.pc-laptop-fields').show();
          }
        } else {
          // Hide asset type info if no selection
          assetTypeInfo.hide();
        }
      });

      // Trigger change event on page load if there's a selected value (for form validation errors)
      if ($('#model_id').val()) {
        $('#model_id').trigger('change');
      }
    });
  </script>
  <script>
    $(":input").keypress(function(event){
      if (event.which == '10' || event.which == '13') {
        event.preventDefault();
      }
    });
  </script>
@endsection


