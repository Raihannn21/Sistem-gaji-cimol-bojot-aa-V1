# Sistem Informasi Penggajian & Kehadiran - Cimol Bojot AA

Sistem Informasi Penggajian dan Kehadiran berbasis web modern untuk mengelola administrasi kehadiran, perhitungan lembur, tunjangan risiko, slip gaji bulanan, serta pelaporan keuangan terintegrasi bagi karyawan **Cimol Bojot AA**. Sistem ini mendukung klasifikasi karyawan bulanan kontrak (**PKWT**) dan harian lepas (**PHL**).

📋 Tentang Project
Sistem ini memfasilitasi HRD dan Finance dalam melakukan kalkulasi payroll bulanan secara otomatis berdasarkan impor data absensi dari Excel. Fitur utama mencakup manajemen periode gaji, pencatatan lembur & risiko, ekspor daftar transfer BCA Auto-Credit (siap unggah), pencetakan laporan bulanan/tahunan (PDF & Excel), serta Slip Gaji digital yang dapat dikirimkan langsung ke email masing-masing karyawan secara massal.

🛠️ Tech Stack

- **Framework:** Laravel 12.x (PHP 8.2+)
- **Frontend CSS:** Tailwind CSS (dengan desain premium light/dark mode)
- **Frontend Logic:** Alpine.js (pencarian reaktif, modifikasi modal, client-side pagination)
- **Database:** PostgreSQL (Neon Cloud) / MySQL
- **Library Ekspor/Impor:** Maatwebsite Excel (Laravel Excel)
- **Library Rendering PDF:** Barryvdh DomPDF
- **Visualisasi Grafis:** ApexCharts (Analytics Dashboard)
- **Package Manager:** Composer (PHP) & NPM (NodeJS)

📦 Requirements

- Node.js >= 20.x
- NPM >= 10.x
- PHP >= 8.2 dengan ekstensi `GD`, `PDO_PGSQL`, `XML`, `ZIP`, `MBSTRING` aktif
- Composer >= 2.x
- Server Database PostgreSQL atau MySQL

🚀 Installation

1. Clone Repository

    ```bash
    git clone https://github.com/Raihannn21/Sistem-gaji-cimol-bojot-aa-V1.git
    cd Sistem-gaji-cimol-bojot-aa-V1
    ```

2. Install Dependencies

    ```bash
    composer install
    npm install
    ```

3. Environment Configuration
   Salin berkas contoh konfigurasi lingkungan:

    ```bash
    # Di Windows (PowerShell/CMD):
    copy .env.example .env
    # Di Linux/macOS:
    cp .env.example .env
    ```

    Sesuaikan kredensial database Anda di berkas `.env` baru:

    ```env
    DB_CONNECTION=pgsql
    DB_HOST=127.0.0.1
    DB_PORT=5432
    DB_DATABASE=nama_database_anda
    DB_USERNAME=username_database
    DB_PASSWORD=password_database
    ```

4. Generate Key & Run Database Migration/Seeders
    ```bash
    php artisan key:generate
    php artisan migrate --seed
    ```

🏃 Running the Application
Development Server
Jalankan server aplikasi PHP:

```bash
php artisan serve
```

Dan jalankan aset Vite untuk kompilasi CSS & JS:

```bash
npm run dev
```

Buka peramban Anda di alamat [http://localhost:8000](http://localhost:8000).

Akun Administrator Default:

- **Email:** `admin@cimolbojot.com`
- **Password:** `password`

Production Build
Untuk membangun aset terkompresi produksi:

```bash
npm run build
php artisan optimize
```

📁 Project Structure

```
Sistem-gaji-cimol-bojot-aa-V1/
├── app/
│   ├── Exports/               # Kelas penanganan ekspor data ke Excel
│   │   ├── BcaPayrollExport.php         # Daftar transfer BCA (PHL)
│   │   ├── PkwtBcaPayrollExport.php     # Daftar transfer BCA (PKWT)
│   │   ├── PhlPayrollExport.php         # Rekap gaji PHL lengkap
│   │   ├── PkwtPayrollExport.php        # Rekap gaji PKWT lengkap
│   │   ├── SummaryPayrollReportExport.php # Laporan rekap tahunan
│   │   └── MonthlyPayrollReportExport.php # Laporan rekap bulanan
│   ├── Http/Controllers/      # Controller logika bisnis modular
│   │   ├── Payroll/
│   │   │   ├── Phl/           # Submodul Harian Lepas (PHL)
│   │   │   │   ├── PeriodController.php
│   │   │   │   ├── AttendanceController.php
│   │   │   │   ├── OvertimeController.php
│   │   │   │   ├── AllowanceController.php
│   │   │   │   └── ExportController.php
│   │   │   └── Pkwt/          # Submodul Karyawan Kontrak (PKWT)
│   │   │       ├── PeriodController.php
│   │   │       ├── AttendanceController.php
│   │   │       ├── OvertimeController.php
│   │   │       ├── AllowanceController.php
│   │   │       └── ExportController.php
│   │   └── Report/            # Controller untuk halaman komparasi & laporan
│   │       ├── SummaryReportController.php
│   │       └── MonthlyReportController.php
│   └── Models/                # Model database Eloquent (Employee, Attendance, dll)
├── public/                    # Aset statis publik (favicon, logo, dll)
├── resources/
│   ├── css/                   # Berkas stylesheet utama
│   ├── js/                    # Berkas Javascript utama (Alpine.js integration)
│   └── views/
│       ├── components/        # Komponen Blade kustom (x-form.select-pagination, modal, toast)
│       ├── exports/           # Templat HTML untuk render ekspor Excel & PDF
│       ├── layouts/           # Templat layout aplikasi (sidebar, header, app)
│       └── pages/             # Berkas halaman Blade view utama (payroll, reports, dll)
└── routes/
    └── web.php                # Definisi seluruh rute aplikasi
```

🔌 Fitur Utama & Integrasi

- 📊 **Dashboard Analytics:** Visualisasi dinamis ApexCharts untuk melacak lateness rate (angka keterlambatan), tren disiplin absensi, total beban gaji bulanan/tahunan, serta rasio PKWT/PHL.
- 🏦 **BCA Auto-Credit Transfer List:** Ekspor otomatis daftar transfer gaji bank BCA dalam format Excel yang ramah sistem bank (mengabaikan simbol mata uang dan format teks decimal corrupt).
- 📧 **Batch Email Slip Gaji:** Pengiriman slip gaji individu PDF secara massal atau satuan melalui integrasi SMTP Mail Laravel langsung ke surel karyawan.
- 📄 **Desain Ekspor PDF Korporat Formal:** Format laporan PDF bersih (Rekap Bulanan & Tahunan) tanpa elemen visual UI web, menggunakan kop surat ganda formal dan panel penandatanganan approval terstruktur.
- 📅 **Kalender Kerja Tim Fleksibel:** Pengaturan jumlah hari kerja aktif bulanan dan pengecualian hari libur tim demi keakuratan sistem hitung gaji pro-rata PKWT.

🔧 Development Tools

1. **Lacak Rute Aplikasi**
    ```bash
    php artisan route:list
    ```
2. **Bersihkan Cache Konfigurasi & Rute**
    ```bash
    php artisan optimize:clear
    ```
3. **Penyelarasan Favicon Kustom**
   Favicon diletakkan di `/public/favicon.ico` dan telah di-resize secara khusus (32x32px alpha transparent) dari logo utama agar tab peramban saat memuat PDF cetak tetap memuat logo Cimol Bojot AA secara konsisten.

⚠️ Known Issues & Troubleshooting

- **Kesalahan XML Parser pada Ekspor Excel:** Pastikan tidak ada karakter ampersand mentah (`&`) pada berkas templat HTML ekspor Excel. Karakter ini harus ditulis sebagai entitas HTML aman (`&amp;`) untuk mencegah error _PhpOffice\PhpSpreadsheet\Reader\Exception_.
- **Format Nominal Rp di Excel BCA:** Nilai ratusan ribu rupiah dikirim ke Excel dalam format angka bulat murni dan dipetakan dengan _interface_ `WithColumnFormatting` menggunakan format pola `#,##0` guna menghindari bug pembacaan desimal otomatis (seperti `122.500` dibaca `122,5` oleh Excel regional Indonesia).
- **Pengelompokan Bulan Laporan:** Laporan summary tahunan dan bulanan dikelompokkan secara unik berdasarkan **Tanggal Mulai (`start_date`)** dari periode payroll (contoh: periode 21 Maret s.d. 20 April akan terhitung unik di bulan Maret saja).

🔐 Security

- **Rute Terproteksi:** Seluruh rute dilindungi oleh middleware `auth` untuk mencegah akses tidak sah.
- **Proteksi CSRF:** Semua form interaktif Alpine.js dilengkapi dengan token CSRF Laravel terenkripsi.
- **Penyimpanan Kredensial:** Data sensitif API dan koneksi database dilarang disimpan di dalam repositori git (selalu gunakan file `.env`).

👨💻 Development Guidelines

- **Paginasi Komponen Kustom:** Seluruh tabel menggunakan pagination client-side berbasis Alpine.js dengan komponen pembantu kustom `<x-form.select-pagination>` untuk mengubah per-page limits secara reaktif.
- **Modularitas Controller:** Setiap fungsionalitas baru wajib memisahkan logika PHL dan PKWT di sub-controller tersendiri agar kode tetap modular dan mudah dipelihara.
- **Pesan Komitmen Git:** Harap menggunakan pesan commit yang deskriptif dan mencerminkan fitur/bug yang sedang dikerjakan.

📖 Panduan Penggunaan Sistem (Workflow Penggajian)

Berikut adalah alur kerja operasional penggunaan sistem informasi penggajian Cimol Bojot AA dari awal hingga proses pembayaran selesai:

### 1. Pendaftaran & Manajemen Karyawan

- Masuk ke menu **Data Karyawan** pada sidebar.
- Daftarkan karyawan baru dengan mengisi data diri lengkap, jenis kontrak (**PKWT** atau **PHL**), Jabatan, Nomor Tim, Gaji Pokok (bulanan untuk PKWT, harian untuk PHL), serta informasi rekening bank BCA untuk tujuan transfer.
- _Catatan:_ Karyawan harus memiliki status **Aktif** agar masuk dalam perhitungan payroll periode baru.

### 2. Membuat Periode Penggajian Baru

- Pilih menu **Payroll PKWT** atau **Payroll PHL** sesuai kategori yang ingin diproses.
- Klik tombol **Buat Periode Baru** dan tentukan:
    - Judul Periode (misal: _Periode 21 Maret - 20 April 2026_).
    - Tanggal Mulai dan Tanggal Selesai.
- **Khusus PKWT (Hari Kerja Efektif):**
    - Sebelum memproses absensi, masuk ke **Setup Periode** untuk mengonfigurasi jumlah hari kerja aktif dan hari libur spesifik untuk masing-masing Tim pada periode tersebut. Konfigurasi ini penting untuk perhitungan upah harian pro-rata.

### 3. Mengimpor Data Kehadiran Karyawan

- Masuk ke detail periode yang baru dibuat dengan mengklik tombol **Detail / View**.
- Masuk ke tab **Attendance / Absensi**, lalu klik tombol **Import Excel**.
- Unggah file Excel rekap kehadiran yang sesuai dengan format templat absensi. Sistem secara otomatis memetakan nama, ID karyawan, menghitung jumlah hari kehadiran, keterlambatan, absensi, serta durasi jam kerja.

### 4. Input Lembur & Tunjangan

- **Lembur (Overtime):**
    - Buka tab **Overtime / Lembur** pada detail periode.
    - Klik **Tambah Lembur** untuk menginput jam lembur karyawan. Tarif nominal lembur akan terhitung otomatis sesuai ketentuan sistem atau dapat disesuaikan secara manual.
- **Tunjangan Risiko & Lain-lain:**
    - Buka tab **Risk Allowances / Tunjangan Risiko** (atau **Other Allowances** untuk PKWT).
    - Masukkan nominal tunjangan risiko kerja harian atau tunjangan tambahan khusus periode untuk karyawan yang berhak menerimanya.

### 5. Review & Validasi Payroll (Draft)

- Masuk ke tab **Overview** pada detail periode untuk melihat rekapitulasi penggajian seluruh karyawan secara terpusat.
- Periksa nominal _Take Home Pay_ (Gaji Bersih) setelah dikurangi potongan BPJS Kesehatan, BPJS Ketenagakerjaan, dan PPh21 (untuk karyawan PKWT).
- Pastikan tidak ada data yang janggal atau karyawan aktif yang tertinggal (karyawan tanpa catatan kehadiran akan otomatis bernilai Rp 0).

### 6. Mengunci Periode (Lock Period)

- Jika semua data nominal gaji, lembur, dan tunjangan sudah divalidasi dan benar, klik tombol **Lock / Kunci Periode**.
- Status periode akan berubah menjadi **Locked (Terkunci / Sudah Dibayar)**.
- _Penting:_ Setelah periode dikunci, seluruh data di dalamnya bersifat permanen (read-only) dan tidak dapat diubah kembali untuk menjaga integritas data keuangan perusahaan.

### 7. Proses Ekspor Laporan & Pembayaran

Setelah periode dikunci, Anda dapat melakukan ekspor berikut untuk penyelesaian pembayaran:

- **BCA Auto-Credit List (Format Excel):**
    - Klik tombol **Export BCA** pada detail periode. Berkas Excel yang diunduh dapat langsung diunggah ke portal _BCA KlikBisnis_ untuk mentransfer gaji ke rekening karyawan secara massal dan otomatis tanpa perlu input manual.
- **Rekap Payroll (Excel / PDF):**
    - Klik tombol ekspor laporan lengkap untuk mencetak rekam jejak pembukuan kas payroll dalam bentuk fisik (PDF) maupun arsip data (Excel).
- **Batch Kirim Slip Gaji:**
    - Masuk ke tab **Slips / Slip Gaji**.
    - Klik **Kirim Email Massal** untuk mengirimkan dokumen slip gaji berformat PDF resmi langsung ke alamat email terdaftar masing-masing karyawan secara otomatis dalam sekali klik.
