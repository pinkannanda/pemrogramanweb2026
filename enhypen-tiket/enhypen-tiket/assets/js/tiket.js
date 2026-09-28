"use strict";

/* Kartu kategori tiket di index.html (halaman di root, jadi data/tiket.json) */
const URL_TIKET = "data/tiket.json";

function buatKartuTiket(t) {
  const kartu = document.createElement("div");
  kartu.className = "ticket-card";

  const swatch = document.createElement("span");
  swatch.className = "swatch";
  swatch.style.background = t.warna;

  const judul = document.createElement("h3");
  judul.textContent = t.nama;

  const tipe = document.createElement("p");
  tipe.className = "type";
  tipe.textContent = t.tipe;

  const harga = document.createElement("p");
  harga.className = "price";
  harga.textContent = "Rp " + t.harga.toLocaleString("id-ID");

  kartu.append(swatch, judul, tipe, harga);
  return kartu;
}

function pesanKartu(wadah, pesan) {
  const p = document.createElement("p");
  p.className = "pesan-gagal";
  p.textContent = pesan;
  wadah.append(p);
}

async function muatTiket() {
  const wadah = document.querySelector("#daftar-tiket");
  const loading = document.querySelector("#loading-tiket");
  if (!wadah || !loading) return;

  wadah.innerHTML = "";
  loading.hidden = false;

  try {
    await tunda(DELAY_SIMULASI);
    const res = await fetch(URL_TIKET);
    if (!res.ok) throw new Error("server membalas status " + res.status);

    const data = await res.json();
    if (!Array.isArray(data) || data.length === 0) {
      pesanKartu(wadah, "Belum ada kategori tiket.");
      return;
    }
    data.forEach(function (t) { wadah.append(buatKartuTiket(t)); });
  } catch (err) {
    pesanKartu(wadah, "Gagal memuat kategori tiket: " + err.message);
  } finally {
    loading.hidden = true;
  }
}

document.addEventListener("DOMContentLoaded", muatTiket);
