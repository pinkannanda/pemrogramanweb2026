"use strict";

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
        e.preventDefault();
        pertama.focus();
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

/* 3. Pencarian tabel real-time (filter client-side, di atas hasil server) */
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

/* 4. Konfirmasi Hapus (Jobsheet 9): sekarang form sungguhan, jadi
   konfirmasi dipasang di event SUBMIT (bisa dibatalkan dengan
   preventDefault), bukan lagi CLICK seperti Jobsheet 5-8. */
function initHapusConfirm() {
  document.querySelectorAll(".form-hapus").forEach(function (form) {
    form.addEventListener("submit", function (e) {
      const tombol = form.querySelector(".btn-hapus");
      const nama = tombol?.dataset.nama || "data ini";
      const yakin = confirm("Yakin ingin menghapus " + nama + "?");
      if (!yakin) {
        e.preventDefault(); // batalkan submit, tidak ada request ke server sama sekali
      }
    });
  });
}

document.addEventListener("DOMContentLoaded", function () {
  initHamburger();
  initValidasiForm();
  initTableFilter();
  initHapusConfirm();
});