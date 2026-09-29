"use strict";

/*
 * Jobsheet 7: rendering tabel sekarang dikerjakan PHP di server
 * (lihat jadwal/list.php dan pesanan/list.php), bukan lagi oleh
 * fetch() + JavaScript seperti Jobsheet 6. Karena itu helper
 * pembuat baris tabel (buatSel, buatSelHapus, pesanTabel, tunda)
 * yang dulu ada di file ini sudah tidak dipakai lagi dan dihapus.
 *
 * Validasi di file ini TETAP dipakai sebagai lapisan pertama (cepat
 * terasa oleh pengguna), tapi TIDAK BISA DIANDALKAN SENDIRIAN, karena
 * bisa dilewati dengan menonaktifkan JavaScript. Validasi yang
 * benar-benar mengikat ada di proses_tambah.php (server-side).
 */

/* 1. Hamburger menu */
function initHamburger() {
  const tombol = document.querySelector(".nav-toggle");
  const nav = document.querySelector("nav.main-nav");
  if (!tombol || !nav) return;
  tombol.addEventListener("click", function () {
    const terbuka = nav.classList.toggle("nav-open");
    tombol.setAttribute("aria-expanded", String(terbuka));
  });
}

/* 2. Validasi form client-side (lapisan pertama, boleh dilewati) */
function hapusError(form) {
  form.querySelectorAll(".pesan-error").forEach(function (el) { el.remove(); });
  form.querySelectorAll(".input-error").forEach(function (el) { el.classList.remove("input-error"); });
}

function tampilkanError(field, pesan) {
  const span = document.createElement("span");
  span.className = "pesan-error";
  span.setAttribute("role", "alert");
  span.textContent = pesan;
  field.classList.add("input-error");
  field.insertAdjacentElement("afterend", span);
}

function validasiField(field) {
  const nilai = field.value.trim();
  const label = field.dataset.label || field.name || "Field";

  if (field.required && nilai === "") return label + " wajib diisi.";
  if (nilai === "") return "";

  if (field.type === "email" && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(nilai)) {
    return "Format email tidak valid.";
  }
  if (field.type === "tel" && !/^\+?[0-9\s-]{9,15}$/.test(nilai)) {
    return "No. WhatsApp harus 9-15 digit angka.";
  }
  if (field.type === "date") {
    if (field.min && nilai < field.min) return label + " tidak boleh sebelum " + field.min + ".";
    if (field.max && nilai > field.max) return label + " tidak boleh setelah " + field.max + ".";
  }
  if (field.type === "number") {
    const angka = Number(nilai);
    if (Number.isNaN(angka)) return label + " harus berupa angka.";
    if (field.min !== "" && angka < Number(field.min)) {
      return field.min === "0" ? label + " tidak boleh negatif." : label + " minimal " + field.min + ".";
    }
    if (field.max !== "" && angka > Number(field.max)) return label + " maksimal " + field.max + ".";
  }
  return "";
}

function initValidasiForm() {
  document.querySelectorAll("form[data-validasi]").forEach(function (form) {
    form.setAttribute("novalidate", "");

    form.addEventListener("submit", function (e) {
      hapusError(form);
      let pertama = null;

      form.querySelectorAll("input, select, textarea").forEach(function (field) {
        const pesan = validasiField(field);
        if (pesan) {
          tampilkanError(field, pesan);
          if (!pertama) pertama = field;
        }
      });

      if (pertama) {
        // Ada error di browser: batalkan submit, jangan sampai ke server.
        e.preventDefault();
        pertama.focus();
      }
      // PENTING (Jobsheet 7): kalau TIDAK ada error, JANGAN preventDefault.
      // Biarkan form benar-benar ter-submit (POST) ke action="proses_tambah.php".
      // Validasi server di sana yang memutuskan final, bukan JS ini.
    });

    form.addEventListener("input", function (e) {
      const field = e.target;
      const berikut = field.nextElementSibling;
      if (berikut && berikut.classList.contains("pesan-error")) berikut.remove();
      field.classList.remove("input-error");
    });
  });
}

/* 3. Pencarian tabel real-time (keyup) */
function initTableFilter() {
  document.querySelectorAll("[data-filter-tabel]").forEach(function (input) {
    const tabel = document.querySelector(input.dataset.filterTabel);
    if (!tabel) return;
    input.addEventListener("keyup", function () {
      const kata = input.value.trim().toLowerCase();
      tabel.querySelectorAll("tbody tr").forEach(function (baris) {
        baris.style.display = baris.textContent.toLowerCase().includes(kata) ? "" : "none";
      });
    });
  });
}

/* 4. Tombol Hapus: masih front-end saja (confirm() lalu hapus dari tampilan). */
function initHapusConfirm() {
  document.addEventListener("click", function (e) {
    const tombol = e.target.closest(".btn-hapus");
    if (!tombol) return;
    const baris = tombol.closest("tr");
    if (!baris) return;
    const nama = tombol.dataset.nama || "data ini";
    if (confirm("Yakin ingin menghapus " + nama + "?")) baris.remove();
  });
}

document.addEventListener("DOMContentLoaded", function () {
  initHamburger();
  initValidasiForm();
  initTableFilter();
  initHapusConfirm();
});