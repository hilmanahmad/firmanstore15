@extends('layout.template')
@section('container')
    <div id="content-page" class="content-page">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="iq-card">
                        <div class="iq-card-header d-flex justify-content-between">
                            <div class="iq-header-title">
                                <h4 class="card-title">{{ $title }}</h4>
                            </div>
                        </div>
                        <div class="iq-card-body">
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <label for="role_code">Pilih Role <span class="text-danger">*</span></label>
                                    <select class="form-control form-control-sm select2" id="role_code" name="role_code"
                                        data-placeholder="Pilih Role" onchange="loadMenuAccess(this.value)">
                                        <option value="">-- Pilih Role --</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->code }}">{{ $role->name }} ({{ $role->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button class="btn btn-primary btn-sm" onclick="saveMenuAccess()" id="btnSave"
                                            disabled>
                                            <i class="fa fa-save"></i> Simpan
                                        </button>
                                        <button class="btn btn-success btn-sm" onclick="checkAll()" id="btnCheckAll"
                                            disabled>
                                            <i class="fa fa-check-square"></i> Centang Semua
                                        </button>
                                        <button class="btn btn-warning btn-sm" onclick="uncheckAll()" id="btnUncheckAll"
                                            disabled>
                                            <i class="fa fa-square"></i> Hapus Centang
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="menuAccessTable">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="35%">Menu</th>
                                            <th width="15%" class="text-center">View</th>
                                            <th width="15%" class="text-center">Create</th>
                                            <th width="15%" class="text-center">Edit</th>
                                            <th width="15%" class="text-center">Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody id="menuAccessBody">
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">
                                                Silahkan pilih role terlebih dahulu
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @stack('scripts')
@endsection

<script src="{{ asset('assets') }}/js/jquery-3.6.0.min.js"></script>
<script type="text/javascript">
    let menuAccessData = [];

    function loadMenuAccess(roleCode) {
        if (!roleCode) {
            $('#menuAccessBody').html(
                '<tr><td colspan="6" class="text-center text-muted">Silahkan pilih role terlebih dahulu</td></tr>');
            $('#btnSave, #btnCheckAll, #btnUncheckAll').prop('disabled', true);
            return;
        }

        $.ajax({
            url: '{{ url('role-menu-access/get-by-role') }}',
            type: 'GET',
            data: {
                role_code: roleCode
            },
            dataType: 'json',
            beforeSend: function() {
                $('#menuAccessBody').html(
                    '<tr><td colspan="6" class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</td></tr>'
                    );
            },
            success: function(response) {
                if (response.status) {
                    menuAccessData = response.data;
                    renderMenuTable(response.data);
                    $('#btnSave, #btnCheckAll, #btnUncheckAll').prop('disabled', false);
                } else {
                    error(response.message);
                }
            },
            error: function(xhr) {
                error('Gagal memuat data menu access');
            }
        });
    }

    function renderMenuTable(data) {
        let html = '';
        let no = 1;

        data.forEach(function(item, index) {
            let indent = item.is_header === 'true' ? '' : '&nbsp;&nbsp;&nbsp;&nbsp;├─ ';
            let fontWeight = item.is_header === 'true' ? 'font-weight: bold;' : '';

            html += `<tr data-menu-id="${item.menu_id}">
                <td>${no++}</td>
                <td style="${fontWeight}">${indent}${item.menu_name}</td>
                <td class="text-center">
                    <input type="checkbox" class="form-check-input access-checkbox" 
                        data-menu-id="${item.menu_id}" data-permission="can_view"
                        ${item.can_view ? 'checked' : ''}>
                </td>
                <td class="text-center">
                    <input type="checkbox" class="form-check-input access-checkbox" 
                        data-menu-id="${item.menu_id}" data-permission="can_create"
                        ${item.can_create ? 'checked' : ''}>
                </td>
                <td class="text-center">
                    <input type="checkbox" class="form-check-input access-checkbox" 
                        data-menu-id="${item.menu_id}" data-permission="can_edit"
                        ${item.can_edit ? 'checked' : ''}>
                </td>
                <td class="text-center">
                    <input type="checkbox" class="form-check-input access-checkbox" 
                        data-menu-id="${item.menu_id}" data-permission="can_delete"
                        ${item.can_delete ? 'checked' : ''}>
                </td>
            </tr>`;
        });

        $('#menuAccessBody').html(html);
    }

    function saveMenuAccess() {
        let roleCode = $('#role_code').val();
        if (!roleCode) {
            error('Pilih role terlebih dahulu');
            return;
        }

        let menuAccess = [];
        $('#menuAccessBody tr').each(function() {
            let menuId = $(this).data('menu-id');
            if (menuId) {
                menuAccess.push({
                    menu_id: menuId,
                    can_view: $(this).find('[data-permission="can_view"]').is(':checked'),
                    can_create: $(this).find('[data-permission="can_create"]').is(':checked'),
                    can_edit: $(this).find('[data-permission="can_edit"]').is(':checked'),
                    can_delete: $(this).find('[data-permission="can_delete"]').is(':checked')
                });
            }
        });

        $.ajax({
            url: '{{ url('role-menu-access') }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                role_code: roleCode,
                menu_access: menuAccess
            },
            dataType: 'json',
            beforeSend: function() {
                $('#btnSave').prop('disabled', true).html(
                    '<i class="fa fa-spinner fa-spin"></i> Menyimpan...');
            },
            success: function(response) {
                $('#btnSave').prop('disabled', false).html('<i class="fa fa-save"></i> Simpan');
                if (response.status) {
                    success(response.message);
                } else {
                    error(response.message);
                }
            },
            error: function(xhr) {
                $('#btnSave').prop('disabled', false).html('<i class="fa fa-save"></i> Simpan');
                error('Gagal menyimpan menu access');
            }
        });
    }

    function checkAll() {
        $('.access-checkbox').prop('checked', true);
    }

    function uncheckAll() {
        $('.access-checkbox').prop('checked', false);
    }

    $(document).ready(function() {
        if (typeof $.fn.select2 !== 'undefined') {
            $('#role_code').select2({
                placeholder: 'Pilih Role',
                allowClear: true
            });
        }
    });
</script>
