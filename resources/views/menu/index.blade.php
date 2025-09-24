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
                        <form action="{{ route('administrator.menu.store') }}" method="post">
                            @csrf
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
                                    <table id="datagrid" class="easyui-datagrid"
                                        url="{{ url('/administrator/menu-datatable') }}" toolbar="#tb" pagination="true"
                                        rownumbers="true" fitColumns="true" singleSelect="true" striped="true"
                                        nowrap="false" loadMsg="Loading, Silahkan Tunggu..." method="get"
                                        style="width:100%;height:auto;">
                                        <thead>
                                            <tr>
                                                <th field="action" formatter="action" width="100" halign="center"
                                                    align="center">#</th>
                                                <th field="id" hidden></th>
                                                <th field="is_header" hidden></th>
                                                <th field="have_sub_menu" hidden></th>
                                                <th field="parent" hidden></th>
                                                <th field="sort" hidden></th>
                                                </th>
                                                <th field="menu_name" width="250" halign="center" sortable="true">Nama
                                                </th>
                                                <th field="url" width="250" halign="center" sortable="true">Url
                                                </th>
                                                <th field="icon" width="250" halign="center" sortable="true">Icon
                                                </th>
                                        </thead>
                                    </table>
                                </div>
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
                            <form action="{{ route('administrator.menu.store') }}" method="POST" id="formData">
                                @csrf
                                <div class="form-row">
                                    <div class="col-md-12 mb-3">
                                        <label for="">Nama</label>
                                        <input type="hidden" class="form-control form-control-sm" id="id"
                                            name="id">
                                        <input type="text" class="form-control form-control-sm" id="menu_name"
                                            name="menu_name">
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="">Menu Type</label>
                                        <select name="menu_type" id="menu_type" class="form-control form-control-sm select2"
                                            onchange="getParent(this.value)" data-placeholder="-- Pilih --">
                                            <option value=""></option>
                                            <option value="0">Main Menu</option>
                                            <option value="1">Sub Menu</option>
                                        </select>
                                    </div>
                                    <div class="col-md-12 mb-3 has-sub-menu">
                                        <div class="custom-control custom-switch custom-switch-icon custom-control-inline">
                                            <div class="custom-switch-inner">
                                                <label for="">Has Sub Menu?</label>
                                                <input type="hidden" id="is_header" name="is_header" value="1">
                                                <input type="hidden" id="parent" name="parent" value="false">
                                                <input type="checkbox" name="have_sub_menu"
                                                    class="custom-control-input bg-primary have_sub_menu"
                                                    id="customSwitch01">
                                                <label class="custom-control-label" for="customSwitch01"></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3 parent-menu">
                                        <label for="">Parent Menu</label>
                                        <input type="hidden" id="parentId" name="parentId" value="false">
                                        <select name="parent" id="parent_id" class="form-control form-control-sm"
                                            data-placeholder="-- Pilih --">
                                            <option value=""></option>
                                        </select>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="">Icon</label>
                                        <input type="text" class="form-control form-control-sm" id="icon"
                                            name="icon">
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="">Sort</label>
                                        <input type="number" class="form-control form-control-sm" id="sort"
                                            name="sort">
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="">Url</label>
                                        <textarea name="url" class="form-control" id="url" cols="5" rows="1"></textarea>
                                    </div>
                                    <button class="btn btn-primary" type="submit">Save</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <form id="delete-form" action="/administrator/menu/" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
    @stack('scripts')
@endsection
<script src="{{ asset('assets') }}/js/jquery-3.6.0.min.js"></script>
<script type="text/javascript" src="{{ asset('assets') }}/js/myJs/menu.js"></script>
