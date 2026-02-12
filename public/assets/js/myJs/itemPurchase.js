const { read } = require("@popperjs/core");

function action(value, row, index) {
    let buttons = "";

    // Tombol konfirmasi manual jika masih pending
    if (row.status === "pending") {
        buttons += `
            <a class="action-buttons btn btn-sm btn-success" href="javascript:void(0)" onclick="confirmManual('${row.tracking_number}')" style="display: inline-flex;align-items: center;justify-content: center;" title="Konfirmasi Diterima">
                <i class="fa fa-check" style="margin-right: 0px;"></i>
            </a>`;
    }

    // Tombol edit (hanya jika pending)
    if (row.status === "pending") {
        buttons += `
            <a class="action-buttons btn btn-sm btn-primary" href="javascript:void(0)" onclick="editData('${
                row.id
            }', '${encodeURIComponent(
                JSON.stringify(row),
            )}')" style="display: inline-flex;align-items: center;justify-content: center;" title="Edit">
                <i class="fa fa-edit" style="margin-right: 0px;"></i>
            </a>`;
    }

    // Tombol hapus
    buttons += `
        <a class="action-buttons btn btn-sm btn-danger" href="javascript:void(0)" onclick="deleteConfirmation('delete-form', '${row.id}')" style="display: inline-flex;align-items: center;justify-content: center;" title="Hapus">
            <i class="fa fa-trash" style="margin-right: 0px;"></i>
        </a>`;

    return buttons;
}

function statusFormatter(value, row) {
    if (value === "received") {
        return '<span class="badge badge-success">Diterima</span>';
    } else {
        return '<span class="badge badge-warning">Pending</span>';
    }
}

function confirmManual(trackingNumber) {
    Swal.fire({
        title: "Konfirmasi Barang Masuk",
        text: `Apakah barang dengan resi "${trackingNumber}" sudah diterima?`,
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#28a745",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Ya, Sudah Diterima!",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: "Memproses...",
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                },
            });

            $.ajax({
                url: "/item-purchase/confirm-received",
                method: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr("content"),
                    tracking_number: trackingNumber,
                },
                dataType: "json",
                success: function (response) {
                    Swal.close();
                    if (response.status) {
                        Swal.fire({
                            icon: "success",
                            title: "Berhasil!",
                            html: response.message,
                            confirmButtonColor: "#28a745",
                        });
                        $("#datagrid").datagrid("reload");
                    } else {
                        Swal.fire("Gagal", response.message, "error");
                    }
                },
                error: function (xhr) {
                    Swal.close();
                    Swal.fire(
                        "Error",
                        xhr.responseJSON?.message || "Terjadi kesalahan",
                        "error",
                    );
                },
            });
        }
    });
}

function editData(id, row) {
    var rowData = JSON.parse(decodeURIComponent(row));

    $("#id").val(rowData.id);
    $("#itemId").val(rowData.item_id);
    $("#typeId").val(rowData.type_id);
    $("#userId").val(rowData.user_id);

    let purchasePrice = rowData.purchase_price.replace(/[^\d]/g, "");
    $("#purchase_price").val(parseInt(purchasePrice).toLocaleString("id-ID"));

    $("#qty").val(rowData.qty);
    $("#tracking_number").val(
        rowData.tracking_number !== "-" ? rowData.tracking_number : "",
    );

    item();
    type();

    // Load user selector if exists (SUPERADMIN only)
    if ($("#user_id").length) {
        user();
    }

    $("#btn-text").text("Update");

    $("html, body").animate(
        {
            scrollTop: $("#formData").offset().top - 100,
        },
        500,
    );
}

function searchData() {
    var searchKey = $("#searchKey").val();
    $("#datagrid").datagrid("load", { searchKey: searchKey });
}

function filter() {
    var itemId = $("#id_item").val();
    var typeId = $("#id_type").val();
    var status = $("#id_status").val();

    $("#datagrid").datagrid("load", {
        item_id: itemId,
        type_id: typeId,
        status: status,
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
        placeholder: "-- Pilih Barang --",
        preselectedId: itemId,
        formatText: (text) => text.toUpperCase(),
    });
}

function user() {
    const userId = $("#userId").val();
    initSelect2Ajax({
        selector: ".user_id",
        table: "users",
        valueField: "id",
        textField: "name",
        orderField: "name",
        searchField: "name",
        placeholder: "-- Pilih Pembeli --",
        preselectedId: userId,
        formatText: (text) => text.toUpperCase(),
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
        placeholder: "-- Pilih Satuan --",
        preselectedId: typeId,
        formatText: (text) => text.toUpperCase(),
    });
}

function item_filter() {
    initSelect2Ajax({
        selector: ".id_item",
        table: "items",
        valueField: "id",
        textField: "name",
        orderField: "name",
        searchField: "name",
        placeholder: "-- Barang --",
        formatText: (text) => text.toUpperCase(),
    });
}

function type_filter() {
    initSelect2Ajax({
        selector: ".id_type",
        table: "types",
        valueField: "id",
        textField: "name",
        orderField: "name",
        searchField: "name",
        placeholder: "-- Satuan --",
        formatText: (text) => text.toUpperCase(),
    });
}

function resetForm() {
    $("#formData")[0].reset();
    $("#id").val("");
    $("#item_id").val(null).trigger("change");
    $("#type_id").val(null).trigger("change");
    $("#btn-text").text("Simpan");
}
