@extends('layouts.app')

@section('content')
<div class="row mb-3">
    <div class="col-md-8">
        <h2>Suppliers Management</h2>
    </div>
    <div class="col-md-4 text-end d-flex justify-content-end align-items-center gap-2">
        @can('export', App\Models\Supplier::class)
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-outline-success btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-download"></i> Export
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="javascript:void(0)" onclick="exportData('suppliers', 'excel')">Excel</a></li>
                <li><a class="dropdown-item" href="javascript:void(0)" onclick="exportData('suppliers', 'csv')">CSV</a></li>
                <li><a class="dropdown-item" href="javascript:void(0)" onclick="exportData('suppliers', 'pdf')">PDF</a></li>
            </ul>
        </div>
        @endcan
        @can('create', App\Models\Supplier::class)
        <button class="btn btn-primary btn-sm" onclick="showAddModal()">Add Supplier</button>
        @endcan
    </div>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-bordered table-striped" id="suppliersTable" style="width:100%">
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
<div class="modal fade" id="supplierModal" tabindex="-1" aria-labelledby="supplierModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form id="supplierForm">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="supplierModalLabel">Add Supplier</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="supplierId" name="supplier_id">
            
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
    let suppliersTable;

    const canEditSupplier = @json(auth()->user()->can('update', new App\Models\Supplier));
    const canDeleteSupplier = @json(auth()->user()->can('delete', new App\Models\Supplier));

    $(document).ready(function() {
        suppliersTable = $('#suppliersTable').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                url: '{{ route("api.suppliers.index") }}',
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
                        if (canEditSupplier) {
                            buttons += `<button class="btn btn-sm btn-info" onclick="editSupplier(${row.id})">Edit</button> `;
                        }
                        if (canDeleteSupplier) {
                            buttons += `<button class="btn btn-sm btn-danger" onclick="deleteSupplier(${row.id})">Delete</button>`;
                        }
                        return buttons || '';
                    }
                }
            ]
        });

        $('#supplierForm').validate({
            rules: {
                name: { required: true },
                email: { email: true }
            },
            submitHandler: function(form) {
                saveSupplier();
            }
        });
    });

    function showAddModal() {
        $('#supplierForm')[0].reset();
        $('#supplierId').val('');
        $('#supplierModalLabel').text('Add Supplier');
        $('#supplierModal').modal('show');
    }

    function editSupplier(id) {
        $.ajax({
            url: `api/suppliers/${id}`,
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('api_token'),
                'Accept': 'application/json'
            },
            success: function(res) {
                let supplier = res.data;
                $('#supplierForm')[0].reset();
                $('#supplierId').val(supplier.id);
                $('#supplierModalLabel').text('Edit Supplier');
                
                $('#name').val(supplier.name);
                $('#email').val(supplier.email);
                $('#contact_number').val(supplier.contact_number);
                $('#address').val(supplier.address);

                $('#supplierModal').modal('show');
            }
        });
    }

    function saveSupplier() {
        let data = {
            name: $('#name').val(),
            email: $('#email').val(),
            contact_number: $('#contact_number').val(),
            address: $('#address').val()
        };
        
        let id = $('#supplierId').val();
        let url = id ? `api/suppliers/${id}` : 'api/suppliers';
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
                $('#supplierModal').modal('hide');
                suppliersTable.ajax.reload(null, false);
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

    function deleteSupplier(id) {
        if(confirm('Are you sure you want to delete this supplier?')) {
            $.ajax({
                url: `api/suppliers/${id}`,
                method: 'DELETE',
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('api_token'),
                    'Accept': 'application/json'
                },
                success: function(res) {
                    suppliersTable.ajax.reload(null, false);
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
