<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Firman Store 15 </title>
    @include('layout.header')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .wrapper-full {
            width: 100%;
            max-width: 100%;
            padding: 0;
            margin: 0;
        }

        .wrapper-full .container-fluid {
            padding-left: 15px;
            padding-right: 15px;
            max-width: 100%;
        }

        body {
            overflow-x: hidden;
        }

        /* Sembunyikan tombol menu */
        .main-circle,
        .wrapper-menu,
        .iq-menu-bt {
            display: none !important;
        }

        /* Sidebar selalu tertutup */
        .iq-sidebar {
            width: 0 !important;
            min-width: 0 !important;
            transform: translateX(-100%);
        }

        .iq-sidebar .iq-navbar-logo,
        .iq-sidebar #sidebar-scrollbar {
            display: none !important;
        }

        /* Content full width */
        .content-page {
            margin-left: 0 !important;
            padding-left: 0 !important;
        }
    </style>
</head>

<body class="sidebar-main-active right-column-fixed sidebar-main-close">
    <!-- loader Start -->
    <div id="loading">
        <div id="loading-center">
        </div>
    </div>
    <!-- loader END -->
    <!-- Wrapper Start -->
    <div class="wrapper-full">
        <!-- Page Content  -->
        @yield('container')
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    @include('layout.footer')
</body>

</html>
