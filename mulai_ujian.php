<?php
// mulai_ujian.php
session_start();
require "koneksi.php";

if(!isset($_SESSION['userid']) || !isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$ujian_id = $_GET['id'];
$query = "SELECT * FROM bank_soal WHERE ujian_id = ?";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("i", $ujian_id);
$stmt->execute();
$result = $stmt->get_result();
$soal_array = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ujian Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700;1,900&family=PT+Serif:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            overflow: hidden;
            background-color: #f8f9fa;
            font-family: merriweather;
        }
        .soal-numbers {
            height: calc(100vh - 70px);
            overflow-y: auto;
        }
        .soal-number {
            width: 40px;
            height: 40px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .soal-number:hover {
            transform: scale(1.1);
        }
        .soal-content {
            height: calc(100vh - 70px);
            overflow-y: auto;
        }
        .option-card {
            cursor: pointer;
            transition: all 0.2s;
        }
        .option-card:hover {
            background-color: #e9ecef;
        }
        .option-card.selected {
            background-color: #da7756;
            color: white;
        }
        .color-web {
            background-color: rgb(218, 119, 86);
            transition: background-color 0.3s ease;
        }

        .color-web:hover{
            background-color: rgb(206, 100, 65);
        }
        .soal-number[data-status="answered"] {
            background-color: #da7756;
            color: white;
        }

        .soal-number[data-status="marked"] {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
    <nav class="navbar color-web">
        <div class="container-fluid">
        <div href="beranda.php" style="text-decoration: none; color: black;" class="d-flex align-items-center gap-2">
                            <img src="assets/smagaedu.png" alt="" width="30px" class="logo_orange">
                            <div>
                                <h1 class="display-5  p-0 m-0 text-white" style="font-size: 20px;">SMAGAEdu</h1>
                            </div>
        </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Nomor Soal -->
            <div class="col-md-3 border-end p-3">
                <div class="soal-numbers">
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach($soal_array as $index => $soal): ?>
                            <div class="soal-number rounded border d-flex align-items-center justify-content-center" 
                                data-soal="<?php echo $index; ?>"
                                data-status="unanswered">
                                <?php echo $index + 1; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Konten Soal -->
            <div class="col-md-9 p-4">
                <div class="soal-content">
                    <form id="exam-form">
                        <?php foreach($soal_array as $index => $soal): ?>
                            <div class="soal-page <?php echo $index === 0 ? '' : 'd-none'; ?>" 
                                 data-index="<?php echo $index; ?>">
                                <h5 class="mb-4">Soal <?php echo $index + 1; ?></h5>
                                <p class="mb-4"><?php echo $soal['pertanyaan']; ?></p>
                                
                                <?php
                                $options = ['a' => $soal['jawaban_a'], 
                                          'b' => $soal['jawaban_b'], 
                                          'c' => $soal['jawaban_c'], 
                                          'd' => $soal['jawaban_d']];
                                foreach($options as $key => $value):
                                ?>
                                    <div class="option-card p-3 rounded border mb-3" 
                                         data-value="<?php echo $key; ?>">
                                        <?php echo strtoupper($key) . ". " . $value; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    </form>
                    </div>
                    <!-- Navigation -->
                    <div class="d-flex justify-content-between position-fixed bottom-0 start-0 end-0 p-3 bg-white border-top" style="margin-left: 25%;">
                        <button class="btn color-web text-white bi-chevron-left" id="prev"></button>
                        <div class="d-flex gap-2">
                            <button class="btn btn-danger text-white bi-bookmark-star-fill" id="mark">
                                <p class="p-0 m-0 mt-1">Tandai Soal</p>
                            </button>
                            <button class="btn btn-secondary text-white bi-border" id="clear">
                                <p class="p-0 m-0 mt-1">Hapus Jawaban</p>
                            </button>
                            <button class="btn btn-success text-white bi-flag-fill" id="finish">
                                <p class="p-0 m-0 mt-1">Selesai Ujian</p>
                            </button>
                        </div>
                        <button class="btn color-web text-white bi-chevron-right" id="next"></button>
                    </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let currentSoal = 0;
        const totalSoal = <?php echo count($soal_array); ?>;
        const answers = new Map();
        const markedQuestions = new Set();

        function showSoal(index) {
            $('.soal-page').addClass('d-none');
            $(`.soal-page[data-index="${index}"]`).removeClass('d-none');
            currentSoal = index;
        }

        $('.soal-number').click(function() {
            const index = $(this).data('soal');
            showSoal(index);
        });

        $('.option-card').click(function() {
            const soalIndex = $(this).closest('.soal-page').data('index');
            const jawaban = $(this).data('value');
            
            $(this).closest('.soal-page').find('.option-card').removeClass('selected');
            $(this).addClass('selected');
            
            answers.set(soalIndex, jawaban);
            $(`.soal-number[data-soal="${soalIndex}"]`).attr('data-status', 'answered');

            $.post('save_jawaban.php', {
                ujian_id: <?php echo $ujian_id; ?>,
                soal_index: soalIndex,
                jawaban: jawaban
            });
        });

        $('#mark').click(() => {
            if(markedQuestions.has(currentSoal)) {
                markedQuestions.delete(currentSoal);
                $(`.soal-number[data-soal="${currentSoal}"]`).attr('data-status', 
                    answers.has(currentSoal) ? 'answered' : 'unanswered'
                );
            } else {
                markedQuestions.add(currentSoal);
                $(`.soal-number[data-soal="${currentSoal}"]`).attr('data-status', 'marked');
            }
        });

        $('#prev').click(() => {
            if(currentSoal > 0) {
                showSoal(currentSoal - 1);
            }
        });

        $('#next').click(() => {
            if(currentSoal < totalSoal - 1) {
                showSoal(currentSoal + 1);
            }
        });

        // Simpan jawaban di localStorage
        function saveAnswer(soalIndex, jawaban) {
            const storageKey = `ujian_${<?php echo $ujian_id; ?>}`;
            let answers = JSON.parse(localStorage.getItem(storageKey) || '{}');
            answers[soalIndex] = jawaban;
            localStorage.setItem(storageKey, JSON.stringify(answers));
        }

        // Load jawaban saat halaman dimuat
        function loadAnswers() {
            const storageKey = `ujian_${<?php echo $ujian_id; ?>}`;
            const answers = JSON.parse(localStorage.getItem(storageKey) || '{}');
            
            Object.entries(answers).forEach(([index, jawaban]) => {
                $(`.soal-page[data-index="${index}"] .option-card[data-value="${jawaban}"]`).addClass('selected');
                $(`.soal-number[data-soal="${index}"]`).attr('data-status', 'answered');
            });
        }

        // Update click handler
        $('.option-card').click(function() {
            const soalIndex = $(this).closest('.soal-page').data('index');
            const jawaban = $(this).data('value');
            
            $(this).closest('.soal-page').find('.option-card').removeClass('selected');
            $(this).addClass('selected');
            
            saveAnswer(soalIndex, jawaban);
            $(`.soal-number[data-soal="${soalIndex}"]`).attr('data-status', 'answered');
        });

        // Panggil loadAnswers saat halaman dimuat
        $(document).ready(loadAnswers);

        // Fungsi hapus jawaban
        $('#clear').click(() => {
            const soalIndex = currentSoal;
            
            // Hapus visual selection
            $(`.soal-page[data-index="${soalIndex}"] .option-card`).removeClass('selected');
            
            // Hapus status jawaban
            $(`.soal-number[data-soal="${soalIndex}"]`).attr('data-status', 'unanswered');
            
            // Hapus dari localStorage
            const storageKey = `ujian_${<?php echo $ujian_id; ?>}`;
            let answers = JSON.parse(localStorage.getItem(storageKey) || '{}');
            delete answers[soalIndex];
            localStorage.setItem(storageKey, JSON.stringify(answers));
            
            // Hapus dari database
            $.post('save_jawaban.php', {
                ujian_id: <?php echo $ujian_id; ?>,
                soal_index: soalIndex,
                jawaban: null
            });
        });

        $('#finish').click(() => {
    if(confirm('Apakah Anda yakin ingin menyelesaikan ujian?')) {
        const storageKey = `ujian_${<?php echo $ujian_id; ?>}`;
        const answers = JSON.parse(localStorage.getItem(storageKey) || '{}');
        
        $.post('submit_ujian.php', {
            ujian_id: <?php echo $ujian_id; ?>,
            answers: JSON.stringify(answers)
        }, function(response) {
            console.log(response);
            try {
                const result = JSON.parse(response);
                if(result.success) {
                    alert('Ujian berhasil diselesaikan');
                    window.location.href = 'ujian.php';
                } else {
                    alert('Terjadi kesalahan: ' + (result.error || 'Undefined error'));
                }
            } catch(e) {
                console.error('Parse error', e);
                alert('Terjadi kesalahan parsing');
            }
        }).fail(function(xhr, status, error) {
            console.error('AJAX Error:', status, error);
            alert('Terjadi kesalahan koneksi');
        });
    }
});

</script>
</body>
</html>