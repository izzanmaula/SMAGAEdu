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
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700;1,900&family=PT+Serif:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">    <title>Masuk - Smagaedu</title>
</head>
<style>
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

body {
  background-color: rgb(238, 236, 226);
}

.logo {
  width: 60px;
  padding:10px;
}

@media screen and (max-width: 768px) {
    body {
        background-color: white;
    }
    .logo {
      width: 40px;
      padding:5px;
    }
}

</style>
<body class="d-flex align-content-center justify-content-center">
    <!-- Container utama dengan width yang responsif -->
    <div class="row mt-4 w-100 w-md-75 w-lg-75 mx-auto" style="max-width: 1200px;">
        <!-- Kolom kiri - Form login -->
        <div class="form col-12 col-md-6 bg-white p-3 p-md-4 p-lg-5 rounded-start-3 rounded-end-3 rounded-end-md-0 shadow-sm">
            <!-- Logo -->
            <div class="mb-md-2 mb-2">
              <img src="assets/smagaedu.png" alt="SMAGA Edu Logo" class="bg-white border rounded-circle logo">
                <img src="assets/logo.png" alt="Logo" class="bg-white border rounded-circle logo">
            </div>

            
            <!-- Header text -->
            <div class="mb-4">
                <p class="p-0 m-0 fs-6">Selamat Datang</p>
                <h5 class="mb-2 p-0 m-0 fs-5 fw-bold">Aplikasi SMAGAEdu</h5>
            </div>

            <!-- Alert error -->
            <?php if(isset($_GET['pesan'])) { ?>
                <div class="alert alert-danger fs-6 fade show animate-alert" role="alert">
                    <?php 
                        if($_GET['pesan'] == "password_salah") {
                            echo "Password yang Anda masukkan salah!";
                        } else if($_GET['pesan'] == "user_tidak_ditemukan") {
                            echo "User ID tidak ditemukan!";
                        }
                    ?>
                </div>
            <?php } ?>
            
            <!-- style untuk alert -->
            <style>
              .animate-alert {
                  animation: fadeIn 0.5s ease-in;
              }

              @keyframes fadeIn {
                  0% {
                      opacity: 0;
                      transform: translateY(-10px);
                  }
                  100% {
                      opacity: 1;
                      transform: translateY(0);
                  }
              }
            </style>

            <!-- Form -->
            <form method="POST" action="login_back.php">
                <div class="mb-3">
                    <label for="exampleInputEmail1" class="form-label fs-6">Nama ID</label>
                    <input type="text" name="userid" class="form-control" id="exampleInputEmail1" 
                           aria-describedby="emailHelp" placeholder="Masukkan ID kamu">
                </div>
                <div class="mb-4">
                    <label for="exampleInputPassword1" class="form-label fs-6">Kata Sandi</label>
                    <input type="password" name="password" class="form-control" id="exampleInputPassword1" 
                           placeholder="Masukkan Kata Sandi kamu">
                </div>

                <div class="d-flex">
                    <button type="submit" class="flex-fill color-web text-white p-2 rounded">Masuk</button>
                </div>
            </form>

            <!-- Footer -->
            <div class="pt-3">
                <p class="text-muted" style="font-size: 10px;">©️ Dikelola dan dikembangkan oleh Tim IT SMAGA - 2025</p>
            </div>
        </div>  

        <!-- Kolom kanan - Banner -->
        <div class="col-12 col-md-6 rounded-end-3 shadow p-3 p-md-4 p-lg-5 color-web d-none d-md-flex align-items-end">
            <h1 class="text-white text-end fs-1 fw-bold flex-fill">
                Satu <br>Platform <br>Ciptakan <br> Peluang.
            </h1>
        </div>
    </div>

    <style>
        .color-web {
            background-color: your-color-here;
        }
        button {
            transition: background-color 0.3s ease;
            border: 0;
        }
        button:hover {
            background-color: rgb(219, 106, 68);
        }
        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }
            .fs-6 {
                font-size: 0.875rem !important;
            }
            .form {
              border: 0!important;
              box-shadow: none!important;
            }
        }
    </style>
</body>

</html>