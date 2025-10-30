@extends('layouts.app')

@section('main-content')

{{-- All styles moved to public/css/ui-enhancements.css for better performance and maintainability --}}

@include('components.page-header', [
    'title' => 'Create New Ticket',
    'subtitle' => 'Submit a new support ticket',
    'breadcrumbs' => [
        ['label' => 'Home', 'url' => route('admin.dashboard'), 'icon' => 'home'],
        ['label' => 'Tickets', 'url' => route('tickets.index')],
        ['label' => 'Create']
    ]
])

  <div class="row">
    <div class="col-md-8">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Ticket Information</h3>
        </div>
        <div class="box-body">
          {{-- Flash Messages --}}
          @if(session('success'))
            <div class="alert alert-success alert-dismissible">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
              <i class="icon fa fa-check"></i> {{ session('success') }}
            </div>
          @endif

          @if(session('error'))
            <div class="alert alert-danger alert-dismissible">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
              <i class="icon fa fa-ban"></i> {{ session('error') }}
            </div>
          @endif

          <form method="POST" action="{{ url('tickets') }}" id="ticket-create-form">
            {{csrf_field()}}
            
            {{-- Hidden creator field --}}
            <input type="hidden" name="user_id" value="{{ old('user_id', Auth::id()) }}">

            {{-- SECTION 1: Basic Information --}}
            <fieldset>
              <legend><i class="fa fa-info-circle"></i> Basic Information</legend>

              <div class="form-group">
                <label>Creator / Reporter</label>
                <p class="form-control-static"><i class="fa fa-user"></i> <strong>{{ Auth::user()->name }}</strong> ({{ Auth::user()->email }})</p>
                <small class="text-muted">You are creating this ticket on behalf of yourself</small>
              </div>

              <div class="form-group">
                <label for="subject">Subject <span class="text-red">*</span></label>
                <input type="text" class="form-control @error('subject') is-invalid @enderror" name="subject" id="subject" value="{{old('subject')}}" required maxlength="255">
                <small class="text-muted">Brief summary of the issue (max 255 characters)</small>
                @error('subject')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
              </div>

              <div class="form-group">
                <label for="description">Description <span class="text-red">*</span></label>
                <span id="char-counter">0 / 10 characters (minimum 10)</span>
                <textarea class="form-control @error('description') is-invalid @enderror" rows="5" name="description" id="description" required minlength="10">{{old('description')}}</textarea>
                <small class="text-muted">Detailed description of the issue or request (minimum 10 characters)</small>
                @error('description')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
              </div>

              <div class="form-group">
                <label for="ticket_type_id">Ticket Type <span class="text-red">*</span></label>
                <select class="form-control ticket_type_id @error('ticket_type_id') is-invalid @enderror" name="ticket_type_id" id="ticket_type_id" required>
                  <option value="">-- Select Ticket Type --</option>
                  @foreach($ticketsTypes as $ticketType)
                    <option value="{{$ticketType->id}}" {{ old('ticket_type_id') == $ticketType->id ? 'selected' : '' }}>{{$ticketType->type}}</option>
                  @endforeach
                </select>
                <small class="text-muted">Category of request (e.g., Hardware Issue, Software Support, Network Problem)</small>
                @error('ticket_type_id')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
              </div>

              <div class="form-group">
                <label for="ticket_priority_id">Priority <span class="text-red">*</span></label>
                <select class="form-control ticket_priority_id @error('ticket_priority_id') is-invalid @enderror" name="ticket_priority_id" id="ticket_priority_id" required>
                  <option value="">-- Select Priority --</option>
                  @foreach($ticketsPriorities as $ticketsPriority)
                    <option value="{{$ticketsPriority->id}}" {{ old('ticket_priority_id') == $ticketsPriority->id ? 'selected' : '' }}>{{$ticketsPriority->priority}}</option>
                  @endforeach
                </select>
                <small class="text-muted">Urgency level - affects SLA due date (High = urgent, Medium = normal, Low = can wait)</small>
                @error('ticket_priority_id')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
              </div>

              <div class="form-group">
                <label for="ticket_status_id">Initial Status</label>
                <select class="form-control ticket_status_id @error('ticket_status_id') is-invalid @enderror" name="ticket_status_id" id="ticket_status_id">
                  <option value="">-- Select Status (defaults to Open) --</option>
                  @foreach($ticketsStatuses as $ticketStatus)
                    <option value="{{$ticketStatus->id}}" {{ old('ticket_status_id') == $ticketStatus->id ? 'selected' : '' }}>{{$ticketStatus->status}}</option>
                  @endforeach
                </select>
                <small class="text-muted">Leave blank to set as "Open" automatically</small>
                @error('ticket_status_id')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
              </div>
            </fieldset>

            {{-- SECTION 2: Location & Assignment --}}
            <fieldset>
              <legend><i class="fa fa-map-marker"></i> Location & Assignment</legend>

              <div class="form-group">
                <label for="location_id">Location <span class="text-red">*</span></label>
                <select class="form-control location_id @error('location_id') is-invalid @enderror" name="location_id" id="location_id" required>
                  <option value="">-- Select Location --</option>
                  @foreach($locations as $location)
                    <option value="{{$location->id}}" {{ old('location_id') == $location->id ? 'selected' : '' }}>{{$location->location_name}} - {{$location->building}}, {{$location->office}}</option>
                  @endforeach
                </select>
                <small class="text-muted">Physical location where issue is occurring</small>
                @error('location_id')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
              </div>
            </fieldset>

            {{-- SECTION 3: Asset Association --}}
            <fieldset>
              <legend><i class="fa fa-laptop"></i> Asset Association</legend>

              <div class="form-group">
                <label for="asset_ids">Related Assets (Optional)</label>
                <select class="form-control asset_ids @error('asset_ids') is-invalid @enderror @error('asset_ids.*') is-invalid @enderror" name="asset_ids[]" id="asset_ids" multiple>
                  @foreach($assets as $asset)
                    <option value="{{$asset->id}}" {{ (old('asset_ids') && in_array($asset->id, old('asset_ids'))) || (isset($preselectedAssetId) && $preselectedAssetId == $asset->id) ? 'selected' : '' }}>
                      {{ $asset->model_name ? $asset->model_name : 'Unknown Model' }} ({{ $asset->asset_tag }}) - {{ $asset->location ? $asset->location->location_name : 'No Location' }}
                    </option>
                  @endforeach
                </select>
                <small class="text-muted">Select one or more assets related to this ticket (use Ctrl/Cmd + Click for multiple)</small>
                @error('asset_ids')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                @error('asset_ids.*')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
              </div>
            </fieldset>

            {{-- Submit Buttons --}}
            <div class="form-group" style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #e3e3e3;">
              <button type="submit" class="btn btn-primary btn-lg">
                <i class="fa fa-save"></i> <b>Create Ticket</b>
              </button>
              <a href="{{ route('tickets.index') }}" class="btn btn-default btn-lg">
                <i class="fa fa-times"></i> Cancel
              </a>
            </div>
          </form>
        </div>
      </div>

      {{-- Display validation errors if any --}}
      @if(count($errors))
        <div class="alert alert-danger">
          <h4><i class="icon fa fa-ban"></i> Validation Errors!</h4>
          <ul>
            @foreach($errors->all() as $error)
              <li>{{$error}}</li>
            @endforeach
          </ul>
        </div>
      @endif
    </div>
    
    {{-- SIDEBAR: Canned Fields & Help --}}
    <div class="col-md-4">
      {{-- Canned Fields Quick Select --}}
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-magic"></i> Quick Templates</h3>
        </div>
        <div class="box-body">
          <p class="text-muted"><small>Use pre-defined templates to speed up ticket creation</small></p>
          <form method="POST" action="/canned">
            {{csrf_field()}}
            <div class="form-group">
              <label for="canned_subject">Template</label>
              <select class="form-control subject" name="subject" id="canned_subject">
                <option value="">-- Select Template --</option>
                @foreach($ticketsCannedFields as $ticketsCannedField)
                  <option value="{{$ticketsCannedField->id}}">{{$ticketsCannedField->subject}}</option>
                @endforeach
              </select>
            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-info btn-block">
                <i class="fa fa-magic"></i> Use Template
              </button>
            </div>
          </form>
        </div>
      </div>

      {{-- Help & Tips --}}
      <div class="box box-success">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-question-circle"></i> Help & Tips</h3>
        </div>
        <div class="box-body">
          <p><strong>Priority Guidelines:</strong></p>
          <ul class="list-unstyled">
            <li><span class="badge bg-red">High</span> System down, critical issue</li>
            <li><span class="badge bg-yellow">Medium</span> Affecting work but not critical</li>
            <li><span class="badge bg-green">Low</span> Minor issue or request</li>
          </ul>
          
          <hr>
          
          <p><strong>Common Ticket Types:</strong></p>
          <ul style="font-size: 12px;">
            <li><i class="fa fa-wrench"></i> Hardware Issue</li>
            <li><i class="fa fa-code"></i> Software Support</li>
            <li><i class="fa fa-wifi"></i> Network Problem</li>
            <li><i class="fa fa-user-plus"></i> Access Request</li>
          </ul>

          <hr>

          <p><strong>Tips for Better Support:</strong></p>
          <ul style="font-size: 12px;">
            <li>Be specific in your description</li>
            <li>Include error messages if any</li>
            <li>Mention when the issue started</li>
            <li>Select the correct asset</li>
          </ul>
        </div>
      </div>

      {{-- SLA Information --}}
      <div class="box box-warning">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-clock-o"></i> SLA Information</h3>
        </div>
        <div class="box-body">
          <p class="text-muted"><small>Expected response times based on priority:</small></p>
          <table class="table table-condensed" style="font-size: 12px;">
            <tr>
              <td><span class="badge bg-red">High</span></td>
              <td>4 hours</td>
            </tr>
            <tr>
              <td><span class="badge bg-yellow">Medium</span></td>
              <td>24 hours</td>
            </tr>
            <tr>
              <td><span class="badge bg-green">Low</span></td>
              <td>48 hours</td>
            </tr>
          </table>
          <p class="text-muted"><small><em>* SLA clock starts when ticket is created</em></small></p>
        </div>
      </div>
    </div>
  </div>

@include('components.loading-overlay')

@endsection

@section('footer')
  <script type="text/javascript">
    $(document).ready(function() {
      // Initialize Select2 for all dropdowns
      $(".location_id").select2({ placeholder: 'Select location', allowClear: true });
      $(".ticket_status_id").select2({ placeholder: 'Select status (optional)', allowClear: true });
      $(".ticket_type_id").select2({ placeholder: 'Select ticket type', allowClear: false });
      $(".ticket_priority_id").select2({ placeholder: 'Select priority', allowClear: false });
      $(".subject").select2({ placeholder: 'Select template', allowClear: true });
      $(".asset_ids").select2({ 
        placeholder: 'Search and select asset(s)', 
        allowClear: true,
        width: '100%'
      });

      // Character counter for description
      function updateCharCounter() {
        var length = $('#description').val().length;
        var minLength = 10;
        var counter = $('#char-counter');
        
        counter.text(length + ' / ' + minLength + ' characters (minimum ' + minLength + ')');
        
        if (length >= minLength) {
          counter.removeClass('invalid').addClass('valid');
        } else {
          counter.removeClass('valid').addClass('invalid');
        }
      }

      // Update counter on load and on input
      updateCharCounter();
      $('#description').on('input', updateCharCounter);

      // Add loading overlay on form submit
      $('#ticket-create-form').on('submit', function() {
        showLoading('Creating ticket...');
      });

      // Prevent enter key from submitting form
      $(":input").keypress(function(event){
        if (event.which == '10' || event.which == '13') {
          event.preventDefault();
        }
      });
    });
  </script>
@endsection


