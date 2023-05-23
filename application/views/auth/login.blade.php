<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable">

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
	<script src="{{ asset('dist/custom.min.js') }}"></script>

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
				<svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 1440 120">
					<path d="M 0,36 C 144,53.6 432,123.2 720,124 C 1008,124.8 1296,56.8 1440,40L1440 140L0 140z"></path>
				</svg>
			</div>
		</div>

		<!-- auth page content -->
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
									<!-- <img src="{{ asset('upload/logo.png') }}" alt="" width="60%" class="img-fluid"> -->
									<h5 class="text-primary">Welcome Back !</h5>
									<p class="text-muted">Sign in to your account.</p>
								</div>
								<div class="p-2 mt-4">
									<form id="formAuthentication" method="POST">

										<div class="mb-3">
											<label for="username" class="form-label">Username</label>
											<input type="text" class="form-control" id="username" name="username" placeholder="Enter username / email" autocomplete="off">
										</div>

										<div class="mb-3">
											<div class="float-end">
												<a href="{{ url('auth/forgot-password') }}" class="text-muted">Forgot
													password?</a>
											</div>
											<label class="form-label" for="password-input">Password</label>
											<div class="position-relative auth-pass-inputgroup mb-3">
												<input type="password" class="form-control pe-5 password-input" placeholder="Enter password" id="password-input" name="password">
												<button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
											</div>
										</div>

										<div class="form-check">
											<input class="form-check-input" type="checkbox" value="1" id="auth-remember-check" name="remember">
											<label class="form-check-label" for="auth-remember-check">Remember
												me</label>
										</div>

										<div class="mt-4">
											{!! recaptchaDiv() !!}
											<button id="loginBtn" class="btn btn-success w-100" type="submit">Sign
												In</button>
										</div>

										<div class="mt-4 text-center">
											<div class="signin-other-title">
												<h5 class="fs-13 mb-4 title"> OR </h5>
											</div>
											<div>
												<button type="button" class="btn btn-danger btn-icon waves-effect waves-light w-100 google-signin" onclick="googleLogin()" disabled>
													<i class="ri-google-fill fs-16"></i> &nbsp; Sign In with Google
												</button>
												<!-- <button type="button" class="btn btn-primary btn-icon waves-effect waves-light"><i class="ri-facebook-fill fs-16"></i></button>
            <button type="button" class="btn btn-danger btn-icon waves-effect waves-light"><i class="ri-google-fill fs-16"></i></button>
            <button type="button" class="btn btn-dark btn-icon waves-effect waves-light"><i class="ri-github-fill fs-16"></i></button>
            <button type="button" class="btn btn-info btn-icon waves-effect waves-light"><i class="ri-twitter-fill fs-16"></i></button> -->
											</div>
										</div>
									</form>
								</div>
							</div>
							<!-- end card body -->
						</div>
						<!-- end card -->

						<div class="mt-4 text-center">
							<!-- <p class="mb-0">Don't have an account ? <a href="auth-signup-basic.html" class="fw-semibold text-primary text-decoration-underline"> Signup </a> </p> -->
						</div>

					</div>
				</div>
				<!-- end row -->
			</div>
			<!-- end container -->
		</div>
		<!-- end auth page content -->

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
	<script src="{{ asset('template/libs/feather-icons/feather.min.js') }}"></script>
	<script src="{{ asset('template/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>
	<!-- <script src="{{ asset('template/js/plugins.js') }}"></script> -->

	<!-- particles js -->
	<script src="{{ asset('template/libs/particles.js/particles.js') }}"></script>
	<!-- particles app js -->
	<script src="{{ asset('template/js/pages/particles.app.js') }}"></script>
	<!-- password-addon init -->
	<script src="{{ asset('template/js/pages/password-addon.init.js') }}"></script>

	<script type="text/javascript">
		$(document).ready(function() {
			setTimeout(function() {
				googleLogin();
			}, 15);
		});

		var onloadCallback = function() {
			grecaptcha.execute();
		};

		function setResponse(response) {
			document.getElementById('captcha-response').value = response;
		}

		function googleLogin() {

			var auth2;

			gapi.load('auth2', function() {
				var gapiConfig = JSON.parse('<?= gapiConfig() ?>');

				// Retrieve the singleton for the GoogleAuth library and set up the client.
				auth2 = gapi.auth2.init(gapiConfig)
					.then(
						//oninit
						function(GoogleAuth) {
							attachSignin(GoogleAuth, document.getElementsByClassName('google-signin')[0]);
							$('.google-signin').attr('disabled', false);
						},
						//onerror
						function(error) {
							console.log('error initialize', error);
							noti(500, 'Google Auth cannot be initialize');
						}
					);
			});
		}

		function attachSignin(GoogleAuth, element) {
			GoogleAuth.attachClickHandler(element, {},
				function(googleUser) {
					var profile = googleUser.getBasicProfile();
					var google_id_token = googleUser.getAuthResponse().id_token;
					loginGoogle(profile.getEmail());
				},
				function(res) {
					if (res.error != 'popup_closed_by_user') {
						noti(400, "Login using google was unsuccessful");
					} else {
						console.log('error', res);
					}
				});
		}

		async function loginGoogle(googleEmail) {

			const res = await callApi('post', 'auth/socialite', {
				'email': googleEmail
			});

			if (isSuccess(res.status)) {
				if (res.data != null) {
					const resCode = parseInt(res.data.resCode);
					noti(resCode, res.data.message);

					if (isSuccess(resCode)) {
						setTimeout(function() {
							window.location.href = res.data.redirectUrl;
						}, 650);
					}
				} else {
					noti(500, 'Email not found or not registered!');
				}
			}
		}

		$("#formAuthentication").submit(async function(event) {
			event.preventDefault();
			var username = $('#username').val();
			var password = $('#password').val();

			if (validateData()) {
				var form = $(this);
				const res = await loginApi("auth/sign-in", form.serializeArray(), 'formAuthentication');

				if (isSuccess(res)) {
					const data = res.data;
					const resCode = parseInt(data.resCode);
					noti(resCode, data.message);

					if (isSuccess(resCode)) {
						setTimeout(function() {
							window.location.href = data.redirectUrl;
						}, 400);
					} else {
						$("#loginBtn").html('Sign In');
						$("#loginBtn").attr('disabled', false);
					}
				} else {
					$("#loginBtn").html('Sign In');
					$("#loginBtn").attr('disabled', false);
				}

			} else {
				validationJsError('toastr', 'multi'); // single or multi
			}

			grecaptcha.reset();
			onloadCallback();

		});

		function validateData() {

			const rules = {
				'password': 'required',
				'username': 'required',
			};

			return validationJs(rules);
		}
	</script>


</body>

</html>