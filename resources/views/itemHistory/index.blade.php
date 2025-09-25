@extends('layout.template')
<script type="text/javascript" src="{{ asset('assets') }}/js/myJs/itemHistory.js"></script>
@section('container')
    <div id="content-page" class="content-page">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-5">
                    <div class="iq-card">
                        <div class="iq-card-header d-flex justify-content-between">
                            <div class="iq-header-title">
                                <h4 class="card-title">Form {{ $title }}</h4>
                            </div>
                        </div>
                        <div class="iq-card-body">
                            <form action="{{ route('item-history.store') }}" method="POST" id="formData">
                                @csrf
                                <div class="form-row">
                                    <div class="col-md-12 mb-3">
                                        <label for="">Barang</label>
                                        <input type="hidden" id="id" name="id">
                                        <input type="hidden" id="itemId">
                                        <input type="hidden" id="typeId">
                                        <select name="item_id" id="item_id"
                                            class="form-control form-control-sm item_id"></select>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="">Satuan Barang</label>
                                        <select name="type_id" id="type_id"
                                            class="form-control form-control-sm type_id"></select>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="">Harga Beli</label>
                                        <input type="text" class="form-control form-control-sm" id="purchase_price"
                                            name="purchase_price" oninput="formatNumber(this)" required>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="">Jumlah</label>
                                        <input type="number" class="form-control form-control-sm" id="qty"
                                            name="qty" required>
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

                <div class="col-sm-7">
                    <div class="iq-card">
                        <div class="iq-card-header d-flex justify-content-between">
                            <div class="iq-header-title">
                                <h4 class="card-title">{{ $title }}</h4>
                            </div>
                        </div>
                        <div class="iq-card-body">
                            <div class="table-responsive">
                                <div class="row justify-content-between">
                                    <div class="col-sm-12 col-md-3 mb-2">
                                        <div class="user-list-files d-flex">

                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-9 mb-2">
                                        <div class="user-list-files d-flex float-right">
                                            <div class="mr-2">
                                                <select name="item_id" id="id_item"
                                                    class="form-control form-control-sm id_item ml-2"
                                                    onchange="filter()"></select>
                                            </div>
                                            <div class="mr-2">
                                                <select name="type_id" id="id_type"
                                                    class="form-control form-control-sm id_type"
                                                    onchange="filter()"></select>
                                            </div>
                                            <input type="search" class="form-control form-control-sm ml-2" id="searchKey"
                                                placeholder="Cari">
                                            <a class="iq-bg-primary" href="javascript:void();" onclick="searchData()">
                                                <i class="fa fa-search"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <table id="datagrid" class="easyui-datagrid" url="{{ url('/item-history-datatable') }}"
                                    toolbar="#tb" pagination="true" rownumbers="true" fitColumns="true" singleSelect="true"
                                    striped="true" nowrap="false" loadMsg="Loading, Silahkan Tunggu..." method="get"
                                    style="width:100%;height:auto;">
                                    <thead>
                                        <tr>
                                            <th field="action" formatter="action" width="100" halign="center"
                                                align="center">#</th>
                                            <th field="id" hidden></th>
                                            <th field="item_id" hidden></th>
                                            <th field="type_id" hidden></th>
                                            <th field="created_at" width="250" halign="center" align="center"
                                                sortable="true">Tanggal
                                                Masuk
                                            </th>
                                            <th field="item" width="250" halign="center" sortable="true">Barang
                                            </th>
                                            <th field="type" width="250" halign="center" sortable="true">Satuan
                                            </th>
                                            <th field="purchase_price" width="250" halign="center" sortable="true">
                                                Harga
                                                Beli
                                            </th>
                                            <th field="qty" width="250" halign="center" sortable="true">Jumlah
                                            </th>
                                            <th field="qty_sold" width="250" halign="center" sortable="true">Jumlah
                                                Terjual
                                            </th>
                                            <th field="profit" width="250" halign="center" sortable="true">Keuntungan
                                            </th>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <form id="delete-form" action="/item-history/" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
    @stack('scripts')
@endsection
<script src="{{ asset('assets') }}/js/jquery-3.6.0.min.js"></script>
<script type="text/javascript">
    $(function() {
        item();
        type();
        item_filter();
        type_filter();
        $("form").submit(function(event) {
            event.preventDefault(); // Mencegah reload halaman
            showLoadingButton(event);
            $.ajax({
                url: $(this).attr("action"),
                data: $(this).serialize(),
                type: $(this).attr("method"),
                dataType: 'json',
                success: function(data) {
                    if (data.status == false) {
                        error(data.message);
                    } else {
                        success(data.message);
                        $("#datagrid").datagrid("reload");
                        clearFormData(event.target)
                        $("#validation").addClass("none");
                    }
                    hideLoadingButton(event);
                },
                error: function(xhr, status, error) {
                    hideLoadingButton(event);
                    error("Terjadi kesalahan: " + error);
                }
            })
            return false;
        });
    });
</script>
