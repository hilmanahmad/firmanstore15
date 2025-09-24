<div id="content-page" class="content-page">
    <div class="container-fluid">
    </div>
</div>
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script type="text/javascript" src="{{ asset('assets') }}/js/myJs/dashboard.js"></script>
@endpush
@stack('scripts')
