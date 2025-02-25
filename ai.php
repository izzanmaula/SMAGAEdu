<?php
session_start();
require "koneksi.php";

if(!isset($_SESSION['userid']) || $_SESSION['level'] != 'siswa') {
    header("Location: index.php");
    exit();
}

// Ambil userid dari session
$userid = $_SESSION['userid'];


// Change query variable name from $guru to $siswa
$query = "SELECT s.*, 
    k.nama_kelas AS kelas_saat_ini 
    FROM siswa s 
    LEFT JOIN kelas_siswa ks ON s.id = ks.siswa_id 
    LEFT JOIN kelas k ON ks.kelas_id = k.id 
    WHERE s.username = ?";

$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "s", $userid);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$siswa = mysqli_fetch_assoc($result);

$profilePath = !empty($siswa['foto_profil']) ? 'uploads/profil/'.$siswa['foto_profil'] : 'assets/pp.png';
error_log("Profile image path: " . $profilePath);
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
    <title>SMAGA AI - SMAGAEdu</title>
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

<?php include 'includes/styles.php'; ?>

<div class="container-fluid">
        <div class="row">
            <!-- Sidebar for desktop -->
            <?php include 'includes/sidebar_siswa.php'; ?>

            <!-- Mobile navigation -->
            <?php include 'includes/mobile_nav siswa.php'; ?>

            <!-- Settings Modal -->
            <?php include 'includes/settings_modal.php'; ?>

            
        </div>
    </div>    

                <style>
                .menu-samping {
                    position: fixed;
                    width: 13rem;
                    z-index: 1000;
                    /* Tambahkan flexbox dan height */
                    display: flex;
                    flex-direction: column;
                    height: 100vh;
                    
                }
                .menu-content {
                    flex: 1;
                    display: flex;
                    flex-direction: column;
                }
                .menu-atas {
                    height: calc(100vh - 80px); /* 80px adalah perkiraan tinggi dropdown */

                }
                .menu-bawah {
                    position: fixed;
                    bottom: 1rem;
                    width: 10rem; /* Sesuaikan dengan lebar menu */
                }
                .col-utama {
                    margin-left: 0;
                }
                @media (min-width: 768px) {
                    .col-utama {
                        margin-left: 13rem;
                    }
                }
                </style>

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
                            <div class="headerChat">
                                <h3 class="mb-0 fw-bold">SMAGA AI</h3>
                                <div>
                                    <p class="loading animate__animated animate__fadeIn animate__flash animate__infinite text-muted p-0 m-0" id="loading" style="font-size: 13px; z-index: 10;display: none;">Tunggu sebentar</p>
                                    <p class="animate__animated animate__fadeIn text-muted p-0 m-0" style="font-size: 13px; z-index: 10;" id="tersedia">SMAGAAI tersedia</p>
                                </div>
                            </div>
                            <style>
                                .loading{
                                    animation-duration: 3s;
                                }
                                @media (max-width: 768px) {
                                    .chat-container {
                                        height: calc(105vh - 250px) !important;
                                    }
                                    .input-wrapper {
                                        width: 100% !important;
                                        padding: 10px;
                                    }
                                    .headerChat{
                                        display: none;
                                    }
                                }
                            </style>
                        </div>
                        
                        <!-- Chat Messages Container -->
                        <div id="chat-container" class="card-body chat-container mt-2 pe-3 mb-0 pb-1" style="height: 29rem; overflow-y: auto; overflow-x: hidden;">
                        <div data-user-image="<?php echo !empty($siswa['foto_profil']) ? $siswa['foto_profil'] : 'assets/pp.png'; ?>" hidden></div>
                            <!-- Pesan chat akan ditampilkan di sini -->
                        </div>

                        <!-- Tambah HTML setelah chat-container: -->
                        <div class="recommendation-container" style="position: relative; margin-top: -30px; z-index: 1000;">
                        <div class="d-flex flex-wrap justify-content-center gap-2">
                            <button class="btn buttonRekomendasi" onclick="fillPrompt('Berikan saya tips belajar yang efektif')">
                            <i class="bi bi-pen-fill pe-1"></i>
                            Tips belajar efektif  
                            </button>
                            <button class="btn buttonRekomendasi buttonRekomendasi2" onclick="fillPrompt('Berikan saya Ide belajar seru!')">
                            <i class="bi bi-lightbulb-fill pe-1"></i>
                            Ide belajar seru!
                            </button>
                            <button class="btn buttonRekomendasi" onclick="fillPrompt('Beri saya kejutan!')">
                            <i class="bi bi-emoji-surprise-fill pe-1"></i>
                            Beri saya kejutan!
                            </button>
                        </div>
                        </div>

                        <style>
                            .buttonRekomendasi {
                                background-color: #EEECE2;
                                border: 0;
                                padding: 5px 10px;
                                font-size: 12px;
                                border-radius: 10px;
                                cursor: pointer;
                            }
                            @media (max-width: 768px) {
                                .buttonRekomendasi2 {
                                    display: none;
                                }
                            }

                        </style>
                        
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

                            /* CSS untuk animasi fade out rekomendasi chat */
                            .recommendation-container {
                                transition: opacity 0.3s;
                            }

                            .recommendation-container.hide {
                                opacity: 0;
                                pointer-events: none;
                            }

                            .recommendation-container {
                                margin: 10px 0;
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
                            <p class="text-muted p-0 m-0" style="font-size: 9px;">SMAGA AI mungkin dapat membuat kesalahan, selalu cek kembali setiap respons SMAGA AI.</p>
                        </div>
                    </div>
                </div>
            

                <script>
            // Elemen DOM
            const chatContainer = document.getElementById('chat-container');
            const userInput = document.getElementById('user-input');
            const sendButton = document.getElementById('send-button');

            // Gambar profil
            let userImage = document.querySelector('[data-user-image]').getAttribute('data-user-image');

            const aiImage = 'assets/ai_chat.png';

            let conversationHistory = [];
            const MAX_HISTORY = 10; // Batasan riwayat

            let isFirstChat = true; //rekomendasi chat

            // Loading animation
            let loadingInterval;

            // Fungsi untuk menampilkan pesan loading
            function showLoader() {
                const loadingEl = document.getElementById('loading');
                loadingEl.style.display = 'block';
                let dots = 0;
                loadingInterval = setInterval(() => {
                    loadingEl.textContent = 'Tunggu sebentar' + '.'.repeat(dots);
                    dots = (dots + 1) % 4;
                }, 500);
            }

            function hideLoader() {
                clearInterval(loadingInterval);
                document.getElementById('loading').style.display = 'none';
            }



            // Fungsi untuk menambahkan pesan ke chat
            async function addMessage(sender, text) {
                // Buat elemen wrapper untuk pesan
                const messageWrapper = document.createElement('div');
                messageWrapper.classList.add(
                    'd-flex', 
                    'mb-3', 
                    sender === 'user' ? 'justify-content-end' : 'justify-content-start'
                );

            // Improved text parsing
            const formatText = (text) => {
                    // Handle bold text
                    text = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
                    
                    // Handle lists with proper spacing
                    text = text.replace(/^(\d+\.|\-)\s+/gm, '<br>$1 ');
                    
                    // Handle paragraphs with proper spacing
                    const paragraphs = text.split('\n\n').map(p => {
                        const lines = p.split('\n').map(line => 
                            `<p style="font-size: 12px; margin: 0; padding: 2px 0;">${line}</p>`
                        );
                        return lines.join('');
                    });
                    
                    return paragraphs.join('<br>');
                };

                const formattedText = formatText(text);

                // Dalam fungsi addMessage, ubah bagian img src
                messageWrapper.innerHTML = `
                    <div class="d-flex align-items-center pt-1 pb-1 p-2 rounded-4 ${sender === 'user' ? 'flex-row-reverse' : ''}" 
                        style="background-color: rgb(239, 239, 239); max-width:30rem">
                        <img src="${sender === 'user' ? userImage : aiImage}" 
                            class="chat-profile bg-white ms-2 me-2 rounded-circle" 
                            alt="${sender} profile" 
                            style="width: 20px; height: 20px; object-fit: cover;">
                        <div class="chat-bubble rounded p-2 align-content-center" style="font-size: 13px;">
                            ${sender === 'user' ? formattedText : ''}
                        </div>
                    </div>
                `;

                // Tambahkan pesan ke kontainer
                chatContainer.appendChild(messageWrapper);

                if (sender === 'ai') {
                    const chatBubble = messageWrapper.querySelector('.chat-bubble');
                    await typeMessage(chatBubble, text);
                }
                
                // Gulir ke bawah
                chatContainer.scrollTop = chatContainer.scrollHeight;
            }       
            
            // Update the typeMessage function for better formatting:
            async function typeMessage(element, text) {
                const formattedText = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                                        .replace(/^(\d+\.|\-)\s+/gm, '<br>$1 ')
                                        .split('\n\n')
                                        .join('<br><br>');
                
                const words = formattedText.split(' ');
                element.innerHTML = '';
                
                for (let i = 0; i < words.length; i++) {
                    let word = words[i];
                    if (word.includes('<br>')) {
                        element.innerHTML += word;
                    } else {
                        element.innerHTML += word + ' ';
                    }
                   // Auto-scroll after each word
                            const elementBottom = element.getBoundingClientRect().bottom;
                            const containerBottom = chatContainer.getBoundingClientRect().bottom;
                            
                            if (elementBottom > containerBottom) {
                                chatContainer.scrollTop = chatContainer.scrollHeight;
                            }
                            
                            await new Promise(resolve => setTimeout(resolve, 50));
                        }
                    }

                    let systemMessage = {
                        role: "system",
                        content: `Kamu adalah SMAGA AI, asisten pembelajaran di LMS SMAGAEdu untuk siswa SMP Muhammadiyah 2 Gatak dan SMA Muhammadiyah 5 Gatak.

                        Panduan karaktermu:
                        - Berperan sebagai teman belajar yang supportif dan bisa diandalkan
                        - Kamu adalah teman sebaya siswa, jadi gunakan bahasa yang akrab
                        - Gunakan bahasa santai tapi tetap sopan
                        - Gunakan "aku" untuk diri sendiri
                        - Gunakan "kamu" untuk menyapa siswa
                        - hindari kalimat deskriptif yang bertumpuk, gunakan point (-) atau nomerik untuk menjelaskan step by step
                        - Tunjukkan empati pada masalah akademik/pribadi siswa
                        - Ajak siswa berpikir kritis dengan pertanyaan penggiring
                        - Sesuaikan jawaban dengan tingkat pemahaman siswa SMP/SMA
                        - Jika user terdeteksi laki-laki: bertindak sebagai asisten perempuan (ramah, lembut, perhatian)
                        - Jika user terdeteksi perempuan: bertindak sebagai asisten laki-laki (supportif, protective, motivatif)
                        
                        Cara merespon:
                        - Mulai dengan sapaan hangat dan personal
                        - Berikan jawaban fokus tapi tetap ramah
                        - Tawarkan penjelasan lebih detail jika dibutuhkan
                        - Sisipkan emoji 😊 secara wajar untuk kesan bersahabat
                        - Akhiri dengan pertanyaan lanjutan yang relevan
                        - Dorong diskusi dua arah yang konstruktif
                        - Kalau kamu tidak tahu mengenai user terlebih apa yang sudah dibicarakan oleh user pada pertemuam sebelumnya
                        , jangan sok tau, cukup jawab singkat dan tawarkan pertanyaan lainya
                        - jangan terlalu banyak menjawab, siswa yang kamu hadapi tidak suka membaca teks banyak, cukup jelaskan singkat dan intinya saja
                        
                        Area bantuan:
                        - Pembelajaran akademik semua mata pelajaran
                        - Saran pengembangan diri dan soft skill
                        - Diskusi karir dan cita-cita 
                        - Konsultasi masalah pribadi/sosial
                        - Tips belajar efektif
                        - Informasi kegiatan sekolah`
                    };

                    let contohDialog = [
                            {role: "user", content: "halo"},
                            {role: "assistant", content: "Hai! 😊 Apa yang ingin kamu diskusikan hari ini? Bisa soal pelajaran, karir, atau hal lain yang kamu pikirkan."},
                        ];

                        // Function to handle API call and get AI response
                        async function getAIResponse(userMessage) {
                            // Replace with your Groq API key
                            const API_KEY = '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';
                            const API_ENDPOINT = 'https://api.groq.com/openai/v1/chat/completions';

                            conversationHistory.push({
                                role: "user",
                                content: userMessage
                            });

                            // Tambahkan pesan user ke history
                            conversationHistory.push({
                                role: "user",
                                content: userMessage
                            });

                            // Batasi riwayat
                            if (conversationHistory.length > MAX_HISTORY * 2) { // *2 karena setiap interaksi ada 2 pesan
                                conversationHistory = conversationHistory.slice(-MAX_HISTORY * 2);
                            }

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
                                            ...conversationHistory,
                                            {
                                            role: "user",
                                            content: userMessage
                                        }]
                                    })
                                });

                                if (!response.ok) {
                                    throw new Error(`HTTP error! status: ${response.status}`);
                                }

                                const data = await response.json();
                                const aiResponse = data.choices[0].message.content;

                                // Tambahkan respons AI ke history
                                conversationHistory.push({
                                    role: "assistant",
                                    content: aiResponse
                                });

                                return aiResponse;

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

                        async function saveToDatabase(userMessage, aiResponse) {
                            try {
                                const response = await fetch('save_chat.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                    },
                                    body: JSON.stringify({
                                        user_id: '<?php echo $_SESSION["userid"]; ?>',
                                        pesan: userMessage,
                                        respons: aiResponse
                                    })
                                });
                                if (!response.ok) throw new Error('Failed to save chat');
                            } catch (error) {
                                console.error('Error saving chat:', error);
                            }
                        }

                        function fillPrompt(text) {
                            document.getElementById('user-input').value = text;
                            document.getElementById('user-input').focus();
                        }

                        // Fungsi untuk mengirim pesan
                        async function sendMessage() {
                            const userMessage = userInput.value.trim();
                            if (userMessage === '') return;

                            if (isFirstChat) {
                                document.querySelector('.recommendation-container').style.display = 'none';
                                isFirstChat = false;
                            }

                            // Tampilkan pesan pengguna
                            addMessage('user', userMessage);

                            // Bersihkan input
                            userInput.value = '';

                            // sembunyikan status gemini dan tampilkan loading
                            hideTersedia();
                            showLoader();

                            // Dapatkan respons AI
                            const aiResponse = await getAIResponse(userMessage);

                            // Simpan chat ke database
                            await saveToDatabase(userMessage, aiResponse);


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