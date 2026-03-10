function action(value, row, index) {
    var detailButton = `
        <a class="action-buttons btn btn-sm btn-info" href="javascript:void(0)" onclick="viewDetail('${row.no_trans}')" style="display: inline-flex;align-items: center;justify-content: center;" title="Lihat Detail">
            <i class="fa fa-eye" style="margin-right: 0px;"></i>
        </a>`;

    var editButton = `
        <a class="action-buttons btn btn-sm btn-primary" href="javascript:void(0)" onclick="editTransaction('${row.no_trans}')" style="display: inline-flex;align-items: center;justify-content: center;" title="Edit">
            <i class="fa fa-edit" style="margin-right: 0px;"></i>
        </a>`;

    var deleteButton = `
        <a class="action-buttons btn btn-sm btn-danger" href="javascript:void(0)" onclick="deleteTransactionGroup('${row.no_trans}')" style="display: inline-flex;align-items: center;justify-content: center;" title="Hapus">
            <i class="fa fa-trash" style="margin-right: 0px;"></i>
        </a>`;

    return detailButton + editButton + deleteButton;
}

function viewDetail(noTrans) {
    $.ajax({
        url: `/transaction-detail/${noTrans}`,
        method: "GET",
        dataType: "json",
        success: function (response) {
            if (response.status) {
                showDetailModal(response.data);
            } else {
                failed(response.message);
            }
        },
        error: function (xhr, status, error) {
            failed("Gagal memuat detail transaksi: " + error);
        },
    });
}

function showDetailModal(data) {
    console.log("Data received:", data); // Untuk debugging

    if (!data || !Array.isArray(data)) {
        failed("Format data tidak sesuai");
        return;
    }

    let itemRows = "";
    let itemNumber = 1;
    let grandTotal = 0;
    let totalProfit = 0;

    data.forEach((item) => {
        itemRows += `
            <div class="item-section mb-3">
                <div class="card">
                    <div class="card-header bg-light">
                        <strong>Item ${itemNumber}: ${item.item} (${item.type})</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-md-3">
                                <small><strong>Qty:</strong> ${item.qty}</small>
                            </div>
                            <div class="col-md-3">
                                <small><strong>Harga Jual:</strong> ${item.selling_price}</small>
                            </div>
                            <div class="col-md-3">
                                <small><strong>Subtotal:</strong> ${item.selling_price}</small>
                            </div>
                            <div class="col-md-3">
                                <small><strong>Profit:</strong> <span class="text-success">${item.profit}</span></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Hitung grand total (parse rupiah format)
        let itemAmount = item.selling_price
            ? item.selling_price.replace(/[^\d]/g, "")
            : 0;
        let itemProfit = item.profit ? item.profit.replace(/[^\d]/g, "") : 0;
        grandTotal += parseInt(itemAmount) || 0;
        totalProfit += parseInt(itemProfit) || 0;

        itemNumber++;
    });

    let firstItem = data[0] || {};

    let modalContent = `
        <div class="modal fade" id="detailModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Detail Transaksi - ${
                            firstItem.no_trans || "-"
                        }</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td width="40%"><strong>No. Transaksi</strong></td>
                                        <td>: ${firstItem.no_trans || "-"}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Pelanggan</strong></td>
                                        <td>: ${firstItem.customer || "-"}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td width="40%"><strong>Tanggal</strong></td>
                                        <td>: ${
                                            firstItem.created_at || "-"
                                        }</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Jumlah Item</strong></td>
                                        <td>: ${data.length} item</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <h6 class="mb-3"><strong>Detail Item Transaksi</strong></h6>
                        ${itemRows}
                        
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <table class="table table-bordered">
                                    <tr class="bg-info text-white">
                                        <td class="text-right" width="70%"><strong>GRAND TOTAL:</strong></td>
                                        <td class="text-right" width="15%"><strong>Rp ${grandTotal.toLocaleString(
                                            "id-ID",
                                        )}</strong></td>
                                        <td class="text-right" width="15%"><strong>Rp ${totalProfit.toLocaleString(
                                            "id-ID",
                                        )}</strong></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" onclick="printReceipt('${
                            firstItem.no_trans
                        }')">
                            <i class="fa fa-print"></i> Cetak Nota
                        </button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Hapus modal lama jika ada
    $("#detailModal").remove();

    // Tambahkan modal baru
    $("body").append(modalContent);

    // Tampilkan modal
    $("#detailModal").modal("show");

    // Hapus modal setelah ditutup
    $("#detailModal").on("hidden.bs.modal", function () {
        $(this).remove();
    });
}

function printReceipt(noTrans) {
    $.ajax({
        url: `/transaction-detail/${noTrans}`,
        method: "GET",
        dataType: "json",
        success: function (response) {
            if (response.status) {
                generateReceipt(response.data);
            } else {
                failed(response.message);
            }
        },
        error: function (xhr, status, error) {
            failed("Gagal memuat data untuk cetak: " + error);
        },
    });
}

function generateReceipt(data) {
    if (!data || !Array.isArray(data) || data.length === 0) {
        failed("Data tidak valid");
        return;
    }

    let firstItem = data[0];
    let itemRows = "";
    let grandTotal = 0;
    let totalQty = 0;

    data.forEach((item, index) => {
        let itemAmount = item.selling_price
            ? item.selling_price.replace(/[^\d]/g, "")
            : 0;
        let itemTotal = parseInt(itemAmount) * parseInt(item.qty);
        grandTotal += itemTotal;
        totalQty += parseInt(item.qty);

        itemRows += `
            <tr>
                <td style="padding: 8px 5px; border-bottom: 1px dashed #ddd;">${
                    index + 1
                }</td>
                <td style="padding: 8px 5px; border-bottom: 1px dashed #ddd;">${
                    item.item
                }</td>
                <td style="padding: 8px 5px; border-bottom: 1px dashed #ddd; text-align: center;">${
                    item.type
                }</td>
                <td style="padding: 8px 5px; border-bottom: 1px dashed #ddd; text-align: center;">${
                    item.qty
                }</td>
                <td style="padding: 8px 5px; border-bottom: 1px dashed #ddd; text-align: right;">${
                    item.selling_price
                }</td>
                <td style="padding: 8px 5px; border-bottom: 1px dashed #ddd; text-align: right;">Rp ${itemTotal.toLocaleString(
                    "id-ID",
                )}</td>
            </tr>
        `;
    });

    let receiptContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Nota Pembayaran - ${firstItem.no_trans}</title>
            <style>
                @media print {
                    @page {
                        size: A4;
                        margin: 0;
                    }
                    body {
                        margin: 1cm;
                    }
                    .no-print {
                        display: none;
                    }
                }
                
                body {
                    font-family: 'Arial', sans-serif;
                    margin: 0;
                    padding: 20px;
                    background: #f5f5f5;
                }
                
                .receipt-container {
                    max-width: 800px;
                    margin: 0 auto;
                    background: white;
                    padding: 40px;
                    box-shadow: 0 0 20px rgba(0,0,0,0.1);
                    border-radius: 10px;
                }
                
                .receipt-header {
                    text-align: center;
                    border-bottom: 3px solid #007bff;
                    padding-bottom: 20px;
                    margin-bottom: 30px;
                }
                
                .company-name {
                    font-size: 32px;
                    font-weight: bold;
                    color: #007bff;
                    margin: 0;
                    text-transform: uppercase;
                    letter-spacing: 2px;
                }
                
                .company-info {
                    color: #666;
                    font-size: 14px;
                    margin-top: 10px;
                }
                
                .receipt-title {
                    text-align: center;
                    font-size: 24px;
                    font-weight: bold;
                    color: #333;
                    margin: 20px 0;
                    text-transform: uppercase;
                    letter-spacing: 1px;
                }
                
                .transaction-info {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 20px;
                    margin-bottom: 30px;
                    padding: 20px;
                    background: #f8f9fa;
                    border-radius: 8px;
                }
                
                .info-group {
                    display: flex;
                    flex-direction: column;
                    gap: 8px;
                }
                
                .info-label {
                    font-size: 12px;
                    color: #666;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                }
                
                .info-value {
                    font-size: 16px;
                    font-weight: bold;
                    color: #333;
                }
                
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin: 20px 0;
                }
                
                thead {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                }
                
                thead th {
                    padding: 15px 5px;
                    text-align: left;
                    font-size: 14px;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                }
                
                tbody tr:hover {
                    background: #f8f9fa;
                }
                
                .total-section {
                    margin-top: 30px;
                    padding-top: 20px;
                    border-top: 2px solid #ddd;
                }
                
                .total-row {
                    display: flex;
                    justify-content: space-between;
                    padding: 10px 0;
                    font-size: 16px;
                }
                
                .grand-total {
                    display: flex;
                    justify-content: space-between;
                    padding: 20px;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                    font-size: 24px;
                    font-weight: bold;
                    border-radius: 8px;
                    margin-top: 15px;
                }
                
                .receipt-footer {
                    margin-top: 40px;
                    padding-top: 20px;
                    border-top: 2px dashed #ddd;
                    text-align: center;
                }
                
                .signature-section {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 40px;
                    margin-top: 40px;
                    text-align: center;
                }
                
                .signature-box {
                    padding: 10px;
                }
                
                .signature-line {
                    margin-top: 80px;
                    border-top: 2px solid #333;
                    padding-top: 10px;
                    font-weight: bold;
                }
                
                .thank-you {
                    font-size: 18px;
                    color: #007bff;
                    font-weight: bold;
                    margin: 20px 0;
                }
                
                .notes {
                    font-size: 12px;
                    color: #666;
                    font-style: italic;
                    margin-top: 10px;
                }
                
                .print-button {
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    padding: 12px 30px;
                    background: #007bff;
                    color: white;
                    border: none;
                    border-radius: 25px;
                    font-size: 16px;
                    cursor: pointer;
                    box-shadow: 0 4px 15px rgba(0,123,255,0.3);
                    transition: all 0.3s;
                }
                
                .print-button:hover {
                    background: #0056b3;
                    transform: translateY(-2px);
                    box-shadow: 0 6px 20px rgba(0,123,255,0.4);
                }
                
                .badge {
                    display: inline-block;
                    padding: 5px 15px;
                    background: #28a745;
                    color: white;
                    border-radius: 20px;
                    font-size: 12px;
                    font-weight: bold;
                    margin-left: 10px;
                }
            </style>
        </head>
        <body>
            <button class="print-button no-print" onclick="window.print()">
                <i class="fa fa-print"></i> Cetak Nota
            </button>
            
            <div class="receipt-container">
                <div class="receipt-header">
                    <h1 class="company-name">FIRMAN STORE 15</h1>
                    <div class="company-info">
                        The Paradise Park Residence Blok C 20, Sepatan, Sepatan<br>
                    Indonesia, 15520
                        Telp: 089524113345 | Email: hilmanahmad089@gmail.com
                    </div>
                </div>
                
                <div class="receipt-title">
                    NOTA PEMBAYARAN
                    <span class="badge">LUNAS</span>
                </div>
                
                <div class="transaction-info">
                    <div class="info-group">
                        <div class="info-label">No. Transaksi</div>
                        <div class="info-value">${
                            firstItem.no_trans || "-"
                        }</div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Tanggal</div>
                        <div class="info-value">${
                            firstItem.created_at || "-"
                        }</div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Pelanggan</div>
                        <div class="info-value">${
                            firstItem.customer || "-"
                        }</div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Total Item</div>
                        <div class="info-value">${data.length} Item</div>
                    </div>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th style="width: 5%;">No</th>
                            <th style="width: 35%;">Nama Barang</th>
                            <th style="width: 15%; text-align: center;">Satuan</th>
                            <th style="width: 10%; text-align: center;">Qty</th>
                            <th style="width: 17.5%; text-align: right;">Harga</th>
                            <th style="width: 17.5%; text-align: right;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${itemRows}
                    </tbody>
                </table>
                
                <div class="total-section">
                    <div class="total-row">
                        <span>Subtotal:</span>
                        <span>Rp ${grandTotal.toLocaleString("id-ID")}</span>
                    </div>
                    <div class="total-row">
                        <span>Total Quantity:</span>
                        <span>${totalQty} Item</span>
                    </div>
                    <div class="grand-total">
                        <span>TOTAL PEMBAYARAN:</span>
                        <span>Rp ${grandTotal.toLocaleString("id-ID")}</span>
                    </div>
                </div>
                
                <div class="receipt-footer">
                    <div class="thank-you">*** Terima Kasih Atas Pembelian Anda ***</div>
                    <div class="notes">
                        Barang yang sudah dibeli tidak dapat dikembalikan.<br>
                        Simpan nota ini sebagai bukti pembayaran yang sah.
                    </div>
                </div>
            </div>
            
            <script>
                // Auto print ketika halaman dibuka (optional)
                // window.onload = function() { window.print(); }
            </script>
        </body>
        </html>
    `;

    // Buka di window baru
    let printWindow = window.open("", "_blank", "width=800,height=600");
    printWindow.document.write(receiptContent);
    printWindow.document.close();

    // Trigger print dialog setelah konten dimuat
    printWindow.onload = function () {
        printWindow.focus();
        // Uncomment line berikut jika ingin auto print
        // printWindow.print();
    };
}
function deleteTransactionGroup(noTrans) {
    Swal.fire({
        title: "Konfirmasi Hapus",
        text: "Yakin ingin menghapus semua item dalam transaksi ini?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Ya, Hapus!",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            // Submit form delete dengan no_trans
            let form = $("#delete-form");
            form.attr("action", "/transaction-delete-group/" + noTrans);

            $.ajax({
                url: form.attr("action"),
                method: "DELETE",
                data: {
                    _token: $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (response) {
                    let data = JSON.parse(response);
                    if (data.status) {
                        success(data.message);
                        $("#datagrid").datagrid("reload");
                    } else {
                        failed(data.message);
                    }
                },
                error: function (xhr, status, error) {
                    failed("Gagal menghapus transaksi: " + error);
                },
            });
        }
    });
}

function item() {
    const itemId = $("#itemId").val();

    initSelect2Ajax({
        selector: ".item_id",
        table: "items",
        valueField: "id",
        textField: "name",
        orderField: "name",
        searchField: "name",
        placeholder: "-- Pilih --",
        preselectedId: itemId,
        formatText: (text) => text.toUpperCase(), // Format text menjadi uppercase
    });
}

function customer(customerId) {
    initSelect2Ajax({
        selector: ".customer_id",
        table: "customers",
        valueField: "id",
        textField: "name",
        orderField: "name",
        searchField: "name",
        placeholder: "-- Pilih --",
        preselectedId: customerId,
        formatText: (text) => text.toUpperCase(), // Format text menjadi uppercase
    });
}

function type() {
    const typeId = $("#typeId").val();

    initSelect2Ajax({
        selector: ".type_id",
        table: "types",
        valueField: "id",
        textField: "name",
        orderField: "name",
        searchField: "name",
        placeholder: "-- Pilih --",
        preselectedId: typeId,
        formatText: (text) => text.toUpperCase(), // Format text menjadi uppercase
    });
}

function item_filter() {
    const idItem = $("#idItem").val();

    initSelect2Ajax({
        selector: ".id_item",
        table: "items",
        valueField: "id",
        textField: "name",
        orderField: "name",
        searchField: "name",
        placeholder: "-- Barang --",
        preselectedId: idItem,
        formatText: (text) => text.toUpperCase(), // Format text menjadi uppercase
    });
}

function customer_filter() {
    const idcustomer = $("#idcustomer").val();

    initSelect2Ajax({
        selector: ".id_customer",
        table: "customers",
        valueField: "id",
        textField: "name",
        orderField: "name",
        searchField: "name",
        placeholder: "-- Pelanggan --",
        preselectedId: idcustomer,
        formatText: (text) => text.toUpperCase(), // Format text menjadi uppercase
    });
}

function type_filter() {
    const idType = $("#idType").val();

    initSelect2Ajax({
        selector: ".id_type",
        table: "types",
        valueField: "id",
        textField: "name",
        orderField: "name",
        searchField: "name",
        placeholder: "-- Satuan Barang --",
        preselectedId: idType,
        formatText: (text) => text.toUpperCase(), // Format text menjadi uppercase
    });
}

function editTransaction(noTrans) {
    $.ajax({
        url: `/transaction-detail/${noTrans}`,
        method: "GET",
        dataType: "json",
        success: function (response) {
            if (response.status) {
                populateEditForm(response.data);
                // Scroll ke form
                $("html, body").animate(
                    {
                        scrollTop: $("#formData").offset().top - 100,
                    },
                    500,
                );
            } else {
                failed(response.message);
            }
        },
        error: function (xhr, status, error) {
            failed("Gagal memuat data untuk cetak: " + error);
        },
    });
}

function populateEditForm(data) {
    if (!data || !Array.isArray(data) || data.length === 0) {
        failed("Data transaksi tidak valid");
        return;
    }

    let firstItem = data[0];

    // Set mode edit
    $("#formData").data("mode", "edit");
    $("#formData").data("no_trans", firstItem.no_trans);

    // Set customer
    customer(firstItem.customer_id);

    // Clear existing items
    $("#itemsTableBody").empty();
    itemRowCounter = 0;

    // Populate items
    data.forEach((item, index) => {
        itemRowCounter++;
        let rowHtml = `
            <tr id="row_${itemRowCounter}" data-transaction-id="${item.id}">
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
                           value="${item.selling_price.replace(/[^\d]/g, "")}"
                           oninput="formatNumberRow(this)"
                           required>
                </td>
                <td>
                    <input type="number" 
                           name="items[${itemRowCounter}][qty]" 
                           class="form-control form-control-sm qty_row" 
                           data-row="${itemRowCounter}"
                           value="${item.qty}"
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

        $("#itemsTableBody").append(rowHtml);

        // Initialize Select2 dengan preselected value
        initRowSelect2WithValue(itemRowCounter, item.item_id, item.type_id);

        // Format harga
        let priceInput = $(
            `input[name="items[${itemRowCounter}][selling_price]"]`,
        );
        formatNumberRow(priceInput[0]);

        // Calculate subtotal
        calculateRowSubtotal(itemRowCounter);
    });

    // Update button text
    $("#btn-text").text("Update Transaksi");

    success("Data transaksi dimuat, silakan edit");
}

function initRowSelect2WithValue(rowId, itemId, typeId) {
    // Init Item Select2 with preselected value
    let itemSelector = `select[name="items[${rowId}][item_id]"]`;
    initSelect2Ajax({
        selector: itemSelector,
        table: "items",
        valueField: "id",
        textField: "name",
        orderField: "name",
        searchField: "name",
        placeholder: "-- Pilih Barang --",
        formatText: (text) => text.toUpperCase(),
        preselectedId: itemId,
    });

    // Init Type Select2 with preselected value
    let typeSelector = `select[name="items[${rowId}][type_id]"]`;
    initSelect2Ajax({
        selector: typeSelector,
        table: "types",
        valueField: "id",
        textField: "name",
        orderField: "name",
        searchField: "name",
        placeholder: "-- Pilih Satuan --",
        formatText: (text) => text.toUpperCase(),
        preselectedId: typeId,
    });
}

function resetForm() {
    $("#formData")[0].reset();
    $("#formData").removeData("mode");
    $("#formData").removeData("no_trans");
    $("#itemsTableBody").empty();
    itemRowCounter = 0;
    addItemRow();
    $("#grandTotal").text("Rp 0");
    $("#customer_id").val(null).trigger("change");
    $("#btn-text").text("Simpan Transaksi");
}
