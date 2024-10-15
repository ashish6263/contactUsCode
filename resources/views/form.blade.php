<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Form</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Include Bootstrap CSS for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google reCAPTCHA -->
    {{-- {!! NoCaptcha::renderJs() !!} --}}
    <style>
        /* Full-page gradient background */
        body {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        /* Card styling */
        .contact-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 30px;
            max-width: 600px;
            width: 100%;
        }
        /* Header styling */
        .contact-card h2 {
            margin-bottom: 20px;
            font-weight: bold;
            color: #343a40;
        }
        /* Button styling */
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        /* Success and error messages */
        #success-message, #error-message {
            border-radius: 5px;
            padding: 15px;
        }
    </style>
</head>
<body>
    <div class="contact-card">
        <h2>Please Log In</h2>
        <div id="success-message" class="alert alert-success d-none"></div>
        <div id="error-message" class="alert alert-danger d-none"></div>
        <form id="contact-form">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
                <div class="invalid-feedback" id="error-name"></div>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
                <div class="invalid-feedback" id="error-email"></div>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" class="form-control" id="phone" name="phone" required>
                <div class="invalid-feedback" id="error-phone"></div>
            </div>
            <div class="mb-3">
                <label for="notes" class="form-label">Notes</label>
                <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                <div class="invalid-feedback" id="error-notes"></div>
            </div>
            {{-- <div class="mb-3">
                {!! NoCaptcha::display() !!}
                <div class="invalid-feedback d-block" id="error-g-recaptcha-response"></div>
            </div> --}}
            <button type="submit" class="btn btn-primary w-100">Submit</button>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#contact-form').on('submit', function(e) {
                e.preventDefault();
                // Clear previous errors
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');
                $('#success-message').addClass('d-none');
                $('#error-message').addClass('d-none');

                $.ajax({
                    type: 'POST',
                    url: "{{ route('contact.submit') }}",
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#success-message').text(response.message).removeClass('d-none');
                            $('#contact-form')[0].reset();
                            // If using reCAPTCHA, uncomment the next line
                            // grecaptcha.reset();
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#error-' + key).text(value[0]);
                                $('#' + key).addClass('is-invalid');
                            });
                        } else {
                            $('#error-message').text('An unexpected error occurred. Please try again.').removeClass('d-none');
                        }
                        // If using reCAPTCHA, uncomment the next line
                        // grecaptcha.reset();
                    }
                });
            });
        });
    </script>
    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
