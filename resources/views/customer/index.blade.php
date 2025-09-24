@extends('layout.template')
<script type="text/javascript" src="{{ asset('assets') }}/js/myJs/customer.js"></script>
@section('container')
    <div id="content-page" class="content-page">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-5">
                    <div class="iq-card">
                        <div class="iq-card-header d-flex justify-content-between">
                            <div class="iq-header-title">
                                <h4 class="card-title">Form {{ $title }}</h4>
                            </div>
                        </div>
                        <div class="iq-card-body">
                            <form action="{{ route('customer.store') }}" method="POST" id="formData">
                                @csrf
                                <div class="form-row">
                                    <div class="col-md-12 mb-3">
                                        <label for="">Nama</label>
                                        <input type="hidden" class="form-control form-control-sm" id="id"
                                            name="id">
                                        <input type="text" class="form-control form-control-sm" id="name"
                                            name="name" required>
                                    </div>
                                    <button id="simpan" class="btn btn-primary" type="submit"><span
                                            id="btn-text">Save</span>
                                        <span id="btn-spinner" class="spinner-border spinner-border-sm d-none"
                                            role="status" aria-hidden="true"></span></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
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
                                <table id="datagrid" class="easyui-datagrid" url="{{ url('/customer-datatable') }}"
                                    toolbar="#tb" pagination="true" rownumbers="true" fitColumns="true" singleSelect="true"
                                    striped="true" nowrap="false" loadMsg="Loading, Silahkan Tunggu..." method="get"
                                    style="width:100%;height:auto;">
                                    <thead>
                                        <tr>
                                            <th field="action" formatter="action" width="100" halign="center"
                                                align="center">#</th>
                                            <th field="id" hidden></th>
                                            <th field="name" width="250" halign="center" sortable="true">Nama
                                            </th>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <form id="delete-form" action="/customer/" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
    @stack('scripts')
@endsection
<script src="{{ asset('assets') }}/js/jquery-3.6.0.min.js"></script>
<script type="text/javascript">
    $(function() {
        $("form").submit(function(event) {
            event.preventDefault(); // Mencegah reload halaman
            showLoadingButton(event);
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
                        clearFormData(event.target)
                        $("#validation").addClass("none");
                    }
                    hideLoadingButton(event);
                }
            })
            return false;
        });
    });
</script>
