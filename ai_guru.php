<?php
session_start();
require "koneksi.php";
if(!isset($_SESSION['userid']) || $_SESSION['level'] != 'guru') {
    header("Location: index.php");
    exit();
}

// Ambil data guru
$userid = $_SESSION['userid'];
$query = "SELECT * FROM guru WHERE username = '$userid'";
$result = mysqli_query($koneksi, $query);
$guru = mysqli_fetch_assoc($result);


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
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700;1,900&family=PT+Serif:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <title>Gemini - SMAGAEdu</title>
</head>
<style>
        body{ 
            font-family: merriweather;
        }

        .color-web {
            background-color: rgb(218, 119, 86);
        }

        .btn {
            background-color: rgb(218, 119, 86);
            border: 0;
        }

        .btn:hover{
            background-color: rgb(219, 106, 68);

        }
    
</style>
<body>
     <!-- row col untuk halaman utama -->
    <!-- Navbar Mobile -->
    <nav class="navbar navbar-dark d-md-none color-web fixed-top">
        <div class="container-fluid">
            <!-- Logo dan Nama -->
            <a class="navbar-brand d-flex align-items-center gap-2 text-white" href="#">
                <img src="assets/logo_white.png" alt="" width="30px" class="logo_putih">
            <div>
                    <h1 class="p-0 m-0" style="font-size: 20px;">SMAGAEdu</h1>
                    <p class="p-0 m-0 d-none d-md-block" style="font-size: 12px;">LMS</p>
                </div>
            </a>
            
            <!-- Tombol Toggle -->
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar">
                <span class="navbar-toggler-icon" style="color:white"></span>
            </button>
            
            <!-- Offcanvas/Sidebar Mobile -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" style="font-size: 30px;">Menu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                </div>
                <div class="offcanvas-body">
                    <div class="d-flex flex-column gap-2">
                        <!-- Menu Beranda -->
                        <a href="beranda_guru.php" class="text-decoration-none text-black">
                            <div class="d-flex align-items-center rounded p-2">
                                <img src="assets/beranda_fill.png" alt="" width="50px" class="pe-4">
                                <p class="p-0 m-0 text-white">Beranda</p>
                            </div>
                        </a>
                        
                        <!-- Menu Cari -->
                        <a href="cari_guru.php" class="text-decoration-none text-black">
                            <div class="d-flex align-items-center rounded p-2">
                                <img src="assets/pencarian.png" alt="" width="50px" class="pe-4">
                                <p class="p-0 m-0">Cari</p>
                            </div>
                        </a>
                        
                        <!-- Menu Ujian -->
                        <a href="ujian_guru.php" class="text-decoration-none text-black">
                            <div class="d-flex align-items-center rounded p-2">
                                <img src="assets/ujian_outfill.png" alt="" width="50px" class="pe-4">
                                <p class="p-0 m-0">Ujian</p>
                            </div>
                        </a>
                        
                        <!-- Menu Profil -->
                        <a href="profil_guru.php" class="text-decoration-none text-black">
                            <div class="d-flex align-items-center rounded p-2">
                                <img src="assets/profil_outfill.png" alt="" width="50px" class="pe-4">
                                <p class="p-0 m-0">Profil</p>
                            </div>
                        </a>
                        
                        <!-- Menu AI -->
                        <a href="ai.php" class="text-decoration-none text-black">
                            <div class="d-flex align-items-center bg-white shadow-sm  rounded p-2">
                                <img src="assets/ai.png" alt="" width="50px" class="pe-4">
                                <p class="p-0 m-0">Gemini</p>
                            </div>
                        </a>
                        
                        <!-- Menu Bantuan -->
                        <a href="bantuan.php" class="text-decoration-none text-black">
                            <div class="d-flex align-items-center rounded p-2">
                                <img src="assets/bantuan_outfill.png" alt="" width="50px" class="pe-4">
                                <p class="p-0 m-0">Bantuan</p>
                            </div>
                        </a>
                    </div>
                    
                <!-- Profile Dropdown -->
                <div class="mt-3 dropdown"> <!-- Tambahkan class dropdown di sini -->
                    <button class="btn d-flex align-items-center gap-3 p-2 rounded-3 border w-100" 
                            style="background-color: #F8F8F7;" 
                            type="button" 
                            data-bs-toggle="dropdown" 
                            aria-expanded="false">
                            <img src="<?php echo !empty($guru['foto_profil']) ? 'uploads/profil/'.$guru['foto_profil'] : 'assets/pp.png'; ?>"  width="30px" class="rounded-circle" style="background-color: white;">
                            <p class="p-0 m-0 text-truncate" style="font-size: 12px;"><?php echo $guru['namaLengkap']; ?></p>
                    </button>
                    <ul class="dropdown-menu w-100" style="font-size: 12px;"> <!-- Tambahkan w-100 agar lebar sama -->
                        <li><a class="dropdown-item" href="#">Pengaturan</a></li>
                        <li><a class="dropdown-item" href="logout.php">Keluar</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

     <!-- row col untuk halaman utama -->
    <div class="container-fluid">
        <div class="row">
        <div class="col-auto vh-100 p-2 p-md-4 shadow-sm menu-samping d-none d-md-block" style="background-color:rgb(238, 236, 226)">
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
                            <img src="assets/smagaedu.png" alt="" width="30px" class="logo_orange">
                            <div>
                                <h1 class="display-5  p-0 m-0" style="font-size: 20px; text-decoration: none;">SMAGAEdu</h1>
                                <p class="p-0 m-0 text-muted" style="font-size: 12px;">LMS</p>
                            </div>
                        </a>
                    </div>  
                    <div class="col">
                        <a href="beranda_guru.php" class="text-decoration-none text-black">
                        <div class="d-flex align-items-center rounded p-2" style="">
                            <img src="assets/beranda_outfill.png" alt="" width="50px" class="pe-4">
                            <p class="p-0 m-0">Beranda</p>
                        </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="cari_guru.php" class="text-decoration-none text-black">
                        <div class="d-flex align-items-center rounded p-2" style="">
                            <img src="assets/pencarian.png" alt="" width="50px" class="pe-4">
                            <p class="p-0 m-0">Cari</p>
                        </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="ujian_guru.php" class="text-decoration-none text-black">
                        <div class="d-flex align-items-center rounded p-2" style="">
                            <img src="assets/ujian_outfill.png" alt="" width="50px" class="pe-4">
                            <p class="p-0 m-0">Ujian</p>
                        </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="profil_guru.php" class="text-decoration-none text-black">
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
                        <div class="d-flex align-items-center bg-white shadow-sm  rounded p-2" style="">
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
                        <img src="<?php echo !empty($guru['foto_profil']) ? 'uploads/profil/'.$guru['foto_profil'] : 'assets/pp.png'; ?>"  width="30px" class="rounded-circle" style="background-color: white;">
                        <p class="p-0 m-0 text-truncate" style="font-size: 12px;"><?php echo $guru['namaLengkap']; ?></p>
                    </div>
                    <!-- dropdown menu option -->
                    <ul class="dropdown-menu" style="font-size: 12px;">
                        <li><a class="dropdown-item" href="#">Pengaturan</a></li>
                        <li><a class="dropdown-item" href="logout.php">Keluar</a></li>
                      </ul>
                </div>
            </div>

            <!-- ini isi kontennya -->
            <div class="col pt-0 pb-0 p-4 col-utama">
                <style>
                    .col-utama{
                        margin-left: 13rem;
                    }
                    @media (max-width: 768px) {
                        .menu-samping {
                            display: none;
                        }
                        .col-utama {
                            margin-left: 0;
                            margin-top: 3rem;
                        }
                        .peringatan {
                            display: none;
                        }
                    }  
                </style>
                <div class="container-fluid mt-4">
                    <div class="">
                        <div class="">
                            <h3 class="mb-0 fw-bold">SMAGA AI</h3>
                            <div>
                                <p class="loading animate__animated animate__fadeIn animate__flash animate__infinite text-muted p-0 m-0" id="loading" style="font-size: 13px; z-index: 10;display: none;">Tunggu sebentar</p>
                                <p class="animate__animated animate__fadeIn text-muted p-0 m-0" style="font-size: 13px; z-index: 10;" id="tersedia">SMAGAAI tersedia</p>
                            </div>
                            <style>
                                .loading{
                                    animation-duration: 3s;
                                }
                                @media (max-width: 768px) {
                                    .chat-container {
                                        height: calc(100vh - 250px) !important;
                                    }
                                    .input-wrapper {
                                        width: 100% !important;
                                        padding: 10px;
                                    }
                                }
                            </style>
                        </div>
                        
                        <!-- Chat Messages Container -->
                        <div id="chat-container" class="card-body chat-container mt-2 pe-3 mb-0 pb-1" style="height: 29rem; overflow-y: auto; overflow-x: hidden;">
                            <!-- Pesan chat akan ditampilkan di sini -->
                        </div>
                        
                        <!-- Input Area -->
                        <div class="d-flex justify-content-center input-container mt-0 pt-0">
                            <style>
                                @media (max-width: 768px) {
                                    .input-container {
                                        position: fixed;
                                        bottom: 0;
                                        left: 0;
                                        right: 0;
                                        background-color: #EEECE2;
                                        padding: 10px;
                                        z-index: 1000;
                                    }
                                }

                                .input-wrapper {
                                max-height: 150px;
                                overflow: hidden;
                            }

                            #user-input {
                                min-height: 38px;
                                max-height: 150px;
                                resize: none;
                                overflow-y: auto;
                            }

                            @media (max-width: 768px) {
                                .input-wrapper {
                                    position: fixed;
                                    bottom: 0;
                                    left: 0;
                                    right: 0;
                                    padding: 10px;
                                    background-color: #EEECE2;
                                }
                                
                                #user-input {
                                    height: auto;
                                }
                            }

                            </style>
                            <div class="input-wrapper card-footer p-2 rounded-3 w-100" style="max-width: 45rem; background-color: #EEECE2;">
                                <div class="input-group">
                                    <input type="text" id="user-input" class="form-control border-0" style="background-color: transparent;" placeholder="Pesan Anda">
                                    <button id="send-button" class="btn btn-primary bi-send rounded"></button>
                                </div>
                            </div>    
                        </div>
                        <div class="text-center peringatan pt-1">
                            <p class="text-muted p-0 m-0" style="font-size: 9px;">Gemini mungkin dapat membuat kesalahan, selalu cek kembali setiap respons gemini.</p>
                        </div>
                    </div>
                </div>
            

                <script>
            // Elemen DOM
            const chatContainer = document.getElementById('chat-container');
            const userInput = document.getElementById('user-input');
            const sendButton = document.getElementById('send-button');

            // Gambar profil
            const userImage = '<?php echo !empty($guru["foto_profil"]) ? "uploads/profil/".$guru["foto_profil"] : "assets/pp.png"; ?>';

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

                // Dalam fungsi addMessage, ubah bagian img src
                messageWrapper.innerHTML = `
                    <div class="d-flex align-items-center pt-1 pb-1 p-2 rounded-4 ${sender === 'user' ? 'flex-row-reverse' : ''}" style="background-color: rgb(239, 239, 239); max-width:30rem">
                        ${sender === 'user' ? 
                            `<img src="${userImage}" class="chat-profile bg-white ms-2 me-2 rounded-circle" alt="user profile" style="width: 20px; height: 20px; object-fit: cover;">` :
                            `<img src="${aiImage}" class="chat-profile bg-white ms-2 me-2 rounded-circle" alt="ai profile" style="width: 20px; height: 20px; object-fit: cover;">`
                        }
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
                        let systemMessage = {
                            role: "system", 
                            content: `namamu adalah SMAGA AI, kamu berada di LMS SMAGAEdu, 
                            kamu adaah penasehat guru untuk SMP Muhammadiyah 2 Gatak dan SMA MUhammadiyah 5 Gatak (SMAGA). 

                            panduan yang harus kamu lakukan adalah:
                            - gunakan bahasa indonesia yang santai, sesekali bercanda namun sopan
                            - berikan jawaban yang fokus, jangan muter-muter
                            - jika guru bertanya jangan langsung kamu beritahu semua, tawarkan dulu apakah ingin di lanjutkan ke penjelasan yang lebih rinci
                            - jangan basa basi atau perkenalan yang berlebihan
                            - jangan mengulangi informasi yang sudah di sampaikan
                            - gunakan "saya" untuk diri sendiri
                            - gunakan "Bapak/Ibu" untuk menyapa guru

                            jika di tanya halo, maka jawabnya halo juga, dan tawarkan pertanyaan lainya, selalu tawarkan pertanyaan
                            ke guru agar guru merasa di hargai dan di perhatikan namun tetap tidak berlebihan. cukup satu tawaran pertanyaan saja.
                            `
                            };

                        let contohDialog = [
                            {role: "user", content: "halo"},
                            {role: "assistant", content: "Halo, Bapak/Ibu! Ada yang bisa saya bantu hari ini? 😊"},
                            {role: "user", content: "Bagaimana cara membuat kuis di LMS?"},
                            {role: "assistant", content: "Mudah kok, Bapak/Ibu! Saya bisa jelaskan langkah-langkah detailnya kalau ingin. Mau dijelaskan sekarang?"},
                        ];

                        // Function to handle API call and get AI response
                        async function getAIResponse(userMessage) {
                            // Replace with your Groq API key
                            const API_KEY = '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';
                            const API_ENDPOINT = 'https://api.groq.com/openai/v1/chat/completions';

                            try {
                                const response = await fetch(API_ENDPOINT, {
                                    method: 'POST',
                                    headers: {
                                        'Authorization': `Bearer ${API_KEY}`,
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        model: "llama-3.3-70b-versatile",
                                        messages: [
                                            systemMessage,
                                            ...contohDialog,
                                            {
                                            role: "user",
                                            content: userMessage
                                        }]
                                    })
                                });

                                if (!response.ok) {
                                    throw new Error('Failed to get response from Groq');
                                }

                                const data = await response.json();
                                return data.choices[0].message.content || 'Sorry, could not process the request.';
                            } catch (error) {
                                console.error('Error fetching Groq response:', error);
                                return 'Sorry, there was an error communicating with AI.';
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
            
</body>
</html>