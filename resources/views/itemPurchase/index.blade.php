@extends('layout.template')
<script type="text/javascript" src="{{ asset('assets') }}/js/myJs/itemPurchase.js"></script>
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
                            <form action="{{ route('item-purchase.store') }}" method="POST" id="formData">
                                @csrf
                                <div class="form-row">
                                    <div class="col-md-12 mb-3">
                                        <label for="">Barang <span class="text-danger">*</span></label>
                                        <input type="hidden" id="id" name="id">
                                        <input type="hidden" id="itemId">
                                        <input type="hidden" id="typeId">
                                        <select name="item_id" id="item_id" class="form-control form-control-sm item_id"
                                            required></select>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="">Satuan Barang <span class="text-danger">*</span></label>
                                        <select name="type_id" id="type_id" class="form-control form-control-sm type_id"
                                            required></select>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="">Harga Beli <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-sm" id="purchase_price"
                                            name="purchase_price" oninput="formatNumber(this)" placeholder="0" required>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="">Jumlah <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control form-control-sm" id="qty"
                                            name="qty" min="1" placeholder="0" required>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="">No. Resi / Tracking Number</label>
                                        <input type="text" class="form-control form-control-sm" id="tracking_number"
                                            name="tracking_number" placeholder="Opsional">
                                    </div>
                                    <div class="col-md-12">
                                        <button id="simpan" class="btn btn-primary" type="submit">
                                            <span id="btn-text">Simpan</span>
                                            <span id="btn-spinner" class="spinner-border spinner-border-sm d-none"
                                                role="status" aria-hidden="true"></span>
                                        </button>
                                        <button type="button" class="btn btn-secondary"
                                            onclick="resetForm()">Reset</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-sm-7">
                    <div class="iq-card">
                        <div class="iq-card-header d-flex justify-content-between">
                            <div class="iq-header-title">
                                <h4 class="card-title">Daftar {{ $title }}</h4>
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
                                            <div style="width: 180px;">
                                                <input type="search" class="form-control form-control-sm" id="searchKey"
                                                    placeholder="Cari...">
                                            </div>
                                            <a class="iq-bg-primary" href="javascript:void();" onclick="searchData()">
                                                <i class="fa fa-search"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <table id="datagrid" class="easyui-datagrid" url="{{ url('/item-purchase-datatable') }}"
                                    toolbar="#tb" pagination="true" rownumbers="true" fitColumns="true"
                                    singleSelect="true" striped="true" nowrap="false"
                                    loadMsg="Loading, Silahkan Tunggu..." method="get" style="width:100%;height:auto;">
                                    <thead>
                                        <tr>
                                            <th field="action" formatter="action" width="80" halign="center"
                                                align="center">#</th>
                                            <th field="id" hidden></th>
                                            <th field="item_id" hidden></th>
                                            <th field="type_id" hidden></th>
                                            <th field="status_badge" width="100" halign="center" align="center">
                                                Status</th>
                                            <th field="created_at" width="120" halign="center" align="center"
                                                sortable="true">Tanggal</th>
                                            <th field="item" width="200" halign="center" sortable="true">Barang
                                            </th>
                                            <th field="type" width="100" halign="center" sortable="true">Satuan
                                            </th>
                                            <th field="purchase_price" width="120" halign="center" align="right"
                                                sortable="true">
                                                Harga Beli</th>
                                            <th field="qty" width="80" halign="center" align="center"
                                                sortable="true">Qty</th>
                                            <th field="total" width="120" halign="center" align="right"
                                                sortable="true">Total</th>
                                            <th field="tracking_number" width="150" halign="center" sortable="true">
                                                No. Resi</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <form id="delete-form" action="/item-purchase/" method="POST" style="display: none;">
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

        $("form#formData").submit(function(event) {
            event.preventDefault();
            showLoadingButton(event);
            $.ajax({
                url: $(this).attr("action"),
                data: $(this).serialize(),
                type: $(this).attr("method"),
                dataType: 'json',
                success: function(data) {
                    if (data.status == false) {
                        Swal.fire('Error', data.message, 'error');
                    } else {
                        Swal.fire('Sukses', data.message, 'success');
                        $("#datagrid").datagrid("reload");
                        resetForm();
                    }
                    hideLoadingButton(event);
                },
                error: function(xhr, status, error) {
                    hideLoadingButton(event);
                    Swal.fire('Error', "Terjadi kesalahan: " + (xhr.responseJSON?.message ||
                        error), 'error');
                }
            });
            return false;
        });
    });

    function resetForm() {
        $('#formData')[0].reset();
        $('#id').val('');
        $('#item_id').val(null).trigger('change');
        $('#type_id').val(null).trigger('change');
        $('#btn-text').text('Simpan');
    }
</script>
