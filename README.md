1. Nama dan Deskripsi Proyek
   Nama proyek: Gizantara

   Gizantara adalah website edukasi yang menyatukan bacaan seputar pangan, gizi, dan kesehatan, informasi komposisi gizi bahan pangan, serta alat untuk memahami kebiasaan sehari-hari. Pengguna dapat membaca artikel, menelusuri katalog bahan pangan, menghitung perkiraan kandungan gizi, dan mencatat kebiasaan hidup sehat seperti minum air, tidur, dan aktivitas fisik. <br>
   Proyek ini berfokus pada dua tujuan pembangunan berkelanjutan (SDGs): <br>
   SDG 2: Tanpa Kelaparan (Zero Hunger) <br>
   SDG 3: Kehidupan Sehat dan Sejahtera (Good Health and Well-being)


2. Latar Belakang dan Tujuan Proyek <br>
   Latar belakang <br>
   Pilihan pangan sehari-hari memengaruhi gizi dan kesehatan, namun informasi tentang kandungan gizi bahan pangan belum selalu mudah dikenali dan dipahami. Banyak orang juga belum memiliki cara sederhana untuk mencatat kebiasaan sehat seperti minum air, tidur, dan aktivitas fisik. Dibutuhkan ruang edukasi yang menyatukan bacaan, informasi pangan, dan alat untuk memahami kebiasaan sehari-hari.

   Tujuan <br>
   a. Menyediakan bacaan edukatif seputar pangan, gizi, dan kesehatan. <br>
   b. Menampilkan komposisi gizi bahan pangan per 100 gram. <br>
   c. Membantu memperkirakan kandungan gizi bahan pangan sesuai beratnya. <br>
   d. Mendorong kebiasaan sehat lewat pencatatan air minum, tidur, dan aktivitas fisik. <br>

3. DAFTAR FITUR DAN MODUL <br>
   a. Registrasi & Login (Pendaftaran akun (username, email, password minimal 8 karakter) dan masuk untuk membuka fitur pribadi). <br>
   b.	Artikel Edukasi	(Bacaan seputar pangan, gizi, dan kesehatan. Setiap artikel ditandai SDG terkait, memiliki tanggal terbit, dan halaman detail). <br>
   c. Katalog Pangan (Daftar bahan pangan dengan informasi gizi per 100 gram, kondisi penyajian (mis. direbus atau matang), dan pencarian berdasarkan nama bahan). <br>
   d. Kalkulator Gizi	(Memperkirakan kandungan gizi satu bahan pangan sesuai beratnya). <br>
   e.	Monitoring Pola Hidup	(Mencatat air minum, tidur, dan aktivitas fisik secara pribadi). <br>
   f.	Tentang Kami	(Halaman yang menjelaskan tujuan, manfaat, dan kontribusi Gizantara terhadap SDGs). <br>
   g.	Catatan Edukatif	(Pernyataan bahwa seluruh konten bersifat edukatif, bukan pengganti saran tenaga kesehatan). <br>

4. TEKNOLOGI YANG DIGUNAKAN <br>
   a. Bahasa Pemrograman (PHP, CSS, HTML, JS) <br>
   b. Database (MySql)

5. PANDUAN INSTALASI DAN KONFIGURASI
   INSTALASI
   Cara singkat menjalankan [Gizantara](https://github.com/hafizhsimpangan-alzaqi/gizantara) di komputer lokal. Disarankan memakai **XAMPP untuk Windows** atau **LAMPP/XAMPP untuk Linux** (Apache, PHP, MySQL/MariaDB, dan phpMyAdmin).
   ## 1. Clone repositori
      Pastikan **Git** dan **XAMPP/LAMPP** sudah terinstal, lalu jalankan:
      **Windows** (PowerShell):
      ```powershell
      cd C:\xampp\htdocs
      git clone https://github.com/hafizhsimpangan-alzaqi/gizantara.git
      ```

      **Linux** (Terminal):
      ```bash
      cd /opt/lampp/htdocs
      git clone https://github.com/hafizhsimpangan-alzaqi/gizantara.git
      ```

      > Di Linux, pastikan pengguna memiliki izin menulis ke folder `htdocs`.
      
      ## 2. Konfigurasi database
   1. Jalankan **Apache** dan **MySQL** melalui XAMPP Control Panel (Windows) atau `sudo /opt/lampp/lampp start` (Linux).
   2. Buka **http://localhost/phpmyadmin**.
   3. **Import** `process/common/schema.sql` untuk membuat database `sdgs_web`.
   4. Pilih database **`sdgs_web`**, kemudian **Import** `process/common/migrate_cr03.sql` **sekali** untuk melengkapi struktur database.
     
      > Langkah SQL tersebut untuk **instalasi baru**, bukan database yang sudah berisi data aplikasi.
      
      ## 3. Konfigurasi aplikasi
      Salin `.env.example` menjadi `.env` di folder proyek, lalu sesuaikan:
      ```ini
      DB_HOST=localhost
      DB_PORT=3306
      DB_NAME=sdgs_web
      DB_USER=root
      DB_PASS=
      BASE_URL=http://localhost/gizantara
      ```

      Nilai `root` tanpa password hanya contoh untuk XAMPP lokal; sesuaikan jika database menggunakan kredensial atau port lain.
      Buat folder **`view/uploads/artikel`** dan **`view/uploads/pangan`**, lalu pastikan keduanya dapat ditulis oleh Apache/PHP agar unggah gambar berfungsi.

6. PANDUAN MENJALANKAN APLIKASI
   ## Jalankan Gizantara
   Pastikan **Apache** dan **MySQL** aktif, lalu buka:
   **http://localhost/gizantara/**
   Gunakan menu **Daftar** untuk membuat akun pengguna. Jika halaman gagal dibuka, periksa layanan Apache/MySQL, file `.env`, dan hasil impor database.
