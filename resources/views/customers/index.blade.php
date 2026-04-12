@extends('layouts.app')

@section('content')
<div class="row mb-3">
    <div class="col-md-8">
        <h2>Customers Management</h2>
    </div>
    <div class="col-md-4 text-end d-flex justify-content-end align-items-center gap-2">
        @can('export', App\Models\Customer::class)
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-outline-success btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-download"></i> Export
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="javascript:void(0)" onclick="exportData('customers', 'excel')">Excel</a></li>
                <li><a class="dropdown-item" href="javascript:void(0)" onclick="exportData('customers', 'csv')">CSV</a></li>
                <li><a class="dropdown-item" href="javascript:void(0)" onclick="exportData('customers', 'pdf')">PDF</a></li>
            </ul>
        </div>
        @endcan
        @can('create', App\Models\Customer::class)
        <button class="btn btn-primary btn-sm" onclick="showAddModal()">Add Customer</button>
        @endcan
    </div>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-bordered table-striped" id="customersTable" style="width:100%">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Contact</th>
                    <th>Address</th>
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
<div class="modal fade" id="customerModal" tabindex="-1" aria-labelledby="customerModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form id="customerForm">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="customerModalLabel">Add Customer</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="customerId" name="customer_id">
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Name</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label>Email</label>
                    <input type="email" name="email" id="email" class="form-control">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Contact Number</label>
                    <input type="text" name="contact_number" id="contact_number" class="form-control">
                </div>
                <div class="col-md-6">
                    <label>Address</label>
                    <input type="text" name="address" id="address" class="form-control">
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
@endpush

@push('scripts')
<script>
    let customersTable;

    const canEditCustomer = @json(auth()->user()->can('update', new App\Models\Customer));
    const canDeleteCustomer = @json(auth()->user()->can('delete', new App\Models\Customer));

    $(document).ready(function() {
        customersTable = $('#customersTable').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                url: '{{ route("api.customers.index") }}',
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
                    data: 'email',
                    render: function(data) { return data || '-'; }
                },
                { 
                    data: 'contact_number',
                    render: function(data) { return data || '-'; }
                },
                { 
                    data: 'address',
                    render: function(data) { return data || '-'; },
                    orderable: false
                },
                { 
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        let buttons = '';
                        if (canEditCustomer) {
                            buttons += `<button class="btn btn-sm btn-info" onclick="editCustomer(${row.id})">Edit</button> `;
                        }
                        if (canDeleteCustomer) {
                            buttons += `<button class="btn btn-sm btn-danger" onclick="deleteCustomer(${row.id})">Delete</button>`;
                        }
                        return buttons || '';
                    }
                }
            ]
        });

        $('#customerForm').validate({
            rules: {
                name: { required: true },
                email: { email: true }
            },
            submitHandler: function(form) {
                saveCustomer();
            }
        });
    });

    function showAddModal() {
        $('#customerForm')[0].reset();
        $('#customerId').val('');
        $('#customerModalLabel').text('Add Customer');
        $('#customerModal').modal('show');
    }

    function editCustomer(id) {
        $.ajax({
            url: `api/customers/${id}`,
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('api_token'),
                'Accept': 'application/json'
            },
            success: function(res) {
                let customer = res.data;
                $('#customerForm')[0].reset();
                $('#customerId').val(customer.id);
                $('#customerModalLabel').text('Edit Customer');
                
                $('#name').val(customer.name);
                $('#email').val(customer.email);
                $('#contact_number').val(customer.contact_number);
                $('#address').val(customer.address);

                $('#customerModal').modal('show');
            }
        });
    }

    function saveCustomer() {
        let data = {
            name: $('#name').val(),
            email: $('#email').val(),
            contact_number: $('#contact_number').val(),
            address: $('#address').val()
        };
        
        let id = $('#customerId').val();
        let url = id ? `api/customers/${id}` : 'api/customers';
        let method = id ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            method: method,
            data: data,
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('api_token'),
                'Accept': 'application/json'
            },
            success: function(res) {
                $('#customerModal').modal('hide');
                customersTable.ajax.reload(null, false);
                toastr.success(res.message);
            },
            error: function(xhr) {
                if(xhr.status === 422) {
                    let errors = xhr.responseJSON.errors || xhr.responseJSON.data;
                    for(let key in errors) {
                        toastr.error(errors[key][0]);
                    }
                } else if(xhr.status === 403) {
                    toastr.error('Unauthorized action.');
                } else {
                    toastr.error('An error occurred.');
                }
            }
        });
    }

    function deleteCustomer(id) {
        if(confirm('Are you sure you want to delete this customer?')) {
            $.ajax({
                url: `api/customers/${id}`,
                method: 'DELETE',
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('api_token'),
                    'Accept': 'application/json'
                },
                success: function(res) {
                    customersTable.ajax.reload(null, false);
                    toastr.success(res.message);
                },
                error: function(xhr) {
                    if(xhr.status === 403) {
                        toastr.error('Unauthorized action.');
                    } else {
                        toastr.error('An error occurred.');
                    }
                }
            });
        }
    }
</script>
@endpush
