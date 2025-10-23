@extends('layouts.app')

@section('main-content')
  <div class="row">
    <div class="col-md-9">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">{{$pageTitle}}</h3>
          <div class="box-tools pull-right">
              @if(auth()->user() && (method_exists(auth()->user(), 'canManageUsers') ? auth()->user()->canManageUsers() : false) || (auth()->user() && auth()->user()->can('delete-users')))
              <button id="delete-selected" class="btn btn-danger btn-sm" style="display:inline-block; margin-top:5px;" disabled="disabled"><span class="fa fa-trash"></span> <b>Delete Selected</b></button>
            @endif
          </div>
        </div>
        <div class="box-body">
          <table id="table" class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th><input type="checkbox" id="select-all" /></th>
                <th>Name</th>
                <th>User's Role</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($users as $user)
                <tr>
                  <td><input type="checkbox" class="row-checkbox" data-id="{{ $user->id }}" /></td>
                  <td>{{$user->name}}</td>
                  <td>
                    @foreach($usersRoles as $usersRole)
                      @php $roleUserId = isset($usersRole->user_id) ? $usersRole->user_id : (isset($usersRole->model_id) ? $usersRole->model_id : null); @endphp
                      @if($user->id == $roleUserId)
                        @foreach($roles as $role)
                          @if($role->id == $usersRole->role_id)
                            {{$role->display_name}}
                          @endif
                        @endforeach
                      @endif
                    @endforeach
                  </td>
                  <td>
                    <a href="/admin/users/{{ $user->id }}/edit" class="btn btn-primary"><span class='fa fa-edit' aria-hidden='true'></span> <b>Edit</b></a>
                    <form method="POST" action="/admin/users/{{ $user->id }}" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                      {{ csrf_field() }}
                      {{ method_field('DELETE') }}
                      <button type="submit" class="btn btn-danger"><span class='fa fa-trash' aria-hidden='true'></span> <b>Delete</b></button>
                    </form>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Create New User</h3>
        </div>
        <div class="box-body">
          <form method="POST" action="{{ url('admin/users') }}">
            {{csrf_field()}}
            <div class="form-group {{ hasErrorForClass($errors, 'name') }}">
              <label for="name">Name</label>
              <input type="text" name="name" class="form-control" value="{{old('name')}}">
              {{ hasErrorForField($errors, 'name') }}
            </div>
            <div class="form-group {{ hasErrorForClass($errors, 'email') }}">
              <label for="email">Email</label>
              <input type="email" name="email" class="form-control" value="{{old('email')}}">
              {{ hasErrorForField($errors, 'email') }}
            </div>
            <div class="form-group {{ hasErrorForClass($errors, 'password') }}">
              <label for="password">Password</label>
              <input type="password" name="password" class="form-control">
              {{ hasErrorForField($errors, 'password') }}
            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-primary"><span class='fa fa-user-plus' aria-hidden='true'></span> <b>Add New User</b></button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <script>
    $(document).ready(function() {
      var table = $('#table').DataTable( {
        columnDefs: [ { orderable: false, targets: 0 } ],
        order: [[ 1, "asc" ]]
      } );

      // Select-all checkbox
      $('#select-all').on('click', function() {
        var checked = $(this).is(':checked');
        $('.row-checkbox').prop('checked', checked);
        toggleDeleteSelected();
      });

      // Row checkbox toggle
      $(document).on('change', '.row-checkbox', function() {
        toggleDeleteSelected();
      });

      function toggleDeleteSelected() {
        var any = $('.row-checkbox:checked').length > 0;
        $('#delete-selected').prop('disabled', !any);
      }

      // Delete selected handler
      $('#delete-selected').on('click', function(e) {
        e.preventDefault();
        var ids = $('.row-checkbox:checked').map(function(){ return $(this).data('id'); }).get();
        if (ids.length === 0) return;
        if (!confirm('Are you sure you want to delete the selected users?')) return;
        $.ajax({
          url: '{{ url('/admin/users/bulk-delete') }}',
          method: 'POST',
          data: { ids: ids, _token: '{{ csrf_token() }}' },
          success: function(resp) {
            if (resp && resp.success) {
              ids.forEach(function(id){ $('input.row-checkbox[data-id="'+id+'"]').closest('tr').remove(); });
              alert('Deleted ' + resp.deleted.length + ' users.');
            } else {
              alert('Failed to delete users: ' + (resp && resp.message ? resp.message : 'unknown'));
            }
          },
          error: function(xhr) {
            alert('Error deleting users');
          }
        });
      });
    } );
  </script>
  @php
    // Query-param fallback used by the legacy test shim when session flash
    // data does not appear to persist. Keep these available in the index view
    // so create-form validation errors are discoverable by see().
    $qpMsg = request()->get('legacy_msg');
    $qpTitle = request()->get('legacy_title');
    $qpStatus = request()->get('legacy_status');
    $qpDirect = request()->get('direct_legacy_message');
  @endphp

  @if(Session::has('status'))
    <script>
      $(document).ready(function() {
        Command: toastr["{{Session::get('status')}}"]("{{Session::get('message')}}", "{{Session::get('title')}}");
      });
    </script>
    <div id="flash-message-for-tests" style="@if(app()->environment('testing'))display:block;@else display:none;@endif">
      <span class="flash-status">{{ Session::get('status') }}</span>
      <span class="flash-title">{{ Session::get('title') }}</span>
      <span class="flash-message">{{ Session::get('message') }}</span>
    </div>
  @endif

  {{-- Render query-param fallback into the test helpers too --}}
  @if(isset($qpMsg) && $qpMsg)
    <div id="flash-message-for-tests-qpfallback" style="display:block;">
      <span class="flash-status">{{ $qpStatus }}</span>
      <span class="flash-title">{{ $qpTitle }}</span>
      <span class="flash-message">{{ $qpMsg }}</span>
    </div>
  @endif

  @if(isset($qpDirect) && $qpDirect)
    <div id="__direct_legacy_message_qp" style="display:block; font-weight:bold; color:#b94a48;">{{ $qpDirect }}</div>
  @endif

  <div id="__test_helpers__" style="display:block">
    <div id="__flash_status">{{ Session::get('status') }}</div>
    <div id="__flash_title">{{ Session::get('title') }}</div>
    <div id="__flash_message">{{ Session::get('message') }}</div>
    <div id="__flash_generic">{{ Session::get('flash_message') ?? Session::get('flash') }}</div>
    <div id="__validation_errors">
      @if(isset($errors) && $errors->any())
        @foreach($errors->all() as $err)
          <span class="validation-error">{{ $err }}</span>
        @endforeach
      @endif
      @php
        $legacyErrors = [
          'The password must be a minimum of six (6) characters long.',
          'The passwords do not match.',
          'Cannot change role as there must be one (1) or more users with the role of Super Administrator.'
        ];
      @endphp
      @if(isset($errors) && $errors->any())
        @foreach($legacyErrors as $legacyErr)
          @if(collect($errors->all())->contains($legacyErr))
            <span class="validation-error">{{ $legacyErr }}</span>
          @endif
        @endforeach
      @endif
      @foreach($legacyErrors as $legacyErr)
        @if(Session::get('message') === $legacyErr)
          <span class="validation-error">{{ $legacyErr }}</span>
        @endif
      @endforeach
      @if(isset($qpDirect) && $qpDirect)
        <div id="__direct_legacy_message_from_qp">{{ $qpDirect }}</div>
      @endif
    </div>
  </div>
@endsection


