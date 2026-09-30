<?php
/**
 * Kumpulan fungsi bantu dipakai di banyak halaman.
 * File ini TIDAK mencetak apa pun di luar fungsi, supaya aman
 * di-require sebelum session_start() atau header('Location: ...').
 */

/** Escape teks agar aman ditampilkan di HTML (cegah XSS). */
function h(string $teks): string
{
    return htmlspecialchars($teks, ENT_QUOTES, 'UTF-8');
}

/** Format angka menjadi "Rp 1.234.567". */
function formatRupiah(int $angka): string
{
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

/** Format angka biasa menjadi "45.000" (pemisah ribuan ala Indonesia). */
function formatAngka(int $angka): string
{
    return number_format($angka, 0, ',', '.');
}

/** Ubah "2026-03-14" (dari <input type="date">) menjadi "14 Mar 2026". */
function formatTanggalIndo(string $ymd): string
{
    $bulan = [
        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
        7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
    ];
    $ts = strtotime($ymd);
    if ($ts === false) {
        return $ymd; // kalau gagal diparse, tampilkan apa adanya
    }
    return (int) date('j', $ts) . ' ' . $bulan[(int) date('n', $ts)] . ' ' . date('Y', $ts);
}

/** Kode status ("open"/"soon"/"closed") -> label yang tampil di tabel. */
function labelStatus(string $kode): string
{
    $peta = [
        'open'   => 'Tiket Tersedia',
        'soon'   => 'Segera Dibuka',
        'closed' => 'Habis Terjual',
    ];
    return $peta[$kode] ?? $kode;
}

/** Teks label status (dari <select>) -> kode singkat untuk disimpan. */
function kodeStatus(string $label): string
{
    $peta = [
        'Tiket Tersedia' => 'open',
        'Segera Dibuka'  => 'soon',
        'Habis Terjual'  => 'closed',
    ];
    return $peta[$label] ?? 'open';
}

/** Simpan pesanan flash message ke session. */
function setFlash(string $type, string $pesan): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['flash'] = [
        'type'  => $type,
        'pesan' => $pesan
    ];
}

/**
 * Ambil flash message dari session lalu langsung hapus
 * (pola "sekali tampil"). Return null kalau tidak ada.
 */
function ambilFlash(): ?array
{
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/** Simpan input lama ke session agar form tidak hilang saat error validation. */
function simpanOldInput(array $data): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['old'] = $data;
}

/**
 * Ambil input lama (dari percobaan submit yang gagal) lalu hapus
 * dari session. Dipakai supaya form tidak perlu diisi ulang dari
 * nol setelah muncul error.
 */
function ambilOldInput(): array
{
    if (!empty($_SESSION['old'])) {
        $old = $_SESSION['old'];
        unset($_SESSION['old']);
        return $old;
    }
    return [];
}

/** Helper singkat untuk mengambil satu nilai old-input, aman kalau tidak ada. */
function old(array $old, string $key, string $default = ''): string
{
    return isset($old[$key]) ? (string) $old[$key] : $default;
}