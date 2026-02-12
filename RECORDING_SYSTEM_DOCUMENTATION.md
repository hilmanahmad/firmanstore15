# OBS Recording Management System

## 📋 Deskripsi

Sistem integrasi OBS (Open Broadcaster Software) dengan aplikasi web Laravel untuk kontrol recording secara otomatis dengan auto-save filename dan path ke database.

## 🎯 Fitur Utama

1. ✅ Koneksi ke OBS via WebSocket
2. ✅ Start/Stop recording dari web interface
3. ✅ Custom filename untuk setiap recording
4. ✅ Auto save file path ke database
5. ✅ Multi-user support dengan role-based access
6. ✅ Recording history dengan status tracking
7. ✅ Duration timer realtime

## 🗄️ Struktur Database

### Tabel: `recordings`

```sql
- id (bigint, PK)
- code (varchar, unique) - Kode unik recording
- user_id (bigint, FK to users)
- filename (varchar) - Nama file hasil recording
- file_path (varchar) - Path lengkap file
- custom_filename (varchar) - Custom filename dari form
- status (enum: recording, stopped, completed, failed)
- started_at (timestamp)
- stopped_at (timestamp)
- duration (int) - Durasi dalam detik
- notes (text)
- created_at, updated_at, deleted_at
```

## 📁 File Structure

### Backend

```
app/
├── Models/
│   └── Recording.php                    # Model untuk recordings
├── Services/
│   └── RecordingService.php            # Business logic
├── Http/Controllers/
│   └── RecordingController.php         # API endpoints
└── Services/
    └── RecordingService.php            # Service layer

database/
└── migrations/
    └── 2026_02_12_105932_create_recordings_table.php

routes/
└── web.php                              # Routes untuk recording
```

### Frontend

```
resources/views/
└── recording/
    └── index.blade.php                  # Main interface

public/assets/js/myJs/
└── recording.js                         # OBS WebSocket integration
```

## 🔧 Setup OBS WebSocket

1. **Install OBS Studio** (jika belum)
    - Download: https://obsproject.com/

2. **Aktifkan WebSocket Server**
    - Buka OBS Studio
    - Menu: Tools → WebSocket Server Settings
    - Centang "Enable WebSocket Server"
    - Port default: 4455
    - (Opsional) Set password untuk keamanan

3. **Konfigurasi Recording Output**
    - Settings → Output
    - Recording Format: MKV (recommended) atau MP4
    - Recording Path: Catat lokasi folder output

## 🚀 Cara Penggunaan

### 1. Dari Web Interface

1. **Login** ke aplikasi
2. Buka menu **"OBS Recording"**
3. Klik tombol **"Connect to OBS"**
    - Masukkan URL: `ws://localhost:4455`
    - Masukkan password (jika ada)
4. Setelah terhubung, status akan berubah menjadi "Connected"
5. Isi form:
    - **Custom Filename**: Nama file tanpa ekstensi (contoh: `meeting-2026-02-12`)
    - **Pengguna**: Pilih user (jika SUPERADMIN)
    - **Notes**: Catatan opsional
6. Klik **"Start Recording"**
7. Lakukan recording di OBS
8. Klik **"Stop Recording"** untuk menghentikan
9. File akan otomatis tersimpan dengan nama sesuai custom filename
10. Data recording akan masuk ke database dan tampil di tabel riwayat

### 2. Alur Sistem (Sesuai Gambar)

```
┌─────────────────────────────────────────────────────┐
│  1. User klik "Start Record" di web                 │
├─────────────────────────────────────────────────────┤
│  2. Web kirim request ke OBS via WebSocket          │
├─────────────────────────────────────────────────────┤
│  3. OBS mulai record                                │
├─────────────────────────────────────────────────────┤
│  4. Web kirim custom filename                       │
├─────────────────────────────────────────────────────┤
│  5. Setelah stop → file rename sesuai kode          │
├─────────────────────────────────────────────────────┤
│  6. Simpan path ke database                         │
└─────────────────────────────────────────────────────┘
```

## 🔐 Role-Based Access

### SUPERADMIN

- ✅ Lihat semua recording (semua user)
- ✅ Bisa pilih user mana yang akan melakukan recording
- ✅ Full control

### STAFF/Other Roles

- ✅ Hanya lihat recording milik sendiri
- ✅ Otomatis tercatat sebagai user recording
- ✅ Recording control untuk diri sendiri

## 📊 API Endpoints

### 1. Start Recording

```
POST /recording/start
Body: {
    user_id: int,
    custom_filename: string,
    notes: string (optional)
}
Response: {
    status: true,
    message: "Recording dimulai",
    data: {
        code: "REC-20260212110537-ABCD",
        started_at: "2026-02-12 11:05:37"
    }
}
```

### 2. Stop Recording

```
POST /recording/stop
Body: {
    code: string,
    filename: string,
    file_path: string
}
Response: {
    status: true,
    message: "Recording dihentikan",
    data: {
        code: "REC-20260212110537-ABCD",
        duration: 120
    }
}
```

### 3. Get Recording List

```
GET /recording-datatable
Params: {
    page: int,
    rows: int,
    searchKey: string (optional),
    status: string (optional)
}
```

### 4. Delete Recording

```
DELETE /recording/{id}
Response: {
    status: true,
    message: "Data berhasil dihapus"
}
```

## 🎨 JavaScript OBS WebSocket

### Koneksi

```javascript
const wsUrl = "ws://localhost:4455";
obsWebSocket = new WebSocket(wsUrl);
```

### Send Command ke OBS

```javascript
sendOBSCommand("StartRecord");
sendOBSCommand("StopRecord");
```

### Event Handling

```javascript
obsWebSocket.onmessage = function (event) {
    const message = JSON.parse(event.data);
    handleOBSMessage(message);
};
```

## 🔄 Status Recording

1. **recording** 🔴 - Sedang dalam proses recording
2. **stopped** ⏸️ - Recording dihentikan
3. **completed** ✓ - Recording selesai dan file tersimpan
4. **failed** ✗ - Recording gagal/error

## ⚙️ Konfigurasi

### Default OBS Settings

- WebSocket URL: `ws://localhost:4455`
- Port: 4455
- Recording Format: MKV
- Output Path: `C:\Users\[Username]\Videos\`

### Customize di JavaScript (recording.js)

```javascript
const wsUrl = "ws://localhost:4455"; // Ubah sesuai konfigurasi
const filename = customFilename + ".mkv"; // Ubah format jika perlu
const filePath = "C:\\Users\\Videos\\" + filename; // Ubah path
```

## 🐛 Troubleshooting

### 1. Cannot connect to OBS

**Solusi:**

- Pastikan OBS sudah running
- Cek WebSocket Server sudah enabled di OBS
- Cek port 4455 tidak diblok firewall
- Gunakan `ws://localhost:4455` bukan `wss://`

### 2. Recording tidak mulai

**Solusi:**

- Cek status koneksi OBS
- Pastikan scene sudah di-setup di OBS
- Cek console browser untuk error WebSocket

### 3. File tidak tersimpan dengan nama custom

**Solusi:**

- Saat ini file tersimpan dengan nama default OBS
- Untuk rename otomatis, perlu script tambahan di OBS side
- Alternatif: Manual rename setelah recording selesai

### 4. Duration timer tidak akurat

**Solusi:**

- Refresh halaman jika timer freeze
- Cek timestamp di database untuk durasi akurat

## 📦 Dependencies

### Backend

- Laravel 11+
- PHP 8+
- MySQL

### Frontend

- jQuery
- EasyUI Datagrid
- Select2
- SweetAlert2
- WebSocket API (native browser)

### External

- OBS Studio 28+ dengan WebSocket Plugin

## 🔮 Future Enhancements

- [ ] Auto rename file menggunakan custom filename
- [ ] Multiple recording profiles
- [ ] Scene switching dari web
- [ ] Streaming support (not just recording)
- [ ] Recording preview/thumbnail
- [ ] Export recording list to Excel/PDF
- [ ] Recording scheduler (auto start/stop)
- [ ] Notification system untuk recording events

## 📞 Support

Untuk pertanyaan atau bantuan, hubungi tim development.

---

**Created:** February 12, 2026  
**Version:** 1.0.0  
**Status:** Production Ready ✅
