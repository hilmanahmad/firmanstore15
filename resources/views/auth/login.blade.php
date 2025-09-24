<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Vehicle</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="../assets/images/logo.png" />
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <!-- Typography CSS -->
    <link rel="stylesheet" href="../assets/css/typography.css">
    <!-- Style CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- Responsive CSS -->
    <link rel="stylesheet" href="../assets/css/responsive.css">

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
                                            <input type="text" name="username"
                                                class="form-control mb-0 @error('username') is-invalid @enderror"
                                                value="{{ old('username') }}" required autocomplete="username" autofocus
                                                placeholder="Input Username">
                                            @error('username')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="password">Password</label>
                                            <a href="#" class="float-right">Forgot password?</a>
                                            <input type="password" name="Input password"
                                                class="form-control mb-0 @error('password') is-invalid @enderror"
                                                name="password" required autocomplete="current-password"
                                                placeholder="Password">
                                            @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
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
                                            <span class="text-dark dark-color d-inline-block line-height-2">Don't have
                                                an
                                                account? <a href="#">Sign up</a></span>
                                        </div>
                                    </form>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-7 text-center sign-in-page-image">
                            <div class="sign-in-detail text-white">
                                <a class="sign-in-logo mb-5" href="#"><img src="../assets/images/logo-full.png"
                                        class="img-fluid" alt="logo"></a>
                                <div class="owl-carousel" data-autoplay="true" data-loop="true" data-nav="false"
                                    data-dots="true" data-items="1" data-items-laptop="1" data-items-tab="1"
                                    data-items-mobile="1" data-items-mobile-sm="1" data-margin="0">
                                    <div class="item">
                                        <img src="../assets/images/login/1.png" class="img-fluid mb-4" alt="logo">
                                        <h4 class="mb-1 text-white">Find new friends</h4>
                                        <p>It is a long established fact that a reader will be distracted by the
                                            readable content.</p>
                                    </div>
                                    <div class="item">
                                        <img src="../assets/images/login/1.png" class="img-fluid mb-4" alt="logo">
                                        <h4 class="mb-1 text-white">Connect with the world</h4>
                                        <p>It is a long established fact that a reader will be distracted by the
                                            readable content.</p>
                                    </div>
                                    <div class="item">
                                        <img src="../assets/images/login/1.png" class="img-fluid mb-4" alt="logo">
                                        <h4 class="mb-1 text-white">Create new events</h4>
                                        <p>It is a long established fact that a reader will be distracted by the
                                            readable content.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">{{ __('Login') }}</div>

                        <div class="card-body">
                            <form method="POST" action="{{ route('login') }}">
                                @csrf

                                <div class="row mb-3">
                                    <label for="email"
                                        class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                                    <div class="col-md-6">
                                        <input id="email" type="email"
                                            class="form-control @error('email') is-invalid @enderror" name="email"
                                            value="{{ old('email') }}" required autocomplete="email" autofocus>

                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="password"
                                        class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                                    <div class="col-md-6">
                                        <input id="password" type="password"
                                            class="form-control @error('password') is-invalid @enderror"
                                            name="password" required autocomplete="current-password">

                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6 offset-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remember"
                                                id="remember" {{ old('remember') ? 'checked' : '' }}>

                                            <label class="form-check-label" for="remember">
                                                {{ __('Remember Me') }}
                                            </label>
                                        </div>
                                    </div>
                                </div> --}}

        {{-- <div class="row mb-0">
                                    <div class="col-md-8 offset-md-4">
                                        <button type="submit" class="btn btn-primary">
                                            {{ __('Login') }}
                                        </button>

                                        @if (Route::has('password.request'))
                                            <a class="btn btn-link" href="{{ route('password.request') }}">
                                                {{ __('Forgot Your Password?') }}
                                            </a>
                                        @endif
                                    </div>
                                </div> --}}
        {{-- </form>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}

        <!-- Optional JavaScript -->
        <script src="../assets/js/jquery.min.js"></script>
        <script src="../assets/js/popper.min.js"></script>
        <script src="../assets/js/bootstrap.min.js"></script>
        <!-- Appear JavaScript -->
        <script src="../assets/js/jquery.appear.js"></script>
        <!-- Countdown JavaScript -->
        <script src="../assets/js/countdown.min.js"></script>
        <!-- Counterup JavaScript -->
        <script src="../assets/js/waypoints.min.js"></script>
        <script src="../assets/js/jquery.counterup.min.js"></script>
        <!-- Wow JavaScript -->
        <script src="../assets/js/wow.min.js"></script>
        <!-- Apexcharts JavaScript -->
        <script src="../assets/js/apexcharts.js"></script>
        <!-- Slick JavaScript -->
        <script src="../assets/js/slick.min.js"></script>
        <!-- Select2 JavaScript -->
        <script src="../assets/js/select2.min.js"></script>
        <!-- Owl Carousel JavaScript -->
        <script src="../assets/js/owl.carousel.min.js"></script>
        <!-- Magnific Popup JavaScript -->
        <script src="../assets/js/jquery.magnific-popup.min.js"></script>
        <!-- Smooth Scrollbar JavaScript -->
        <script src="../assets/js/smooth-scrollbar.js"></script>
        <!-- lottie JavaScript -->
        <script src="../assets/js/lottie.js"></script>
        <!-- am core JavaScript -->
        <script src="../assets/js/core.js"></script>
        <!-- am charts JavaScript -->
        <script src="../assets/js/charts.js"></script>
        <!-- am animated JavaScript -->
        <script src="../assets/js/animated.js"></script>
        <!-- am kelly JavaScript -->
        <script src="../assets/js/kelly.js"></script>
        <!-- am maps JavaScript -->
        <script src="../assets/js/maps.js"></script>
        <!-- am worldLow JavaScript -->
        <script src="../assets/js/worldLow.js"></script>
        <!-- Style Customizer -->
        <script src="../assets/js/style-customizer.js"></script>
        <!-- Chart Custom JavaScript -->
        <script src="../assets/js/chart-custom.js"></script>
        <!-- Custom JavaScript -->
        <script src="../assets/js/custom.js"></script>

</body>

</html>
