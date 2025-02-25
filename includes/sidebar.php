<?php
$is_guru = $_SESSION['level'] == 'guru';
?>

<div class="col-auto vh-100 p-3 p-md-4 menu-samping d-none d-md-block" style="background-color:rgb(238, 236, 226)">
    <!-- Logo -->
    <div class="ps-2 mb-4">
        <a href="<?php echo $is_guru ? 'beranda_guru.php' : 'beranda.php'; ?>" class="text-decoration-none text-dark d-flex align-items-center gap-2">
            <img src="assets/smagaedu.png" alt="" width="28" class="logo_orange">
            <div>
                <h1 class="m-0" style="font-size: 18px;">SMAGAEdu</h1>
                <p class="m-0 text-muted" style="font-size: 11px;">LMS</p>
            </div>
        </a>
    </div>

    <!-- Menu Items -->
    <div class="d-flex flex-column gap-1">
        <?php
        $menu_items = $is_guru ? [
            ['url' => 'beranda_guru.php', 'icon' => 'bi-house-door', 'text' => 'Beranda'],
            ['url' => 'ujian_guru.php', 'icon' => 'bi-file-text', 'text' => 'Ujian'],
            ['url' => 'profil_guru.php', 'icon' => 'bi-person', 'text' => 'Profil'],
            ['url' => 'ai_guru.php', 'icon' => 'bi-stars', 'text' => 'SAGA AI'],
            ['url' => 'raport_pg.php', 'icon' => 'bi-journal-text', 'text' => 'Raport', 'subtitle' => 'P. GUIDENCE'],
            ['url' => 'bantuan.php', 'icon' => 'bantuan_outfill.png', 'text' => 'Bantuan', 'is_image' => true]
        ] : [
            ['url' => 'beranda.php', 'icon' => 'bi-house-door', 'text' => 'Beranda'],
            ['url' => 'ujian.php', 'icon' => 'bi-file-text', 'text' => 'Ujian'],
            ['url' => 'profil.php', 'icon' => 'bi-person', 'text' => 'Profil'],
            ['url' => 'ai.php', 'icon' => 'bi-stars', 'text' => 'SAGA AI'],
            ['url' => 'bantuan.php', 'icon' => 'bantuan_outfill.png', 'text' => 'Bantuan', 'is_image' => true]
        ];

        $current_page = basename($_SERVER['PHP_SELF']);
        
        foreach($menu_items as $item) {
            $is_active = ($current_page === $item['url']) ? 'active' : '';
            ?>
            <a href="<?= $item['url'] ?>" class="text-decoration-none text-dark">
                <div class="menu-item <?= $is_active ?> d-flex align-items-center">
                    <?php if(isset($item['is_image']) && $item['is_image']): ?>
                        <img src="assets/<?= $item['icon'] ?>" alt="" class="menu-icon">
                    <?php else: ?>
                        <i class="bi <?= $item['icon'] ?> menu-icon"></i>
                    <?php endif; ?>
                    <div>
                        <span class="menu-text m-0 p-0"><?= $item['text'] ?></span>
                        <?php if(isset($item['subtitle'])): ?>
                            <p class="text-muted m-0 p-0" style="font-size: 10px;"><?= $item['subtitle'] ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </a>
        <?php } ?>
    </div>

    <!-- Profile Section -->
    <div class="mt-auto position-absolute bottom-0 start-0 p-3 w-100">
        <div class="dropdown">
            <button class="btn d-flex align-items-center gap-2 w-100 rounded-3 border bg-white" type="button" data-bs-toggle="dropdown">
                <img src="<?php echo $is_guru ? 
                    (!empty($guru['foto_profil']) ? 'uploads/profil/'.$guru['foto_profil'] : 'assets/pp.png') :
                    (!empty($siswa['photo_url']) ? $siswa['photo_url'] : 'assets/pp.png'); ?>" 
                    width="32" class="rounded-circle">
                <span class="text-truncate" style="font-size: 13px;">
                    <?php echo $is_guru ? $guru['namaLengkap'] : $siswa['nama']; ?>
                </span>
            </button>
            <ul class="dropdown-menu dropdown-menu-start w-100 shadow-sm border-0 py-2" style="font-size: 13px;">
                <li>
                    <a class="dropdown-item py-2 px-3" href="#" data-bs-toggle="modal" data-bs-target="#modal_pengaturan">
                        <i class="bi bi-gear me-2 text-muted"></i>Pengaturan
                    </a>
                </li>
                <li><hr class="dropdown-divider mx-2"></li>
                <li>
                    <a class="dropdown-item py-2 px-3 text-danger" href="logout.php">
                        <i class="bi bi-box-arrow-right me-2"></i>Keluar
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
