@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<h3 class="card-title text-center mb-4">Login</h3>
<form id="loginForm">
    <div class="mb-3">
        <label for="email" class="form-label">Email address</label>
        <input type="email" class="form-control" id="email" name="email" required>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="password" required>
    </div>
    <button type="submit" class="btn btn-primary w-100" id="loginBtn">Sign In</button>
    <div class="text-center mt-3">
        <a href="{{ route('register') }}" class="text-decoration-none">Don't have an account? Register here.</a>
    </div>
</form>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Unconditionally clear token on login page
        localStorage.removeItem('api_token');
        localStorage.removeItem('user_roles');
        document.cookie = "api_token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";

        $('#loginForm').validate({
            rules: {
                email: { required: true, email: true },
                password: { required: true, minlength: 6 }
            },
            submitHandler: function(form) {
                $('#loginBtn').prop('disabled', true).text('Signing in...');

                $.ajax({
                    url: '{{ route("api.login") }}',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        email: $('#email').val(),
                        password: $('#password').val()
                    }),
                    success: function(response) {
                        if (response.success && response.data.token) {
                            localStorage.setItem('api_token', response.data.token);
                            localStorage.setItem('user_roles', JSON.stringify(response.data.user.roles));
                            localStorage.setItem('user_permissions', JSON.stringify(response.data.user.permissions));
                            // Set a session cookie so Laravel SSR knows the user is logged in
                            document.cookie = "api_token=" + response.data.token + "; path=/;";
                            toastr.success('Logged in successfully!');
                            setTimeout(() => {
                                window.location.href = '{{ route("dashboard") }}';
                            }, 500);
                        } else {
                            toastr.error('Login failed.');
                            $('#loginBtn').prop('disabled', false).text('Sign In');
                        }
                    },
                    error: function(xhr) {
                        let msg = 'Invalid credentials or server error.';
                        if(xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        toastr.error(msg);
                        $('#loginBtn').prop('disabled', false).text('Sign In');
                    }
                });
                return false;
            }
        });
    });
</script>
@endpush
