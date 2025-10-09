@extends('layouts.app')

@section('page_title')
    Ticket Types Management
@endsection

@section('page_description')
    Manage ticket type categories
@endsection

@section('main-content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-tags"></i> Ticket Types
                </h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addTypeModal">
                        <i class="fa fa-plus"></i> Add Type
                    </button>
                    <a href="{{ route('system-settings.index') }}" class="btn btn-default btn-sm">
                        <i class="fa fa-arrow-left"></i> Back to Settings
                    </a>
                </div>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table id="typesTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Type Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($types as $type)
                            <tr>
                                <td>{{ $type->id }}</td>
                                <td>
                                    <span class="badge badge-success">
                                        {{ $type->name }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-info btn-edit-type" 
                                                data-id="{{ $type->id }}"
                                                data-name="{{ $type->name }}"
                                                data-toggle="modal" data-target="#editTypeModal">
                                            <i class="fa fa-edit"></i> Edit
                                        </button>
                                        <form method="POST" action="{{ route('tickets-type.destroy', $type->id) }}" 
                                              style="display: inline-block;"
                                              onsubmit="return confirm('Are you sure you want to delete this type?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center">
                                    <p class="text-muted">No ticket types found. <a href="#" data-toggle="modal" data-target="#addTypeModal">Create your first one</a>.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Type Modal -->
<div class="modal fade" id="addTypeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('tickets-type.store') }}">
                @csrf
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Add New Ticket Type</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="type">Type Name <span class="text-red">*</span></label>
                        <input type="text" class="form-control" id="type" name="type" required 
                               placeholder="e.g., Hardware Issue, Software Problem, Account Request">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Type</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Type Modal -->
<div class="modal fade" id="editTypeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" id="editTypeForm">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Edit Ticket Type</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_type">Type Name <span class="text-red">*</span></label>
                        <input type="text" class="form-control" id="edit_type" name="type" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Type</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#typesTable').DataTable({
        columnDefs: [{
            orderable: false, targets: 2 // Actions column
        }],
        order: [[ 0, "asc" ]]
    });

    // Handle edit type modal
    $('.btn-edit-type').on('click', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');

        $('#edit_type').val(name);
        $('#editTypeForm').attr('action', '/tickets-type/' + id);
    });
});
</script>
@endsection