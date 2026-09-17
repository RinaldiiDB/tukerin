| ID     | Fitur / Skenario                   | Kondisi / Input                | Expected Result                                                          |
| ------ | ---------------------------------- | ------------------------------ | ------------------------------------------------------------------------ |
| USR-01 | Login sebagai Nasabah              | Email & password valid         | Nasabah diarahkan ke dashboard user                                      |
| USR-02 | Registrasi Nasabah                 | Data registrasi valid          | Akun, profile, role, dan QR Code berhasil dibuat                         |
| USR-03 | Melihat QR Code                    | Akun Nasabah valid             | QR Code Nasabah berhasil ditampilkan                                     |
| USR-04 | Melihat histori transaksi          | User login                     | Hanya histori transaksi milik Nasabah yang ditampilkan                   |
| USR-05 | Pengajuan pencairan poin           | Poin mencukupi                 | Request pencairan dibuat dengan status `pending`                         |
| USR-06 | Pengajuan pencairan melebihi saldo | Poin melebihi saldo            | Pengajuan ditolak dengan pesan `"Poin tidak mencukupi"`                  |
| EMP-01 | Login sebagai Pegawai              | Email & password valid         | Pegawai diarahkan ke dashboard employee                                  |
| EMP-02 | Scan QR Nasabah                    | QR Code valid                  | Data Nasabah berhasil ditemukan                                          |
| EMP-03 | Scan barcode botol                 | Barcode valid                  | Jenis botol dan nilai poin berhasil ditemukan                            |
| EMP-04 | Konfirmasi transaksi               | QR + barcode botol valid       | Transaksi tersimpan dan saldo Nasabah bertambah                          |
| EMP-05 | Melihat histori transaksi          | Pegawai login                  | Hanya transaksi yang diproses Pegawai tersebut ditampilkan               |
| ADM-01 | Login sebagai Admin                | Email & password valid         | Admin diarahkan ke dashboard admin                                       |
| ADM-02 | Membuat akun Pegawai               | Data Pegawai valid             | Akun Pegawai berhasil dibuat dengan role `employee`                      |
| ADM-03 | Mengubah data Pegawai              | Data perubahan valid           | Data Pegawai berhasil diperbarui                                         |
| ADM-04 | Menghapus akun Pegawai             | ID Pegawai valid               | Akun Pegawai berhasil dihapus                                            |
| ADM-05 | Approve pencairan poin             | Request `pending`              | Status menjadi `approved` dan saldo poin berkurang                       |
| ADM-06 | Reject pencairan poin              | Request `pending` + alasan     | Status menjadi `rejected`, alasan tersimpan, saldo tidak berubah         |
| ADM-07 | Melihat seluruh transaksi          | Admin login                    | Seluruh transaksi sistem dapat ditampilkan                               |
| SYS-01 | Integrasi transaksi penukaran      | QR + barcode + data transaksi  | Data user, botol, transaksi, detail, dan saldo terintegrasi dengan benar |
| SYS-02 | Atomic transaction                 | Terjadi error saat penyimpanan | Seluruh perubahan dibatalkan (*rollback*)                                |
| SYS-03 | Pembatasan akses berdasarkan role  | User mengakses halaman Admin   | Akses ditolak sesuai role                                                |
