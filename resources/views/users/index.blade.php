@extends('layouts.app')

@section('content')
<div class="row mb-3">
    <div class="col-md-8">
        <h2>Users Management</h2>
    </div>
    <div class="col-md-4 text-end d-flex justify-content-end align-items-center gap-2">
        @can('export', App\Models\User::class)
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-outline-success btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-download"></i> Export
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="javascript:void(0)" onclick="exportData('users', 'excel')">Excel</a></li>
                <li><a class="dropdown-item" href="javascript:void(0)" onclick="exportData('users', 'csv')">CSV</a></li>
                <li><a class="dropdown-item" href="javascript:void(0)" onclick="exportData('users', 'pdf')">PDF</a></li>
            </ul>
        </div>
        @endcan
        @can('create', App\Models\User::class)
        <button class="btn btn-primary btn-sm" onclick="showAddModal()">Add User</button>
        @endcan
    </div>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-bordered table-striped" id="usersTable">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Contact</th>
                    <th>Location</th>
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
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form id="userForm">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="userModalLabel">Add User</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="userId" name="user_id">
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label>First Name</label>
                    <input type="text" name="first_name" id="first_name" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label>Last Name</label>
                    <input type="text" name="last_name" id="last_name" class="form-control" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Email</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label>Contact Number</label>
                    <input type="text" name="contact_number" id="contact_number" class="form-control" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Password</label>
                    <input type="password" name="password" id="password" class="form-control">
                    <small class="text-muted pwd-hint">Leave blank to keep existing password (on edit).</small>
                </div>
                <div class="col-md-6">
                    <label>Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label>State</label>
                    <select name="state_id" id="state_id" class="form-select">
                        <option value="">Select State</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>City</label>
                    <select name="city_id" id="city_id" class="form-select" disabled>
                        <option value="">Select City</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Postcode</label>
                    <input type="text" name="postcode" id="postcode" class="form-control">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="d-block">Gender</label>
                    <div class="form-check form-check-inline mt-2">
                        <input class="form-check-input" type="radio" name="gender" id="gender_male" value="male">
                        <label class="form-check-label" for="gender_male">Male</label>
                    </div>
                    <div class="form-check form-check-inline mt-2">
                        <input class="form-check-input" type="radio" name="gender" id="gender_female" value="female">
                        <label class="form-check-label" for="gender_female">Female</label>
                    </div>
                    <div class="form-check form-check-inline mt-2">
                        <input class="form-check-input" type="radio" name="gender" id="gender_other" value="other">
                        <label class="form-check-label" for="gender_other">Other</label>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="d-block">Hobbies</label>
                    @foreach(['Reading', 'Traveling', 'Coding', 'Sports', 'Music'] as $hobby)
                        <div class="form-check form-check-inline mt-2">
                            <input class="form-check-input hobby-checkbox" type="checkbox" name="hobbies[]" value="{{ $hobby }}" id="hobby_{{ $hobby }}">
                            <label class="form-check-label" for="hobby_{{ $hobby }}">{{ $hobby }}</label>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Documents (Multiple)</label>
                    <input type="file" name="documents[]" id="documents" class="form-control" multiple>
                    <div id="existingDocuments" class="mt-2"></div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label>Roles</label>
                    <select name="roles[]" id="roles_dropdown" class="form-control" multiple="multiple" style="width: 100%;">
                        <!-- Populated dynamically -->
                    </select>
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
    /* Fix Select2 z-index inside Bootstrap modals */
    .select2-container--open {
        z-index: 1060 !important;
    }
</style>
@endpush

@push('scripts')
<script>
    let usersTable;

    const canEditUser = @json(auth()->user()->can('update', new App\Models\User));
    const canDeleteUser = @json(auth()->user()->can('delete', new App\Models\User));

    $(document).ready(function() {
        $('#roles_dropdown').select2({
            theme: "bootstrap-5",
            placeholder: "Select roles...",
            allowClear: true,
            dropdownParent: $('#userModal .modal-body')
        });

        usersTable = $('#usersTable').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                url: '{{ route("api.users.index") }}',
                type: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('api_token'),
                    'Accept': 'application/json'
                }
            },
            columns: [
                { data: 'id' },
                { 
                    data: null, 
                    render: function(data, type, row) {
                        return row.first_name + ' ' + row.last_name;
                    }
                },
                { data: 'email' },
                { 
                    data: 'contact_number',
                    render: function(data) { return data || '-'; }
                },
                { 
                    data: null,
                    orderable: false,
                    render: function(data, type, row) {
                        let city = row.city ? row.city.name : '';
                        let state = row.state ? row.state.name : '';
                        if (city && state) {
                            return `${city}, ${state}`;
                        } else if (city) {
                            return city;
                        } else if (state) {
                            return state;
                        }
                        return '-';
                    }
                },
                { 
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        let buttons = '';
                        if (canEditUser || {{ auth()->id() }} == row.id) {
                            buttons += `<button class="btn btn-sm btn-info" onclick="editUser(${row.id})">Edit</button> `;
                        }
                        if (canDeleteUser && {{ auth()->id() }} != row.id) {
                            buttons += `<button class="btn btn-sm btn-danger" onclick="deleteUser(${row.id})">Delete</button>`;
                        }
                        return buttons || '';
                    }
                }
            ]
        });

        loadStates();
        loadRoles();

        // Custom Validation Methods
        $.validator.addMethod("alphanumeric", function(value, element) {
            return this.optional(element) || /^[a-zA-Z0-9]+$/i.test(value);
        }, "Letters and numbers only please");

        $.validator.addMethod("contactformat", function(value, element) {
            return this.optional(element) || /^[\d\s\+\-\()]+$/.test(value);
        }, "Invalid contact number format");

        $('#userForm').validate({
            rules: {
                first_name: { required: true, alphanumeric: true },
                last_name: { required: true, alphanumeric: true },
                email: { required: true, email: true },
                contact_number: { required: true, contactformat: true },
                password: { minlength: 8 },
                password_confirmation: { equalTo: "#password" }
            },
            submitHandler: function(form) {
                saveUser();
            }
        });

        $('#state_id').change(function() {
            let stateId = $(this).val();
            if(stateId) {
                loadCities(stateId);
            } else {
                $('#city_id').html('<option value="">Select City</option>').prop('disabled', true);
            }
        });
    });



    function loadStates() {
        $.get('{{ route("api.states") }}', function(res) {
            let options = '<option value="">Select State</option>';
            res.data.forEach(state => {
                options += `<option value="${state.id}">${state.name}</option>`;
            });
            $('#state_id').html(options);
        });
    }

    function loadRoles() {
        $.ajax({
            url: '{{ route("api.roles.index") }}',
            type: 'GET',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('api_token'),
                'Accept': 'application/json'
            },
            success: function(res) {
                let html = '';
                res.data.forEach(r => {
                    html += `<option value="${r.id}">${r.name}</option>`;
                });
                $('#roles_dropdown').html(html).trigger('change');
            }
        });
    }

    function loadCities(stateId, selectedCity = null) {
        $.get(`api/states/${stateId}/cities`, function(res) {
            let options = '<option value="">Select City</option>';
            res.data.forEach(city => {
                let selected = (selectedCity == city.id) ? 'selected' : '';
                options += `<option value="${city.id}" ${selected}>${city.name}</option>`;
            });
            $('#city_id').html(options).prop('disabled', false);
        });
    }

    function showAddModal() {
        $('#userForm')[0].reset();
        $('#userId').val('');
        $('#roles_dropdown').val(null).trigger('change');
        $('input[name="gender"]').prop('checked', false);
        $('.hobby-checkbox').prop('checked', false);
        $('#existingDocuments').empty();
        $('#userModalLabel').text('Add User');
        $('#password').prop('required', true);
        $('.pwd-hint').hide();
        $('#city_id').html('<option value="">Select City</option>').prop('disabled', true);
        $('#userModal').modal('show');
    }

    function editUser(id) {
        $.get(`api/users/${id}`, function(res) {
            let user = res.data;
            $('#userForm')[0].reset();
            $('#userId').val(user.id);
            $('#userModalLabel').text('Edit User');
            
            $('#first_name').val(user.first_name);
            $('#last_name').val(user.last_name);
            $('#email').val(user.email);
            $('#contact_number').val(user.contact_number);
            $('#postcode').val(user.postcode);
            $('#state_id').val(user.state_id);

            // Populate Gender
            if (user.gender) {
                $(`input[name="gender"][value="${user.gender}"]`).prop('checked', true);
            }

            // Populate Hobbies
            $('.hobby-checkbox').prop('checked', false);
            if (user.hobbies && user.hobbies.length > 0 && Array.isArray(user.hobbies)) {
                user.hobbies.forEach(hobby => {
                    $(`.hobby-checkbox[value="${hobby}"]`).prop('checked', true);
                });
            }

            // Populate Existing Documents
            $('#existingDocuments').empty();
            if (user.documents && user.documents.length > 0) {
                let docHtml = '<label class="d-block small text-muted">Uploaded Documents:</label><ul class="list-unstyled">';
                user.documents.forEach(doc => {
                    docHtml += `<li><a href="storage/${doc.file_path}" target="_blank" class="small" download="${doc.file_name}"><i class="fas fa-file"></i> ${doc.file_name}</a></li>`;
                });
                docHtml += '</ul>';
                $('#existingDocuments').html(docHtml);
            }

            $('#password').prop('required', false);
            $('.pwd-hint').show();

            if (user.roles) {
                let roleIds = user.roles.map(r => r.id);
                $('#roles_dropdown').val(roleIds).trigger('change');
            } else {
                $('#roles_dropdown').val(null).trigger('change');
            }

            if(user.state_id) {
                loadCities(user.state_id, user.city_id);
            } else {
                $('#city_id').html('<option value="">Select City</option>').prop('disabled', true);
            }

            $('#userModal').modal('show');
        });
    }

    function saveUser() {
        let fd = new FormData($('#userForm')[0]);
        let id = $('#userId').val();
        let url = id ? `api/users/${id}` : 'api/users';
        if(id) {
            fd.append('_method', 'PUT');
        }

        $.ajax({
            url: url,
            method: 'POST', // Use POST with _method=PUT for file uploads in PHP
            data: fd,
            processData: false,
            contentType: false,
            success: function(res) {
                $('#userModal').modal('hide');
                usersTable.ajax.reload(null, false);
                toastr.success(res.message);
            },
            error: function(xhr) {
                if(xhr.status === 422) {
                    let errors = xhr.responseJSON.errors || xhr.responseJSON.data;
                    for(let key in errors) {
                        toastr.error(errors[key][0]);
                    }
                } else {
                    toastr.error('An error occurred.');
                }
            }
        });
    }

    function deleteUser(id) {
        if(confirm('Are you sure you want to delete this user?')) {
            $.ajax({
                url: `api/users/${id}`,
                method: 'DELETE',
                success: function(res) {
                    usersTable.ajax.reload(null, false);
                    toastr.success(res.message);
                }
            });
        }
    }
</script>
@endpush
