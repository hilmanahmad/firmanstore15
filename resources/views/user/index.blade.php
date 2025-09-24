@extends('layout.template')
<script type="text/javascript" src="{{ asset('assets') }}/js/myJs/user.js"></script>
@section('container')
    <div id="content-page" class="content-page">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-7">
                    <div class="iq-card">
                        <div class="iq-card-header d-flex justify-content-between">
                            <div class="iq-header-title">
                                <h4 class="card-title">User</h4>
                            </div>
                        </div>
                        <form action="{{ route('administrator.user.store') }}" method="post">
                            @csrf
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
                                <table id="datagrid" class="easyui-datagrid"
                                    url="{{ url('/administrator/user-datatable') }}" toolbar="#tb" pagination="true"
                                    rownumbers="true" fitColumns="true" singleSelect="true" striped="true" nowrap="true"
                                    loadMsg="Loading, Silahkan Tunggu..." method="get" style="width:100%;height:auto;">
                                    <thead>
                                        <tr>
                                            <th field="action" formatter="action" width="150" halign="center"
                                                align="center">#</th>
                                            <th field="uuid" hidden></th>
                                            <th field="role_code" hidden></th>
                                            <th field="name" width="250" halign="center" sortable="true">Name
                                            </th>
                                            <th field="username" width="250" halign="center" sortable="true">Username
                                            </th>
                                            <th field="role" width="250">Role</th>
                                            <th field="group_code" width="100">SBU</th>
                                    </thead>
                                </table>
                            </div>
                        </form>
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
                            <form action="{{ route('administrator.user.store') }}" method="POST" id="formData">
                                @csrf
                                <div class="form-row">
                                    <div class="col-md-12 mb-3">
                                        <label for="">Name</label>
                                        <input type="hidden" class="form-control form-control-sm" id="uuid"
                                            name="uuid">
                                        <input type="hidden" class="form-control form-control-sm" id="role"
                                            name="role">
                                        <input type="hidden" class="form-control form-control-sm" id="group_code"
                                            name="group_code">
                                        <input type="text" class="form-control form-control-sm" id="name"
                                            name="name">
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="">Username</label>
                                        <input type="text" class="form-control form-control-sm" id="username"
                                            name="username">
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="role_code">Role Access</label>
                                        <select class="form-control form-control-sm select2" id="role_code" name="role_code"
                                            data-placeholder="Select Role" onchange="sbu(this.value)">
                                            <option value=""></option>
                                        </select>
                                    </div>
                                    <div id="sbu" class="col-md-12 mb-3" style="display: none">
                                        <label for="sbu_code">SBU</label>
                                        <select class="form-control form-control-sm select2" id="sbu_code"
                                            name="sbu_code" data-placeholder="Select SBU">
                                            <option value=""></option>
                                        </select>
                                    </div>
                                    <button class="btn btn-primary" type="submit">Save</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <form id="delete-form" action="/administrator/user/" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
    @stack('scripts')
@endsection
<script src="{{ asset('assets') }}/js/jquery-3.6.0.min.js"></script>
<script type="text/javascript">
    $(function() {
        role();
        $("form").submit(function() {
            $.ajax({
                url: $(this).attr("action"),
                data: $(this).serialize(),
                type: $(this).attr("method"),
                dataType: 'json',
                success: function(data) {
                    if (data.status == 'failed') {
                        error(data.message);
                    } else {
                        success(data.message);
                        $("#datagrid").datagrid("reload");
                        clearForm("formData")
                        $("#validation").addClass("none");
                    }
                }
            })
            return false;
        });
    });
</script>
