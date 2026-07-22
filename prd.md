# Product Requirements Document (PRD)
## Sistem Arsip Digital Pajak Daerah — Bapenda Provinsi Riau

---

## 1. Problem Statement

Bapenda Provinsi Riau saat ini mengelola arsip pajak daerah (mencakup berbagai jenis pajak seperti Pajak Kendaraan, Pajak Alat Berat, dan lainnya) secara fisik, yang disimpan di ruangan arsip dan dicatat menggunakan daftar arsip aktif/inaktif berbasis Excel.

Pendekatan ini menimbulkan beberapa masalah:
- Pencarian arsip membutuhkan waktu lama karena harus dilakukan secara manual.
- Pencatatan di Excel rentan human error dan tidak konsisten antar entri.
- Data arsip tersebar di banyak unit/UPT (±50 unit) tanpa struktur terpusat.
- Tidak ada cara cepat untuk melihat status arsip (aktif/inaktif) maupun lokasi rak fisiknya secara sistematis.

## 2. Goals

- Membangun sistem digital yang menggantikan pencatatan manual Excel dengan sistem yang lebih terstruktur.
- Mempercepat proses pencarian arsip pajak berdasarkan metadata (nomor arsip, nama wajib pajak, jenis pajak, tahun, unit).
- Memungkinkan digitalisasi arsip fisik melalui upload dokumen (PDF/gambar hasil pindai).
- Memudahkan migrasi data lama dari Excel ke sistem baru tanpa input ulang manual.
- Sistem selesai dan siap digunakan dalam waktu satu minggu (sesuai tenggat KP).

## 3. Target User

- **Staf arsip Bapenda Provinsi Riau** — pengguna utama sistem, bertindak sebagai satu-satunya admin/login yang mengelola seluruh data arsip.
- Pengguna diasumsikan tidak terlalu familiar dengan aplikasi web modern, sehingga antarmuka harus sederhana dan mudah dipahami.

## 4. User Stories

- Sebagai staf arsip, saya ingin **menambahkan data arsip baru** (manual) agar arsip yang baru masuk tercatat di sistem.
- Sebagai staf arsip, saya ingin **mengunggah file hasil pindai/foto arsip** agar dokumen fisik memiliki salinan digital.
- Sebagai staf arsip, saya ingin **mencari arsip berdasarkan nomor arsip, nama wajib pajak, jenis pajak, tahun, atau unit** agar tidak perlu mencari secara manual di ruangan arsip.
- Sebagai staf arsip, saya ingin **mengimpor data dari file Excel yang sudah ada** agar tidak perlu mengetik ulang seluruh data lama.
- Sebagai staf arsip, saya ingin **melihat status arsip (aktif/inaktif)** agar tahu mana arsip yang masih berlaku.
- Sebagai staf arsip, saya ingin **melihat nomor rak setiap arsip** agar bisa menemukan lokasi fisiknya dengan cepat jika dibutuhkan.
- Sebagai staf arsip, saya ingin **mengedit atau menghapus data arsip yang salah/usang** agar data tetap akurat.

## 5. Functional Requirements

| No | Requirement |
|----|-------------|
| FR1 | Sistem menyediakan form tambah arsip dengan field: nomor arsip, jenis pajak, nama wajib pajak, tahun arsip, nomor rak, unit/UPT, status (aktif/inaktif). |
| FR2 | Sistem menyediakan fitur unggah file (PDF/gambar) yang terhubung ke satu record arsip. |
| FR3 | Sistem menyediakan halaman daftar arsip dalam bentuk tabel dengan fitur pencarian dan filter (nomor arsip, nama wajib pajak, jenis pajak, tahun, unit, status). |
| FR4 | Sistem menyediakan fitur edit dan hapus data arsip. |
| FR5 | Sistem menyediakan fitur impor data dari file Excel (.xlsx), dengan pratinjau data sebelum konfirmasi impor. |
| FR6 | Sistem menyimpan data jenis pajak sebagai master data (bukan input teks bebas), yang dapat ditambah oleh admin. |
| FR7 | Sistem menyimpan data unit/UPT sebagai master data yang dapat dikaitkan ke setiap arsip. |
| FR8 | Sistem menyediakan halaman detail arsip yang menampilkan seluruh metadata beserta pratinjau file yang diunggah. |
| FR9 | Sistem menyediakan halaman beranda berisi ringkasan (total arsip, arsip aktif, arsip inaktif, total unit). |

## 6. Non-Functional Requirements

| No | Requirement |
|----|-------------|
| NFR1 | Sistem berjalan pada lingkungan localhost (Laragon) tanpa memerlukan koneksi internet/hosting publik. |
| NFR2 | File dokumen arsip disimpan secara terpisah dari database (folder storage), bukan sebagai data biner di database, untuk menjaga performa dan ukuran database. |
| NFR3 | Antarmuka harus sederhana dan mudah digunakan oleh staf non-teknis. |
| NFR4 | Sistem menggunakan satu akun admin tanpa pembagian hak akses berjenjang antar unit. |
| NFR5 | Proses pencarian arsip harus responsif meskipun volume data arsip besar (ribuan hingga puluhan ribu record). |
| NFR6 | Sistem harus dapat dipindahkan (deploy ulang) ke komputer lain (milik staf arsip) dengan proses instalasi yang sederhana melalui Laragon. |

## 7. Scope

**Termasuk dalam scope:**
- Digitalisasi arsip pajak (input data + unggah file).
- Penyimpanan dan pengelolaan metadata arsip.
- Pencarian dan filter arsip.
- Impor data arsip dari Excel.
- Manajemen master data jenis pajak dan unit/UPT.

**Tidak termasuk dalam scope:**
- Proses peminjaman/pengembalian arsip.
- Alur persetujuan (approval) berjenjang.
- Proses penyusutan/pemusnahan arsip sesuai jadwal retensi.
- Akses publik/masyarakat (sistem hanya untuk internal Bapenda).
- Deployment ke server hosting/publik (hanya localhost).
- Manajemen hak akses multi-role/multi-unit.

## 8. Jika Ada Perubahan, Sesuaikan

Dokumen ini bersifat hidup (living document) dan dapat berubah mengikuti perkembangan konsultasi dengan pembimbing atau pihak Bapenda. Perubahan pada problem statement, scope, atau requirement harus dicatat ulang di dokumen ini agar seluruh pihak memiliki acuan yang sama dan konsisten dengan kondisi terbaru di lapangan.

---

> Dokumen ini merupakan pedoman utama (living document) bagi agent AI dalam mengembangkan Sistem Arsip Digital Pajak Daerah Bapenda Provinsi Riau. Setiap perubahan requirement wajib diperbarui di dokumen ini agar implementasi tetap selaras dengan kebutuhan pengguna.