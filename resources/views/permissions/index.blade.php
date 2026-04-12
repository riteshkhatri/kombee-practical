@extends('layouts.app')

@section('content')
<div class="row mb-3">
    <div class="col-md-8">
        <h2>Permissions Management</h2>
    </div>
    <div class="col-md-4 text-end">
        @can('create', App\Models\Permission::class)
        <button class="btn btn-primary" onclick="showAddModal()">Add Permission</button>
        @endcan
    </div>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-bordered table-striped" id="permissionsTable">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Populated via AJAX -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="permissionModal" tabindex="-1" aria-labelledby="permissionModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="permissionForm">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="permissionModalLabel">Add Permission</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="permissionId" name="permission_id">
            
            <div class="mb-3">
                <label>Permission Name</label>
                <input type="text" name="name" id="name" class="form-control" required placeholder="e.g. view users">
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save changes</button>
        </div>
      </div>
    </form>
  </div>
</div>

@endsection

@push('styles')
@endpush

@push('scripts')
<script>
    let permissionsTable;

    const canEditPermission = @json(auth()->user()->can('update', new App\Models\Permission));
    const canDeletePermission = @json(auth()->user()->can('delete', new App\Models\Permission));

    $(document).ready(function() {
        permissionsTable = $('#permissionsTable').DataTable({
            ajax: {
                url: '{{ route("api.permissions.index") }}',
                dataSrc: 'data',
                type: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('api_token'),
                    'Accept': 'application/json'
                }
            },
            columns: [
                { data: 'id' },
                { data: 'name' },
                { 
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        let buttons = '';
                        if (canEditPermission) {
                            buttons += `<button class="btn btn-sm btn-info" onclick="editPermission(${row.id})">Edit</button> `;
                        }
                        if (canDeletePermission) {
                            buttons += `<button class="btn btn-sm btn-danger" onclick="deletePermission(${row.id})">Delete</button>`;
                        }
                        return buttons || '';
                    }
                }
            ]
        });

        $('#permissionForm').validate({
            rules: {
                name: { required: true }
            },
            submitHandler: function(form) {
                savePermission();
            }
        });
    });

    function showAddModal() {
        $('#permissionForm')[0].reset();
        $('#permissionId').val('');
        $('#permissionModalLabel').text('Add Permission');
        $('#permissionModal').modal('show');
    }

    function editPermission(id) {
        $.ajax({
            url: `api/permissions/${id}`,
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('api_token'),
                'Accept': 'application/json'
            },
            success: function(res) {
                let permission = res.data;
                $('#permissionForm')[0].reset();
                $('#permissionId').val(permission.id);
                $('#permissionModalLabel').text('Edit Permission');
                $('#name').val(permission.name);
                $('#permissionModal').modal('show');
            }
        });
    }

    function savePermission() {
        let fd = new FormData($('#permissionForm')[0]);
        let id = $('#permissionId').val();
        let url = id ? `api/permissions/${id}` : 'api/permissions';
        if(id) {
            fd.append('_method', 'PUT');
        }

        $.ajax({
            url: url,
            method: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('api_token'),
                'Accept': 'application/json'
            },
            success: function(res) {
                $('#permissionModal').modal('hide');
                permissionsTable.ajax.reload(null, false);
                toastr.success(res.message);
            },
            error: function(xhr) {
                if(xhr.status === 422) {
                    let errors = xhr.responseJSON.errors || xhr.responseJSON.data || xhr.responseJSON.message;
                    if (typeof errors === 'object') {
                        for(let key in errors) {
                            toastr.error(errors[key][0]);
                        }
                    } else {
                        toastr.error(errors);
                    }
                } else if(xhr.status === 403) {
                    toastr.error('Unauthorized action.');
                } else {
                    toastr.error('An error occurred.');
                }
            }
        });
    }

    function deletePermission(id) {
        if(confirm('Are you sure you want to delete this permission?')) {
            $.ajax({
                url: `api/permissions/${id}`,
                method: 'DELETE',
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('api_token'),
                    'Accept': 'application/json'
                },
                success: function(res) {
                    permissionsTable.ajax.reload(null, false);
                    toastr.success(res.message || 'Permission deleted successfully.');
                },
                error: function(xhr) {
                    if(xhr.status === 403) {
                        toastr.error('Unauthorized action.');
                    } else {
                        toastr.error('An error occurred while deleting.');
                    }
                }
            });
        }
    }
</script>
@endpush
