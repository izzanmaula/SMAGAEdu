<?php
session_start();
if(!isset($_SESSION['userid']) || $_SESSION['level'] != 'guru') {
    header("Location: ../index.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700;1,900&family=PT+Serif:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">    <title>Masuk - Smagaedu</title>
    <title>Beranda - SMAGAEdu</title>
</head>
<style>
        .custom-card {
            width: 300px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .custom-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }
        .custom-card .profile-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border: 3px solid white;
            margin-top: -40px;
        }
        .custom-card .card-body {
            text-align: left;
        }
        .merriweather-light {
        font-family: "Merriweather", serif;
        font-weight: 300;
        font-style: normal;
        }

        .merriweather-regular {
        font-family: "Merriweather", serif;
        font-weight: 400;
        font-style: normal;
        }

        .merriweather-bold {
        font-family: "Merriweather", serif;
        font-weight: 700;
        font-style: normal;
        }

        .merriweather-black {
        font-family: "Merriweather", serif;
        font-weight: 900;
        font-style: normal;
        }

        .merriweather-light-italic {
        font-family: "Merriweather", serif;
        font-weight: 300;
        font-style: italic;
        }

        .merriweather-regular-italic {
        font-family: "Merriweather", serif;
        font-weight: 400;
        font-style: italic;
        }

        .merriweather-bold-italic {
        font-family: "Merriweather", serif;
        font-weight: 700;
        font-style: italic;
        }

        .merriweather-black-italic {
        font-family: "Merriweather", serif;
        font-weight: 900;
        font-style: italic;
        }
        body{ 
            font-family: merriweather;
        }
        .color-web {
            background-color: rgb(218, 119, 86);
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
                        <a href="#" class="text-decoration-none text-black">
                        <div class="d-flex align-items-center rounded bg-white shadow-sm p-2" style="">
                            <img src="assets/beranda_fill.png" alt="" width="50px" class="pe-4">
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
                        <div class="d-flex align-items-center rounded p-2" style="">
                            <img src="assets/bantuan_outfill.png" alt="" width="50px" class="pe-4">
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
                        <li><a class="dropdown-item" href="logout.php">Keluar</a></li>
                      </ul>
                </div>
            </div>

            <!-- ini isi kontennya -->
            <div class="col p-4 col-utama">
                <style>
                    .col-utama{
                        margin-left: 13rem;
                        
                    }
                </style>
                <div class="row justify-content-between align-items-center">
                    <div class="col">
                        <h3 style="font-weight: bold;">Beranda</h3>
                    </div>
                    <button type="button" data-bs-toggle="modal" data-bs-target="#modal_tambah_kelas" class="btn col-auto text-end d-flex align-items-center border p-2 me-3" style="padding: 5px 10px; border-radius: 5px;">
                        <img src="assets/tambah.png" alt="Tambah" width="25px" class="me-2">
                        <p class="m-0">Buat Kelas</p>
                    </button>
                </div>

                <div class="d-flex gap-3">
                    <div class="d-flex pt-3">
                        <div class="custom-card">
                            <img src="assets/bg.jpg" alt="Background Image">
                            <div class="card-body" style="text-align: right; padding-right: 30px; background-color: white;">
                                <a href="profil.html">
                                    <img src="assets/pp.png" alt="Profile Image" class="profile-img rounded-4 border-0 bg-white">
                                </a>
                            </div>
                            <div class="ps-3">
                                <h5 class="mt-3 p-0 mb-1" style="font-weight: bold; font-size: 20px;">Pendidikan Agama Islam</h5>
                                <p class="p-0 m-0" style="font-size: 12px;">Ayundy Anditaningrum, S.Ag</p>
                            </div>
                            <div class="d-flex btn-group gap-2 p-3">
                                <a href="../kelas.php" class="color-web btn btn w-45 rounded" style="text-decoration: none; color: white;">Masuk</a>
                            </div>
                            <style>
                            .btn {
                                transition: background-color 0.3s ease;
                                border: 0;
                                border-radius: 5px;
                            }
                            .btn:hover{
                                background-color: rgb(219, 106, 68);
                            }

                            </style>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <!-- modal untuk buat kelas -->
     <!-- Modal -->
     <div class="modal fade" id="modal_tambah_kelas" tabindex="-1" aria-labelledby="label_tambah_kelas" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
            <h1 class="modal-title fs-5" id="label_tambah_kelas" style="font-weight: bold;">Buat Kelas</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <form action="">
                <div class="mb-3">
                    <div class="dropdown">
                            <label for="dropdownField" class="form-label" style="font-size: 14px;">Pilih mata pelajaran Anda</label>
                            <select class="form-select" id="dropdownField" aria-label="Default select example">
                                <option selected>Pilih salah satu</option>
                                <option value="1">Bahasa Indonesia</option>
                                <option value="2">Matematika</option>
                                <option value="3">Ilmu Pengetahuan Alam</option>
                            </select>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="dropdown">
                            <label for="dropdownField" class="form-label" style="font-size: 14px;">Kelas apa yang ingin Anda tambahkan?</label>
                            <select class="form-select" id="dropdownField" aria-label="Default select example">
                                <option selected>Pilih salah satu</option>
                                <option value="1">Kelas 7</option>
                                <option value="2">Kelas 8</option>
                                <option value="3">Kelas 9</option>
                            </select>
                    </div>
                </div>
                <div class="container mb-3 p-0">
                    <div class="form-group">
                        <label for="bg_kelas" style="font-size: 14px;">Tambahkan gambar latar belakang kelas Anda</label>
                        <input type="file" class="form-control" id="bg_kelas">    
                    </div>
                </div>
                <div class="container mb-3 p-0">
                    <div class="form-group">
                        <label for="bg_kelas" style="font-size: 14px;">Deskripsi kelas Anda</label>
                        <div class="form-floating">
                            <textarea class="form-control" style="width: 100%;" placeholder="Leave a comment here" id="floatingTextarea"></textarea>
                            <label for="floatingTextarea">Kelas ini bertujuan untuk ...</label>
                        </div>
                    </div>
                </div>
            </form>
            </div>
            <div class="modal-footer d-flex">
            <button type="button" class="btn color-web text-white flex-fill">Buat</button>
            </div>
        </div>
        </div>
    </div>



</body>
</html>