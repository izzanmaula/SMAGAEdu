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
    <title>Profil - SMAGAEdu</title>
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
        .btn {
                                transition: background-color 0.3s ease;
                                border: 0;
                                border-radius: 5px;
                            }
                            .btn:hover{
                                background-color: rgb(219, 106, 68);
                                color: white;
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
                        <div class="d-flex align-items-center rounded shadow-sm bg-white p-2" style="">
                            <img src="assets/profil_fill.png" alt="" width="50px" class="pe-4">
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
                        <li><a class="dropdown-item" href="#">Keluar</a></li>
                      </ul>
                </div>
            </div>


            <!-- ini isi kontennya -->
            <div class="col pt-0 p-4 col-utama">
                <style>
                    .col-utama{
                        margin-left: 13rem;
                    }
                </style>
                <div style="background-image: url(assets/bg-profil.png); height: 300px; padding-top: 200px; margin-top: 15px; background-position: center; position: relative;" class="rounded text-white shadow-lg latar-belakang">
                    <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.5); z-index: 0;" class="rounded"></div>
                    <div class="ps-3" style="position: relative; z-index: 2;"></div>
                </div>
                <div style="text-align: center;">
                    <img src="assets/pp.png" alt="" width="200px" class="rounded-circle" style="background-color: white; margin-top: -150px; z-index: 10; position: relative; border: 3px solid white;">
                </div>
                <div class="text-center mt-1">
                    <h3 class="p-0 m-1">Ayundy Anditaningrum, S.Ag </h3>
                    <p class="p-0 m-0">Guru Pendidikan Agama Islam</p>
                </div>
                <div class="mt-2 text-center">
                    <button class="btn border bi-pencil-square" style="font-size: 12px;" data-bs-toggle="modal" data-bs-target="#gantinama">    Edit Nama Anda</button>
                    <button class="btn border bi-image" style="font-size: 12px;" data-bs-toggle="modal" data-bs-target="#gantifoto">    Edit Foto dan Latar Belakang</button>
                    <button class="btn border bi-asterisk" style="font-size: 12px;" data-bs-toggle="modal" data-bs-target="#gantilebih">    Edit Rekam Anda</button>
                </div>
                <div class="px-5">
                    <hr class="text-muted">
                </div>
                <div class="col d-flex justify-content-center mt-2">
                    <div class="row ">
                        <!-- pendidikan sekolah sebelum dan saat ini -->
                        <div class="">
                            <div class="d-flex gap-3">
                                <!-- pendidikan sebelumnya -->
                                <div class="border rounded-4 p-3 flex-fill d-flex gap-2 align-items-center">
                                    <img src="assets/lulusan.png" alt="" width="35px" height="35px" class="rounded">
                                    <div>
                                        <p style="font-size: 12px; font-weight: bold;" class="p-0 m-0">Lulusan</h6>
                                        <p class="p-0 m-0">Universitas Muhammadiyah Surakarta</p>        
                                    </div>
                                </div>
                                <div class="border rounded-4 p-3 flex-fill d-flex gap-2 align-items-center">
                                    <img src="assets/jabatan.png" alt="" width="35px" height="35px" class="rounded">
                                    <div>
                                        <p style="font-size: 12px; font-weight: bold;" class="p-0 m-0">Jabatan Saat Ini</h6>
                                        <p class="p-0 m-0">Operator</p>        
                                    </div>
                                </div>    
                            </div>
                        </div>
                        <!-- sertifikasi -->
                         <div class="d-flex mt-3">
                            <div class="border rounded-4 p-3 flex-fill">
                                <div class="d-flex gap-2 align-items-center">
                                    <img src="assets/sertifikat.png" alt="" width="35px" height="35px" class="rounded">
                                    <div>
                                        <p style="font-size: 12px; font-weight: bold;" class="p-0 m-0">Sertifikasi</h6>
                                        <p style="font-size: 12px;" class="text-muted p-0 m-0">Sertifikat yang dimiliki</p>        
                                    </div>    
                                </div>
                                <div class="p-3">
                                    <li>Sertifikasi Kompetensi Pedagogik dan Prosesional (SKPP)</li>
                                    <li>TOEP UMS Bahasa Inggris</li>
                                    <li>Cisco Certified Network Associate (CCNA)</li>
                                </div>
                            </div>
                         </div>

                        <!-- riwayat publikasi -->
                        <div class="d-flex mt-3">
                            <div class="border rounded-4 p-3 flex-fill">
                                <div class="d-flex gap-2 align-items-center">
                                    <img src="assets/publikasi.png" alt="" width="35px" height="35px" class="rounded">
                                    <div>
                                        <p style="font-size: 12px; font-weight: bold;" class="p-0 m-0">Publikasi</h6>
                                        <p style="font-size: 12px;" class="text-muted p-0 m-0">Riwayat penulisan yang telah di publikasi</p>        
                                    </div>    
                                </div>
                                <div class="p-3">
                                    <li>Pengembangan Sistem Informasi Manajemen Perpustakaan Berbasis Web. Jurnal Informatika, 12(2): 45-58.</li>
                                    <li>Analisis Sentimen terhadap Ulasan Produk Elektronik di Media Sosial (bersama dengan Siti Fatimah). Jurnal Teknologi Informasi, 10(1), doi: 10.1234/jti.10.1.</li>
                                    <li>Cisco Certified Network Associate (CCNA)</li>
                                </div>
                            </div>
                         </div>

                        <!-- riwayat publikasi -->
                        <div class="d-flex mt-3">
                            <div class="border rounded-4 p-3 flex-fill">
                                <div class="d-flex gap-2 align-items-center">
                                    <img src="assets/proyek.png" alt="" width="35px" height="35px" class="rounded">
                                    <div>
                                        <p style="font-size: 12px; font-weight: bold;" class="p-0 m-0">Riwayat Proyek</h6>
                                        <p style="font-size: 12px;" class="text-muted p-0 m-0">Riwayat proyek pendidikan yang telah di ikuti</p>        
                                    </div>    
                                </div>
                                <div class="p-3">
                                    <li>Pengembangan Sistem Informasi Manajemen Perpustakaan Berbasis Web. Jurnal Informatika, 12(2): 45-58.</li>
                                    <li>Analisis Sentimen terhadap Ulasan Produk Elektronik di Media Sosial (bersama dengan Siti Fatimah). Jurnal Teknologi Informasi, 10(1), doi: 10.1234/jti.10.1.</li>
                                    <li>Cisco Certified Network Associate (CCNA)</li>
                                </div>
                            </div>
                         </div>
                        </div>

                        
                    </div>
                    </div>

                </div>
            </div>

            
            <!-- modal untuk ganti nama -->
            <div class="modal fade" id="gantinama" tabindex="-1" aria-labelledby="modalgantinamalabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalgantinamalabel">Edit Profil</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div>
                            <p style="font-size: 14px;">Tuliskan Nama Lengkap dan gelar Anda</p>
                        </div>
                        <div>
                            <form class="form-floating">
                                <input type="email" class="form-control" id="floatingInputValue" placeholder="name@example.com" value="Ayundy Anditaningrum, S.Ag">
                                <label for="floatingInputValue">Nama dan Gelar</label>
                              </form>
                        </div>
                    </div>
                    <div class="modal-footer d-flex">
                    <button type="button" class="btn color-web flex-fill" style="color: white;">Simpan Perubahan</button>
                    </div>
                </div>
                </div>
            </div>

            <!-- modal untuk ganti latar gambar dan profil -->
            <div class="modal fade" id="gantifoto" tabindex="-1" aria-labelledby="modalgantifoto" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalgantifoto">Edit Latar Belakang Anda</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex p-2 gap-3">
                            <div class="btn flex-fill ganti-latar text-center p-3 rounded-2">
                                <img src="assets/profil_fill.png" width="40px" alt="">
                                <p style="font-size: 13px;">Ubah Foto</p>
                            </div>
                            <div class="btn flex-fill text-center p-3 ganti-latar rounded-2">
                                <img src="assets/background.png" alt="" width="40px">
                                <p style="font-size: 13px;">Ubah Latar Belakang</p>
                            </div>
                            <!-- style untuk buttton edit latar belakang -->
                             <style>
                                .ganti-latar{
                                    background-color: rgb(238, 238, 238);
                                    transform: background-color 0.3s ease;
                                }
                                .ganti-latar:hover{
                                    background-color: rgb(218, 119, 86);
                                }
                             </style>

                        </div>
                    </div>
                </div>
                </div>
            </div> 
            
                        <!-- modal buat ganti deskripsi siswa -->
                        <div class="modal fade" id="gantilebih" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalgantinamalabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                <h1 class="modal-title fs-5" id="modalgantinamalabel">Edit Deskripsi Siswa</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <!-- Sertifikasi sebelumnya -->
                                    <div class="mt-3">
                                        <label for="pendidikansebelumnya" class="form-label" style="font-size: 13px;">Riwayat Pendidikan</label>
                                        <div class="input-group mb-2">
                                            <span class="input-group-text" id="sertifikasi-pertama">S1</span>
                                            <input type="text" id="pendidikansebelumnya" class="form-control">    
                                        </div>      
                                        <div class="input-group mb-2">
                                            <span class="input-group-text" id="sertifikasi-pertama">S2</span>
                                            <input type="text" id="pendidikansebelumnya" class="form-control">    
                                        </div>             
                                        <div class="input-group mb-2">
                                            <span class="input-group-text" id="sertifikasi-pertama">Lainya</span>
                                            <input type="text" id="pendidikansebelumnya" class="form-control">    
                                        </div>                    
                                     </div>
                                    <!-- jabatan -->
                                    <div class="mt-3">
                                        <label for="pendidikansebelumnya" class="form-label p-0 m-0" style="font-size: 13px;">Jabatan Sekolah Saat ini</label>
                                        <div id="jabatan" class="form-text p-0 m-0" style="font-size: 12px;">Pilih jabatan tertinggi Anda</div>
                                        <select class="form-select" aria-label="Default select example" id="pendidikansebelumnya" aria-describedby="jabatan">
                                            <option selected>Pilih salah satu</option>
                                            <option value="1">Kepala Sekolah</option>
                                            <option value="2">Wakil Kepala Sekolah</option>
                                            <option value="3">Bag. Kurikulum</option>
                                            <option value="3">Bag. Kesiswaan</option>
                                            <option value="3">Kepala Tata Usaha</option>
                                            <option value="3">Wali Kelas</option>
                                            <option value="3">Bag. Ekonomi Bisnis</option>
                                            <option value="3">Staf IT</option>
                                            <option value="3">Staf TU</option>
                                          </select>  
                                    </div>
                                    <!-- Sertifikasi sebelumnya -->
                                     <div class="mt-3">
                                        <label for="pendidikansebelumnya" class="form-label p-0 m-0" style="font-size: 13px;">Sertifikasi</label>
                                        <div id="sertifikasi" class="form-text p-0 m-0" style="font-size: 12px;">Pilih sertifikasi terbaru Anda</div>
                                        <div class="input-group mb-2">
                                            <span class="input-group-text" id="sertifikasi-pertama">1</span>
                                            <input type="text" id="pendidikansebelumnya" class="form-control">    
                                        </div>      
                                        <div class="input-group mb-2">
                                            <span class="input-group-text" id="sertifikasi-pertama">2</span>
                                            <input type="text" id="pendidikansebelumnya" class="form-control">    
                                        </div>             
                                        <div class="input-group mb-2">
                                            <span class="input-group-text" id="sertifikasi-pertama">3</span>
                                            <input type="text" id="pendidikansebelumnya" class="form-control">    
                                        </div>                    
                                     </div>

                                    <!-- Publikasi sebelumnya -->
                                    <div class="mt-3">
                                        <label for="pendidikansebelumnya" class="form-label p-0 m-0" style="font-size: 13px;">Publikasi</label>
                                        <p class="text-muted p-0 m-0" style="font-size: 12px;">Pilih publikasi terbaru Anda</p>
                                        <div class="input-group mb-2">
                                            <span class="input-group-text" id="sertifikasi-pertama">1</span>
                                            <input type="text" id="pendidikansebelumnya" class="form-control">    
                                        </div>      
                                        <div class="input-group mb-2">
                                            <span class="input-group-text" id="sertifikasi-pertama">2</span>
                                            <input type="text" id="pendidikansebelumnya" class="form-control">    
                                        </div>             
                                        <div class="input-group mb-2">
                                            <span class="input-group-text" id="sertifikasi-pertama">3</span>
                                            <input type="text" id="pendidikansebelumnya" class="form-control">    
                                        </div>                    
                                     </div>

                                    <!-- Proyek sebelumnya -->
                                    <div class="mt-3">
                                        <label for="pendidikansebelumnya" class="form-label" style="font-size: 13px;">Proyek</label>
                                        <p class="text-muted p-0 m-0" style="font-size: 12px;">Pilih proyek terbaru Anda</p>
                                        <div class="input-group mb-2">
                                            <span class="input-group-text" id="sertifikasi-pertama">1</span>
                                            <input type="text" id="pendidikansebelumnya" class="form-control">    
                                        </div>      
                                        <div class="input-group mb-2">
                                            <span class="input-group-text" id="sertifikasi-pertama">2</span>
                                            <input type="text" id="pendidikansebelumnya" class="form-control">    
                                        </div>             
                                        <div class="input-group mb-2">
                                            <span class="input-group-text" id="sertifikasi-pertama">3</span>
                                            <input type="text" id="pendidikansebelumnya" class="form-control">    
                                        </div>                    
                                     </div>

                                </div>
                                <div class="modal-footer d-flex">
                                <button type="button" class="btn color-web flex-fill" style="color: white;">Simpan Perubahan</button>
                                </div>
                            </div>
                            </div>
                        </div>

</body>
</html>