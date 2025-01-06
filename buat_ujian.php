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
    <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"
  />

    <title>Buat Soal - SMAGAEdu</title>
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
            <div class="col-md-2 vh-100 p-4 shadow-sm  menu-samping" style="background-color:rgb(238, 236, 226)">
                <style>
                    .menu-samping{
                        position:fixed;
                        width: 13rem;
                        z-index: 1000;
                    }
                </style>
                <div class="row gap-0">
                    <div class="ps-3 mb-3">
                        <a href="#" style="text-decoration: none; color: black;" class="d-flex align-items-center gap-2">
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
                        <a href="ujian.php" class="text-decoration-none text-black">
                        <div class="d-flex align-items-center rounded bg-white shadow-sm p-2" style="">
                            <img src="assets/ujian_fill.png" alt="" width="50px" class="pe-4">
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
                <div class="row gap-0" style="margin-bottom: 14rem;">
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
            <div class="col p-4 ">
                <div class="row justify-content-between">
                    <div class="col-8 col-utama">
                        <style>
                            .col-utama {
                                margin-left: 13rem;
                                width: 40rem;
                            }
                        </style>
                        <div id="previewArea" class="mt-4 text-center"></div>
                        <h3 style="font-weight: bold;">Buat Ujian</h3>                    
                        <p style="font-size: 12px;">Anda dapat menggunakan salah satu dari metode cara untuk membuat soal, Anda dapat mengupload soal dari word dan biarkan lah kami mengurusnya atau buat soal dalam laman web kami.</p>
                        <!-- upload file soal -->
                        <div class="container my-3 d-flex p-0">
                            <div class="upload-box p-5 text-center flex-fill" id="uploadBox" style="max-width: 700px;">
                                <p id="uploadText" class="mb-0" style="font-size: 12px;">
                                    <strong>Upload Soal</strong><br>
                                    Pastikan Upload Soal sesuai dengan arahan atau template yang telah disediakan
                                </p>
                            </div>
                        </div>
                        <!-- style kotak upload -->
                         <style>
                            .upload-box {
                                border: 2px dashed #ccc;
                                background-color: #f8f9fa;
                                transition: background-color 0.3s ease, border-color 0.3s ease;
                            }

                            .upload-box.dragging {
                                background-color: #e0f7fa;
                                border-color: #00acc1;
                            }
                         </style>
                         <!-- logika js upload file -->
                         <script>
                            const uploadBox = document.getElementById("uploadBox");
                            const uploadText = document.getElementById("uploadText");
                            const previewArea = document.getElementById("previewArea");
                        
                            // Drag and drop events
                            uploadBox.addEventListener("dragover", (e) => {
                                e.preventDefault();
                                uploadBox.classList.add("dragging");
                                uploadText.innerHTML = "<strong>Seret kesini</strong>";
                            });
                        
                            uploadBox.addEventListener("dragleave", () => {
                                uploadBox.classList.remove("dragging");
                                uploadText.innerHTML = `
                                    <strong>Upload Soal</strong><br>
                                    Pastikan Upload Soal sesuai dengan arahan atau template yang telah disediakan
                                `;
                            });
                        
                            uploadBox.addEventListener("drop", (e) => {
                                e.preventDefault();
                                uploadBox.classList.remove("dragging");
                                const file = e.dataTransfer.files[0];
                        
                                if (file && file.type === "application/vnd.openxmlformats-officedocument.wordprocessingml.document") {
                                    // Display the file name
                                    previewArea.innerHTML = `<div class="alert alert-success">File yang diunggah: <strong>${file.name}</strong></div>`;
                                } else {
                                    previewArea.innerHTML = `<div class="alert alert-danger">Harap unggah file MS Word (.docx)</div>`;
                                }
                            });
                        </script>

                        <div>
                            <hr>
                        </div>
                        <!-- buat soal manual -->
                         <div>
                            <div class="mb-3">
                                <div class="dropdown">
                                        <label for="dropdownField" class="form-label" style="font-size: 12px;">Pilih mata pelajaran Anda</label>
                                        <select class="form-select" id="dropdownField" aria-label="Default select example">
                                            <option selected>Pilih salah satu</option>
                                            <option value="1">Bahasa Indonesia</option>
                                            <option value="2">Matematika</option>
                                            <option value="3">Ilmu Pengetahuan Alam</option>
                                        </select>
                                </div>
                            </div>   
                            <div class="mb-3 p-0">
                                <div class="form-group">
                                    <label for="bg_kelas" style="font-size: 12px;" class="form-label">Tanggal berapa Mata Pelajaran Anda diujikan?</label>
                                    <div class="form-floating">
                                        <div class="input-group input-group-sm mb-3">
                                            <span class="input-group-text bi-calendar" id="inputGroup-sizing-sm"></span>
                                            <input type="date" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                          </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label for="bg_kelas" style="font-size: 12px;" class="form-label">Lalu jam berapa?</label>
                                <div class="input-group" style="font-size: 12px;">
                                    <input type="time" aria-label="First name" class="form-control">
                                    <input type="time" aria-label="Last name" class="form-control">
                                </div>                     
                            </div>
                         </div>

                         <!-- soalnya -->
                         <div class="container my-5">
                            <h4 class="mb-4">Soal Anda</h4>
                            <div class="row" id="soalContainer">
                                <!-- Soal cards akan ditambahkan di sini -->
                            </div>
                            <div class="text-center mt-4">
                                <div class="add-circle" id="addSoal">
                                    <span class="fs-4 fw-bold">+</span>
                                </div>
                                <!-- style tombol + -->
                                 <style>
                                     .soal-card {
                                        background-color: #e9ecef;
                                        border-radius: 8px;
                                        padding: 20px;
                                        text-align: center;
                                        margin-bottom: 20px;
                                    }

                                    .add-circle {
                                        width: 60px;
                                        height: 60px;
                                        background-color: #f8f9fa;
                                        border: 2px solid #bbb;
                                        border-radius: 50%;
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                        cursor: pointer;
                                        transition: all 0.3s ease;
                                    }

                                    .add-circle:hover {
                                        background-color: #e0f7fa;
                                        border-color: #00acc1;
                                    }
                                 </style>
                            </div>
                        </div>
                        
                        <!-- Modal Pilih Tipe Soal -->
                        <div class="modal fade" id="pilihTipeModal" tabindex="-1" aria-labelledby="pilihTipeModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="pilihTipeModalLabel">Pilih Tipe Soal Anda</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-center">
                                        <button id="btnPilihanGanda" class="btn btn-primary w-100 mb-2">Pilihan Ganda</button>
                                        <button id="btnUraian" class="btn btn-secondary w-100">Uraian</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Modal Form Soal -->
                        <div class="modal fade" id="formSoalModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"  aria-labelledby="formSoalModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="formSoalModalLabel">Isi Soal</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- Form untuk Pilihan Ganda -->
                                        <div id="formPilihanGanda" class="d-none">
                                            <div class="mb-3">
                                                <label for="pertanyaanPG" class="form-label">Pertanyaan</label>
                                                <input type="text" id="pertanyaanPG" class="form-control" placeholder="Masukkan pertanyaan">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Pilihan Jawaban</label>
                                                <input type="text" id="jawabanA" class="form-control mb-2" placeholder="Jawaban A">
                                                <input type="text" id="jawabanB" class="form-control mb-2" placeholder="Jawaban B">
                                                <input type="text" id="jawabanC" class="form-control mb-2" placeholder="Jawaban C">
                                                <input type="text" id="jawabanD" class="form-control mb-2" placeholder="Jawaban D">
                                            </div>
                                            <div class="mb-3">
                                                <label for="jawabanBenar" class="form-label">Jawaban Benar</label>
                                                <select id="jawabanBenar" class="form-select">
                                                    <option value="A">A</option>
                                                    <option value="B">B</option>
                                                    <option value="C">C</option>
                                                    <option value="D">D</option>
                                                </select>
                                            </div>
                                        </div>
                                        <!-- Form untuk Uraian -->
                                        <div id="formUraian" class="d-none">
                                            <div class="mb-3">
                                                <label for="pertanyaanUraian" class="form-label">Pertanyaan</label>
                                                <input type="text" id="pertanyaanUraian" class="form-control" placeholder="Masukkan pertanyaan">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer d-flex">
                                        <button type="button" class="btn btn-primary flex-fill" id="simpanSoal">Buat</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
                        <script>
                            const soalContainer = document.getElementById("soalContainer");
                            const addSoal = document.getElementById("addSoal");
                            const pilihTipeModal = new bootstrap.Modal(document.getElementById("pilihTipeModal"));
                            const formSoalModal = new bootstrap.Modal(document.getElementById("formSoalModal"));
                            const formPilihanGanda = document.getElementById("formPilihanGanda");
                            const formUraian = document.getElementById("formUraian");
                            const simpanSoal = document.getElementById("simpanSoal");
                        
                            let currentTipe = null;
                            let soalCount = 0;
                        
                            // Klik tombol tambah soal
                            addSoal.addEventListener("click", () => {
                                pilihTipeModal.show();
                            });
                        
                            // Pilih jenis soal
                            document.getElementById("btnPilihanGanda").addEventListener("click", () => {
                                currentTipe = "Pilihan Ganda";
                                pilihTipeModal.hide();
                                showFormSoal();
                            });
                        
                            document.getElementById("btnUraian").addEventListener("click", () => {
                                currentTipe = "Uraian";
                                pilihTipeModal.hide();
                                showFormSoal();
                            });
                        
                            // Tampilkan form soal
                            function showFormSoal() {
                                if (currentTipe === "Pilihan Ganda") {
                                    formPilihanGanda.classList.remove("d-none");
                                    formUraian.classList.add("d-none");
                                } else if (currentTipe === "Uraian") {
                                    formPilihanGanda.classList.add("d-none");
                                    formUraian.classList.remove("d-none");
                                }
                                formSoalModal.show();
                            }
                        
                            // Simpan soal
                            simpanSoal.addEventListener("click", () => {
                                soalCount++;
                                const col = document.createElement("div");
                                col.className = "col-6 col-md-3";
                                col.innerHTML = `
                                    <div class="soal-card">
                                        <p>Soal No.<br><span class="fs-3 fw-bold">${soalCount}</span></p>
                                        <button class="btn btn-secondary btn-sm">${currentTipe}</button>
                                    </div>
                                `;
                                soalContainer.appendChild(col);
                                formSoalModal.hide();
                            });
                        </script>                    </div>
                    <!-- smaga ai -->
                    <div class="col">
                        <div style="position:fixed; z-index: 1000;">
                            <div>
                                <div class="p-3 d-flex align-items-center border rounded-top">
                                    <img src="assets/ai.png" alt="" style="width: 25px; height: 25px;">
                                    <h6 class="p-0 m-0">Gemini</h6>
                                    <div>
                                        <p class="loading animate__animated animate__fadeIn text-muted text-muted ms-2 p-0 m-0" id="loading" style="font-size: 8px; z-index: 10;display: none;">SIBUK</p>
                                        <p class="animate__animated animate__fadeIn text-muted ms-2 p-0 m-0" style="font-size: 8px; z-index: 10;" id="tersedia">TERSEDIA</p>    
                                    </div>    
                                </div>
                            </div>
                            <div class="p-3 border border-top-0 border-bottom-0" style="height: 400px; width: 21rem; overflow-y: scroll;overflow-x: hidden;">
                                <!-- percakapan user dan AI -->
                                <!-- Chat Messages Container -->
                                <div id="chat-container" class="card-body chat-container pe-2" style="height: 25rem; overflow-y: auto; overflow-x: hidden;">
                            
                                <!-- Pesan chat akan ditampilkan di sini -->
                                </div>
                        
                               
                            </div> 
                            <div style="bottom: 0; background: white; z-index: 100;">
                                <div class="p-3 d-flex border rounded-bottom align-items-center">
                                    <div class="input-group align-items-center w-100">
                                        <input type="text" id="user-input" class="form-control" placeholder="Beri pesan Gemini">
                                        <button id="send-button" class="btn color-web bi-send" style="color: white;"></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                            <!-- script untuk ai -->
                            <script>
                                // Elemen DOM
                                const chatContainer = document.getElementById('chat-container');
                                const userInput = document.getElementById('user-input');
                                const sendButton = document.getElementById('send-button');
                    
                                // Gambar profil
                                const userImage = 'assets/pp.png';
                                const aiImage = 'assets/ai_chat.png';
                    
                                // Fungsi untuk menambahkan pesan ke chat
                                function addMessage(sender, text) {
                                    // Buat elemen wrapper untuk pesan
                                    const messageWrapper = document.createElement('div');
                                    messageWrapper.classList.add(
                                        'd-flex', 
                                        'mb-3', 
                                        sender === 'user' ? 'justify-content-end' : 'justify-content-start'
                                    );
                    
                                    // Parse the text and handle bold formatting
                                    const parsedText = text.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
                                    const paragraphs = parsedText.split('\n\n').map(p => p.split('\n').map(line => `<p style=" font-size: 12px; margin: 0; padding: 0;">${line}</p>`).join('')).join('<br>');
                    
                                    // Buat HTML untuk pesan
                                    messageWrapper.innerHTML = `
                                        <div class="d-flex align-items-center pt-1 pb-1 p-2 rounded-4 ${sender === 'user' ? 'flex-row-reverse' : ''}" style="background-color: rgb(239, 239, 239);">
                                            <img src="${sender === 'user' ? userImage : aiImage}" 
                                                class="chat-profile bg-white ms-2 me-2 rounded-circle" 
                                                alt="${sender} profile"
                                                style="width: 20px; height: 20px; object-fit: cover;">
                                            <div class="chat-bubble rounded p-2 align-content-center ${sender === 'user' ? 'user-bubble' : 'ai-bubble'}">
                                                ${paragraphs}
                                            </div>
                                        </div>
                                    `;
                                    // Tambahkan pesan ke kontainer
                                    chatContainer.appendChild(messageWrapper);
                                    
                                    // Gulir ke bawah
                                    chatContainer.scrollTop = chatContainer.scrollHeight;
                                }            
                                            // Function to handle API call and get AI response
                                            // Fungsi untuk mendapatkan respons AI dari Gemini
                                            async function getAIResponse(userMessage) {
                                                // Ganti 'YOUR_API_KEY' dengan API key resmi Anda dari Google AI Studio
                                                const API_KEY = 'AIzaSyAm6yuSvkKYnjmlqor8HjciqFiFAwahUgM';
                                                const API_ENDPOINT = `https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=${API_KEY}`;
                    
                                                try {
                                                    const response = await fetch(API_ENDPOINT, {
                                                        method: 'POST',
                                                        headers: {
                                                            'Content-Type': 'application/json'
                                                        },
                                                        body: JSON.stringify({
                                                            contents: [{
                                                                parts: [{
                                                                    text: userMessage
                                                                }]
                                                            }]
                                                        })
                                                    });

                                                    console.log("respons : ", response);
                    
                                                    if (!response.ok) {
                                                        const errorData = await response.json().catch(() => { return {}; }); // Handle jika response bukan JSON
                                                        const errorMessage = errorData.error?.message || response.statusText;
                                                        throw new Error('Gagal mendapatkan respons dari Gemini');
                                                    }
                    
                                                    const data = await response.json();
                                                    return data.candidates[0].content.parts[0].text || 'Maaf, tidak dapat memproses permintaan.';
                                                } catch (error) {
                                                    console.error('Error fetching Gemini response:', error);
                                                    return 'Maaf, terjadi kesalahan dalam komunikasi dengan AI. ${error.message}';
                                                }
                                            }
                    
                                            // tampilkan loading
                                            function showLoader() {
                                                document.getElementById('loading').style.display='block';
                                            }
                    
                                            // sembunyikan loading
                                            function hideLoader (){
                                                document.getElementById('loading').style.display='none';
                                            }
                    
                                            //tampilkan gemini tersedia
                                            function showTersedia(){
                                                document.getElementById('tersedia').style.display='block';
                                            }
                    
                                            // sembunyikan gemini tersedia
                                            function hideTersedia (){
                                                document.getElementById('tersedia').style.display='none';
                                            }
                    
                                            // Fungsi untuk mengirim pesan
                                            async function sendMessage() {
                                                const userMessage = userInput.value.trim();
                                                if (userMessage === '') return;
                    
                                                // Tampilkan pesan pengguna
                                                addMessage('user', userMessage);
                    
                                                // Bersihkan input
                                                userInput.value = '';
                    
                                                // sembunyikan status gemini dan tampilkan loading
                                                hideTersedia();
                                                showLoader();
                    
                    
                                                // Dapatkan respons AI
                                                const aiResponse = await getAIResponse(userMessage);
                    
                                                // sembunyikan loading dan tampilkan status gemini
                                                hideLoader();
                                                showTersedia();
                    
                                                // Tampilkan respons AI
                                                addMessage('ai', aiResponse);
                                            }
                    
                                            // Event listener untuk tombol Kirim
                                            sendButton.addEventListener('click', sendMessage);
                    
                                            // Event listener untuk tombol Enter
                                            userInput.addEventListener('keydown', (event) => {
                                                if (event.key === 'Enter') {
                                                    event.preventDefault(); // Mencegah form submit (default behavior)
                                                    sendMessage();
                                                }
                                            });
                                        </script>
                
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>