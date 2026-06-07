# Software Requirement & Technical Specification: Tuker.in

## 1. Project Overview
Tuker.in adalah sistem informasi berbasis web dengan pendekatan *mobile-first design* yang ditujukan untuk operasional perusahaan daur ulang botol plastik. Sistem ini mendigitalisasi proses penukaran botol menjadi poin *reward* yang nantinya dapat dicairkan. Sistem menghubungkan tiga aktor utama: **User (Nasabah)**, **Pegawai (Employee)**, dan **Admin**.

## 2. System Architecture & Tech Stack
* **Backend Framework:** Laravel 13(Konsep MVC)
* **Database:** MySQL (Relational Database)
* **View / Template Engine:** Blade
* **Frontend Styling:** Tailwind CSS
* **Sistem Keamanan Auth:** Laravel *Single Guard* (`web`) dengan *Role-based Authorization*
* **Batasan Sistem (Constraints):**
  * Tidak ada integrasi *payment gateway* (pencairan manual).
  * Tidak menggunakan *real-time system* (WebSocket).
  * *Primary Key* pada tabel yang terekspos ke publik WAJIB menggunakan **UUID v7**.

---

## 3. Database Schema
Sistem menggunakan 8 tabel. *Migration* harus dibuat dengan urutan pembuatan tabel referensi terlebih dahulu (*roles*, *users*, *bottle_types*) sebelum tabel transaksi.

### 3.1 Tabel `roles`
* `id`: BIGINT UNSIGNED, PK, Auto Increment
* `name`: VARCHAR(255), Unique (Nilai: 'user', 'employee', 'admin')
* `label`: VARCHAR(255) (Nilai: 'User', 'Pegawai', 'Admin')

### 3.2 Tabel `users`
* `id`: CHAR(36), PK, UUID v7
* `name`: VARCHAR(255)
* `email`: VARCHAR(255), Unique
* `password`: VARCHAR(255), Hash (bcrypt)
* `role_id`: BIGINT UNSIGNED, FK ke `roles.id`
* `remember_token`: VARCHAR(100), Nullable
* `created_at`, `updated_at`: TIMESTAMP

### 3.3 Tabel `user_profiles` (One-to-One dengan `users` role 'user')
* `id`: BIGINT UNSIGNED, PK, Auto Increment
* `user_id`: CHAR(36), FK ke `users.id`, Unique
* `phone`: VARCHAR(20)
* `qr_code`: VARCHAR(255), Unique (*Auto-generated* string/UUID saat registrasi)
* `points_balance`: INT UNSIGNED, Default 0

### 3.4 Tabel `bottle_types`
* `id`: BIGINT UNSIGNED, PK, Auto Increment
* `name`: VARCHAR(255)
* `barcode`: VARCHAR(255), Unique
* `description`: TEXT, Nullable
* `points_value`: INT UNSIGNED
* `created_at`, `updated_at`: TIMESTAMP

### 3.5 Tabel `exchange_transactions`
* `id`: CHAR(36), PK, UUID v7
* `user_id`: CHAR(36), FK ke `users.id` (Nasabah)
* `employee_id`: CHAR(36), FK ke `users.id` (Pegawai yang memproses)
* `total_points`: INT UNSIGNED
* `transacted_at`: TIMESTAMP

### 3.6 Tabel `exchange_transaction_details`
* `id`: BIGINT UNSIGNED, PK, Auto Increment
* `transaction_id`: CHAR(36), FK ke `exchange_transactions.id`
* `bottle_type_id`: BIGINT UNSIGNED, FK ke `bottle_types.id`
* `quantity`: INT UNSIGNED
* `points_earned`: INT UNSIGNED (Kalkulasi: `quantity` * `points_value`)

### 3.7 Tabel `redemption_requests`
* `id`: CHAR(36), PK, UUID v7
* `user_id`: CHAR(36), FK ke `users.id`
* `points_used`: INT UNSIGNED
* `amount`: BIGINT UNSIGNED
* `method`: VARCHAR(20) (Enum: 'cash', 'ewallet')
* `bank_name`: VARCHAR(100)
* `recipient_account`: VARCHAR(100)
* `status`: VARCHAR(20), Default 'pending' (Enum: 'pending', 'approved', 'rejected')
* `rejection_note`: TEXT, Nullable
* `processed_at`: TIMESTAMP, Nullable
* `created_at`, `updated_at`: TIMESTAMP

---

## 4. Core Business Logic (Domain Driven)

### 4.1 Authentication & Authorization
* Gunakan satu form login (`/login`) untuk semua aktor. 
* *Controller* memverifikasi kredensial menggunakan `Auth::attempt()`. Jika sukses, periksa relasi `role_id` pengguna dan lakukan *redirect* ke *dashboard* masing-masing aktor.
* Registrasi publik (`/register`) HANYA melayani pembuatan akun Nasabah (`role: user`). 
* Saat Nasabah baru dibuat, sistem secara otomatis merelasikan `role_id` ke 'user' dan membuat *record* di tabel `user_profiles`, serta meng- *generate* nilai unik untuk `qr_code`.

### 4.2 User Management
* Pembuatan akun Pegawai ('employee') sepenuhnya dikontrol oleh Admin melalui *dashboard* Admin (Fitur CRUD Pegawai).
* Sistem otomatis memberikan `role_id` 'employee' saat Admin menyimpan data pegawai baru.

### 4.3 QR & Scan Process (Pegawai)
* Proses terdiri dari dua tahap pemindaian menggunakan *library* JavaScript (*client-side camera access*):
* **Tahap 1:** Pegawai memindai QR code dari *smartphone* Nasabah. *Backend* melakukan *lookup* ke kolom `user_profiles.qr_code` untuk menarik identitas Nasabah.
* **Tahap 2:** Pegawai memindai *barcode* fisik pada botol plastik. *Backend* melakukan *lookup* ke `bottle_types.barcode` untuk mengetahui `points_value`. Data diakumulasi sementara dalam sesi tampilan pegawai.

### 4.4 Transaction Processing
* Saat Pegawai menekan tombol "Konfirmasi", simpan data menggunakan **Atomic Database Transaction** di Laravel (`DB::transaction`).
* Buat satu *record* *parent* di `exchange_transactions`.
* Buat *multiple records* (sesuai jenis botol) di `exchange_transaction_details`.
* Lakukan penambahan ( *increment* ) pada `user_profiles.points_balance` sebesar `total_points`.

### 4.5 Reward Redemption
* **Validasi Pengajuan:** Nasabah mengajukan pencairan poin. *Backend* WAJIB memvalidasi (*Form Request Validation*) bahwa `points_used` <= `points_balance`.
* **Pembuatan Request:** Data disimpan di `redemption_requests` dengan status `pending`. Saldo poin Nasabah **belum dikurangi** pada tahap ini.
* **Proses Persetujuan (Admin):**
  * Jika di- *Approve*: Ubah status menjadi `approved`, catat waktu di `processed_at`, dan lakukan pemotongan (*decrement*) pada `user_profiles.points_balance`.
  * Jika di- *Reject*: Ubah status menjadi `rejected`, isi kolom `rejection_note`, saldo poin Nasabah tidak berubah.

---

## 5. UI/UX & Routing Structure

### Public Routes
* `GET /login`, `POST /login`
* `GET /register`, `POST /register`

### User (Nasabah) Routes
* `GET /user/dashboard` (Menampilkan total saldo poin aktif, ringkasan histori transaksi, tombol jalan pintas)
* `GET /user/qr` (Merender nilai `user_profiles.qr_code` menjadi visualisasi gambar QR)
* `GET /user/transactions` (Daftar histori penukaran botol)
* `GET /user/rewards` (Riwayat pengajuan *reward*)
* `GET /user/rewards/create`, `POST /user/rewards` (Form pencairan dengan metode Bank/E-Wallet)

### Employee (Pegawai) Routes
* `GET /employee/dashboard` (Ringkasan transaksi hari ini)
* `GET /employee/scan` (Akses kamera, interaksi scan QR User dan Barcode botol, form *submit* transaksi)
* `POST /employee/transactions` (Menerima konfirmasi dari proses *scan*)
* `GET /employee/transactions` (Daftar histori transaksi yang secara spesifik diproses oleh `employee_id` yang *login*)

### Admin Routes
* `GET /admin/dashboard` (Statistik sistem: total user, total transaksi, perputaran poin, *request pending*)
* `Resource /admin/employees` (CRUD data akun pegawai)
* `GET /admin/users` (Daftar seluruh nasabah terdaftar beserta detail saldo)
* `GET /admin/transactions` (Histori semua *exchange_transactions* di dalam sistem)
* `GET /admin/redemptions` (Daftar *request reward*)
* `POST /admin/redemptions/{id}/approve`
* `POST /admin/redemptions/{id}/reject`

---

## 6. AI Agent Implementation Directives
1. **Migration & Seeders:** Buat *migration* untuk 8 tabel sesuai urutan relasi. Buat `DatabaseSeeder.php` yang secara otomatis mengisi data statis `roles`, daftar produk awal di `bottle_types`, serta satu akun Admin pertama (sebab UI registrasi admin tidak ada).
2. **Security & Validation:** Gunakan *Form Request Validation* untuk semua *endpoint* `POST/PUT`. Pastikan *query scopes* diterapkan agar Nasabah dan Pegawai hanya bisa mengakses data transaksi yang dimiliki ID mereka sendiri. Admin dapat melihat semuanya.
3. **Responsiveness:** Pastikan seluruh *Blade views* menggunakan *utility classes* Tailwind CSS dengan pendekatan *mobile-first* (`sm:`, `md:`, `lg:`). Elemen seperti tabel harus dibungkus kontainer responsif dengan kemampunan *horizontal-scroll* jika dibuka di layar kecil.
4. **Error Handling:** Lempar pesan *error* secara spesifik (misal: "QR Code tidak ditemukan", "Poin tidak mencukupi", "Barcode tidak valid") dan tampilkan sebagai *flash messages* di UI.