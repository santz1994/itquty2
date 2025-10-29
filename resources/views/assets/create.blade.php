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
            {{-- Standardized fields per request: Kode Assets, Kategori, Lokasi, User/PIC, Tanggal Beli, Suplier, Spesifikasi, IP, MAC, S/N --}}
            <div class="form-group">
              <label for="asset_tag">Kode Assets <span class="text-red">*</span></label>
              <input type="text" name="asset_tag" id="asset_tag" class="form-control" value="{{ old('asset_tag') }}" required maxlength="50" placeholder="e.g., AST-001">
            </div>

            <div class="form-group">
              <label for="asset_type_id">Kategori (Tipe Asset) <span class="text-red">*</span></label>
              <select name="asset_type_id" id="asset_type_id" class="form-control asset_type_id" required>
                <option value="">-- Pilih Kategori (Tipe) --</option>
                @foreach($asset_types as $atype)
                  <option value="{{ $atype->id }}" {{ old('asset_type_id') == $atype->id ? 'selected' : '' }}>{{ $atype->type_name }}</option>
                @endforeach
              </select>
            </div>

            <div class="form-group">
              <label for="model_id">Model (optional)</label>
              <select name="model_id" id="model_id" class="form-control model_id">
                <option value="">-- Pilih Model (optional) --</option>
                @foreach($asset_models as $model)
                  <option value="{{ $model->id }}" data-asset-type="{{ $model->asset_type_id }}" {{ old('model_id') == $model->id ? 'selected' : '' }}>{{ $model->manufacturer->name ?? '' }} - {{ $model->asset_model }}</option>
                @endforeach
              </select>
            </div>

            <div class="form-group">
              <label for="location_id">Lokasi <span class="text-red">*</span></label>
              <select class="form-control location_id" name="location_id" id="location_id" required>
                <option value="">-- Pilih Lokasi --</option>
                @foreach($locations as $location)
                    <option value="{{$location->id}}" {{ old('location_id') == $location->id ? 'selected' : '' }}>{{$location->location_name}} - {{$location->building}}, {{$location->office}}</option>
                @endforeach
              </select>
            </div>

            <div class="form-group">
              <label for="assigned_to">User / PIC <span class="text-red">*</span></label>
              <select name="assigned_to" id="assigned_to" class="form-control assigned_to" required>
                <option value="">-- Pilih User / PIC --</option>
                @php $activeUsers = \App\User::where('is_active', 1)->orderBy('name')->get(); @endphp
                @foreach($activeUsers as $u)
                  <option value="{{ $u->id }}" {{ old('assigned_to') == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->email }})</option>
                @endforeach
              </select>
            </div>

            <div class="form-group">
              <label for="purchase_date">Tanggal Beli <span class="text-red">*</span></label>
              <input type="date" name="purchase_date" id="purchase_date" class="form-control" value="{{ old('purchase_date') }}" required>
            </div>

            <div class="form-group">
              <label for="supplier_id">Suplier <span class="text-red">*</span></label>
              <select class="form-control supplier_id" name="supplier_id" id="supplier_id" required>
                <option value="">-- Pilih Supplier --</option>
                @foreach($suppliers as $supplier)
                    <option value="{{$supplier->id}}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{$supplier->name}}</option>
                @endforeach
              </select>
            </div>

            <div class="form-group">
              <label for="purchase_order_id">Purchase Order (Optional)</label>
              <select class="form-control purchase_order_id" name="purchase_order_id" id="purchase_order_id">
                <option value="">-- No Purchase Order --</option>
        @foreach($purchaseOrders ?? [] as $po)
          <option value="{{ $po->id }}" {{ old('purchase_order_id') == $po->id ? 'selected' : '' }}>
            {{ $po->po_number }} - {{ $po->order_date ? \Carbon\Carbon::parse($po->order_date)->format('Y-m-d') : '' }} - {{ $po->supplier ? $po->supplier->name : '' }}
          </option>
        @endforeach
              </select>
            </div>

            <div class="form-group">
              <label for="warranty_type_id">Jenis Garansi <span class="text-red">*</span></label>
              <select class="form-control warranty_type_id" name="warranty_type_id" id="warranty_type_id" required>
                <option value="">-- Pilih Jenis Garansi --</option>
                @foreach($warranty_types as $warranty_type)
                    <option value="{{$warranty_type->id}}" {{ old('warranty_type_id') == $warranty_type->id ? 'selected' : '' }}>{{$warranty_type->name}}</option>
                @endforeach
              </select>
            </div>

            <div class="form-group">
              <label for="notes">Spesifikasi <span class="text-red">*</span></label>
              <textarea name="notes" id="notes" class="form-control" rows="3" required>{{ old('notes') }}</textarea>
            </div>

            <div class="form-group">
              <label for="ip_address">IP Address</label>
              <input type="text" name="ip_address" id="ip_address" class="form-control" value="{{ old('ip_address') }}" placeholder="e.g., 192.168.1.100">
            </div>

            <div class="form-group">
              <label for="mac_address">MAC Address</label>
              <input type="text" name="mac_address" id="mac_address" class="form-control" value="{{ old('mac_address') }}" placeholder="e.g., 00:1B:44:11:3A:B7">
            </div>

            <div class="form-group">
              <label for="serial_number">S/N</label>
              <input type="text" name="serial_number" id="serial_number" class="form-control" value="{{ old('serial_number') }}">
              <small id="serial-feedback" class="text-muted" style="display:none"></small>
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
            <!-- Removed duplicate purchase_date and warranty_type blocks (kept the required ones above) -->
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
              <input type="text" name="asset_tag" id="asset_tag" class="form-control" value="{{old('asset_tag')}}" required maxlength="50" placeholder="e.g., AST-001">
              <small class="text-muted">Maximum 50 characters, must be unique</small>
            </div>
            
            {{-- Keep status and warranty fields hidden but preserve existing inputs so other logic continues to work (set defaults) --}}
            <input type="hidden" name="status_id" value="{{ old('status_id', 1) }}">
            <input type="hidden" name="warranty_type_id" value="{{ old('warranty_type_id') }}">
            <input type="hidden" name="warranty_months" value="{{ old('warranty_months', 0) }}">

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
  <script>
    // Serial number uniqueness check (AJAX) for create form
    $(function(){
      $('#serial_number').on('blur', function(){
        var serial = $(this).val().trim();
        if (!serial) {
          $('#serial-feedback').hide();
          return;
        }
        $.getJSON('{{ route("api.assets.checkSerial") }}', { serial: serial })
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
  </script>
@endsection

@section('footer')
  <script type="text/javascript">
  $(document).ready(function() {
  $(".model_id").select2();
  $(".division_id").select2();
  $(".supplier_id").select2();
  $(".location").select2();
  $(".location_id").select2();
  $(".assigned_to").select2();
  $(".asset_type_id").select2();
  $(".warranty_type_id").select2();
  $(".invoice_id").select2();
  $(".status_id").select2();

      // Handle asset model change to show/hide conditional fields
      // When asset type changes, show/hide PC-specific fields and filter model list
      $('#asset_type_id').on('change', function() {
        var selectedText = $(this).find('option:selected').text();
        var selectedId = $(this).val();
        // Hide PC/Laptop fields by default
        $('.pc-laptop-fields').hide();

        if (selectedText && (selectedText.toLowerCase().includes('pc') || selectedText.toLowerCase().includes('laptop') || selectedText.toLowerCase().includes('computer'))) {
          $('.pc-laptop-fields').show();
        }

        // Filter model select options by data-asset-type
        $('#model_id option').each(function() {
          var mt = $(this).data('asset-type') ? String($(this).data('asset-type')) : '';
          if (!selectedId || mt === '' || mt === selectedId) {
            $(this).show();
          } else {
            $(this).hide();
          }
        });
        // Reset model selection if current option hidden
        if ($('#model_id option:selected').is(':hidden')) {
          $('#model_id').val('').trigger('change');
        }
      });

      // Trigger change event on page load if there's a selected value (for form validation errors)
      if ($('#model_id').val()) {
        $('#model_id').trigger('change');
      }
    });
  </script>
  <script>
    // Serial number uniqueness check (AJAX)
    $(function(){
      $('#serial_number').on('blur', function(){
        var serial = $(this).val().trim();
        if (!serial) {
          $('#serial-feedback').hide();
          return;
        }
        // Call API endpoint to check uniqueness
        $.getJSON('{{ route("api.assets.checkSerial") }}', { serial: serial })
          .done(function(resp){
            if (resp && resp.success) {
              if (resp.exists) {
                $('#serial-feedback').show().removeClass('text-muted text-success').addClass('text-danger').text('Serial number already exists in the system.');
              } else {
                $('#serial-feedback').show().removeClass('text-danger text-muted').addClass('text-success').text('Serial number available.');
              }
            }
          }).fail(function(){
            // silently fail (keep UX responsive)
            $('#serial-feedback').hide();
          });
      });
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


