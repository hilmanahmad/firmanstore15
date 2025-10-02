<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Firman Store 15</title>
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
                                                id="username" placeholder="Username">
                                        </div>
                                        <div class="form-group">
                                            <label for="password">Password</label>
                                            <input type="password" name="password" class="form-control mb-0"
                                                id="password" placeholder="Password">
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
                                        </div>
                                    </form>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-7 text-center sign-in-page-image">
                            <!-- Panel kanan -->
                            <aside class="auth-right">
                                <div class="right-overlay"></div>

                                <div class="brand text-center mb-4">
                                    <img src="{{ asset('assets/images/logo.png') }}" alt="FirmanStore15"
                                        class="logo-img"
                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                    <h1 class="logo-text"
                                        style="display:none; color:#fff; font-weight:bold; font-size:2rem;">FIRMAN
                                        STORE15</h1>
                                </div>

                                <h2 class="tagline text-white text-center mb-4">
                                    Jualan lebih cepat, <span class="text-warning">tampil lebih rapi.</span>
                                </h2>

                                <ul class="benefits list-unstyled mb-4">
                                    <li class="d-flex align-items-center mb-3 text-white">
                                        <div class="benefit-icon me-3">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                                <circle cx="12" cy="12" r="10" stroke="currentColor"
                                                    stroke-width="2" />
                                            </svg>
                                        </div>
                                        <span>Kelola produk & stok real-time</span>
                                    </li>
                                    <li class="d-flex align-items-center mb-3 text-white">
                                        <div class="benefit-icon me-3">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <rect x="3" y="3" width="18" height="18" rx="2"
                                                    ry="2" stroke="currentColor" stroke-width="2" />
                                                <line x1="9" y1="9" x2="15" y2="9"
                                                    stroke="currentColor" stroke-width="2" />
                                                <line x1="9" y1="15" x2="15" y2="15"
                                                    stroke="currentColor" stroke-width="2" />
                                            </svg>
                                        </div>
                                        <span>Dashboard ringkas & informatif</span>
                                    </li>
                                    <li class="d-flex align-items-center mb-3 text-white">
                                        <div class="benefit-icon me-3">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="12" cy="12" r="10" stroke="currentColor"
                                                    stroke-width="2" />
                                                <path d="M12 6v6l4 2" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" />
                                            </svg>
                                        </div>
                                        <span>Siap integrasi pembayaran</span>
                                    </li>
                                </ul>

                                <!-- Background decorations -->
                                <div class="decoration-elements">
                                    <div class="blob blob-1"></div>
                                    <div class="blob blob-2"></div>
                                    <div class="floating-shapes">
                                        <div class="shape shape-1"></div>
                                        <div class="shape shape-2"></div>
                                        <div class="shape shape-3"></div>
                                    </div>
                                </div>
                            </aside>
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

<style>
    .auth-right {
        position: relative;
        padding: 2rem;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
        overflow: hidden;
    }

    .right-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.1);
        z-index: 1;
    }

    .auth-right>* {
        position: relative;
        z-index: 2;
    }

    .logo-img {
        max-width: 150px;
        height: auto;
        filter: brightness(0) invert(1);
    }

    .tagline {
        font-size: 1.75rem;
        font-weight: 600;
        line-height: 1.3;
    }

    .tagline span {
        font-weight: 700;
    }

    .benefit-icon {
        width: 40px;
        height: 40px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffc107;
        flex-shrink: 0;
    }

    .benefits li {
        font-size: 0.95rem;
        transition: transform 0.3s ease;
    }

    .benefits li:hover {
        transform: translateX(5px);
    }

    .testimonial {
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .decoration-elements {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 0;
    }

    .blob {
        position: absolute;
        border-radius: 50%;
        opacity: 0.1;
        animation: float 6s ease-in-out infinite;
    }

    .blob-1 {
        width: 200px;
        height: 200px;
        background: #ffc107;
        top: 10%;
        right: -100px;
        animation-delay: 0s;
    }

    .blob-2 {
        width: 150px;
        height: 150px;
        background: #28a745;
        bottom: 10%;
        left: -75px;
        animation-delay: 3s;
    }

    .floating-shapes {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
    }

    .shape {
        position: absolute;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 4px;
        animation: float 8s ease-in-out infinite;
    }

    .shape-1 {
        width: 60px;
        height: 60px;
        top: 20%;
        left: 10%;
        animation-delay: 1s;
    }

    .shape-2 {
        width: 40px;
        height: 40px;
        top: 60%;
        right: 15%;
        animation-delay: 4s;
    }

    .shape-3 {
        width: 80px;
        height: 80px;
        bottom: 30%;
        left: 20%;
        animation-delay: 7s;
    }

    @keyframes float {

        0%,
        100% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-20px);
        }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .auth-right {
            padding: 1rem;
            min-height: auto;
        }

        .tagline {
            font-size: 1.5rem;
        }

        .logo-img {
            max-width: 120px;
        }

        .benefits {
            font-size: 0.9rem;
        }

        .blob-1,
        .blob-2 {
            display: none;
        }
    }

    @media (max-width: 576px) {
        .tagline {
            font-size: 1.25rem;
        }

        .benefit-icon {
            width: 35px;
            height: 35px;
        }
    }
</style>
