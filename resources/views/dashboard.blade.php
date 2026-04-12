@extends('layouts.app')

@section('content')
<div class="row justify-content-center mt-5">
    <div class="col-md-8">
        <div class="card shadow-lg border-0 rounded-lg">
            <div class="card-header bg-gradient bg-primary text-white p-4">
                <h2 class="mb-0">Welcome to Kombee Practical!</h2>
            </div>
            <div class="card-body p-5 text-center">
                <div class="mb-4">
                    <i class="fas fa-user-circle fa-5x text-primary shadow-sm rounded-circle p-2"></i>
                </div>
                <h3 class="display-6 mb-3">Hello, <span id="userName">User</span>!</h3>
                <p class="lead text-muted mb-5">
                    We're glad to have you back. Based on your role and permissions, 
                    the available modules can be accessed from the navigation menu above.
                </p>
                
                <div class="row g-4" id="moduleShortcuts">
                    <!-- Shortcuts will be dynamically populated here if they have permissions -->
                </div>

                <div id="noModulesMessage" class="mt-4 d-none">
                    <div class="alert alert-info border-0 shadow-sm">
                        <i class="fas fa-info-circle me-2"></i>
                        You currently do not have access to any specific management modules. Please contact your administrator for permission settings.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        const token = localStorage.getItem('api_token');
        if (!token) {
            window.location.href = '{{ route("login") }}';
            return;
        }

        // Fetch user info from localStorage (already saved during login)
        // Or we could make an API call to /user if it existed
        // Let's assume we can get it from localStorage or the token payload (though we don't parse JWT here)
        // Actually login.blade.php saves response.data.user
        
        // For now let's just use what's in the navbar or generic
        const roles = JSON.parse(localStorage.getItem('user_roles') || '[]');
        const permissions = JSON.parse(localStorage.getItem('user_permissions') || '[]');
        
        const modules = [
            { name: 'Users', path: '{{ route("users.index") }}', permission: 'view-users', icon: 'fa-users' },
            { name: 'Roles', path: '{{ route("roles.index") }}', permission: 'view-roles', icon: 'fa-user-shield' },
            { name: 'Permissions', path: '{{ route("permissions.index") }}', permission: 'view-permissions', icon: 'fa-key' },
            { name: 'Suppliers', path: '{{ route("suppliers.index") }}', permission: 'view-suppliers', icon: 'fa-truck' },
            { name: 'Customers', path: '{{ route("customers.index") }}', permission: 'view-customers', icon: 'fa-user-tag' }
        ];

        let availableModules = 0;
        modules.forEach(m => {
            if (roles.includes('Admin') || permissions.includes(m.permission)) {
                availableModules++;
                $('#moduleShortcuts').append(`
                    <div class="col-md-4">
                        <a href="${m.path}" class="text-decoration-none">
                            <div class="card h-100 border-0 shadow-sm hover-elevate">
                                <div class="card-body py-4">
                                    <i class="fas ${m.icon} fa-2x mb-3 text-primary"></i>
                                    <h5 class="card-title text-dark mb-0">${m.name}</h5>
                                </div>
                            </div>
                        </a>
                    </div>
                `);
            }
        });

        if (availableModules === 0) {
            $('#noModulesMessage').removeClass('d-none');
        }
    });

    // Custom CSS for hover effect
    const style = document.createElement('style');
    style.innerHTML = `
        .hover-elevate { transition: all 0.3s ease; border: 1px solid transparent !important; }
        .hover-elevate:hover { transform: translateY(-5px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; border-color: rgba(var(--bs-primary-rgb), 0.2) !important; }
        .bg-gradient { background: linear-gradient(45deg, #0d6efd 0%, #00d2ff 100%); }
    `;
    document.head.appendChild(style);
</script>
@endpush
