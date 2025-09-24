function item() {
    var item = $("#itemId").val();

    $(".item_id").select2({
        allowClear: true,
        width: "100%",
        ajax: {
            url: "/option-ajax-where",
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    table: "items",
                    value: "id",
                    order: "name",
                    whereName: "name", // Menggunakan districtId untuk filtering awal
                    whereValue: params.term, // Input pengguna
                };
            },
            processResults: function (data) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            id: item.id,
                            text: item.name.toUpperCase(),
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

    // Jika itemId ada, lakukan permintaan untuk data spesifik dan langsung pilih
    if (item) {
        $.ajax({
            url: "/option-ajax-where",
            dataType: "json",
            data: {
                table: "items",
                value: "id",
                order: "name",
                whereName: "id", // Menggunakan districtId untuk filtering awal
                whereValue: item, // Input pengguna
            },
            success: function (data) {
                if (data && data.length > 0) {
                    var item = data[0];
                    var option = new Option(
                        item.name.toUpperCase(),
                        item.id,
                        true,
                        true
                    );
                    $(".item_id").append(option).trigger("change"); // Menambahkan dan memilih opsi di Select2
                }
            },
        });
    }
}

function customer() {
    var customer = $("#customerId").val();

    $(".customer_id").select2({
        allowClear: true,
        width: "100%",
        ajax: {
            url: "/option-ajax-where",
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    table: "customers",
                    value: "id",
                    order: "name",
                    whereName: "name", // Menggunakan districtId untuk filtering awal
                    whereValue: params.term, // Input pengguna
                };
            },
            processResults: function (data) {
                return {
                    results: $.map(data, function (customer) {
                        return {
                            id: customer.id,
                            text: customer.name.toUpperCase(),
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

    // Jika itemId ada, lakukan permintaan untuk data spesifik dan langsung pilih
    if (customer) {
        $.ajax({
            url: "/option-ajax-where",
            dataType: "json",
            data: {
                table: "customers",
                value: "id",
                order: "name",
                whereName: "id", // Menggunakan districtId untuk filtering awal
                whereValue: customer, // Input pengguna
            },
            success: function (data) {
                if (data && data.length > 0) {
                    var customer = data[0];
                    var option = new Option(
                        customer.name.toUpperCase(),
                        customer.id,
                        true,
                        true
                    );
                    $(".customer_id").append(option).trigger("change"); // Menambahkan dan memilih opsi di Select2
                }
            },
        });
    }
}
