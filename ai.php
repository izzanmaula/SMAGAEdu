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
                        <div class="d-flex align-items-center rounded bg-white shadow-sm p-2" style="">
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
            <div class="col pt-0 pb-0 p-4 col-utama">
                <style>
                    .col-utama{
                        margin-left: 13rem;
                    }
                </style>
                <div class="container mt-4">
                    <div class="">
                        <div class="">
                            <h3 class="mb-0 fw-bold">Chat dengan Gemini</h3>
                            <div>
                                <p class="loading animate__animated animate__fadeIn animate__flash animate__infinite text-muted p-0 m-0" id="loading" style="font-size: 13px; z-index: 10;display: none;">Tunggu, Gemini sedang memahami permintaan Anda ...</p>
                                <p class="animate__animated animate__fadeIn text-muted p-0 m-0" style="font-size: 13px; z-index: 10;" id="tersedia">Gemini tersedia untuk Anda saat ini</p>
                            </div>
                            <style>
                                .loading{
                                    animation-duration: 3s;
                                }
                            </style>
    
                        </div>
                        
                        
                        <!-- Chat Messages Container -->
                        <div id="chat-container" class="card-body chat-container mt-2 pe-3" style="height: 29rem; overflow-y: auto; overflow-x: hidden;">
                            
                            <!-- Pesan chat akan ditampilkan di sini -->
                        </div>
                        
                        <!-- Input Area -->
                         <div style="text-align: center;">
                            <div class="card-footer p-2 rounded-3" style="width: 45rem; margin: auto; background-color: #EEECE2;">
                                <div class="input-group">
                                    <input type="text" id="user-input" class="form-control border-0" style="background-color: transparent;" placeholder="Apa yang bisa Gemini bantu hari ini?">
                                    <button id="send-button" class="btn btn-primary bi-send rounded"></button>
                                </div>
                            </div>    
                            <div class="pt-1">
                                <p class="text-muted p-0 m-0" style="font-size: 9px;">Gemini mungkin dapat membuat kesalahan, selalu cek kembali setiap respons gemini.</p>
                            </div>
                         </div>
                    </div>
                </div>               
            </div>
            

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
                    <div class="d-flex align-items-center pt-1 pb-1 p-2 rounded-4 ${sender === 'user' ? 'flex-row-reverse' : ''}" style="background-color: rgb(239, 239, 239); max-width:30rem">
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

                                if (!response.ok) {
                                    throw new Error('Gagal mendapatkan respons dari Gemini');
                                }

                                const data = await response.json();
                                return data.candidates[0].content.parts[0].text || 'Maaf, tidak dapat memproses permintaan.';
                            } catch (error) {
                                console.error('Error fetching Gemini response:', error);
                                return 'Maaf, terjadi kesalahan dalam komunikasi dengan AI.';
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