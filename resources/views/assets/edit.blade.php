@extends('layouts.app')

@section('main-content')

{{-- Page Header --}}
@include('components.page-header', [
    'title' => $pageTitle ?? 'Edit Asset',
    'subtitle' => 'Update asset information - ' . ($asset->asset_tag ?? ''),
    'breadcrumbs' => [
        ['label' => 'Home', 'url' => route('home'), 'icon' => 'home'],
        ['label' => 'Assets', 'url' => route('assets.index')],
        ['label' => 'Edit']
    ],
    'actions' => '<a href="'.route('assets.show', $asset->id).'" class="btn btn-info">
        <i class="fa fa-eye"></i> View Asset
    </a>
    <a href="'.route('assets.index').'" class="btn btn-secondary">
        <i class="fa fa-arrow-left"></i> Back to List
    </a>'
])

  <div class="row">
    <div class="col-md-6 col-md-offset-3">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Asset Information</h3>
        </div>
        <div class="box-body">
          <form method="POST" action="/assets/{{$asset->id}}" id="asset-edit-form">
            {{method_field('PATCH')}}
            {{csrf_field()}}
            <div class="form-group">
              <label for="serial_number">Serial Number</label>
              <input type="text" name="serial_number" id="serial_number" class="form-control" value="{{$asset->serial_number}}">
            </div>
            <div class="form-group">
              <label for="model_id">Model</label>
              <select class="form-control model_id" name="model_id" id="model_id" required>
                @foreach($asset_models as $asset_model)
                  <option
                    @if($asset->model_id == $asset_model->id)
                      selected
                    @endif
                  value="{{$asset_model->id}}">{{$asset_model->manufacturer->name}} - {{$asset_model->asset_model}}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="division_id">Division</label>
              <select class="form-control division_id" name="division_id" id="division_id">
                @foreach($divisions as $division)
                  <option
                    @if($asset->division_id == $division->id)
                      selected
                    @endif
                  value="{{$division->id}}">{{$division->name}}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="supplier_id">Supplier</label>
              <select class="form-control supplier_id" name="supplier_id" id="supplier_id">
                @foreach($suppliers as $supplier)
                  <option
                    @if($asset->supplier_id == $supplier->id)
                      selected
                    @endif
                  value="{{$supplier->id}}">{{$supplier->name}}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="invoice_id">Invoice</label>
              <select class="form-control invoice_id" name="invoice_id" id="invoice_id">
                <option value=""></option>
                @foreach($invoices as $invoice)
                  <option
                    @if($asset->invoice_id == $invoice->id)
                      selected
                    @endif
                  value="{{$invoice->id}}">{{$invoice->invoice_number}} - {{$invoice->invoiced_date}} - {{$invoice->supplier->name}} - R{{$invoice->total}}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="purchase_date">Purchase Date</label>
1              <input type="date" name="purchase_date" class="form-control" id="purchase_date" value="{{ old('purchase_date', optional($asset->purchase_date)->format('Y-m-d')) }}">
            </div>
            <div class="form-group">
              <label for="warranty_months">Warranty Months</label>
              <input type="number" min="0" name="warranty_months" class="form-control" id="warranty_months" value="{{ old('warranty_months', $asset->warranty_months) }}">
            </div>
            <div class="form-group">
              <label for="warranty_type_id">Warranty Type</label>
              <select class="form-control warranty_type_id" name="warranty_type_id" id="warranty_type_id">
                <option value=""></option>
                @foreach($warranty_types as $warranty_type)
                  <option
                    @if($asset->warranty_type_id == $warranty_type->id)
                      selected
                    @endif
                  value="{{$warranty_type->id}}">{{$warranty_type->name}}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="ip_address">IP Address (If PC/Laptop)</label>
              <input type="text" name="ip_address" class="form-control" id="ip_address" value="{{ old('ip_address', $asset->ip_address) }}">
            </div>
            <div class="form-group">
              <label for="mac_address">MAC Address (If PC/Laptop)</label>
              <input type="text" name="mac_address" class="form-control" id="mac_address" value="{{ old('mac_address', $asset->mac_address) }}">
            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-primary btn-lg">
                <i class="fa fa-save"></i> <b>Update Asset</b>
              </button>
              <a href="{{ route('assets.show', $asset->id) }}" class="btn btn-info btn-lg">
                <i class="fa fa-eye"></i> View
              </a>
              <a href="{{ route('assets.index') }}" class="btn btn-secondary btn-lg">
                <i class="fa fa-times"></i> Cancel
              </a>
            </div>
          </form>
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
  $('#asset-edit-form').on('submit', function() {
    showLoading('Updating asset...');
  });
</script>
@endpush

@section('footer')
  <script type="text/javascript">
    $(document).ready(function() {
      $(".model_id").select2();
      $(".division_id").select2();
      $(".supplier_id").select2();
      $(".invoice_id").select2();
      $(".warranty_type_id").select2();
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


