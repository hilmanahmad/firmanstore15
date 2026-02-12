@extends('layout.template')

<script type="text/javascript" src="{{ asset('assets') }}/js/myJs/recording.js"></script>
@section('container')
    <div id="content-page" class="content-page">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="iq-card">
                        <div class="iq-card-header d-flex justify-content-between">
                            <div class="iq-header-title">
                                <h4 class="card-title">🎥 {{ $title }}</h4>
                            </div>
                        </div>
                        <div class="iq-card-body">
                            <!-- OBS Connection Status -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="alert alert-info" role="alert">
                                        <div class="row align-items-center">
                                            <div class="col-md-8">
                                                <strong>Status Koneksi OBS:</strong>
                                                <span id="obs-status" class="badge badge-secondary">Disconnected</span>
                                                <button type="button" class="btn btn-sm btn-primary ml-3"
                                                    id="btn-connect-obs" onclick="connectToOBS()">
                                                    Connect to OBS
                                                </button>
                                            </div>
                                            <div class="col-md-4 text-right">
                                                <small class="text-muted">
                                                    <i class="fa fa-info-circle"></i>
                                                    @if (request()->getHost() === 'localhost' || request()->getHost() === '127.0.0.1')
                                                        Mode: 🔓 Direct WebSocket
                                                    @else
                                                        Mode: 🔒 Backend Proxy
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Recording Control Panel -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="card border-primary">
                                        <div class="card-header bg-primary text-white">
                                            <h5 class="mb-0">🎬 Recording Control</h5>
                                        </div>
                                        <div class="card-body">
                                            <form id="formRecording">
                                                @csrf
                                                <div class="form-group">
                                                    <label for="">Custom Filename <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="custom_filename"
                                                        name="custom_filename"
                                                        placeholder="Masukkan nama file (tanpa ekstensi)" required>
                                                    <small class="form-text text-muted">Contoh: meeting-2026-02-12</small>
                                                </div>

                                                @if ($isSuperAdmin)
                                                    <div class="form-group">
                                                        <label for="">Pengguna</label>
                                                        <select name="user_id" id="user_id"
                                                            class="form-control user_id"></select>
                                                    </div>
                                                @else
                                                    <input type="hidden" name="user_id" value="{{ $currentUserId }}">
                                                @endif

                                                <div class="form-group">
                                                    <label for="">Notes (Opsional)</label>
                                                    <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Catatan tambahan"></textarea>
                                                </div>

                                                <div class="form-group">
                                                    <button type="button" class="btn btn-danger btn-lg btn-block"
                                                        id="btn-start-recording" onclick="startRecording()" disabled>
                                                        <i class="fa fa-circle"></i> Start Recording
                                                    </button>
                                                    <button type="button" class="btn btn-warning btn-lg btn-block d-none"
                                                        id="btn-stop-recording" onclick="stopRecording()">
                                                        <i class="fa fa-stop"></i> Stop Recording
                                                    </button>
                                                </div>
                                            </form>

                                            <!-- Recording Info -->
                                            <div id="recording-info" class="d-none">
                                                <hr>
                                                <div class="alert alert-danger">
                                                    <h5>🔴 Recording Active</h5>
                                                    <p class="mb-1"><strong>Code:</strong> <span
                                                            id="current-code">-</span></p>
                                                    <p class="mb-1"><strong>Started:</strong> <span
                                                            id="current-started">-</span></p>
                                                    <p class="mb-0"><strong>Duration:</strong> <span
                                                            id="current-duration">00:00:00</span></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card border-info">
                                        <div class="card-header bg-info text-white">
                                            <h5 class="mb-0">ℹ️ Cara Penggunaan</h5>
                                        </div>
                                        <div class="card-body">
                                            <ol class="mb-0">
                                                <li>Buka OBS Studio di komputer Anda</li>
                                                <li>Aktifkan <strong>WebSocket Server</strong> di OBS:
                                                    <ul>
                                                        <li>Tools → WebSocket Server Settings</li>
                                                        <li>Enable WebSocket Server</li>
                                                        <li>Catat Port (default: 4455)</li>
                                                    </ul>
                                                </li>
                                                <li>Klik tombol <strong>"Connect to OBS"</strong></li>
                                                <li>Masukkan nama file custom</li>
                                                <li>Klik <strong>"Start Recording"</strong></li>
                                                <li>Setelah selesai, klik <strong>"Stop Recording"</strong></li>
                                                <li>File akan otomatis tersimpan dengan nama yang Anda tentukan</li>
                                                <li>Data akan otomatis masuk ke database</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Recording History Table -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h5 class="mb-3">📋 Riwayat Recording</h5>
                                    <div class="table-responsive">
                                        <div class="row justify-content-between mb-2">
                                            <div class="col-sm-12 col-md-4">
                                                <select id="filter-status" class="form-control" onchange="filterData()">
                                                    <option value="">-- Semua Status --</option>
                                                    <option value="recording">🔴 Recording</option>
                                                    <option value="stopped">⏸️ Stopped</option>
                                                    <option value="completed">✓ Completed</option>
                                                    <option value="failed">✗ Failed</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-12 col-md-4">
                                                <div class="input-group">
                                                    <input type="search" class="form-control" id="searchKey"
                                                        placeholder="Cari code, filename...">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-primary" onclick="searchData()">
                                                            <i class="fa fa-search"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <table id="datagrid" class="easyui-datagrid"
                                            url="{{ url('/recording-datatable') }}" toolbar="#tb" pagination="true"
                                            rownumbers="true" fitColumns="true" singleSelect="true" striped="true"
                                            nowrap="false" loadMsg="Loading, Silahkan Tunggu..." method="get"
                                            style="width:100%;height:auto;">
                                            <thead>
                                                <tr>
                                                    <th field="action" formatter="action" width="80" halign="center"
                                                        align="center">#</th>
                                                    <th field="id" hidden></th>
                                                    <th field="code" width="150" halign="center" sortable="true">
                                                        Code</th>
                                                    @if ($isSuperAdmin)
                                                        <th field="user_name" width="120" halign="center"
                                                            sortable="true">User</th>
                                                    @endif
                                                    <th field="custom_filename" width="150" halign="center"
                                                        sortable="true">Custom Filename</th>
                                                    <th field="filename" width="200" halign="center" sortable="true">
                                                        Filename</th>
                                                    <th field="status_badge" width="100" halign="center"
                                                        align="center">Status</th>
                                                    <th field="started_at" width="140" halign="center" align="center"
                                                        sortable="true">Started</th>
                                                    <th field="stopped_at" width="140" halign="center" align="center"
                                                        sortable="true">Stopped</th>
                                                    <th field="duration" width="100" halign="center" align="center"
                                                        sortable="true">Duration</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="delete-form" action="/recording/" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    @stack('scripts')
@endsection

@push('scripts')
    <script type="text/javascript">
        $(function() {
            @if ($isSuperAdmin)
                initUserSelect();
            @endif
        });

        function initUserSelect() {
            initSelect2Ajax({
                selector: ".user_id",
                table: "users",
                valueField: "id",
                textField: "name",
                orderField: "name",
                searchField: "name",
                placeholder: "-- Pilih User --",
                formatText: (text) => text.toUpperCase(),
            });
        }

        function searchData() {
            var searchKey = $("#searchKey").val();
            $("#datagrid").datagrid("load", {
                searchKey: searchKey
            });
        }

        function filterData() {
            var status = $("#filter-status").val();
            $("#datagrid").datagrid("load", {
                status: status
            });
        }
    </script>
@endpush
