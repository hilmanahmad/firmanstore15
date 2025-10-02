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
