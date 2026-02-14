# SSH Tunnel Setup untuk OBS WebSocket

## Arsitektur

```
[Browser] → [Server Production] → [SSH Tunnel] → [Komputer Lokal OBS:4455]
              firmanstore15.devhilman.tech      192.168.125.43
```

## Setup Reverse SSH Tunnel

### 1. Dari Komputer Lokal (Windows - dimana OBS berjalan)

Buka PowerShell dan jalankan:

```powershell
# Buat SSH reverse tunnel ke server production
ssh -R 4455:localhost:4455 root@firmanstore15.devhilman.tech -N

# Atau jika ingin background:
ssh -R 4455:localhost:4455 root@firmanstore15.devhilman.tech -N -f
```

**Penjelasan:**

- `-R 4455:localhost:4455` = Port 4455 di server production akan di-forward ke localhost:4455 (OBS lokal)
- `-N` = Tidak execute command remote (hanya tunnel)
- `-f` = Background process (opsional)

**Keep Alive:**
Untuk koneksi tidak putus, tambahkan parameter:

```powershell
ssh -R 4455:localhost:4455 root@firmanstore15.devhilman.tech -N -o ServerAliveInterval=60 -o ServerAliveCountMax=3
```

### 2. Di Server Production

Edit `/etc/ssh/sshd_config`:

```bash
sudo nano /etc/ssh/sshd_config

# Tambahkan atau pastikan line ini ada:
GatewayPorts yes
ClientAliveInterval 60
ClientAliveCountMax 3
```

Restart SSH service:

```bash
sudo systemctl restart sshd
```

### 3. Ubah .env di Server Production

```bash
# Edit .env
nano /var/www/firmanstore15/.env

# Ubah menjadi:
OBS_WEBSOCKET_URL=ws://localhost:4455
OBS_WEBSOCKET_PASSWORD=1CDOUYOP4stj0BY5
```

Clear cache:

```bash
cd /var/www/firmanstore15
php artisan config:clear
```

### 4. Test Koneksi

Dari server production:

```bash
# Test apakah port 4455 sudah listening
ss -tlnp | grep 4455

# Test koneksi WebSocket
curl -i -N -H "Connection: Upgrade" -H "Upgrade: websocket" http://localhost:4455
```

Seharusnya ada output:

```
tcp   LISTEN   0   128   127.0.0.1:4455   0.0.0.0:*
```

### 5. Akses dari Browser

Buka: `https://firmanstore15.devhilman.tech/recording`

- Mode: **Backend Proxy** (karena server akan connect ke localhost:4455 via tunnel)
- Click: **Connect OBS**

## Auto-Start Tunnel (Windows)

Buat file: `C:\scripts\obs-tunnel.bat`

```batch
@echo off
:retry
ssh -R 4455:localhost:4455 root@firmanstore15.devhilman.tech -N -o ServerAliveInterval=60 -o ServerAliveCountMax=3
timeout /t 5
goto retry
```

Jalankan saat startup:

1. Win+R → `shell:startup`
2. Buat shortcut ke `C:\scripts\obs-tunnel.bat`

## Auto-Start Tunnel (Linux/Mac)

Buat systemd service: `/etc/systemd/system/obs-tunnel.service`

```ini
[Unit]
Description=SSH Tunnel for OBS WebSocket
After=network.target

[Service]
Type=simple
User=your-username
ExecStart=/usr/bin/ssh -R 4455:localhost:4455 root@firmanstore15.devhilman.tech -N -o ServerAliveInterval=60 -o ServerAliveCountMax=3
Restart=always
RestartSec=10

[Install]
WantedBy=multi-user.target
```

Enable:

```bash
sudo systemctl enable obs-tunnel
sudo systemctl start obs-tunnel
```

## Troubleshooting

### Tunnel tidak terbentuk

```bash
# Cek apakah SSH bisa connect
ssh root@firmanstore15.devhilman.tech "echo test"

# Cek apakah port sudah digunakan
netstat -an | findstr 4455  # Windows
ss -tlnp | grep 4455        # Linux
```

### Port sudah digunakan di server

```bash
# Kill process yang pakai port 4455
sudo lsof -ti:4455 | xargs kill -9
```

### Koneksi putus terus

- Pastikan `ServerAliveInterval` sudah di set
- Pastikan firewall tidak block port 22 (SSH)
- Cek log: `journalctl -u sshd -f`

## Keamanan

**PENTING:** Tunnel ini membuka akses dari server production ke OBS lokal. Pastikan:

1. OBS WebSocket password kuat
2. Hanya gunakan di environment terpercaya
3. Pertimbangkan batasi IP di firewall server production
