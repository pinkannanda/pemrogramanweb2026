"use strict";

/* ===== Pembantu bersama (Jobsheet 6) ===== */
const DELAY_SIMULASI = 600; // ms, agar "Memuat data..." sempat terlihat
function tunda(ms) {
  return new Promise(function (selesai) { setTimeout(selesai, ms); });
}

function buatSel(teks) {
  const td = document.createElement("td");
  td.textContent = teks; // textContent: aman dari injeksi HTML
  return td;
}

function buatSelHapus(nama) {
  const td = document.createElement("td");
  const tombol = document.createElement("button");
  tombol.type = "button";
  tombol.className = "btn-hapus";
  tombol.dataset.nama = nama;
  tombol.textContent = "Hapus";
  td.append(tombol);
  return td;
}

function pesanTabel(tbody, jumlahKolom, pesan) {
  const tr = document.createElement("tr");
  const td = document.createElement("td");
  td.colSpan = jumlahKolom;
  td.className = "pesan-gagal";
  td.textContent = pesan;
  tr.append(td);
  tbody.append(tr);
}

/* ===== Jobsheet 5 ===== */

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

/* 2. Validasi form client-side */
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
  if (field.type === "date") {   // format YYYY-MM-DD bisa dibandingkan sebagai teks
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
      e.preventDefault(); // belum ada server, submit selalu ditahan
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
        pertama.focus();
      } else {
        alert("Data valid! (Belum dikirim ke server.)");
        form.reset();
      }
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

/* 4. Tombol Hapus: EVENT DELEGATION.
   Listener dipasang di document, bukan di tiap tombol, sehingga tombol
   yang dibuat belakangan oleh fetch() tetap berfungsi (Jobsheet 6). */
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
