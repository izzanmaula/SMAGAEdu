<?php
require "koneksi.php";

if(isset($_GET['tingkat'])) {
    $tingkat = mysqli_real_escape_string($koneksi, $_GET['tingkat']);
    
    $query = "SELECT * FROM siswa WHERE tingkat = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "s", $tingkat);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if(mysqli_num_rows($result) > 0) {
        while($siswa = mysqli_fetch_assoc($result)) {
            ?>
            <div class="form-check mb-2">
                <input class="form-check-input siswa-checkbox" type="checkbox" 
                       name="siswa_ids[]" value="<?php echo $siswa['id']; ?>">
                <label class="form-check-label">
                    <?php echo htmlspecialchars($siswa['nama']); ?>
                </label>
            </div>
            <?php
        }
    } else {
        echo '<p class="text-muted">Tidak ada siswa untuk tingkat ini</p>';
    }
}
?>