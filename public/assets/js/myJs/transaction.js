function action(value, row, index) {
    var pageNumber = $("#datagrid").datagrid("getPager").data("pagination")
        .options.pageNumber;

    var deleteButton = `
        <a class="action-buttons btn btn-sm btn-danger" href="javascript:void(0)" onclick="deleteConfirmation('delete-form', '${row.id}')" style="display: inline-flex;align-items: center;justify-content: center;">
            <i class="fa fa-trash" style="margin-right: 0px;"></i>
        </a>`;
    var editButton = `
        <a class="action-buttons btn btn-sm btn-primary" href="javascript:void(0)" onclick="editData('${
            row.id
        }', '${encodeURIComponent(
        JSON.stringify(row)
    )}', '${pageNumber}')" data-toggle="modal" data-target="#exampleModal">
            <i class="fa fa-edit" style="margin-right: 0px;"></i>
        </a>`;

    return deleteButton + editButton;
}

function editData(id, row, pageNumber) {
    var rowData = JSON.parse(decodeURIComponent(row));
    console.log(rowData);
    $("#id").val(rowData.id);
    $("#customerId").val(rowData.customer_id);
    $("#itemId").val(rowData.item_id);
    $("#typeId").val(rowData.type_id);
    $("#purchase_price").val(rowData.purchase_price);
    $("#qty").val(rowData.qty);
    $("#selling_price").val(rowData.selling_price);
    item();
    type();
    customer();
    $(".modal").modal("show");
}

function searchData() {
    var searchKey = $("#searchKey").val();

    $("#datagrid").datagrid("load", {
        searchKey: searchKey,
    });
}

function filter() {
    var customerId = $("#id_customer").val();
    var itemId = $("#id_item").val();
    var typeId = $("#id_type").val();

    $("#datagrid").datagrid("load", {
        customer_id: customerId,
        item_id: itemId,
        type_id: typeId,
    });
}

function searchEvent() {
    searchData();
}

function addForm(value = null) {
    $("#card-table").addClass("none");
    $("#card-form").removeClass("none");
}

function clearForm(formName) {
    $("#" + formName)[0].reset(); // Reset form fields
    $("#" + formName)
        .find("select")
        .val(null)
        .trigger("change"); // Reset select fields and trigger change event

    // Reset hidden input fields except CSRF token
    $("#" + formName)
        .find("input[type=hidden]")
        .not("[name='_token']") // Exclude the CSRF token field from being reset
        .val(""); // Reset hidden fields except CSRF token
}

document
    .getElementById("formData")
    .addEventListener("submit", function (event) {
        event.preventDefault(); // Mencegah form submit secara default

        // Buat objek FormData dari form
        var formData = new FormData(this);
        fetch(this.action, {
            method: "POST",
            body: formData,
            headers: {
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
            },
        })
            .then((response) => {
                // Periksa apakah respons memiliki status yang berhasil (status 200-299)
                if (!response.ok) {
                    failed("Network response was not ok");
                }
                // Ubah respons ke JSON
                return response.json();
            })
            .then((data) => {
                // Tangani respons JSON
                if (data.status == false) {
                    failed(data.message);
                } else {
                    success(data.message);
                    $("#datagrid").datagrid("reload");
                }

                // Lakukan sesuatu dengan data respons
            })
            .catch((error) => {
                // Tangani kesalahan
                failed(
                    "There was a problem with your fetch operation: " + error
                );
            });
    });

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

function customer() {
    const customerId = $("#customerId").val();

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
