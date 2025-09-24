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
<script src="{{ asset('assets') }}/js/jquery-3.6.0.min.js"></script>
<script type="text/javascript" src="{{ asset('assets') }}/js/easyui/jquery.easyui.min.js"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('assets') }}/easyui/themes/ui-cupertino/easyui.css">
<link rel="stylesheet" type="text/css" href="{{ asset('assets') }}/js/easyui/themes/icon.css">
<link href="{{ asset('assets') }}/sweetalert/sweetalert2.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets') }}/css/myCss/app.css">
<style>
    #map {
        height: 500px;
        width: 100%;
    }

    .form-control.is-invalid {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }

    .invalid-feedback {
        display: block;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875em;
        color: #dc3545;
    }
</style>

@livewireStyles
