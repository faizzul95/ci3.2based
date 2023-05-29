<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none" data-preloader="disable">

<head>

    <meta charset="utf-8" />
    <title> {{ $title }} | {{ env('APP_NAME') }}</title>
    <meta name="base_url" content="{{ baseURL() }}" />

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="{{ env('COMPANY_NAME') }}" name="author" />

    <!-- App favicon -->
    <link href="{{ asset('template/images/favicon.ico') }}" rel="shortcut icon" />
    <!-- Layout config Js -->
    <script src="{{ asset('template/js/layout.js') }}"></script>
    <!-- Bootstrap Css -->
    <link href="{{ asset('template/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('template/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ asset('template/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="{{ asset('template/css/custom.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- custom js & CSS-->
    <link href="{{ asset('custom/css/toastr.min.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ mix('dist/js/custom.min.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/js-cookie@3.0.1/dist/js.cookie.min.js"></script>

    <!-- google -->
    <script src="https://apis.google.com/js/platform.js" async defer></script>
    <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback" async defer></script>

</head>

<body>

    <div class="auth-page-wrapper pt-5">
        <!-- auth page bg -->
        <div class="auth-one-bg-position auth-one-bg" id="auth-particles">
            <div class="bg-overlay"></div>

            <div class="shape">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink"
                    viewBox="0 0 1440 120">
                    <path d="M 0,36 C 144,53.6 432,123.2 720,124 C 1008,124.8 1296,56.8 1440,40L1440 140L0 140z"></path>
                </svg>
            </div>
        </div>

        <!-- forgot page content -->
        <div class="auth-page-content">
            <div class="container">

                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center mt-sm-5 mb-4 text-white-50">
                            <div>
                                <img src="{{ getImageSystemLogo() }}" alt="" width="30%" class="img-fluid">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card mt-4">

                            <div class="card-body p-4">
                                <div class="text-center mt-2">
                                    <h5 class="text-primary">Forgot Password?</h5>
                                    <lord-icon src="https://cdn.lordicon.com/rhvddzym.json" trigger="loop"
                                        colors="primary:#0ab39c" class="avatar-xl"></lord-icon>
                                </div>

                                <div class="alert alert-borderless alert-warning text-center mb-2 mx-2" role="alert">
                                    Enter your email and instructions will be sent to you!
                                </div>
                                <div class="p-2">
                                    <form id="formForgot" method="POST">
                                        <div class="mb-4">
                                            <label class="form-label">Email</label>
                                            <input type="email" id="email" name="email"
                                                placeholder="Enter Email" autocomplete="off" class="form-control">
                                        </div>

                                        <div class="text-center mt-4">
                                            <!-- Google reCAPTCHA widget -->
                                            {!! recaptchaDiv() !!}
                                            <button id="forgotBtn" class="btn btn-success w-100" type="submit">Send
                                                Reset Link</button>
                                        </div>
                                    </form><!-- end form -->
                                </div>
                            </div>
                            <!-- end card body -->
                        </div>
                        <!-- end card -->

                        <div class="mt-4 text-center">
                            <p class="mb-0">Wait, I remember my password... <a href="<?= url('auth') ?>"
                                    class="fw-semibold text-primary text-decoration-underline"> Click here </a> </p>
                        </div>

                    </div>
                </div>

                <!-- end row -->
            </div>
            <!-- end container -->
        </div>
        <!-- end forgot page content -->

        <!-- footer -->
        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <p class="mb-0 text-muted">&copy;
                                {{ currentDate('Y') }} {{ env('APP_NAME') }} by {{ env('COMPANY_NAME') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- end Footer -->
    </div>
    <!-- end auth-page-wrapper -->

    <!-- JAVASCRIPT -->
    <script src="{{ asset('template/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('template/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('template/libs/node-waves/waves.min.js') }}"></script>
    <!-- <script src="{{ asset('template/libs/feather-icons/feather.min.js') }}"></script> -->
    <script src="{{ asset('template/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>
    <!-- <script src="{{ asset('template/js/plugins.js') }}"></script> -->

    <!-- particles js -->
    <script src="{{ asset('template/libs/particles.js/particles.js') }}"></script>
    <!-- particles app js -->
    <script src="{{ asset('template/js/pages/particles.app.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            localStorage.clear();
        });

        var onloadCallback = function() {
            grecaptcha.execute();
        };

        function setResponse(response) {
            document.getElementById('captcha-response').value = response;
        }

        $("#formForgot").submit(async function(event) {
            event.preventDefault();
            var email = $('#email').val();

            if (validateData()) {
                var form = $(this);
                const res = await submitApi("auth/sent-email", form.serializeArray(), 'formForgot');
                if (isSuccess(res)) {
                    const data = res.data;
                    const resCode = parseInt(data.resCode);
                    noti(resCode, data.message);

                    $("#forgotBtn").html('Send Reset Link');

                    if (isSuccess(resCode)) {
                        setTimeout(function() {
                            window.location.href = data.redirectUrl;
                        }, 800);
                    } else {
                        $("#forgotBtn").attr('disabled', false);
                    }
                } else {
                    $("#forgotBtn").html('Send Reset Link');
                    $("#forgotBtn").attr('disabled', false);
                }

            } else {
                validationJsError('toastr', 'multi'); // single or multi
            }

            grecaptcha.reset();
            onloadCallback();

        });

        function validateData() {

            const rules = {
                'email': 'required|email',
            };

            return validationJs(rules);
        }
    </script>

</body>

</html>
