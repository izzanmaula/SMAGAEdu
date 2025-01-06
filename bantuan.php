<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700;1,900&family=PT+Serif:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Bantuan - SMAGAEdu</title>
</head>
<style>
    body{ 
        font-family: merriweather;
    }
    .color-web {
        background-color: rgb(218, 119, 86);
    }
    .btn {
                            transition: background-color 0.3s ease;
                            border: 0;
                            border-radius: 5px;
                        }
                        .btn:hover{
                            background-color: rgb(219, 106, 68);
                        }
</style>
<body>
    
     <!-- row col untuk halaman utama -->
     <div class="container-fluid">
        <div class="row">
            <div class="col-3 col-md-2 vh-100 p-4 shadow-sm menu-samping" style="background-color:rgb(238, 236, 226)">
                <style>
                    .menu-samping {
                        position: fixed;
                        width: 13rem;
                        z-index: 1000;
                    }
                </style>
                <div class="row gap-0">
                    <div class="ps-3 mb-3">
                        <a href="beranda.php" style="text-decoration: none; color: black;" class="d-flex align-items-center gap-2">
                            <img src="assets/smagaedu.png" alt="" width="30px">
                            <div>
                                <h1 class="display-5  p-0 m-0" style="font-size: 20px; text-decoration: none;">SMAGAEdu</h1>
                                <p class="p-0 m-0 text-muted" style="font-size: 12px;">LMS</p>
                            </div>
                        </a>
                    </div>  
                    <div class="col">
                        <a href="beranda.php" class="text-decoration-none text-black">
                        <div class="d-flex align-items-center rounded p-2" style="">
                            <img src="assets/beranda_outfill.png" alt="" width="50px" class="pe-4">
                            <p class="p-0 m-0">Beranda</p>
                        </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="cari.php" class="text-decoration-none text-black">
                        <div class="d-flex align-items-center rounded p-2" style="">
                            <img src="assets/pencarian.png" alt="" width="50px" class="pe-4">
                            <p class="p-0 m-0">Cari</p>
                        </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="ujian.php" class="text-decoration-none text-black">
                        <div class="d-flex align-items-center rounded p-2" style="">
                            <img src="assets/ujian_outfill.png" alt="" width="50px" class="pe-4">
                            <p class="p-0 m-0">Ujian</p>
                        </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="profil.php" class="text-decoration-none text-black">
                        <div class="d-flex align-items-center rounded p-2" style="">
                            <img src="assets/profil_outfill.png" alt="" width="50px" class="pe-4">
                            <p class="p-0 m-0">Profil</p>
                        </div>
                        </a>
                    </div>
                </div>
                <div class="row gap-0" style="margin-bottom: 15rem;">
                    <div class="col">
                        <a href="ai.php" class="text-decoration-none text-black">
                        <div class="d-flex align-items-center rounded p-2" style="">
                            <img src="assets/ai.png" alt="" width="50px" class="pe-4">
                            <p class="p-0 m-0">Gemini</p>
                        </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="bantuan.php" class="text-decoration-none text-black">
                        <div class="d-flex align-items-center rounded bg-white shadow-sm p-2" style="">
                            <img src="assets/bantuan_fill.png" alt="" width="50px" class="pe-4">
                            <p class="p-0 m-0">Bantuan</p>
                        </div>
                        </a>
                    </div>
                </div>
                <div class="row dropdown">
                    <div class="btn d-flex align-items-center gap-3 p-2 rounded-3 border dropdown-toggle" style="background-color: #F8F8F7;" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="assets/pp.png" alt="" class="rounded-circle p-0 m-0" width="30px">
                        <p class="p-0 m-0" style="font-size: 12px;">Halo, Ayundy</p>
                    </div>
                    <!-- dropdown menu option -->
                    <ul class="dropdown-menu" style="font-size: 12px;">
                        <li><a class="dropdown-item" href="#">Pengaturan</a></li>
                        <li><a class="dropdown-item" href="#">Keluar</a></li>
                      </ul>
                </div>
            </div>


            <!-- ini isi kontennya -->
            <div class="col pt-4 p-4 col-utama">
                <style>
                    .col-utama{
                        margin-left: 13rem;
                    }
                </style>
                <h3 class="p-0 m-0" style="font-weight: bold;">Dukungan Bantuan</h3>
                <p class="text-muted m-0 p-0">Apa yang bisa kami bantu?</p>
                <div class="d-grid p-3">
                    <div class="row mt-5 gap-2">
                        <div class="btn col border p-3 rounded-4 align-content-start text-center">
                            <img src="assets/kelas_bantuan.png" alt="" width="40px">
                            <p style="font-size: 13px;">Bagaimana cara membuat kelas?</p>
                        </div>
                        <div class="btn col border p-3 rounded-4 align-content-start text-center">
                            <img src="assets/ujian_bantuan.png" alt="" width="40px">
                            <p style="font-size: 13px;">bagaimana cara membuat ujian?</p>
                        </div>
                        <div class="btn col border p-3 rounded-4 align-content-start text-center">
                            <img src="assets/lupa_password_bantuan.png" alt="" width="40px">
                            <p style="font-size: 13px;">Lupa kata sandi dan ID</p>
                        </div>
                        <div class="btn col border p-3 rounded-4 align-content-start text-center">
                            <img src="assets/ai.png" alt="" width="40px">
                            <p style="font-size: 13px;">Bagaimana cara menggunakan Gemini?</p>
                        </div>
                        <div class="btn col border p-3 rounded-4 align-content-start text-center">
                            <img src="assets/profil_bantuan.png" alt="" width="40px">
                            <p style="font-size: 13px;">Bagaimana menambahkan foto profil pribadi?</p>
                        </div>
                    </div>
                    <div class="row mt-2 gap-2">
                        <div class="btn col border p-3 rounded-4 align-content-start text-center">
                            <img src="assets/privasi.png" alt="" width="40px">
                            <p style="font-size: 13px;">Kebijakan kami mengenai Privasi Anda</p>
                        </div>
                        <div class="btn col border p-3 rounded-4 align-content-start text-center">
                            <img src="assets/pembaruan.png" alt="" width="40px">
                            <p style="font-size: 13px;">Catatan Pembaruan SMAGAEdu</p>
                        </div>
                    </div>
                </div>
            </div>
            
</body>
</html>