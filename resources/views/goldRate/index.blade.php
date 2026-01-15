@extends('layout.template2')

@section('container')
    {{-- Load TradingView Script before Livewire component --}}
    <script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>

    <livewire:gold-rate />
@endsection
