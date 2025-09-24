<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Firman Store 15 </title>
    @include('layout.header')
</head>

<body class="sidebar-main-active right-column-fixed">
    <!-- loader Start -->
    <div id="loading">
        <div id="loading-center">
        </div>
    </div>
    <!-- loader END -->
    <!-- Wrapper Start -->
    <div class="wrapper">
        @include('layout.sidebar')
        <!-- TOP Nav Bar -->
        @include('layout.navbar')
        <!-- TOP Nav Bar END -->

        <!-- Page Content  -->
        @yield('container')
    </div>
    <!-- Wrapper END -->
    <!-- Footer -->
    @include('layout.content')
    <!-- Footer END -->

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    @include('layout.footer')
</body>

</html>
