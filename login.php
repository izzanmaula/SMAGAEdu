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
</style>
<body style="background-color:rgb(238, 236, 226)" class="d-flex align-content-center justify-content-center">
    <div class="row mt-4 w-75">
        <div class="col bg-white pb-0 p-5 rounded-start-3 shadow">
            <img src="assets/smagaedu.png" alt="" width="60px" class="p-1 mb-2 border rounded-circle">
            <div>
                <p class="p-0 m-0">Selamat Datang</p>
                <h5 class="mb-2 p-0 m-0" style="font-weight: bold; font-size: 20px;">Aplikasi SMAGAEdu</h5>
                <p class="text-muted" style="font-size: 11px;">SMAGAEdu merupakan salah satu produk LMS yang digunakan SMP Muhammadiyah 2 Gatak dan SMA Muhammadiyah 5 Gatak,
                     silahkan login untuk dapat melanjutkan belajar.
                </p>
            </div>
            <div>
                <form method="POST" action="logic/login_back.php">
                    <div class="mb-3">
                      <label for="exampleInputEmail1" class="form-label" style="font-size: 12px;">Nama ID</label>
                      <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Masukkan ID kamu">
                      <div id="emailHelp" class="form-text"></div>
                    </div>
                    <div class="mb-3">
                      <label for="exampleInputPassword1" class="form-label" style="font-size: 12px;">Kata Sandi</label>
                      <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Masukkan Kata Sandi kamu">
                    </div>

                    <div class="d-flex">
                        <button type="submit" class="flex-fill color-web text-white p-2">Masuk</button>
                        <style>
                            button {
                                transition: background-color 0.3s ease;
                                border: 0;
                                border-radius: 5px;
                            }
                            button:hover{
                                background-color: rgb(219, 106, 68);
                            }
                        </style>
                    </div>
                  </form>
                  <div class="pt-3">
                    <p class="text-muted" style="font-size: 8px;">©️ Dikelola dan dikembangkan oleh Tim IT SMAGA - 2025</p>
                  </div>
            </div>
        </div>  
        <div class="col rounded-end-3 align-content-end shadow p-3 color-web">
          <div style="text-align: right; margin-bottom: 10px;">
            <img src="assets/logo.png" alt="" style="background-color: white;" class="border rounded-circle p-2" width="60px">
          </div>
            <h1 class="text-white p-0 m-0 text-end" style="font-size: 55px;">Satu <br>Platform Ciptakan Peluang.</h1>
        </div>
    </div>
</body>
</html>