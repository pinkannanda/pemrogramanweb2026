"use strict";

/* Daftar Pemesan Tiket (folder pesanan/). Pola sama dengan jadwal.js, data berbeda. */
const URL_PESANAN = "../data/pesanan.json";

function buatBarisPesanan(a) {
  const tr = document.createElement("tr");
  tr.append(
    buatSel(a.nama),
    buatSel(a.email),
    buatSel(a.kota),
    buatSel(a.kategori),
    buatSel(a.jumlah),
    buatSelHapus("pemesan " + a.nama)
  );
  return tr;
}

async function muatDaftarPesanan() {
  const tbody = document.querySelector("#tabel-pesanan tbody");
  const loading = document.querySelector("#loading");
  if (!tbody || !loading) return;

  tbody.innerHTML = "";
  loading.hidden = false;

  try {
    await tunda(DELAY_SIMULASI);
    const res = await fetch(URL_PESANAN);
    if (!res.ok) throw new Error("server membalas status " + res.status);

    const data = await res.json();
    if (!Array.isArray(data) || data.length === 0) {
      pesanTabel(tbody, 6, "Belum ada pemesan.");
      return;
    }
    data.forEach(function (a) { tbody.append(buatBarisPesanan(a)); });
  } catch (err) {
    pesanTabel(tbody, 6, "Gagal memuat pemesan: " + err.message);
  } finally {
    loading.hidden = true;
  }
}

document.addEventListener("DOMContentLoaded", function () {
  muatDaftarPesanan();
  const ulang = document.querySelector("#btn-muat-ulang");
  if (ulang) ulang.addEventListener("click", muatDaftarPesanan);
});
