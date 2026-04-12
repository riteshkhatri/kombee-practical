@extends('layouts.auth')

@section('title', 'Register')

@section('content')
<h3 class="card-title text-center mb-4">Register</h3>
<form id="registerForm">
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="first_name" class="form-label">First Name</label>
            <input type="text" class="form-control" id="first_name" name="first_name" required>
        </div>
        <div class="col-md-6 mt-3 mt-md-0">
            <label for="last_name" class="form-label">Last Name</label>
            <input type="text" class="form-control" id="last_name" name="last_name" required>
        </div>
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">Email address</label>
        <input type="email" class="form-control" id="email" name="email" required>
    </div>
    <div class="mb-3">
        <label for="contact_number" class="form-label">Contact Number</label>
        <input type="text" class="form-control" id="contact_number" name="contact_number" required>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="password" required>
    </div>
    <div class="mb-3">
        <label for="password_confirmation" class="form-label">Confirm Password</label>
        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
    </div>
    <button type="submit" class="btn btn-primary w-100" id="registerBtn">Register</button>
    <div class="text-center mt-3">
        <a href="{{ route('login') }}" class="text-decoration-none">Already have an account? Login here.</a>
    </div>
</form>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#registerForm').validate({
            rules: {
                first_name: { required: true, maxlength: 255 },
                last_name: { required: true, maxlength: 255 },
                email: { required: true, email: true, maxlength: 255 },
                contact_number: { required: true, maxlength: 20 },
                password: { required: true, minlength: 8 },
                password_confirmation: { required: true, equalTo: "#password" }
            },
            messages: {
                password_confirmation: {
                    equalTo: "Passwords do not match."
                }
            },
            submitHandler: function(form) {
                $('#registerBtn').prop('disabled', true).text('Registering...');

                $.ajax({
                    url: '{{ route("api.register") }}',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        first_name: $('#first_name').val(),
                        last_name: $('#last_name').val(),
                        email: $('#email').val(),
                        contact_number: $('#contact_number').val(),
                        password: $('#password').val(),
                        password_confirmation: $('#password_confirmation').val()
                    }),
                    success: function(response) {
                        toastr.success('Registration successful. Redirecting to login...');
                        setTimeout(function(){
                           window.location.href = '{{ route("login") }}'; 
                        }, 1500);
                    },
                    error: function(xhr) {
                        if(xhr.responseJSON && xhr.responseJSON.errors) {
                            let errors = xhr.responseJSON.errors;
                            for (let key in errors) {
                                toastr.error(errors[key][0]);
                            }
                        } else if(xhr.responseJSON && xhr.responseJSON.message) {
                            toastr.error(xhr.responseJSON.message);
                        } else {
                            toastr.error('Registration failed.');
                        }
                        $('#registerBtn').prop('disabled', false).text('Register');
                    }
                });
                return false;
            }
        });
    });
</script>
@endpush
