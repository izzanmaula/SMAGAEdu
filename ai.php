<?php
session_start();
require "koneksi.php";


if(!isset($_SESSION['userid']) || $_SESSION['level'] != 'siswa') {
    header("Location: index.php");
    exit();
}

// Ambil userid dari session
$userid = $_SESSION['userid'];


if(!isset($_SESSION['userid']) || $_SESSION['level'] != 'siswa') {
    header("Location: index.php");
    exit();
}

$userid = $_SESSION['userid'];

// Get student data
$query = "SELECT * FROM siswa WHERE username = ?";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "s", $userid);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$siswa = mysqli_fetch_assoc($result);

// Get usage count
$query_usage = "SELECT ai_usage_count, ai_usage_date FROM siswa WHERE username = ?";
$stmt_usage = mysqli_prepare($koneksi, $query_usage);
mysqli_stmt_bind_param($stmt_usage, "s", $userid);
mysqli_stmt_execute($stmt_usage);
$result_usage = mysqli_stmt_get_result($stmt_usage);
$usage = mysqli_fetch_assoc($result_usage);

$remaining_usage = 20 - ($usage['ai_usage_count'] ?? 0);

// Get chat history
$query_chat = "SELECT * FROM ai_chat_history WHERE user_id = ? ORDER BY created_at ASC LIMIT 10";
$stmt_chat = mysqli_prepare($koneksi, $query_chat);
mysqli_stmt_bind_param($stmt_chat, "s", $userid);
mysqli_stmt_execute($stmt_chat);
$result_chat = mysqli_stmt_get_result($stmt_chat);

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
    <title>SMAGAAI - SMAGAEdu</title>
</head>
<style>
        body{ 
            font-family: merriweather;
        }
        .color-web {
            background-color: rgb(218, 119, 86);
        }
</style>
    <style>
        .col-utama {
            margin-left: 13rem;
        }
        @media (max-width: 768px) {
            .menu-samping {
                display: none;
            }
            .col-utama {
                margin-left: 0;
            }
        }
        .message {
            max-width: 30%;
            margin-bottom: 1rem;
            padding: 0.8rem 1rem;
            border-radius: 0.5rem;
        }
        .user-message {
            background-color: #EEECE2;
            margin-left: auto;
        }
        .ai-message {
            border: 1px solid #EEECE2;
            margin-right: auto;
        }
        .loading {
            animation-duration: 3s;
        }
    </style>
</style>
<body>
    

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
                        <a href="#" class="text-decoration-none text-black">
                            <div class="d-flex align-items-center rounded  p-2">
                                <img src="assets/beranda_outfill.png" alt="" width="50px" class="pe-4">
                                <p class="p-0 m-0 text-white">Beranda</p>
                            </div>
                        </a>
                        
                        
                        <!-- Menu Ujian -->
                        <a href="ujian.php" class="text-decoration-none text-black">
                            <div class="d-flex align-items-center rounded p-2">
                                <img src="assets/ujian_outfill.png" alt="" width="50px" class="pe-4">
                                <p class="p-0 m-0">Ujian</p>
                            </div>
                        </a>

                        <!-- Menu ai -->
                        <a href="ai.php" class="text-decoration-none text-black">
                            <div class="d-flex align-items-center color-web rounded p-2">
                                <img src="assets/ai.png" alt="" width="50px" class="pe-4">
                                <p class="p-0 m-0">SMAGA AI</p>
                            </div>
                        </a>

                        
                        <!-- Menu Profil -->
                        <a href="profil.php" class="text-decoration-none text-black">
                            <div class="d-flex align-items-center rounded p-2">
                                <img src="assets/profil_outfill.png" alt="" width="50px" class="pe-4">
                                <p class="p-0 m-0">Profil</p>
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
                        <img src="assets/pp.png" alt="" class="rounded-circle p-0 m-0" width="30px">
                        <p class="p-0 m-0 text-truncate" style="font-size: 12px;">Halo, <?php echo htmlspecialchars($_SESSION['nama']); ?></p>
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
                <div class="menu-atas">
                    <div class="ps-1 mb-3">
                        <a href="beranda.php" style="text-decoration: none; color: black;" class="d-flex align-items-center gap-2">
                            <img src="assets/smagaedu.png" alt="" width="30px" class="logo_orange">
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
                    <div class="col">
                        <a href="profil.php" class="text-decoration-none text-black">
                        <div class="d-flex align-items-center rounded bg-white shadow-sm p-2" style="">
                            <img src="assets/ai.png" alt="" width="50px" class="pe-4">
                            <p class="p-0 m-0">SMAGA AI</p>
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
                <div class="menu-bawah">
                    <div class="row dropdown">
                        <div class="btn d-flex align-items-center gap-3 p-2 rounded-3 border dropdown-toggle" style="background-color: #F8F8F7;" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="assets/pp.png" alt="" class="rounded-circle p-0 m-0" width="30px">
                            <p class="p-0 m-0 text-truncate" style="font-size: 12px;"><?php echo htmlspecialchars($_SESSION['nama']); ?></p>
                        </div>
                        <!-- dropdown menu option -->
                        <ul class="dropdown-menu" style="font-size: 12px;">
                            <li><a class="dropdown-item" href="#">Pengaturan</a></li>
                            <li><a class="dropdown-item" href="logout.php">Keluar</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- ini isi kontennya -->
            <div class="col pt-0 pb-0 p-4 col-utama">
        <div class="container mt-4">
            <div class="">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0 fw-bold">Chat dengan Grog AI</h3>
                        <div>
                            <p class="loading animate__animated animate__fadeIn animate__flash animate__infinite text-muted p-0 m-0" 
                               id="loading" style="font-size: 13px; z-index: 10; display: none;">
                               Tunggu, Grog AI sedang memahami permintaan Anda ...
                            </p>
                            <p class="animate__animated animate__fadeIn text-muted p-0 m-0" 
                            style="font-size: 13px; z-index: 10;" 
                            id="tersedia">
                            Sisa penggunaan hari ini: <?php echo $remaining_usage; ?>/20
                            </p>
                        </div>
                    </div>
                </div>

<!-- Chat Messages Container -->
<div id="chat-container" class="card-body chat-container mt-2 pe-3" style="height: 29rem; overflow-y: auto; overflow-x: hidden;">
    <?php while($chat = mysqli_fetch_assoc($result_chat)): ?>
        <!-- User Message -->
        <div class="d-flex mb-3 justify-content-end">
            <div class="d-flex align-items-center pt-1 pb-1 p-2 rounded-4 flex-row-reverse" style="background-color: rgb(239, 239, 239); max-width:30rem">
                <img src="<?php echo !empty($siswa['foto_profil']) ? 'uploads/profil/'.$siswa['foto_profil'] : 'assets/pp.png'; ?>" class="chat-profile bg-white ms-2 me-2 rounded-circle" alt="user profile" style="width: 20px; height: 20px; object-fit: cover;">
                <div class="chat-bubble rounded p-2 align-content-center user-bubble">
                    <p style="font-size: 12px; margin: 0; padding: 0;">
                        <?php echo nl2br(htmlspecialchars($chat['pesan'])); ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- AI Message -->
        <div class="d-flex mb-3 justify-content-start">
            <div class="d-flex align-items-center pt-1 pb-1 p-2 rounded-4" style="background-color: rgb(239, 239, 239); max-width:30rem">
                <img src="assets/ai_chat.png" class="chat-profile bg-white ms-2 me-2 rounded-circle" alt="ai profile" style="width: 20px; height: 20px; object-fit: cover;">
                <div class="chat-bubble rounded p-2 align-content-center ai-bubble">
                    <p style="font-size: 12px; margin: 0; padding: 0;">
                        <?php echo nl2br(htmlspecialchars($chat['respons'])); ?>
                    </p>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<!-- Input Area -->
<div style="text-align: center;">
    <div class="card-footer p-2 rounded-3" style="width: 45rem; margin: auto; background-color: #EEECE2;">
        <form id="chat-form" class="input-group">
            <input type="text" id="user-input" class="form-control border-0" style="background-color: transparent;" placeholder="Apa yang bisa Grog AI bantu hari ini?">
            <button type="submit" class="btn btn-primary bi-send rounded"></button>
        </form>
    </div>
    <div class="pt-1">
        <p class="text-muted p-0 m-0" style="font-size: 9px;">
            Grog AI mungkin dapat membuat kesalahan, selalu cek kembali setiap respons.
        </p>
    </div>
</div>

<style>
.chat-container::-webkit-scrollbar {
    width: 8px;
}

.chat-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.chat-container::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 4px;
}

.chat-container::-webkit-scrollbar-thumb:hover {
    background: #a0aec0;
}

.chat-profile {
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.loading {
    animation-duration: 3s;
}

@media (max-width: 768px) {
    .card-footer {
        width: 100% !important;
    }
}
</style>

<!-- untuk penggunaan ai secara dinamis -->
<script>
// Fungsi untuk mengupdate usage count
async function updateUsageCount() {
    try {
        const response = await fetch('get_usage.php');
        const data = await response.json();
        if (data.remaining_usage !== undefined) {
            document.getElementById('tersedia').innerHTML = 
                `Sisa penggunaan hari ini: ${data.remaining_usage}/20`;
        }
    } catch (error) {
        console.error('Error updating usage count:', error);
    }
}

// Modifikasi bagian handle submit chat
document.getElementById('chat-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const input = document.getElementById('user-input');
    const message = input.value.trim();
    
    if (!message) return;

    loading.style.display = 'block';
    tersedia.style.display = 'none';

    addMessage(message, 'user-message');
    input.value = '';

    try {
        const response = await fetch('ai_process.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ message })
        });

        const data = await response.json();
        
        if (data.error) {
            addMessage(data.error, 'ai-message');
        } else {

            const formattedResponse = data.response
            .replace(/<br \/>/g, '\n')  // Replace <br /> with newlines
            .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>'); // Replace **text** with <strong>text</strong>


            addMessage(data.response, 'ai-message');
            // Update usage count setelah berhasil mendapat respons
            await updateUsageCount();
        }
    } catch (error) {
        console.error('Error:', error);
        addMessage('Maaf, terjadi kesalahan.', 'ai-message');
    } finally {
        loading.style.display = 'none';
        tersedia.style.display = 'block';
    }
});

// Update usage count setiap kali halaman dimuat
document.addEventListener('DOMContentLoaded', updateUsageCount);
</script>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const loading = document.getElementById('loading');
    const tersedia = document.getElementById('tersedia');
    const chatContainer = document.getElementById('chat-container');
    
    function addMessage(text, className) {
        const messageWrapper = document.createElement('div');
        messageWrapper.classList.add('d-flex', 'mb-3', className === 'user-message' ? 'justify-content-end' : 'justify-content-start');

        const isUser = className === 'user-message';
        const profileImg = isUser ? 
            '<?php echo !empty($siswa["foto_profil"]) ? "uploads/profil/".$siswa["foto_profil"] : "assets/pp.png"; ?>' : 
            'assets/ai_chat.png';

        messageWrapper.innerHTML = `
            <div class="d-flex align-items-center pt-1 pb-1 p-2 rounded-4 ${isUser ? 'flex-row-reverse' : ''}" style="background-color: rgb(239, 239, 239); max-width:30rem">
                <img src="${profileImg}" class="chat-profile bg-white ms-2 me-2 rounded-circle" alt="${isUser ? 'user' : 'ai'} profile" style="width: 20px; height: 20px; object-fit: cover;">
                <div class="chat-bubble rounded p-2 align-content-center ${className}">
                    <p style="font-size: 12px; margin: 0; padding: 0;">${text}</p>
                </div>
            </div>
        `;

        chatContainer.appendChild(messageWrapper);
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }

    document.getElementById('chat-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const input = document.getElementById('user-input');
        const message = input.value.trim();
        
        if (!message) return;

        loading.style.display = 'block';
        tersedia.style.display = 'none';

        addMessage(message, 'user-message');
        input.value = '';

        try {
            const response = await fetch('ai_process.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ message })
            });

            const data = await response.json();
            
            if (data.error) {
                addMessage(data.error, 'ai-message');
            } else {
                addMessage(data.response, 'ai-message');
            }
        } catch (error) {
            console.error('Error:', error);
            addMessage('Maaf, terjadi kesalahan.', 'ai-message');
        } finally {
            loading.style.display = 'none';
            tersedia.style.display = 'block';
        }
    });
});
</script>
</body>
</html>