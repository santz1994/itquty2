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
            {{-- Standardized fields per request: Kode Assets, Kategori, Lokasi, User/PIC, Tanggal Beli, Suplier, Spesifikasi, IP, MAC, S/N --}}
            <div class="form-group">
              <label for="asset_tag">Kode Assets <span class="text-red">*</span></label>
              <input type="text" name="asset_tag" id="asset_tag" class="form-control" value="{{ old('asset_tag', $asset->asset_tag) }}" required maxlength="50">
            </div>

            <div class="form-group">
              <label for="asset_type_id">Kategori (Tipe Asset) <span class="text-red">*</span></label>
              <select name="asset_type_id" id="asset_type_id" class="form-control asset_type_id" required>
                <option value="">-- Pilih Kategori (Tipe) --</option>
                @foreach($asset_types as $atype)
                  <option value="{{ $atype->id }}" {{ (old('asset_type_id', $asset->model->asset_type_id ?? '') == $atype->id) ? 'selected' : '' }}>{{ $atype->type_name }}</option>
                @endforeach
              </select>
            </div>

            <div class="form-group">
              <label for="model_id">Model (optional)</label>
              <select name="model_id" id="model_id" class="form-control model_id">
                <option value="">-- Pilih Model (optional) --</option>
                @foreach($asset_models as $asset_model)
                  <option value="{{ $asset_model->id }}" data-asset-type="{{ $asset_model->asset_type_id }}" {{ (old('model_id', $asset->model_id) == $asset_model->id) ? 'selected' : '' }}>{{ $asset_model->manufacturer->name ?? '' }} - {{ $asset_model->asset_model }}</option>
                @endforeach
              </select>
            </div>

            <div class="form-group">
              <label for="location_id">Lokasi <span class="text-red">*</span></label>
              <select class="form-control location_id" name="location_id" id="location_id" required>
                <option value="">-- Pilih Lokasi --</option>
                @foreach($locations as $location)
                  <option value="{{$location->id}}" {{ (old('location_id', $asset->location_id) == $location->id) ? 'selected' : '' }}>{{$location->location_name}} - {{$location->building}}, {{$location->office}}</option>
                @endforeach
              </select>
            </div>

            <div class="form-group">
              <label for="assigned_to">User / PIC <span class="text-red">*</span></label>
              <select name="assigned_to" id="assigned_to" class="form-control assigned_to" required>
                <option value="">-- Pilih User / PIC --</option>
                @php $activeUsers = \App\User::where('is_active', 1)->orderBy('name')->get(); @endphp
                @foreach($activeUsers as $u)
                  <option value="{{ $u->id }}" {{ (old('assigned_to', $asset->assigned_to) == $u->id) ? 'selected' : '' }}>{{ $u->name }} ({{ $u->email }})</option>
                @endforeach
              </select>
            </div>

            <div class="form-group">
              <label for="purchase_date">Tanggal Beli <span class="text-red">*</span></label>
              <input type="date" name="purchase_date" class="form-control" id="purchase_date" value="{{ old('purchase_date', optional($asset->purchase_date)->format('Y-m-d')) }}" required>
            </div>

            <div class="form-group">
              <label for="warranty_type_id">Jenis Garansi <span class="text-red">*</span></label>
              <select class="form-control warranty_type_id" name="warranty_type_id" id="warranty_type_id" required>
                <option value="">-- Pilih Jenis Garansi --</option>
                @foreach($warranty_types as $warranty_type)
                  <option value="{{$warranty_type->id}}" {{ (old('warranty_type_id', $asset->warranty_type_id) == $warranty_type->id) ? 'selected' : '' }}>{{$warranty_type->name}}</option>
                @endforeach
              </select>
            </div>

            <div class="form-group">
              <label for="supplier_id">Suplier <span class="text-red">*</span></label>
              <select class="form-control supplier_id" name="supplier_id" id="supplier_id" required>
                <option value="">-- Pilih Supplier --</option>
                @foreach($suppliers as $supplier)
                  <option value="{{$supplier->id}}" {{ (old('supplier_id', $asset->supplier_id) == $supplier->id) ? 'selected' : '' }}>{{$supplier->name}}</option>
                @endforeach
              </select>
            </div>

            <div class="form-group">
              <label for="purchase_order_id">Purchase Order (Optional)</label>
              <select class="form-control purchase_order_id" name="purchase_order_id" id="purchase_order_id">
                <option value="">-- No Purchase Order --</option>
                @foreach($purchaseOrders ?? [] as $po)
                  <option value="{{ $po->id }}" {{ (old('purchase_order_id', $asset->purchase_order_id) == $po->id) ? 'selected' : '' }}>
                    {{ $po->po_number }} - {{ $po->order_date ? \Carbon\Carbon::parse($po->order_date)->format('Y-m-d') : '' }} - {{ $po->supplier ? $po->supplier->name : '' }}
                  </option>
                @endforeach
              </select>
            </div>

            <div class="form-group">
              <label for="notes">Spesifikasi <span class="text-red">*</span></label>
              <textarea name="notes" id="notes" class="form-control" rows="3" required>{{ old('notes', $asset->notes) }}</textarea>
            </div>

            <div class="form-group">
              <label for="ip_address">IP Address</label>
              <input type="text" name="ip_address" class="form-control" id="ip_address" value="{{ old('ip_address', $asset->ip_address) }}" placeholder="e.g., 192.168.1.100">
            </div>

            <div class="form-group">
              <label for="mac_address">MAC Address</label>
              <input type="text" name="mac_address" class="form-control" id="mac_address" value="{{ old('mac_address', $asset->mac_address) }}" placeholder="e.g., 00:1B:44:11:3A:B7">
            </div>

            <div class="form-group">
              <label for="serial_number">S/N</label>
              <input type="text" name="serial_number" id="serial_number" class="form-control" value="{{ old('serial_number', $asset->serial_number) }}">
              <small id="serial-feedback" class="text-muted" style="display:none"></small>
            </div>

            <div class="form-group">
              <label for="status_id">Status <span class="text-red">*</span></label>
              <select class="form-control status_id" name="status_id" id="status_id" required>
                <option value="">Select Status</option>
                @foreach($statuses as $status)
                  <option
                    @if($asset->status_id == $status->id)
                      selected
                    @endif
                  value="{{$status->id}}">{{$status->name}}</option>
                @endforeach
              </select>
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

@section('footer')
  <script type="text/javascript">
    // Form loading state
    $('#asset-edit-form').on('submit', function() {
      showLoading('Updating asset...');
    });

    // Serial number uniqueness check (AJAX) for edit form
    $(function(){
      $('#serial_number').on('blur', function(){
        var serial = $(this).val().trim();
        var excludeId = '{{ $asset->id }}';
        if (!serial) {
          $('#serial-feedback').hide();
          return;
        }
        $.getJSON('{{ route("api.assets.checkSerial") }}', { serial: serial, exclude_id: excludeId })
          .done(function(resp){
            if (resp && resp.success) {
              if (resp.exists) {
                $('#serial-feedback').show().removeClass('text-muted text-success').addClass('text-danger').text('Serial number already exists in the system.');
              } else {
                $('#serial-feedback').show().removeClass('text-danger text-muted').addClass('text-success').text('Serial number available.');
              }
            }
          }).fail(function(){
            $('#serial-feedback').hide();
          });
      });
    });

    $(":input").keypress(function(event){
      if (event.which == '10' || event.which == '13') {
        event.preventDefault();
      }
    });
  </script>

  <script type="text/javascript">
    $(document).ready(function() {
      // Initialize Select2 for all dropdowns
      $(".model_id").select2();
      $(".division_id").select2();
      $(".supplier_id").select2();
      $(".invoice_id").select2();
      $(".warranty_type_id").select2();
      $(".status_id").select2();
      $(".location_id").select2();
      $(".assigned_to").select2();
      $(".asset_type_id").select2();
      $(".purchase_order_id").select2();

      // When asset type changes in edit form, filter model options and toggle PC fields
      $('#asset_type_id').on('change', function() {
        var selectedText = $(this).find('option:selected').text();
        var selectedId = $(this).val();
        $('.pc-laptop-fields').hide();
        if (selectedText && (selectedText.toLowerCase().includes('pc') || selectedText.toLowerCase().includes('laptop') || selectedText.toLowerCase().includes('computer'))) {
          $('.pc-laptop-fields').show();
        }
        $('#model_id option').each(function() {
          var mt = $(this).data('asset-type') ? String($(this).data('asset-type')) : '';
          if (!selectedId || mt === '' || mt === selectedId) {
            $(this).show();
          } else {
            $(this).hide();
          }
        });
        if ($('#model_id option:selected').is(':hidden')) {
          $('#model_id').val('').trigger('change');
        }
      });

      // Trigger change on load to apply filtering if an asset type is already selected
      if ($('#asset_type_id').val()) {
        $('#asset_type_id').trigger('change');
      }
    });
  </script>
@endsection


