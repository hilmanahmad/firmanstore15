// OBS WebSocket Integration - Supports both direct WS and HTTPS backend proxy
let obsWebSocket = null;
let isConnected = false;
let currentRecordingCode = null;
let durationTimer = null;
let startTime = null;
let obsPassword = null;
let messageId = 1;

// Auto-detect: use backend proxy if page is loaded over HTTPS
const useProxy = window.location.protocol === "https:";

// ===================== CONNECTION =====================

function connectToOBS() {
    if (isConnected) {
        disconnectOBS();
        return;
    }
    connectOBS();
}

function connectOBS() {
    const defaultUrl = "ws://localhost:4455";
    const defaultPassword = "1CDOUYOP4stj0BY5";

    let modeInfo = useProxy
        ? `<div class="alert alert-info p-2 mb-2" style="font-size:12px;">
               <strong>🔒 Mode HTTPS:</strong> Koneksi melalui Laravel backend proxy.<br>
               Server PHP akan menghubungkan ke OBS WebSocket.
           </div>`
        : `<div class="alert alert-success p-2 mb-2" style="font-size:12px;">
               <strong>🔓 Mode HTTP:</strong> Koneksi langsung ke OBS WebSocket dari browser.
           </div>`;

    Swal.fire({
        title: "Koneksi ke OBS",
        html: `
            ${modeInfo}
            <div class="form-group text-left">
                <label>WebSocket URL:</label>
                <input type="text" id="swal-obs-url" class="form-control" value="${defaultUrl}">
                <small class="form-text text-muted">Gunakan <code>ws://localhost:4455</code> jika OBS di PC yang sama</small>
            </div>
            <div class="form-group text-left mt-3">
                <label>Password:</label>
                <input type="password" id="swal-obs-password" class="form-control" value="${defaultPassword}">
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: "Connect",
        cancelButtonText: "Batal",
        preConfirm: () => {
            const url = document.getElementById("swal-obs-url").value;
            const password = document.getElementById("swal-obs-password").value;
            if (!url) {
                Swal.showValidationMessage("URL tidak boleh kosong");
                return false;
            }
            if (!password) {
                Swal.showValidationMessage("Password tidak boleh kosong");
                return false;
            }
            return { url, password };
        },
    }).then((result) => {
        if (result.isConfirmed) {
            if (useProxy) {
                connectViaProxy(result.value.url, result.value.password);
            } else {
                connectDirect(result.value.url, result.value.password);
            }
        }
    });
}

// ===================== PROXY CONNECTION (HTTPS) =====================

function connectViaProxy(url, password) {
    obsPassword = password;

    // Store for subsequent proxy commands
    sessionStorage.setItem("obs_url", url);
    sessionStorage.setItem("obs_password", password);

    Swal.fire({
        title: "Menghubungkan via Backend...",
        html: "Server PHP sedang connect ke OBS WebSocket...",
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading(),
    });

    $.ajax({
        url: "/recording/obs/connect",
        method: "POST",
        data: {
            _token: $('meta[name="csrf-token"]').attr("content"),
            url: url,
            password: password,
        },
        dataType: "json",
        timeout: 15000,
        success: function (response) {
            if (response.status) {
                isConnected = true;
                updateOBSStatus(true);
                Swal.fire({
                    icon: "success",
                    title: "Terhubung!",
                    text:
                        response.message ||
                        "Berhasil terhubung ke OBS via backend proxy",
                    timer: 2000,
                });
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Koneksi Gagal",
                    html: `<p>${response.message}</p>
                           <small class="text-muted">Pastikan OBS berjalan dan WebSocket Server aktif</small>`,
                });
            }
        },
        error: function (xhr) {
            let msg = "Terjadi kesalahan server";
            if (xhr.responseJSON && xhr.responseJSON.message) {
                msg = xhr.responseJSON.message;
            } else if (xhr.status === 0) {
                msg = "Tidak bisa menghubungi server";
            }
            Swal.fire("Error", msg, "error");
        },
    });
}

function sendOBSCommandViaProxy(command) {
    return new Promise((resolve, reject) => {
        let endpoint = "";
        if (command === "StartRecord") endpoint = "/recording/obs/start-record";
        else if (command === "StopRecord")
            endpoint = "/recording/obs/stop-record";
        else {
            reject("Unknown command: " + command);
            return;
        }

        $.ajax({
            url: endpoint,
            method: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr("content"),
                url: sessionStorage.getItem("obs_url") || "ws://localhost:4455",
                password: sessionStorage.getItem("obs_password") || obsPassword,
            },
            dataType: "json",
            timeout: 15000,
            success: function (response) {
                if (response.status) {
                    console.log("✓ OBS " + command + " via proxy OK");
                    resolve(response);
                } else {
                    console.error(
                        "✗ OBS " + command + " failed:",
                        response.message,
                    );
                    reject(response.message);
                }
            },
            error: function (xhr) {
                reject(xhr.responseJSON?.message || "Request failed");
            },
        });
    });
}

// ===================== DIRECT CONNECTION (HTTP) =====================

function connectDirect(url, password) {
    obsPassword = password;

    console.log("=== Starting Direct OBS Connection ===");
    console.log("URL:", url);

    if (typeof crypto === "undefined" || !crypto.subtle) {
        Swal.fire({
            icon: "error",
            title: "Crypto API Not Available",
            html: `<p>Browser Crypto API tidak tersedia di HTTP.</p>
                   <p>Gunakan <code>localhost</code> atau HTTPS mode.</p>`,
        });
        return;
    }

    Swal.fire({
        title: "Menghubungkan...",
        html: "Connecting to OBS WebSocket...",
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading(),
    });

    try {
        if (obsWebSocket) {
            obsWebSocket.close();
            obsWebSocket = null;
        }

        let connectionTimeout = setTimeout(() => {
            if (obsWebSocket && obsWebSocket.readyState !== WebSocket.OPEN) {
                obsWebSocket.close();
                Swal.fire({
                    icon: "error",
                    title: "Connection Timeout",
                    html: `<p>Tidak bisa connect ke OBS dalam 10 detik</p>
                           <ul class="text-left" style="font-size:12px;">
                               <li>Pastikan OBS Studio berjalan</li>
                               <li>Aktifkan WebSocket Server di OBS Settings</li>
                               <li>Cek IP/Port: <code>${url}</code></li>
                           </ul>`,
                });
            }
        }, 10000);

        obsWebSocket = new WebSocket(url);

        obsWebSocket.onopen = function () {
            clearTimeout(connectionTimeout);
            console.log("✓ WebSocket connected, waiting for Hello...");
        };

        obsWebSocket.onmessage = function (event) {
            const message = JSON.parse(event.data);
            console.log("OBS Message:", message);
            handleOBSMessage(message);
        };

        obsWebSocket.onerror = function (error) {
            clearTimeout(connectionTimeout);
            console.error("✗ WebSocket error:", error);
            if (Swal.isVisible() && Swal.isLoading()) {
                Swal.fire({
                    icon: "error",
                    title: "Koneksi Gagal",
                    html: `<p>Tidak dapat terhubung ke: <code>${url}</code></p>
                           <ol class="text-left" style="font-size:13px;">
                               <li>Pastikan OBS Studio sudah terbuka</li>
                               <li>Tools → WebSocket Server Settings → Enable</li>
                               <li>Cek port & IP address</li>
                           </ol>`,
                });
            }
            isConnected = false;
            updateOBSStatus(false);
        };

        obsWebSocket.onclose = function (event) {
            clearTimeout(connectionTimeout);
            console.log("⚠ WebSocket disconnected", event.code, event.reason);
            if (isConnected) {
                Swal.fire("Terputus", "Koneksi ke OBS terputus", "warning");
            }
            isConnected = false;
            updateOBSStatus(false);
        };
    } catch (error) {
        Swal.fire("Error", "Tidak dapat terhubung: " + error.message, "error");
    }
}

async function handleOBSMessage(message) {
    switch (message.op) {
        case 0: // Hello
            await handleHello(message.d);
            break;
        case 2: // Identified
            console.log("✓ OBS Identified - Auth successful!");
            isConnected = true;
            updateOBSStatus(true);
            Swal.fire({
                icon: "success",
                title: "Terhubung!",
                text: "Berhasil terhubung ke OBS WebSocket",
                timer: 2000,
            });
            break;
        case 5: // Event
            handleOBSEvent(message.d);
            break;
        case 7: // RequestResponse
            handleOBSResponse(message.d);
            break;
    }
}

async function handleHello(helloData) {
    const rpcVersion = helloData.rpcVersion || 1;
    let authString = null;

    if (helloData.authentication) {
        const { challenge, salt } = helloData.authentication;
        if (obsPassword) {
            try {
                authString = await generateAuthString(
                    obsPassword,
                    salt,
                    challenge,
                );
            } catch (error) {
                Swal.fire(
                    "Auth Error",
                    "Gagal membuat authentication: " + error.message,
                    "error",
                );
                return;
            }
        } else {
            Swal.fire("Error", "OBS memerlukan password", "error");
            obsWebSocket.close();
            return;
        }
    }

    const identifyMessage = {
        op: 1,
        d: {
            rpcVersion: rpcVersion,
            authentication: authString,
            eventSubscriptions: 33,
        },
    };

    obsWebSocket.send(JSON.stringify(identifyMessage));
    console.log("✓ Identify sent, waiting for response...");
}

async function generateAuthString(password, salt, challenge) {
    const secret = password + salt;
    const secretHash = await sha256(secret);
    const auth = secretHash + challenge;
    return await sha256(auth);
}

async function sha256(message) {
    const msgBuffer = new TextEncoder().encode(message);
    const hashBuffer = await crypto.subtle.digest("SHA-256", msgBuffer);
    const hashArray = Array.from(new Uint8Array(hashBuffer));
    return btoa(String.fromCharCode.apply(null, hashArray));
}

function sendOBSCommandDirect(command, data = {}) {
    if (!obsWebSocket || !isConnected) {
        console.error("OBS not connected");
        return;
    }

    const message = {
        op: 6,
        d: {
            requestType: command,
            requestId:
                Date.now().toString() + Math.random().toString(36).substr(2, 9),
            requestData: data,
        },
    };

    obsWebSocket.send(JSON.stringify(message));
    console.log("Sent OBS command:", command);
}

// ===================== UNIFIED COMMANDS =====================

function sendOBSCommand(command, data = {}) {
    if (useProxy) {
        return sendOBSCommandViaProxy(command);
    } else {
        sendOBSCommandDirect(command, data);
        return Promise.resolve();
    }
}

function disconnectOBS() {
    if (useProxy) {
        $.ajax({
            url: "/recording/obs/disconnect",
            method: "POST",
            data: { _token: $('meta[name="csrf-token"]').attr("content") },
            dataType: "json",
        });
    } else if (obsWebSocket) {
        obsWebSocket.close();
    }
    isConnected = false;
    updateOBSStatus(false);
}

// ===================== UI & STATUS =====================

function updateOBSStatus(connected) {
    const statusBadge = $("#obs-status");
    const btnConnect = $("#btn-connect-obs");
    const btnStart = $("#btn-start-recording");

    if (connected) {
        statusBadge
            .removeClass("badge-secondary badge-danger")
            .addClass("badge-success")
            .text("Connected" + (useProxy ? " (via Proxy)" : ""));
        btnConnect
            .text("Disconnect")
            .removeClass("btn-primary")
            .addClass("btn-danger");
        btnStart.prop("disabled", false);
    } else {
        statusBadge
            .removeClass("badge-success")
            .addClass("badge-secondary")
            .text("Disconnected");
        btnConnect
            .text("Connect to OBS")
            .removeClass("btn-danger")
            .addClass("btn-primary");
        btnStart.prop("disabled", true);
    }
}

function handleOBSEvent(event) {
    console.log("OBS Event:", event);
    if (event.eventType === "RecordStateChanged") {
        if (event.eventData.outputState === "OBS_WEBSOCKET_OUTPUT_STARTED") {
            console.log("Recording started");
        } else if (
            event.eventData.outputState === "OBS_WEBSOCKET_OUTPUT_STOPPED"
        ) {
            console.log("Recording stopped");
        }
    }
}

function handleOBSResponse(response) {
    console.log("OBS Response:", response);
}

// ===================== RECORDING =====================

function startRecording() {
    if (!isConnected) {
        Swal.fire("Error", "Belum terhubung ke OBS", "error");
        return;
    }

    const customFilename = $("#custom_filename").val();
    if (!customFilename) {
        Swal.fire("Error", "Custom filename harus diisi", "error");
        return;
    }

    Swal.fire({
        title: "Memulai Recording...",
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading(),
    });

    // 1. Save recording entry to database
    $.ajax({
        url: "/recording/start",
        method: "POST",
        data: $("#formRecording").serialize(),
        dataType: "json",
        success: function (response) {
            if (response.status) {
                currentRecordingCode = response.data.code;
                startTime = new Date(response.data.started_at);

                // 2. Tell OBS to start recording
                sendOBSCommand("StartRecord")
                    .then(() => {
                        // Update UI
                        $("#btn-start-recording").addClass("d-none");
                        $("#btn-stop-recording").removeClass("d-none");
                        $("#recording-info").removeClass("d-none");
                        $("#current-code").text(currentRecordingCode);
                        $("#current-started").text(
                            new Date(response.data.started_at).toLocaleString(
                                "id-ID",
                            ),
                        );
                        startDurationTimer();

                        Swal.fire({
                            icon: "success",
                            title: "Recording Dimulai!",
                            text: "Code: " + currentRecordingCode,
                            timer: 2000,
                        });

                        $("#custom_filename").prop("disabled", true);
                        $("#user_id").prop("disabled", true);
                        $("#notes").prop("disabled", true);
                    })
                    .catch((err) => {
                        Swal.fire(
                            "OBS Error",
                            "Gagal start recording di OBS: " + err,
                            "error",
                        );
                    });
            } else {
                Swal.fire("Error", response.message, "error");
            }
        },
        error: function (xhr) {
            Swal.fire(
                "Error",
                xhr.responseJSON?.message || "Terjadi kesalahan",
                "error",
            );
        },
    });
}

function stopRecording() {
    if (!currentRecordingCode) {
        Swal.fire("Error", "Tidak ada recording aktif", "error");
        return;
    }

    Swal.fire({
        title: "Menghentikan Recording...",
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading(),
    });

    // Tell OBS to stop recording
    sendOBSCommand("StopRecord")
        .then(() => {
            setTimeout(() => completeRecording(), 1500);
        })
        .catch((err) => {
            console.error("OBS StopRecord error:", err);
            setTimeout(() => completeRecording(), 500);
        });
}

function completeRecording() {
    const customFilename = $("#custom_filename").val();
    const filename = customFilename + ".mkv";
    const filePath = "C:\\Users\\Videos\\" + filename;

    $.ajax({
        url: "/recording/stop",
        method: "POST",
        data: {
            _token: $('meta[name="csrf-token"]').attr("content"),
            code: currentRecordingCode,
            filename: filename,
            file_path: filePath,
        },
        dataType: "json",
        success: function (response) {
            if (response.status) {
                if (durationTimer) {
                    clearInterval(durationTimer);
                    durationTimer = null;
                }

                $("#btn-start-recording").removeClass("d-none");
                $("#btn-stop-recording").addClass("d-none");
                $("#recording-info").addClass("d-none");
                $("#custom_filename").prop("disabled", false).val("");
                $("#user_id").prop("disabled", false);
                $("#notes").prop("disabled", false).val("");

                currentRecordingCode = null;

                Swal.fire({
                    icon: "success",
                    title: "Recording Selesai!",
                    html: `<p>File: ${filename}</p><p>Duration: ${response.data.duration}s</p>`,
                });

                $("#datagrid").datagrid("reload");
            } else {
                Swal.fire("Error", response.message, "error");
            }
        },
        error: function (xhr) {
            Swal.fire(
                "Error",
                xhr.responseJSON?.message || "Terjadi kesalahan",
                "error",
            );
        },
    });
}

function startDurationTimer() {
    durationTimer = setInterval(() => {
        if (startTime) {
            const now = new Date();
            const diff = Math.floor((now - startTime) / 1000);
            const hours = Math.floor(diff / 3600)
                .toString()
                .padStart(2, "0");
            const minutes = Math.floor((diff % 3600) / 60)
                .toString()
                .padStart(2, "0");
            const seconds = (diff % 60).toString().padStart(2, "0");
            $("#current-duration").text(`${hours}:${minutes}:${seconds}`);
        }
    }, 1000);
}

// ===================== DATATABLE ACTIONS =====================

function action(value, row, index) {
    let buttons = "";

    if (row.status === "completed" && row.file_path !== "-") {
        buttons += `
            <a class="action-buttons btn btn-sm btn-info" href="javascript:void(0)"
               onclick="viewRecording('${row.file_path}')"
               style="display:inline-flex;align-items:center;justify-content:center;"
               title="View Path">
                <i class="fa fa-eye" style="margin-right:0px;"></i>
            </a>`;
    }

    buttons += `
        <a class="action-buttons btn btn-sm btn-danger" href="javascript:void(0)"
           onclick="deleteConfirmation('delete-form', '${row.id}')"
           style="display:inline-flex;align-items:center;justify-content:center;"
           title="Hapus">
            <i class="fa fa-trash" style="margin-right:0px;"></i>
        </a>`;

    return buttons;
}

function viewRecording(filePath) {
    Swal.fire({
        title: "Recording File Path",
        html: `<div class="text-left"><p><strong>Path:</strong></p><code>${filePath}</code></div>`,
        icon: "info",
    });
}

// ===================== EVENT LISTENERS =====================

$(document).ready(function () {
    console.log("Recording.js loaded. HTTPS proxy mode:", useProxy);

    $("#btn-start-recording").on("click", function () {
        startRecording();
    });

    $("#btn-stop-recording").on("click", function () {
        stopRecording();
    });
});

function user() {
    const userId = $("#userId").val();
    initSelect2Ajax({
        selector: ".user_id",
        table: "users",
        valueField: "id",
        textField: "name",
        orderField: "name",
        searchField: "name",
        placeholder: "-- Pilih Pembeli --",
        preselectedId: userId,
        formatText: (text) => text.toUpperCase(),
    });
}
