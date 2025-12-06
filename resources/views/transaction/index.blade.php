@extends('layout.template')
<script type="text/javascript" src="{{ asset('assets') }}/js/myJs/transaction.js"></script>
@section('container')
    <div id="content-page" class="content-page">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="iq-card">
                        <div class="iq-card-header d-flex justify-content-between">
                            <div class="iq-header-title">
                                <h4 class="card-title">Form {{ $title }}</h4>
                            </div>
                        </div>
                        <div class="iq-card-body">
                            <form action="{{ route('transaction.store') }}" method="POST" id="formData">
                                @csrf
                                <div class="form-row">
                                    <div class="col-md-12 mb-3">
                                        <label for="">Pelanggan</label>
                                        <input type="hidden" id="no_trans" name="no_trans">
                                        <select name="customer_id" id="customer_id"
                                            class="form-control form-control-sm customer_id" required></select>
                                    </div>

                                    <!-- Multiple Items Section -->
                                    <div class="col-md-12 mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="mb-0"><strong>Daftar Barang</strong></label>
                                            <button type="button" class="btn btn-sm btn-success" onclick="addItemRow()">
                                                <i class="fa fa-plus"></i> Tambah Item
                                            </button>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm" id="itemsTable">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th width="30%">Barang</th>
                                                        <th width="20%">Satuan</th>
                                                        <th width="20%">Harga Jual</th>
                                                        <th width="15%">Jumlah</th>
                                                        <th width="10%">Subtotal</th>
                                                        <th width="5%">#</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="itemsTableBody">
                                                    <!-- Row items akan ditambahkan di sini -->
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="4" class="text-right"><strong>Total:</strong></td>
                                                        <td colspan="2"><strong id="grandTotal">Rp 0</strong></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <button id="simpan" class="btn btn-primary" type="submit">
                                            <span id="btn-text">Simpan Transaksi</span>
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

                <div class="col-sm-12 mt-3">
                    <div class="iq-card">
                        <div class="iq-card-header d-flex justify-content-between">
                            <div class="iq-header-title">
                                <h4 class="card-title">Riwayat {{ $title }}</h4>
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
                                                <select name="customer_id" id="id_customer"
                                                    class="form-control form-control-sm id_customer"
                                                    onchange="filter()"></select>
                                            </div>
                                            <div class="mr-2">
                                                <select name="item_id" id="id_item"
                                                    class="form-control form-control-sm id_item ml-2"
                                                    onchange="filter()"></select>
                                            </div>
                                            <div class="mr-2">
                                                <select name="type_id" id="id_type"
                                                    class="form-control form-control-sm id_type ml-2"
                                                    onchange="filter()"></select>
                                            </div>
                                            <div style="width: 180px;">
                                                <input type="search" class="form-control form-control-sm" id="searchKey"
                                                    placeholder="Cari">
                                            </div>
                                            <a class="iq-bg-primary" href="javascript:void();" onclick="searchData()">
                                                <i class="fa fa-search"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <table id="datagrid" class="easyui-datagrid" url="{{ url('/transaction-datatable') }}"
                                    toolbar="#tb" pagination="true" rownumbers="true" fitColumns="true" singleSelect="true"
                                    striped="true" nowrap="false" loadMsg="Loading, Silahkan Tunggu..." method="get"
                                    style="width:100%;height:auto;">
                                    <thead>
                                        <tr>
                                            <th field="action" formatter="action" width="100" halign="center"
                                                align="center">#</th>
                                            <th field="id" hidden></th>
                                            <th field="customer_id" hidden></th>
                                            <th field="item_id" hidden></th>
                                            <th field="type_id" hidden></th>
                                            <th field="created_at" width="250" halign="center" sortable="true">
                                                Tanggal
                                                Transaksi
                                            </th>
                                            <th field="customer" width="250" halign="center" sortable="true">
                                                Pelanggan
                                            </th>
                                            <th field="item" width="250" halign="center" sortable="true">Barang
                                            </th>
                                            <th field="amount" width="250" halign="center" sortable="true">Jumlah
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
    <form id="delete-form" action="/transaction/" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
    @stack('scripts')
@endsection
<script src="{{ asset('assets') }}/js/jquery-3.6.0.min.js"></script>
<script type="text/javascript">
    let itemRowCounter = 0;

    $(function() {
        customer();
        customer_filter();
        item_filter();
        type_filter();

        // Tambah row pertama saat load
        addItemRow();

        $("form#formData").submit(function(event) {
            event.preventDefault();

            // Validasi minimal 1 item
            if ($('#itemsTableBody tr').length === 0) {
                error('Minimal tambahkan 1 item');
                return false;
            }

            showLoadingButton(event);

            // Validasi setiap item
            let isValid = true;
            $('#itemsTableBody tr').each(function(index) {
                let itemId = $(this).find('.item_id_row').val();
                let typeId = $(this).find('.type_id_row').val();
                let qty = $(this).find('.qty_row').val();
                let sellingPrice = $(this).find('.selling_price_row').val();

                if (!itemId || !typeId || !qty || !sellingPrice) {
                    error('Lengkapi semua data item');
                    isValid = false;
                    return false;
                }
            });

            if (!isValid) {
                hideLoadingButton(event);
                return false;
            }

            // Check mode (create or edit)
            let mode = $(this).data('mode');
            let noTrans = $(this).data('no_trans');
            let url = $(this).attr("action");
            let method = $(this).attr("method");

            // Tambahkan no_trans jika mode edit
            let formData = $(this).serialize();
            if (mode === 'edit' && noTrans) {
                formData += '&no_trans=' + noTrans;
            }

            $.ajax({
                url: url,
                data: formData,
                type: method,
                dataType: 'json',
                success: function(data) {
                    if (data.status == false) {
                        error(data.message);
                    } else {
                        success(data.message);
                        $("#datagrid").datagrid("reload");
                        resetForm();
                    }
                    hideLoadingButton(event);
                },
                error: function(xhr, status, error) {
                    hideLoadingButton(event);
                    error("Terjadi kesalahan: " + (xhr.responseJSON?.message || error));
                }
            });
            return false;
        });
    });

    function addItemRow() {
        itemRowCounter++;
        let rowHtml = `
            <tr id="row_${itemRowCounter}">
                <td>
                    <select name="items[${itemRowCounter}][item_id]" 
                            class="form-control form-control-sm item_id_row" 
                            data-row="${itemRowCounter}" 
                            required>
                    </select>
                </td>
                <td>
                    <select name="items[${itemRowCounter}][type_id]" 
                            class="form-control form-control-sm type_id_row" 
                            data-row="${itemRowCounter}" 
                            required>
                    </select>
                </td>
                <td>
                    <input type="text" 
                           name="items[${itemRowCounter}][selling_price]" 
                           class="form-control form-control-sm selling_price_row" 
                           data-row="${itemRowCounter}"
                           oninput="formatNumberRow(this)"
                           required>
                </td>
                <td>
                    <input type="number" 
                           name="items[${itemRowCounter}][qty]" 
                           class="form-control form-control-sm qty_row" 
                           data-row="${itemRowCounter}"
                           min="1"
                           oninput="calculateRowSubtotal(${itemRowCounter})"
                           required>
                </td>
                <td>
                    <span class="subtotal_row" data-row="${itemRowCounter}">Rp 0</span>
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeItemRow(${itemRowCounter})">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;

        $('#itemsTableBody').append(rowHtml);

        // Initialize Select2 untuk row baru
        initRowSelect2(itemRowCounter);
    }

    function initRowSelect2(rowId) {
        // Init Item Select2
        initSelect2Ajax({
            selector: `select[name="items[${rowId}][item_id]"]`,
            table: "items",
            valueField: "id",
            textField: "name",
            orderField: "name",
            searchField: "name",
            placeholder: "-- Pilih Barang --",
            formatText: (text) => text.toUpperCase(),
        });

        // Init Type Select2
        initSelect2Ajax({
            selector: `select[name="items[${rowId}][type_id]"]`,
            table: "types",
            valueField: "id",
            textField: "name",
            orderField: "name",
            searchField: "name",
            placeholder: "-- Pilih Satuan --",
            formatText: (text) => text.toUpperCase(),
        });
    }

    function removeItemRow(rowId) {
        $(`#row_${rowId}`).remove();
        calculateGrandTotal();
    }

    function formatNumberRow(input) {
        let value = input.value.replace(/[^\d]/g, '');
        input.value = value ? parseInt(value).toLocaleString('id-ID') : '';

        let rowId = $(input).data('row');
        calculateRowSubtotal(rowId);
    }

    function calculateRowSubtotal(rowId) {
        let sellingPrice = $(`input[name="items[${rowId}][selling_price]"]`).val().replace(/[^\d]/g, '') || 0;
        let qty = $(`input[name="items[${rowId}][qty]"]`).val() || 0;

        let subtotal = parseInt(sellingPrice) * parseInt(qty);
        $(`.subtotal_row[data-row="${rowId}"]`).text('Rp ' + subtotal.toLocaleString('id-ID'));

        calculateGrandTotal();
    }

    function calculateGrandTotal() {
        let grandTotal = 0;
        $('.subtotal_row').each(function() {
            let subtotal = $(this).text().replace(/[^\d]/g, '') || 0;
            grandTotal += parseInt(subtotal);
        });

        $('#grandTotal').text('Rp ' + grandTotal.toLocaleString('id-ID'));
    }

    function resetForm() {
        $('#formData')[0].reset();
        $('#itemsTableBody').empty();
        itemRowCounter = 0;
        addItemRow();
        $('#grandTotal').text('Rp 0');
        $('#customer_id').val(null).trigger('change');
    }
</script>
