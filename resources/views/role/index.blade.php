@extends('layout.template')
<script type="text/javascript" src="{{ asset('assets') }}/js/myJs/role.js"></script>
@section('container')
    <div id="content-page" class="content-page">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-7">
                    <div class="iq-card">
                        <div class="iq-card-header d-flex justify-content-between">
                            <div class="iq-header-title">
                                <h4 class="card-title">Role</h4>
                            </div>
                        </div>
                        <div class="iq-card-body">
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
                            <table id="datagrid" class="easyui-datagrid" url="{{ url('/role-datatable') }}" toolbar="#tb"
                                pagination="true" rownumbers="true" fitColumns="true" singleSelect="true" striped="true"
                                nowrap="true" loadMsg="Loading, Silahkan Tunggu..." method="get"
                                style="width:100%;height:auto;">
                                <thead>
                                    <tr>
                                        <th field="action" formatter="action" width="150" halign="center" align="center">
                                            #</th>
                                        <th field="id" hidden></th>
                                        <th field="code" width="100" halign="center" sortable="true">Code</th>
                                        <th field="name" width="200" halign="center" sortable="true">Name</th>
                                        <th field="description" width="250" halign="center">Description</th>
                                        <th field="is_active" width="100" halign="center">Status</th>
                                    </tr>
                                </thead>
                            </table>
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
                            <form action="{{ url('role') }}" method="POST" id="formData">
                                @csrf
                                <div class="form-row">
                                    <div class="col-md-12 mb-3">
                                        <label for="code">Code <span class="text-danger">*</span></label>
                                        <input type="hidden" class="form-control form-control-sm" id="id"
                                            name="id">
                                        <input type="text" class="form-control form-control-sm" id="code"
                                            name="code" required maxlength="50" style="text-transform: uppercase;">
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="name">Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-sm" id="name"
                                            name="name" required maxlength="100">
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="description">Description</label>
                                        <textarea class="form-control form-control-sm" id="description" name="description" rows="3"></textarea>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="is_active"
                                                name="is_active" checked>
                                            <label class="custom-control-label" for="is_active">Active</label>
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
    <form id="delete-form" action="/role/" method="POST" style="display: none;">
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
        let html = '<a href="javascript:void(0)" class="btn btn-sm btn-warning" onclick="edit(' + row.id +
            ')"><i class="fa fa-edit"></i></a> ';
        html += '<a href="javascript:void(0)" class="btn btn-sm btn-danger" onclick="hapus(' + row.id +
            ')"><i class="fa fa-trash"></i></a>';
        return html;
    }

    function edit(id) {
        $.ajax({
            url: '/role/' + id,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                $('#id').val(data.id);
                $('#code').val(data.code);
                $('#name').val(data.name);
                $('#description').val(data.description);
                $('#is_active').prop('checked', data.is_active);
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
                    url: '/role/' + id,
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
</script>
