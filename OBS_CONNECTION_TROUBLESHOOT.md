### Troubleshooting OBS Connection

Pertanyaan penting:

1. **Dimana OBS Studio berjalan?**
    - [ ] Di server production yang sama (firmanstore15.devhilman.tech)
    - [ ] Di komputer lokal (IP 192.168.125.43)
    - [ ] Di komputer lain di jaringan yang sama

2. **Dimana server production?**
    - [ ] Cloud/VPS (DigitalOcean, AWS, dll)
    - [ ] Lokal server di kantor
    - [ ] Di komputer yang sama dengan OBS

---

### SOLUSI 1: Jika OBS di SERVER PRODUCTION yang sama

Ganti di .env server production:

```
OBS_WEBSOCKET_URL=ws://localhost:4455
```

Lalu:

```bash
php artisan config:clear
php artisan cache:clear
```

---

### SOLUSI 2: Jika OBS di KOMPUTER LAIN (tidak bisa direct connect)

Ada 2 opsi:

A) **Setup SSH Tunnel** (Port Forwarding)
Di server production:

```bash
ssh -L 4455:192.168.125.43:4455 user@gateway-komputer-obs
```

Lalu ganti .env:

```
OBS_WEBSOCKET_URL=ws://localhost:4455
```

B) **Expose OBS WebSocket ke public**

- Setup firewall/port forwarding di router komputer OBS
- Expose port 4455 ke public IP
- Gunakan: ws://public-ip:4455
  ⚠️ SECURITY RISK - not recommended

---

### SOLUSI 3: Test koneksi dari server production

SSH ke server production, lalu test:

```bash
# Test jika OBS di localhost
telnet localhost 4455

# Test jika OBS di IP lain
telnet 192.168.125.43 4455
```

Jika "Connection refused" atau "No route to host" -> server tidak bisa akses OBS.

---

### Quick Test di Server Production:

```bash
cd /path/to/firmanstore15
php -r "require 'vendor/autoload.php'; \$client = new \WebSocket\Client('ws://192.168.125.43:4455'); echo 'Connected!';"
```

Jika error "Connection refused" -> server production tidak bisa akses IP 192.168.125.43
