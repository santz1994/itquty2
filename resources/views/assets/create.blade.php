@extends('layouts.app')

@section('main-content')
  <div class="row">
    <div class="col-md-6">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">{{$pageTitle}}</h3>
        </div>
        <div class="box-body">
          <form method="POST" action="{{ url('assets') }}">
            {{csrf_field()}}
            <div class="form-group">
              <label for="serial_number">Serial Number</label>
              <input type="text"  name="serial_number" class="form-control" value="{{old('serial_number')}}" autofocus>
            </div>
            <div class="form-group">
              <label for="asset_model_id">Model</label>
              <select class="form-control asset_model_id" name="asset_model_id" id="asset_model_id">
                <option value = ""></option>
                @foreach($asset_models as $asset_model)
                    <option value="{{$asset_model->id}}" data-asset-type-id="{{$asset_model->asset_type->id}}" data-asset-type="{{$asset_model->asset_type->type_name}}">{{$asset_model->manufacturer->name}} - {{$asset_model->asset_model}}</option>
                @endforeach
              </select>
              <small id="asset-type-info" class="text-muted" style="display: none;"></small>
            </div>
            <div class="form-group">
              <label for="division_id">Division</label>
              <select class="form-control division_id" name="division_id">
                <option value = ""></option>
                @foreach($divisions as $division)
                    <option value="{{$division->id}}">{{$division->name}}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="supplier_id">Supplier</label>
              <select class="form-control supplier_id" name="supplier_id">
                <option value = ""></option>
                @foreach($suppliers as $supplier)
                    <option value="{{$supplier->id}}">{{$supplier->name}}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="invoice_id">Invoice</label>
              <select class="form-control invoice_id" name="invoice_id">
                <option value = ""></option>
                @foreach($invoices as $invoice)
                    <option value="{{$invoice->id}}">{{$invoice->invoice_number}} - {{$invoice->invoiced_date}} - {{$invoice->supplier->name}} - R{{$invoice->total}}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="purchase_date">Purchase Date</label>
              <input type="date"  name="purchase_date" class="form-control" value="{{old('purchase_date')}}">
            </div>
            <div class="form-group">
              <label for="warranty_months">Warranty Months</label>
              <input type="number"  name="warranty_months" class="form-control" value="{{old('warranty_months')}}">
            </div>
            <div class="form-group">
              <label for="warranty_type_id">Warranty Type</label>
              <select class="form-control warranty_type_id" name="warranty_type_id">
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
                  <label for="ip">IP Address</label>
                  <input type="text" name="ip" class="form-control" value="{{old('ip')}}" placeholder="e.g., 192.168.1.100">
                </div>
                <div class="form-group">
                  <label for="mac">MAC Address</label>
                  <input type="text" name="mac" class="form-control" value="{{old('mac')}}" placeholder="e.g., 00:1B:44:11:3A:B7">
                </div>
              </fieldset>
            </div>
            <div class="form-group">
              <label for="location">Deploy to a Location</label>
              <select class="form-control location" name="location">
                <option value = "">No</option>
                @foreach($locations as $location)
                    <option value="{{$location->id}}">{{$location->location_name}} - {{$location->building}}, {{$location->office}}</option>
                @endforeach
              </select>
            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-primary"><b>Add New Asset</b></button>
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
@endsection

@section('footer')
  <script type="text/javascript">
    $(document).ready(function() {
      $(".asset_model_id").select2();
      $(".division_id").select2();
      $(".supplier_id").select2();
      $(".location").select2();
      $(".warranty_type_id").select2();
      $(".invoice_id").select2();

      // Handle asset model change to show/hide conditional fields
      $('#asset_model_id').on('change', function() {
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
      if ($('#asset_model_id').val()) {
        $('#asset_model_id').trigger('change');
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


