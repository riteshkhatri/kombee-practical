<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kombee User Management</title>
    <!-- Bridge for existing API tokens to Cookies -->
    <script>
        if (localStorage.getItem('api_token') && !document.cookie.includes('api_token=')) {
            document.cookie = "api_token=" + localStorage.getItem('api_token') + "; path=/;";
            window.location.reload();
        }
    </script>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <style>
        body { background-color: #f8f9fa; }
        .error { color: #dc3545; font-size: 0.875em; }
    </style>
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">Kombee Practical</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    @can('viewAny', App\Models\User::class)
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('users.index') }}">Users</a>
                    </li>
                    @endcan
                    @can('viewAny', App\Models\Role::class)
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('roles.index') }}">Roles</a>
                    </li>
                    @endcan
                    @can('viewAny', App\Models\Permission::class)
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('permissions.index') }}">Permissions</a>
                    </li>
                    @endcan
                    @can('viewAny', App\Models\Supplier::class)
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('suppliers.index') }}">Suppliers</a>
                    </li>
                    @endcan
                    @can('viewAny', App\Models\Customer::class)
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('customers.index') }}">Customers</a>
                    </li>
                    @endcan
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="#" id="logoutBtn">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container">
        @yield('content')
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery Validation -->
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        // Toastr Configuration
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        // Handle Laravel Session Flashes
        @if(Session::has('success'))
            toastr.success("{{ Session::get('success') }}");
        @endif
        @if(Session::has('error'))
            toastr.error("{{ Session::get('error') }}");
        @endif
        @if(Session::has('info'))
            toastr.info("{{ Session::get('info') }}");
        @endif
        @if(Session::has('warning'))
            toastr.warning("{{ Session::get('warning') }}");
        @endif

        const apiToken = localStorage.getItem('api_token');
        const userRoles = JSON.parse(localStorage.getItem('user_roles') || '[]');
        const isAdmin = userRoles.includes('Admin');
        const userPermissions = JSON.parse(localStorage.getItem('user_permissions') || '[]');
        
        // Redirect to login if token is missing
        if (!apiToken && window.location.pathname !== '/login') {
            window.location.href = `{{ route('login') }}`;
        }


        // Setup AJAX requests
        $.ajaxSetup({
            headers: {
                'Authorization': 'Bearer ' + apiToken,
                'Accept': 'application/json'
            }
        });

        // Global AJAX error handling
        $(document).ajaxError(function(event, xhr, settings, thrownError) {
            if (xhr.status === 401) {
                toastr.error("Session expired. Redirecting to login...");
                localStorage.removeItem('api_token');
                setTimeout(() => {
                    window.location.href = '{{ route("login") }}';
                }, 2000);
            } else if (xhr.status === 403) {
                toastr.error("Access denied: You do not have permission to perform this action.");
            } else if (xhr.status === 404) {
                toastr.error("Resource not found.");
            } else if (xhr.status === 500) {
                toastr.error("Internal server error. Please try again later.");
            }
        });

        // Simple logout handler
        $('#logoutBtn').click(function(e) {
            e.preventDefault();
            $.ajax({
                url: '{{ route("api.logout") }}',
                type: 'POST',
                success: function() {
                    localStorage.removeItem('api_token');
                    localStorage.removeItem('user_roles');
                    document.cookie = "api_token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
                    window.location.href = '{{ route("login") }}';
                },
                error: function() {
                    // Force logout on frontend even if server fails
                    localStorage.removeItem('api_token');
                    localStorage.removeItem('user_roles');
                    document.cookie = "api_token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
                    window.location.href = '{{ route("login") }}';
                }
            });
        });
        // Global Export Function
        window.exportData = function(module, format) {
            const token = localStorage.getItem('api_token');
            if (!token) {
                toastr.warning('You must be logged in to export data.');
                return;
            }

            const url = `api/export/${module}/${format}`;
            
            // Use fetch to handle the download with headers
            fetch(url, {
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    if (response.status === 403) {
                        throw new Error('You do not have permission to export this data.');
                    }
                    throw new Error('Export failed. Please check permissions or try again.');
                }
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json().then(err => { throw new Error(err.message || 'Export failed'); });
                }
                return response.blob();
            })
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                const ext = format === 'excel' ? 'xlsx' : format;
                a.download = `${module}_export_${new Date().toISOString().slice(0,10)}.${ext}`;
                document.body.appendChild(a);
                a.click();
                setTimeout(() => {
                    document.body.removeChild(a);
                    window.URL.revokeObjectURL(url);
                }, 100);
            })
            .catch(error => {
                console.error('Export failed:', error);
                toastr.error(error.message || 'Failed to export data.');
            });
        };
    </script>
    @stack('scripts')
    <script>
        // Suppress DataTables default alert and use toastr instead
        $(document).ready(function() {
            if ($.fn.dataTable) {
                $.fn.dataTable.ext.errMode = 'none';
                $(document).on('error.dt', function(e, settings, techNote, message) {
                    console.error('DataTables Error:', message);
                    // The actual error is often handled by the global ajaxError handler above
                    // This purely suppresses the alert and provides a fallback log
                });
            }
        });
    </script>
</body>
</html>
