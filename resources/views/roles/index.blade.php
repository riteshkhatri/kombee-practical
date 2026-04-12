@extends('layouts.app')

@section('content')
<div class="row mb-3">
    <div class="col-md-8">
        <h2>Roles Management</h2>
    </div>
    <div class="col-md-4 text-end">
        @can('create', App\Models\Role::class)
        <button class="btn btn-primary" onclick="showAddModal()">Add Role</button>
        @endcan
    </div>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-bordered table-striped" id="rolesTable">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Permissions</th>
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
<div class="modal fade" id="roleModal" tabindex="-1" aria-labelledby="roleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="roleForm">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="roleModalLabel">Add Role</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="roleId" name="role_id">
            
            <div class="mb-3">
                <label>Role Name</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Permissions</label>
                <div id="permissionsCheckboxList">
                    <!-- Populated dynamically -->
                </div>
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
<style>
.permission-badge {
    margin-right: 5px;
    margin-bottom: 5px;
    display: inline-block;
}
</style>
@endpush

@push('scripts')
<script>
    let rolesTable;
    let allPermissions = [];

    const canEditRole = @json(auth()->user()->can('update', new App\Models\Role));
    const canDeleteRole = @json(auth()->user()->can('delete', new App\Models\Role));

    $(document).ready(function() {
        loadPermissions();

        // Check if there is pagination in backend. In the provided RoleController, it returns all records inside 'data'.
        // DataTables can handle it directly if we set it up. We may not need serverSide processing if API isn't paginated.
        rolesTable = $('#rolesTable').DataTable({
            ajax: {
                url: '{{ route("api.roles.index") }}',
                // Data property might be just 'data' depending on ApiResponse trait
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
                    data: 'permissions',
                    orderable: false,
                    render: function(data, type, row) {
                        if (!data || data.length === 0) return '-';
                        return data.map(p => `<span class="badge bg-secondary permission-badge">${p.name}</span>`).join('');
                    }
                },
                { 
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        let buttons = '';
                        if (canEditRole) {
                            buttons += `<button class="btn btn-sm btn-info" onclick="editRole(${row.id})">Edit</button> `;
                        }
                        if (canDeleteRole) {
                            buttons += `<button class="btn btn-sm btn-danger" onclick="deleteRole(${row.id})">Delete</button>`;
                        }
                        return buttons || '';
                    }
                }
            ]
        });

        $('#roleForm').validate({
            rules: {
                name: { required: true }
            },
            submitHandler: function(form) {
                saveRole();
            }
        });
    });

    function loadPermissions() {
        $.ajax({
            url: '{{ route("api.permissions.index") }}',
            type: 'GET',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('api_token'),
                'Accept': 'application/json'
            },
            success: function(res) {
                allPermissions = res.data;
                let html = '';
                allPermissions.forEach(p => {
                    html += `
                        <div class="form-check">
                          <input class="form-check-input permission-check" type="checkbox" name="permissions[]" value="${p.id}" id="perm_${p.id}">
                          <label class="form-check-label" for="perm_${p.id}">
                            ${p.name}
                          </label>
                        </div>
                    `;
                });
                $('#permissionsCheckboxList').html(html);
            }
        });
    }

    function showAddModal() {
        $('#roleForm')[0].reset();
        $('#roleId').val('');
        $('.permission-check').prop('checked', false);
        $('#roleModalLabel').text('Add Role');
        $('#roleModal').modal('show');
    }

    function editRole(id) {
        $.ajax({
            url: `api/roles/${id}`,
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('api_token'),
                'Accept': 'application/json'
            },
            success: function(res) {
                let role = res.data;
                $('#roleForm')[0].reset();
                $('#roleId').val(role.id);
                $('#roleModalLabel').text('Edit Role');
                
                $('#name').val(role.name);

                $('.permission-check').prop('checked', false);
                if (role.permissions) {
                    role.permissions.forEach(p => {
                        $(`#perm_${p.id}`).prop('checked', true);
                    });
                }

                $('#roleModal').modal('show');
            }
        });
    }

    function saveRole() {
        let fd = new FormData($('#roleForm')[0]);
        let id = $('#roleId').val();
        let url = id ? `api/roles/${id}` : 'api/roles';
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
                $('#roleModal').modal('hide');
                rolesTable.ajax.reload(null, false);
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

    function deleteRole(id) {
        if(confirm('Are you sure you want to delete this role?')) {
            $.ajax({
                url: `api/roles/${id}`,
                method: 'DELETE',
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('api_token'),
                    'Accept': 'application/json'
                },
                success: function(res) {
                    rolesTable.ajax.reload(null, false);
                    toastr.success(res.message || 'Role deleted successfully.');
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
