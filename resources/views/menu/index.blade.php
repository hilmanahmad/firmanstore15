@extends('layout.template')
@section('container')
    <div id="content-page" class="content-page">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-7">
                    <div class="iq-card">
                        <div class="iq-card-header d-flex justify-content-between">
                            <div class="iq-header-title">
                                <h4 class="card-title">{{ $title }}</h4>
                            </div>
                        </div>
                        <div class="iq-card-body">
                            <div class="table-responsive">
                                <div class="row justify-content-between">
                                    <div class="col-sm-12 col-md-6 mb-2">
                                        <div class="user-list-files d-flex">
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-6 mb-2">
                                        <div class="user-list-files d-flex float-right">
                                            <input type="search" class="form-control form-control-sm" id="searchKey"
                                                placeholder="Cari">
                                            <a class="iq-bg-primary" href="javascript:void();" onclick="searchData()">
                                                <i class="fa fa-search"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <table id="datagrid" class="easyui-datagrid" url="{{ url('/menu-datatable') }}"
                                    toolbar="#tb" pagination="true" rownumbers="true" fitColumns="true" singleSelect="true"
                                    striped="true" nowrap="false" loadMsg="Loading, Silahkan Tunggu..." method="get"
                                    style="width:100%;height:auto;">
                                    <thead>
                                        <tr>
                                            <th field="action" formatter="action" width="100" halign="center"
                                                align="center">#</th>
                                            <th field="id" hidden></th>
                                            <th field="is_header" hidden></th>
                                            <th field="have_sub_menu" hidden></th>
                                            <th field="parent" hidden></th>
                                            <th field="name" width="200" halign="center" sortable="true">Nama</th>
                                            <th field="url" width="150" halign="center" sortable="true">Url</th>
                                            <th field="icon" width="100" halign="center" sortable="true">Icon</th>
                                            <th field="sort" width="60" halign="center">Sort</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="iq-card">
                        <div class="iq-card-header d-flex justify-content-between">
                            <div class="iq-header-title">
                                <h4 class="card-title">Form {{ $title }}</h4>
                            </div>
                        </div>
                        <div class="iq-card-body">
                            <form action="{{ url('menu') }}" method="POST" id="formData">
                                @csrf
                                <div class="form-row">
                                    <div class="col-md-12 mb-3">
                                        <label for="name">Nama <span class="text-danger">*</span></label>
                                        <input type="hidden" class="form-control form-control-sm" id="id"
                                            name="id">
                                        <input type="text" class="form-control form-control-sm" id="name"
                                            name="name" required>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="url">URL <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-sm" id="url"
                                            name="url" required>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="icon">Icon</label>
                                        <input type="text" class="form-control form-control-sm" id="icon"
                                            name="icon" placeholder="ri-home-line">
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="sort">Sort</label>
                                        <input type="number" class="form-control form-control-sm" id="sort"
                                            name="sort" value="0">
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="parent">Parent Menu</label>
                                        <select class="form-control form-control-sm" id="parent" name="parent">
                                            <option value="">-- Pilih Parent (kosongkan jika header) --</option>
                                            @foreach ($parentMenus as $menu)
                                                <option value="{{ $menu->id }}">{{ $menu->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="is_header"
                                                name="is_header">
                                            <label class="custom-control-label" for="is_header">Is Header</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="have_sub_menu"
                                                name="have_sub_menu">
                                            <label class="custom-control-label" for="have_sub_menu">Have Sub Menu</label>
                                        </div>
                                    </div>
                                </div>
                                <button class="btn btn-primary" type="submit">Save</button>
                                <button class="btn btn-secondary" type="button"
                                    onclick="clearForm('formData')">Clear</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <form id="delete-form" action="/menu/" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
    @stack('scripts')
@endsection
<script src="{{ asset('assets') }}/js/jquery-3.6.0.min.js"></script>
<script type="text/javascript">
    $(function() {
        $("form#formData").submit(function(e) {
            e.preventDefault();
            $.ajax({
                url: $(this).attr("action"),
                data: $(this).serialize(),
                type: $(this).attr("method"),
                dataType: 'json',
                success: function(data) {
                    if (data.status == false) {
                        error(data.message);
                    } else {
                        success(data.message);
                        $("#datagrid").datagrid("reload");
                        clearForm("formData");
                    }
                },
                error: function(xhr) {
                    error(xhr.responseJSON?.message || 'Terjadi kesalahan');
                }
            });
            return false;
        });
    });

    function action(value, row, index) {
        let html = '<a href="javascript:void(0)" class="btn btn-sm btn-warning" onclick="edit(\'' + row.id +
            '\')"><i class="fa fa-edit"></i></a> ';
        html += '<a href="javascript:void(0)" class="btn btn-sm btn-danger" onclick="hapus(\'' + row.id +
            '\')"><i class="fa fa-trash"></i></a>';
        return html;
    }

    function edit(id) {
        $.ajax({
            url: '/menu/' + id,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                $('#id').val(data.id);
                $('#name').val(data.name);
                $('#url').val(data.url);
                $('#icon').val(data.icon);
                $('#sort').val(data.sort);
                $('#parent').val(data.parent !== 'false' ? data.parent : '');
                $('#is_header').prop('checked', data.is_header === 'true');
                $('#have_sub_menu').prop('checked', data.have_sub_menu === 'true');
            }
        });
    }

    function hapus(id) {
        Swal.fire({
            title: 'Apakah anda yakin?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/menu/' + id,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(data) {
                        if (data.status) {
                            success(data.message);
                            $("#datagrid").datagrid("reload");
                        } else {
                            error(data.message);
                        }
                    }
                });
            }
        });
    }

    function searchData() {
        var searchKey = $('#searchKey').val();
        $('#datagrid').datagrid('load', {
            searchKey: searchKey
        });
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
</script>
