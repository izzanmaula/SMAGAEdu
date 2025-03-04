<?php
require "koneksi.php";

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700;1,900&family=PT+Serif:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    <title>Masuk - Smagaedu</title>
</head>
<style>
    body {
        font-family: merriweather;
    }

    .color-web {
        background-color: rgb(218, 119, 86);
    }

    .color-web:hover {
        background-color: rgb(231, 95, 50);
    }

    body {
        background-color: rgb(238, 236, 226);
    }

    .logo {
        width: 60px;
        padding: 10px;
    }

    @media screen and (max-width: 768px) {
        body {
            background-color: white;
        }

        .logo {
            width: 40px;
            padding: 5px;
        }
    }
</style>

<!-- animasi modal -->
<style>
    .modal-content {
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .modal .btn {
        font-weight: 500;
        transition: all 0.2s;
    }

    .modal .btn:active {
        transform: scale(0.98);
    }

    .modal.fade .modal-dialog {
        transform: scale(0.95);
        transition: transform 0.2s ease-out;
    }

    .modal.show .modal-dialog {
        transform: scale(1);
    }
</style>


<body class="min-vh-100">
    <div class="container py-5 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-12 col-md-8 col-lg-10">
                <div class="card shadow-sm" style="border-radius: 20px; background: rgba(255, 255, 255, 0.95);">
                    <div class="row g-0">
                        <div class="col-md-6">
                            <div class="card-body p-4">
                                <!-- Logo -->
                                <div class="text-center mb-4">
                                    <img src="assets/smagaedu.png" alt="SMAGA Edu Logo" class="bg-white rounded-circle logo me-2">
                                    <img src="assets/logo.png" alt="Logo" class="bg-white rounded-circle logo">
                                </div>

                                <!-- Header -->
                                <div class="text-center mb-4">
                                    <p class="mb-1">Halo, Selamat Datang</p>
                                    <h5 class="fw-bold">Portal SMAGAEdu LMS</h5>
                                </div>

                                <!-- Alert Modal -->
                                <?php if (isset($_GET['pesan'])) { ?>
                                    <div class="modal fade" id="alertModal" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content" style="border-radius: 15px;">
                                                <div class="modal-body text-center p-4">
                                                    <div class="mb-3">
                                                        <i class="bi bi-exclamation-circle text-danger" style="font-size: 3rem;"></i>
                                                    </div>
                                                    <h5 class="mb-3">Gagal Masuk</h5>
                                                    <p class="mb-4">
                                                        <?php
                                                        if ($_GET['pesan'] == "password_salah") {
                                                            echo "Password yang Anda masukkan salah!";
                                                        } else if ($_GET['pesan'] == "user_tidak_ditemukan") {
                                                            echo "Kami tidak menemukan akunmu, silahkan coba lagi atau hubungi Tim IT.";
                                                        }
                                                        ?>
                                                    </p>
                                                </div>
                                                <div class="modal-footer d-flex">
                                                    <button type="button" class="btn px-4 flex-fill" data-bs-dismiss="modal" style="border-radius: 10px; background-color:rgb(218, 119, 86); color:white;">OK</button>

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            var alertModal = new bootstrap.Modal(document.getElementById('alertModal'));
                                            alertModal.show();
                                        });
                                    </script>
                                <?php } ?>

                                <!-- Form -->
                                <form method="POST" action="login_back.php">
                                    <div class="mb-3">
                                        <input type="text" name="userid" class="form-control form-control-lg" placeholder="Nama ID" style="border-radius: 12px;">
                                    </div>
                                    <div class="mb-4">
                                        <input type="password" name="password" class="form-control form-control-lg" placeholder="Kata Sandi" style="border-radius: 12px;">
                                    </div>
                                    <button type="submit" class="btn w-100 text-white" style="background: rgb(218, 119, 86); border-radius: 12px; padding: 12px;">Masuk</button>
                                </form>

                                <!-- Lupa Sandi Link -->
                                <div class="text-center mt-3">
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#lupaPasswordModal" class="text-decoration-none" style="color: #666;">
                                        <small>Butuh Bantuan?</small>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <!-- Image Column -->
                        <div class="col-md-6 d-none d-md-block" style="min-height: 500px;">
                            <div id="loginCarousel" class="carousel slide carousel-fade h-100" data-bs-ride="carousel">
                                <div class="carousel-inner h-100">
                                    <div class="carousel-item active h-100">
                                        <div class="position-relative h-100">
                                            <div class="position-absolute w-100 h-100" style="background: rgba(0,0,0,0.3);  border-radius: 0 20px 20px 0;"></div>
                                            <img src="assets/bg/bg1.jpg" alt="Login Image" class="w-100 h-100" style="object-fit: cover; border-radius: 0 20px 20px 0;">
                                            <div class="carousel-caption text-start" style="bottom: 20px; left: 20px;">
                                                <h5 class="p-0 m-0 fw-bold">Pelatihan Baris Berbaris</h5>
                                                <p class="p-0 m-0">Pelatihan PBB yang langsung dikomandoi oleh Rakanda dan Adinda Hizbul Wathan</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="carousel-item h-100">
                                        <div class="position-relative h-100">
                                            <div class="position-absolute w-100 h-100" style="background: rgba(0,0,0,0.3);  border-radius: 0 20px 20px 0;"></div>
                                            <img src="assets/bg/bg2.jpg" alt="Login Image" class="w-100 h-100" style="object-fit: cover; border-radius: 0 20px 20px 0;">
                                            <div class="carousel-caption text-start" style="bottom: 20px; left: 20px;">
                                                <h5 class="p-0 m-0 fw-bold">Swafoto Guru</h5>
                                                <p class="p-0 m-0">Potret Interaksi lingkungan sekolah antar guru</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="carousel-item h-100">
                                        <div class="position-relative h-100">
                                            <div class="position-absolute w-100 h-100" style="background: rgba(0,0,0,0.3);  border-radius: 0 20px 20px 0;"></div>
                                            <img src="assets/bg/bg3.jpg" alt="Login Image" class="w-100 h-100" style="object-fit: cover; border-radius: 0 20px 20px 0;">
                                            <div class="carousel-caption text-start" style="bottom: 20px; left: 20px;">
                                                <h5 class="p-0 m-0 fw-bold">Ekstrakulikuler Air Softgun</h5>
                                                <p class="p-0 m-0">Suasana pelatihan ekstrakulikuler siswa Air Softgun yang tegang dan penuh konsentrasi</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="carousel-item h-100">
                                        <div class="position-relative h-100">
                                            <div class="position-absolute w-100 h-100" style="background: rgba(0,0,0,0.3);  border-radius: 0 20px 20px 0;"></div>
                                            <img src="assets/bg/bg4.jpg" alt="Login Image" class="w-100 h-100" style="object-fit: cover; border-radius: 0 20px 20px 0;">
                                            <div class="carousel-caption text-start" style="bottom: 20px; left: 20px;">
                                                <h5 class="p-0 m-0 fw-bold">Outing Class</h5>
                                                <p class="p-0 m-0">Kegiatan pembelajaran luar ruangan kelas dalam pengenalan sejarah budaya keris di Indonesia, Museum Keris.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="carousel-item h-100">
                                        <div class="position-relative h-100">
                                            <div class="position-absolute w-100 h-100" style="background: rgba(0,0,0,0.3);  border-radius: 0 20px 20px 0;"></div>
                                            <img src="assets/bg/bg5.jpg" alt="Login Image" class="w-100 h-100" style="object-fit: cover; border-radius: 0 20px 20px 0;">
                                            <div class="carousel-caption text-start" style="bottom: 20px; left: 20px;">
                                                <h5 class="p-0 m-0 fw-bold">Ekstrakulikuler Memasak</h5>
                                                <p class="p-0 m-0">Potret bahagia siswa berkolaborasi bersama dalam memasak</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Navigation buttons -->
                                <button class="carousel-control-prev" type="button" data-bs-target="#loginCarousel" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Previous</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#loginCarousel" data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Next</span>
                                </button>
                            </div>
                        </div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    var myCarousel = new bootstrap.Carousel(document.getElementById('loginCarousel'), {
        interval: 3000,
        ride: 'carousel'
    });
});
</script>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <style>
        body {
            background:rgb(244, 232, 220);
        }

        .form-control {
            border: 1px solid #eee;
            padding: 12px;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #FF9F43;
        }

        @media (max-width: 768px) {
            body {
                background: white;
            }
            .card {
                border: none;
                box-shadow: none !important;
            }
            .container {
                padding: 0;
            }
        }
    </style>
</body>

</html>