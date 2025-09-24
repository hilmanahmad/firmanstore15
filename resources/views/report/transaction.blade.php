@extends('layout.template')
<script type="text/javascript" src="{{ asset('assets') }}/js/myJs/reportTransaction.js"></script>
@section('container')
    <div id="content-page" class="content-page">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 col-lg-6">
                    <div class="iq-card">
                        <div class="iq-card-header d-flex justify-content-between">
                            <div class="iq-header-title">
                                <h4 class="card-title">Form {{ $title }}</h4>
                            </div>
                        </div>
                        <div class="iq-card-body">
                            <form action="{{ route('report.transaction') }}" method="GET" id="formData">
                                @csrf
                                <div class="form-row">
                                    <div class="col-md-12 mb-3">
                                        <label for="">Pelanggan</label>
                                        <input type="hidden" value="{{ $request->customer_id }}" id="customerId">
                                        <input type="hidden" value="{{ $request->item_id }}" id="itemId">
                                        <select name="customer_id" id="customer_id"
                                            class="form-control form-control-sm customer_id"></select>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="">Barang</label>
                                        <select name="item_id" id="item_id"
                                            class="form-control form-control-sm item_id"></select>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="">Tanggal Awal</label>
                                        <input type="date" class="form-control form-control-sm start_date"
                                            id="start_date" name="start_date" value="{{ $request->start_date }}"
                                            placeholder="Tanggal Awal">
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="">Tanggal Akhir</label>
                                        <input type="date" class="form-control form-control-sm start_date" id="end_date"
                                            name="end_date" value="{{ $request->end_date }}" placeholder="Tanggal Akhir">
                                    </div>
                                    <button id="simpan" class="btn btn-primary" type="submit"><span
                                            id="btn-text">Save</span>
                                        <span id="btn-spinner" class="spinner-border spinner-border-sm d-none"
                                            role="status" aria-hidden="true"></span></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @if ($data)
                    <div class="col-sm-12 col-lg-12">
                        <div class="iq-card">
                            <div class="iq-card-header d-flex justify-content-between">
                                <div class="iq-header-title">
                                    <h4 class="card-title">{{ $title }}</h4>
                                </div>
                            </div>
                            <div class="iq-card-body">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Pelanggan</th>
                                            <th>Barang</th>
                                            <th>Jumlah</th>
                                            <th>Harga Beli</th>
                                            <th>Harga Jual</th>
                                            <th>Keuntungan</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $grand_total_profit = 0;
                                            $grand_total_qty = 0;
                                            $grand_total_purchase = 0;
                                            $grand_total_selling = 0;
                                        @endphp
                                        @foreach ($data as $item)
                                            @php
                                                $transaction_total_profit = 0;
                                                $transaction_total_qty = 0;
                                                $transaction_total_purchase = 0;
                                                $transaction_total_selling = 0;
                                            @endphp
                                            @foreach ($item->detail as $detail)
                                                @php
                                                    $detail_profit =
                                                        ($detail->selling_price -
                                                            $detail->itemHistory->purchase_price) *
                                                        $detail->qty;
                                                    $detail_purchase =
                                                        $detail->itemHistory->purchase_price * $detail->qty;
                                                    $detail_selling = $detail->selling_price * $detail->qty;

                                                    $transaction_total_profit += $detail_profit;
                                                    $transaction_total_qty += $detail->qty;
                                                    $transaction_total_purchase += $detail_purchase;
                                                    $transaction_total_selling += $detail_selling;
                                                @endphp
                                            @endforeach
                                            @php
                                                $grand_total_profit += $transaction_total_profit;
                                                $grand_total_qty += $transaction_total_qty;
                                                $grand_total_purchase += $transaction_total_purchase;
                                                $grand_total_selling += $transaction_total_selling;
                                            @endphp
                                            @if ($item->detail && $item->detail->count() > 0)
                                                @foreach ($item->detail as $detail)
                                                    <tr>
                                                        @if ($loop->first)
                                                            <td rowspan="{{ $item->detail->count() }}">
                                                                {{ $loop->parent->iteration }}</td>
                                                            <td rowspan="{{ $item->detail->count() }}">
                                                                {{ $item->customer->name }}</td>
                                                            <td rowspan="{{ $item->detail->count() }}">
                                                                {{ $item->item->name }}</td>
                                                        @endif
                                                        <td>{{ $detail->qty }}</td>
                                                        <td>{{ number_format($detail->itemHistory->purchase_price, 0, ',', '.') }}
                                                        </td>
                                                        <td>{{ number_format($detail->selling_price, 0, ',', '.') }}</td>
                                                        <td>{{ number_format($detail->selling_price - $detail->itemHistory->purchase_price, 0, ',', '.') }}
                                                        </td>
                                                        @if ($loop->first)
                                                            <td rowspan="{{ $item->detail->count() }}">
                                                                {{ number_format($transaction_total_profit, 0, ',', '.') }}
                                                            </td>
                                                        @endif
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $item->customer->name }}</td>
                                                    <td>{{ $item->item->name }}</td>
                                                    <td>{{ $item->qty }}</td>
                                                    <td>0</td>
                                                    <td>0</td>
                                                    <td>0</td>
                                                    <td>0</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr style="font-weight: bold; background-color: #f8f9fa;">
                                            <td colspan="3" class="text-center">TOTAL</td>
                                            <td>{{ number_format($grand_total_qty, 0, ',', '.') }}</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td>{{ number_format($grand_total_profit, 0, ',', '.') }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    </div>
    <form id="delete-form" action="/category/" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
    @stack('scripts')
@endsection
<script src="{{ asset('assets') }}/js/jquery-3.6.0.min.js"></script>
<script type="text/javascript">
    $(function() {
        customer();
        item();
    });
</script>
