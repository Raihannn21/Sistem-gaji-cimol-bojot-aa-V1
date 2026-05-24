---
title: Payroll Cimol Bojot
emoji: 💰
colorFrom: red
colorTo: pink
sdk: docker
app_port: 7860
pinned: false
---

# Sistem Informasi Penggajian & Kehadiran - Cimol Bojot AA

Aplikasi berbasis web modern untuk mengelola administrasi kehadiran, perhitungan lembur, tunjangan risiko, dan penggajian karyawan **Cimol Bojot AA**. Sistem ini mendukung klasifikasi karyawan bulanan kontrak (**PKWT**) dan harian (**PHL**).

## 🚀 Fitur Utama

*   📊 **Dashboard Analytics**: Pemantauan biaya gaji, lembur, rasio keterlambatan (*lateness rate*) terintegrasi, visualisasi rekrutmen/turnover karyawan, serta grafik tren disiplin kehadiran (ApexCharts).
*   📅 **Konfigurasi Hari Kerja Efektif**: Pengaturan hari kerja aktif dan hari libur berbasis tim menggunakan kalender interaktif untuk perhitungan gaji pro-rata PKWT yang adil.
*   ⏱️ **Pencatatan Kehadiran & Lembur**: Pencatatan data kehadiran (terlambat & pulang cepat) serta kalkulasi upah lembur otomatis untuk PKWT dan PHL dari file Excel.
*   📄 **Slip Gaji & Laporan**: Unduh laporan rekapitulasi bulanan, laporan riwayat penggajian individu, slip gaji PDF karyawan, dan ekspor data ke Excel.

## 🛠️ Teknologi yang Digunakan

*   **Framework**: Laravel 12
*   **Frontend**: Tailwind CSS, Alpine.js (desain responsif dark/light mode premium)
*   **Database**: PostgreSQL (Neon) / MySQL
*   **Library Utama**: Laravel Excel (import/export), DomPDF, ApexCharts (visualisasi grafik)

## 💻 Cara Menjalankan Secara Lokal

1.  **Clone repositori**:
    ```bash
    git clone https://github.com/Raihannn21/Sistem-gaji-cimol-bojot-aa-V1.git
    cd Sistem-gaji-cimol-bojot-aa-V1
    ```
2.  **Instal PHP & Node dependencies**:
    ```bash
    composer install
    npm install
    ```
3.  **Salin berkas konfigurasi lingkungan**:
    ```bash
    copy .env.example .env
    ```
4.  **Buat database baru** dan jalankan migrasi serta seeder:
    ```bash
    php artisan key:generate
    php artisan migrate --seed
    ```
5.  **Jalankan aplikasi**:
    ```bash
    composer run dev
    ```
    Buka [http://localhost:8000](http://localhost:8000) di browser Anda. Akun default admin:
    *   **Email**: `admin@cimolbojot.com`
    *   **Password**: `password`
