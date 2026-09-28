"use strict";

/* Daftar Jadwal Tur (folder jadwal/). Halaman berada di jadwal/, jadi data di ../data/ */
const URL_JADWAL = "../data/jadwal.json";
const LABEL_STATUS = { open: "Tiket Tersedia", soon: "Segera Dibuka", closed: "Habis Terjual" };

function buatBarisJadwal(b) {
  const tr = document.createElement("tr");

  const tdStatus = document.createElement("td");
  const pill = document.createElement("span");
  pill.className = "status-pill status-" + b.status;
  pill.textContent = LABEL_STATUS[b.status] || b.status;
  tdStatus.append(pill);

  tr.append(
    buatSel(b.tanggal),
    buatSel(b.kota),
    buatSel(b.venue),
    buatSel(b.kapasitas.toLocaleString("id-ID")),
    buatSel("Rp " + b.harga_mulai.toLocaleString("id-ID")),
    tdStatus,
    buatSelHapus("jadwal " + b.kota)
  );
  return tr;
}

/* fetch -> cek status -> parse -> render -> error handling */
async function muatDaftarJadwal() {
  const tbody = document.querySelector("#tabel-jadwal tbody");
  const loading = document.querySelector("#loading");
  if (!tbody || !loading) return;

  tbody.innerHTML = "";     // kosongkan dulu, aman dipanggil berulang
  loading.hidden = false;

  try {
    await tunda(DELAY_SIMULASI);
    const res = await fetch(URL_JADWAL);
    if (!res.ok) throw new Error("server membalas status " + res.status); // fetch tidak otomatis error pada 404

    const data = await res.json();
    if (!Array.isArray(data) || data.length === 0) {
      pesanTabel(tbody, 7, "Belum ada jadwal.");
      return;
    }
    data.forEach(function (b) { tbody.append(buatBarisJadwal(b)); });
  } catch (err) {
    pesanTabel(tbody, 7, "Gagal memuat jadwal: " + err.message);
  } finally {
    loading.hidden = true;  // selalu jalan, berhasil maupun gagal
  }
}

document.addEventListener("DOMContentLoaded", function () {
  muatDaftarJadwal();
  const ulang = document.querySelector("#btn-muat-ulang");
  if (ulang) ulang.addEventListener("click", muatDaftarJadwal);
});
