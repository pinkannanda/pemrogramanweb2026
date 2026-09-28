# Dokumentasi Jobsheet 6

1. **Konsep**: AJAX, JSON, `fetch()`, Promise, `async`/`await`, `try`/`catch`/`finally`.
2. **Perubahan HTML**: `<tbody>` kosong, loading indicator, urutan `<script>` (`app.js` dulu, baru `jadwal.js`/`pesanan.js`).
3. **Data JSON**: `data/jadwal.json`, `data/pesanan.json`, `data/tiket.json`.
4. **Fetch & render**: `muatDaftarJadwal()` dan `muatDaftarPesanan()`: fetch, cek `res.ok`, parse, render, error handling.
5. **Event delegation**: `initHapusConfirm()` di `document` agar tombol dinamis tetap berfungsi.
6. **Server lokal**: `fetch()` butuh `http://`, bukan `file://` (batasan CORS).
