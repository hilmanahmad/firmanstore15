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

    return row.id ? deleteButton + editButton : "Total";
}

function editData(id, row, pageNumber) {
    var rowData = JSON.parse(decodeURIComponent(row));
    console.log(rowData);
    $("#id").val(rowData.id);
    $("#categoryId").val(rowData.category_id);
    $("#name").val(rowData.name);
    category();
    $(".modal").modal("show");
}

function searchData() {
    var searchKey = $("#searchKey").val();
    $("#datagrid").datagrid("load", {
        searchKey: searchKey,
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

function category() {
    var category = $("#categoryId").val();

    $(".category_id").select2({
        allowClear: true,
        width: "100%",
        ajax: {
            url: "/option-ajax-where",
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    table: "categories",
                    value: "id",
                    order: "name",
                    whereName: "name", // Menggunakan districtId untuk filtering awal
                    whereValue: params.term, // Input pengguna
                };
            },
            processResults: function (data) {
                return {
                    results: $.map(data, function (category) {
                        return {
                            id: category.id,
                            text: category.name.toUpperCase(),
                        };
                    }),
                };
            },
            cache: true,
        },
        placeholder: "-- Pilih --",
        language: {
            inputTooShort: function () {
                return "Masukkan minimal 3 karakter";
            },
            noResults: function () {
                return "Tidak ada hasil yang ditemukan";
            },
            searching: function () {
                return "Mencari...";
            },
        },
    });

    // Jika categoryId ada, lakukan permintaan untuk data spesifik dan langsung pilih
    if (category) {
        $.ajax({
            url: "/option-ajax-where",
            dataType: "json",
            data: {
                table: "categories",
                value: "id",
                order: "name",
                whereName: "id", // Menggunakan districtId untuk filtering awal
                whereValue: category, // Input pengguna
            },
            success: function (data) {
                if (data && data.length > 0) {
                    var category = data[0];
                    var option = new Option(
                        category.name.toUpperCase(),
                        category.id,
                        true,
                        true
                    );
                    $(".category_id").append(option).trigger("change"); // Menambahkan dan memilih opsi di Select2
                }
            },
        });
    }
}
