<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Vehicle</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets') }}/images/logo.png" />
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets') }}/css/bootstrap.min.css">
    <!-- Typography CSS -->
    <link rel="stylesheet" href="{{ asset('assets') }}/css/typography.css">
    <!-- Style CSS -->
    <link rel="stylesheet" href="{{ asset('assets') }}/css/style.css">
    <!-- Responsive CSS -->
    <link rel="stylesheet" href="{{ asset('assets') }}/css/responsive.css">
    <!-- sweealert -->
    <link href="{{ asset('assets') }}/sweetalert/sweetalert2.min.css" rel="stylesheet">

</head>

<body>
    <!-- loader Start -->
    <div id="loading">
        <div id="loading-center">
        </div>
    </div>
    <!-- loader END -->
    <!-- Sign in Start -->
    <section class="sign-in-page">
        <div id="container-inside">
            <div class="cube"></div>
            <div class="cube"></div>
            <div class="cube"></div>
            <div class="cube"></div>
            <div class="cube"></div>
        </div>
        <div class="container p-0">
            <div class="row no-gutters height-self-center">
                <div class="col-sm-12 align-self-center bg-primary rounded">
                    <div class="row m-0">
                        <div class="col-md-5 bg-white sign-in-page-data">
                            <form action="{{ route('login') }}" method="POST">
                                @csrf
                                <div class="sign-in-from">
                                    <h1 class="mb-0 text-center">Sign in</h1>
                                    <p class="text-center text-dark">Enter your username and password to access admin
                                        panel.</p>
                                    <form class="mt-4">
                                        <div class="form-group">
                                            <label for="username">Username</label>
                                            <input type="text" name="username" class="form-control mb-0"
                                                id="username" placeholder="Input sername">
                                        </div>
                                        <div class="form-group">
                                            <label for="password">Password</label>
                                            <a href="#" class="float-right">Forgot password?</a>
                                            <input type="password" name="password" class="form-control mb-0"
                                                id="password" placeholder="Input Password">
                                        </div>
                                        <div class="d-inline-block w-100">
                                            <div class="custom-control custom-checkbox d-inline-block mt-2 pt-1">
                                                <input type="checkbox" class="custom-control-input" id="customCheck1">
                                                <label class="custom-control-label" for="customCheck1">Remember
                                                    Me</label>
                                            </div>
                                        </div>
                                        <div class="sign-info text-center">
                                            <button type="submit" class="btn btn-primary d-block w-100 mb-2">Sign
                                                in</button>
                                            <span class="text-dark dark-color d-inline-block line-height-2">Would you
                                                like to register as a vendor? <a
                                                    href="register-vendor-management/vendor">Register</a></span>
                                        </div>
                                    </form>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-7 text-center sign-in-page-image">
                            <div class="sign-in-detail text-white">
                                <a class="sign-in-logo mt-5 mb-5" href="#"><img
                                        src="{{ asset('assets') }}/images/logo.png" class="img-fluid" alt="logo"
                                        style="width: 300px;height:300px"></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Sign in END -->
    <script src="{{ asset('assets') }}/js/jquery-3.6.0.min.js"></script>
    <script type="text/javascript">
        $(function() {
            $("form").submit(function() {
                $.ajax({
                    url: $(this).attr("action"),
                    data: $(this).serialize(),
                    type: $(this).attr("method"),
                    dataType: 'json',
                    success: function(data) {
                        if (data.status == 'failed') {
                            error('username dan password tidak sesuai');
                        } else {
                            success('Berhasil login');
                            let previousPage = "{{ session('previous_url', '/dashboard') }}";
                            window.location = document.referrer || '/dashboard';
                        }
                    }
                })
                return false;
            });
        });
    </script>

    <!-- Optional JavaScript -->
    <!-- sweetalert -->
    <script src="{{ asset('assets') }}/sweetalert/sweetalert2.all.min.js"></script>
    <script src="{{ asset('assets') }}/js/jquery.min.js"></script>
    <script src="{{ asset('assets') }}/js/popper.min.js"></script>
    <script src="{{ asset('assets') }}/js/bootstrap.min.js"></script>
    <!-- Countdown JavaScript -->
    <script src="{{ asset('assets') }}/js/countdown.min.js"></script>
    <!-- Owl Carousel JavaScript -->
    <script src="{{ asset('assets') }}/js/owl.carousel.min.js"></script>
    <!-- Magnific Popup JavaScript -->
    <script src="{{ asset('assets') }}/js/jquery.magnific-popup.min.js"></script>
    <!-- Smooth Scrollbar JavaScript -->
    <script src="{{ asset('assets') }}/js/smooth-scrollbar.js"></script>
    <!-- am charts JavaScript -->
    <!-- Style Customizer -->
    <script src="{{ asset('assets') }}/js/style-customizer.js"></script>
    <!-- Custom JavaScript -->
    <script src="{{ asset('assets') }}/js/custom.js"></script>
    <script type="text/javascript" src="{{ asset('assets') }}/js/myJs/app.js"></script>

</body>

</html>
