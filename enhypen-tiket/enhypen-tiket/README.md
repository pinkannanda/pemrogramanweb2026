# ENHYPEN Tickets — Jobsheet 6: Fetch API & JSON

Sistem pemesanan tiket konser ENHYPEN.

## Perubahan dari Jobsheet 5
- Data tabel dipindah dari HTML ke file JSON (`data/`) dan diambil dengan `fetch()`.
- Tambah `assets/js/jadwal.js`, `pesanan.js`, dan `tiket.js`.
- Loading indicator ("Memuat data...") dan penanganan error (`try`/`catch`/`finally`).
- Tombol Hapus memakai event delegation agar berfungsi pada baris buatan `fetch()`.

## Struktur
| Folder / file | Isi |
|---|---|
| `index.html` | Beranda: info konser, kategori tiket, benefit VIP |
| `jadwal/` | Daftar jadwal tur dan form tambah jadwal |
| `pesanan/` | Daftar pesanan tiket dan form pesan tiket |
| `data/jadwal.json` | Data jadwal tur |
| `data/pesanan.json` | Data pesanan tiket |
| `data/tiket.json` | Kategori tiket |
| `assets/` | CSS dan JavaScript |

## Cara menjalankan
`fetch()` tidak bisa dari `file://`. Jalankan lewat server lokal
(Live Server / `php -S localhost:8000` / Laragon), lalu buka `index.html`.
