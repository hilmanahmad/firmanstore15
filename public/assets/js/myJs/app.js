function error(value) {
    const Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.onmouseenter = Swal.stopTimer;
            toast.onmouseleave = Swal.resumeTimer;
        },
    });
    Toast.fire({
        icon: "error",
        title: value,
    });
}

function success(value) {
    const Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.onmouseenter = Swal.stopTimer;
            toast.onmouseleave = Swal.resumeTimer;
        },
    });
    Toast.fire({
        icon: "success",
        title: value,
    });
}

function failed(value) {
    const Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.onmouseenter = Swal.stopTimer;
            toast.onmouseleave = Swal.resumeTimer;
        },
    });
    Toast.fire({
        icon: "error",
        title: value,
    });
}

function warning(value) {
    const Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.onmouseenter = Swal.stopTimer;
            toast.onmouseleave = Swal.resumeTimer;
        },
    });
    Toast.fire({
        icon: "warning",
        title: value,
    });
}

window.deleteConfirmation = function (formId, id) {
    Swal.fire({
        title: "Apakah Anda yakin?",
        text: "Anda akan hapus permanen data ini!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ya, hapus!",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: $("#" + formId).attr("action") + id,
                type: $("#" + formId).attr("method"),
                data: $("#" + formId).serialize(), // Serialize the form data
                dataType: "json",
                beforeSend: function () {
                    // Menambahkan loader atau disable tombol submit jika diperlukan
                    // $("button[type=submit]").attr("disabled", "disabled");
                },
                success: function (data) {
                    if (data.status == true) {
                        // Reload or update your data table
                        // Example: $('#dataTable').DataTable().ajax.reload();
                        success(data.message);
                        $("#datagrid").datagrid("reload");
                        $("#datagrid-detail").datagrid("reload");
                    } else {
                        failed("Data gagal dihapus");
                    }
                },
                error: function (xhr, status, error) {
                    failed("Data gagal dihapus");
                },
            });
        }
    });
};

// Fungsi untuk menampilkan loading
function showLoadingButton(event) {
    event.preventDefault(); // Mencegah submit otomatis agar efek loading terlihat

    let btnSubmit = document.getElementById("simpan");
    let btnText = document.getElementById("btn-text");
    let btnSpinner = document.getElementById("btn-spinner");

    // Ubah teks tombol dan tampilkan spinner
    btnText.innerText = "Processing...";
    btnSpinner.classList.remove("d-none");
    btnSubmit.disabled = true; // Nonaktifkan tombol agar tidak bisa diklik dua kali
}

function hideLoadingButton(event) {
    event.preventDefault(); // Mencegah submit otomatis agar efek loading terlihat

    let btnSubmit = document.getElementById("simpan");
    let btnText = document.getElementById("btn-text");
    let btnSpinner = document.getElementById("btn-spinner");

    // Ubah teks tombol dan tampilkan spinner
    btnText.innerText = "Save";
    btnSpinner.classList.add("d-none");
    btnSubmit.disabled = false; // Nonaktifkan tombol agar tidak bisa diklik dua kali
}

function clearFormData(formName) {
    $(formName)[0].reset(); // Reset form fields
    $(formName).find("select").val(null).trigger("change"); // Reset select fields and trigger change event

    // Reset hidden input fields except CSRF token
    $(formName)
        .find("input[type=hidden]")
        .not("[name='_token']") // Exclude the CSRF token field from being reset
        .val(""); // Reset hidden fields except CSRF token
}

function initMapRoute() {
    var poolId = $("#pool_id").val();
    var locationId = $("#location_id").val();
    var toll = $("#toll").val();
    if (poolId && locationId && toll) {
        $.ajax({
            url: "/map",
            type: "GET",
            dataType: "json",
            data: {
                pool_id: poolId,
                location_id: locationId,
            },
            success: function (response) {
                const origin = {
                    lat: parseFloat(response.truckLat),
                    lng: parseFloat(response.truckLng),
                };
                const destination = {
                    lat: parseFloat(response.destLat),
                    lng: parseFloat(response.destLng),
                };

                const map = new google.maps.Map(
                    document.getElementById("map"),
                    {
                        zoom: 7,
                        center: origin,
                    }
                );

                const directionsService = new google.maps.DirectionsService();
                const directionsRenderer = new google.maps.DirectionsRenderer({
                    map: map,
                });

                directionsService.route(
                    {
                        origin: origin,
                        destination: destination,
                        travelMode: google.maps.TravelMode.DRIVING,
                        avoidTolls: toll == 1 ? false : true,
                    },
                    (directionsResult, status) => {
                        if (status === google.maps.DirectionsStatus.OK) {
                            directionsRenderer.setDirections(directionsResult);

                            const totalMeters =
                                directionsResult.routes[0].legs[0].distance
                                    .value;
                            const totalKm = (totalMeters / 1000).toFixed(0);

                            $("#km").val(totalKm);
                        } else {
                            alert("Directions request failed: " + status);
                        }
                    }
                );
            },
            error: function () {
                alert("Gagal mengambil data koordinat.");
            },
        });
    }
}

function resetMapRoute() {
    // Clear map container
    if (document.getElementById("map")) {
        document.getElementById("map").innerHTML = "";
    }

    // Reset input fields related to map
    $("#km").val("");
    $("#km_toll").val("");
    $("#km_non_toll").val("");
    $("#km_table").val("");
}

function populateYearOptions() {
    const currentYear = new Date().getFullYear();
    const startYear = currentYear - 10; // 10 tahun ke belakang
    const endYear = currentYear + 1; // 1 tahun ke depan

    const yearSelect = $("#year");
    yearSelect.empty(); // Clear existing options
    yearSelect.append('<option value="">Pilih Tahun</option>');

    for (let year = endYear; year >= startYear; year--) {
        const isSelected = year === currentYear ? "selected" : "";
        yearSelect.append(
            `<option value="${year}" ${isSelected}>${year}</option>`
        );
    }
}

function formatNumber(input) {
    let value = input.value.replace(/[^\d]/g, ""); // Hanya angka
    if (value !== "") {
        value = new Intl.NumberFormat("id-ID").format(value); // Format ribuan
    }
    input.value = value; // Update nilai input
}

/**
 * Fungsi reusable untuk membuat Select2 dengan AJAX
 * @param {Object} config - Konfigurasi untuk Select2
 * @param {string} config.selector - Selector elemen select
 * @param {string} config.table - Nama tabel database
 * @param {string} config.valueField - Field untuk value (default: 'id')
 * @param {string} config.textField - Field untuk text (default: 'name')
 * @param {string} config.orderField - Field untuk ordering (default: 'name')
 * @param {string} config.searchField - Field untuk pencarian (default: 'name')
 * @param {string} config.placeholder - Placeholder text
 * @param {string} config.preselectedId - ID yang sudah dipilih sebelumnya
 * @param {function} config.formatText - Fungsi untuk format text (opsional)
 * @param {Object} config.additionalParams - Parameter tambahan untuk AJAX (opsional)
 */
function initSelect2Ajax(config) {
    const {
        selector,
        table,
        valueField = "id",
        textField = "name",
        orderField = "name",
        searchField = "name",
        placeholder = "-- Pilih --",
        preselectedId = null,
        formatText = null,
        additionalParams = {},
    } = config;

    // Initialize Select2
    $(selector).select2({
        allowClear: true,
        width: "100%",
        ajax: {
            url: "/option-ajax-where",
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    table: table,
                    value: valueField,
                    order: orderField,
                    whereName: searchField,
                    whereValue: params.term,
                    ...additionalParams,
                };
            },
            processResults: function (data) {
                return {
                    results: $.map(data, function (item) {
                        let text = item[textField];

                        // Apply custom text formatting if provided
                        if (formatText && typeof formatText === "function") {
                            text = formatText(text, item);
                        }

                        return {
                            id: item[valueField],
                            text: text,
                        };
                    }),
                };
            },
            cache: true,
        },
        placeholder: placeholder,
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

    // Preselect option if ID provided
    if (preselectedId) {
        $.ajax({
            url: "/option-ajax-where",
            dataType: "json",
            data: {
                table: table,
                value: valueField,
                order: orderField,
                whereName: valueField,
                whereValue: preselectedId,
                ...additionalParams,
            },
            success: function (data) {
                if (data && data.length > 0) {
                    const item = data[0];
                    let text = item[textField];

                    // Apply custom text formatting if provided
                    if (formatText && typeof formatText === "function") {
                        text = formatText(text, item);
                    }

                    const option = new Option(
                        text,
                        item[valueField],
                        true,
                        true
                    );
                    $(selector).append(option).trigger("change");
                }
            },
        });
    }
}
