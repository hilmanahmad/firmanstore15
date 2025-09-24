<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="{{ asset('assets') }}/js/popper.min.js"></script>
<script src="{{ asset('assets') }}/js/bootstrap.min.js"></script>
<!-- Appear JavaScript -->
<!-- Countdown JavaScript -->
<script src="{{ asset('assets') }}/js/countdown.min.js"></script>
<!-- Counterup JavaScript -->
<script src="{{ asset('assets') }}/js/waypoints.min.js"></script>
<script src="{{ asset('assets') }}/js/jquery.counterup.min.js"></script>
<!-- Wow JavaScript -->
<script src="{{ asset('assets') }}/js/wow.min.js"></script>
<!-- Slick JavaScript -->
<script src="{{ asset('assets') }}/js/slick.min.js"></script>
<!-- Select2 JavaScript -->
<script src="{{ asset('assets') }}/js/select2.min.js"></script>
<!-- Owl Carousel JavaScript -->
<script src="{{ asset('assets') }}/js/owl.carousel.min.js"></script>
<!-- Magnific Popup JavaScript -->
<script src="{{ asset('assets') }}/js/jquery.magnific-popup.min.js"></script>
<!-- Smooth Scrollbar JavaScript -->
<script src="{{ asset('assets') }}/js/smooth-scrollbar.js"></script>
<!-- lottie JavaScript -->
<script src="{{ asset('assets') }}/js/lottie.js"></script>
<!-- am core JavaScript -->
<script src="{{ asset('assets') }}/js/core.js"></script>
<!-- am charts JavaScript -->
<script src="{{ asset('assets') }}/js/charts.js"></script>
<!-- am animated JavaScript -->
<script src="{{ asset('assets') }}/js/animated.js"></script>
<!-- am kelly JavaScript -->
<script src="{{ asset('assets') }}/js/kelly.js"></script>
<!-- am maps JavaScript -->
<script src="{{ asset('assets') }}/js/maps.js"></script>
<!-- am worldLow JavaScript -->
<script src="{{ asset('assets') }}/js/worldLow.js"></script>
<!-- Style Customizer -->
<script src="{{ asset('assets') }}/js/style-customizer.js"></script>
<!-- Chart Custom JavaScript -->
<script src="{{ asset('assets') }}/js/chart-custom.js"></script>
<!-- Custom JavaScript -->
<script src="{{ asset('assets') }}/js/custom.js"></script>
<script src="{{ asset('assets') }}/sweetalert/sweetalert2.all.min.js"></script>
<script type="text/javascript" src="{{ asset('assets') }}/js/myJs/app.js"></script>
@livewireScripts
<script>
    // Override default DataGrid settings
    $.extend($.fn.datagrid.defaults, {
        pagination: true, // pastikan pagination aktif
        pageSize: 20, // default baris per halaman
        pageList: [10, 20, 50, 100] // pilihan di dropdown
    });

    // Karena DataGrid menggunakan plugin Pagination, override juga di sana:
    $.extend($.fn.pagination.defaults, {
        pageSize: 20,
        pageList: [10, 20, 50, 100]
    });
</script>
