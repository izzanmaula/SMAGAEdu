<?php
// settings_modal.php
?>
<!-- Modal Pengaturan -->
<div class="modal fade" id="modal_pengaturan" tabindex="-1" aria-labelledby="label_pengaturan" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h1 class="modal-title fs-5 fw-bold" id="label_pengaturan">Pengaturan</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4">
                <div class="setting-item d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1">Mode Gelap <span class="badge" style="background-color: #da7756;">Eksperimental</span></h6>
                        <p class="text-muted small mb-0">Mengubah tampilan ke mode gelap, namun masih tahap eksperimental jadi mungkin
                            Anda akan melihat beberapa ketidaksesuaian tampilan.</p>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="darkModeSwitch">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.setting-item {
    padding: 12px 0;
    border-bottom: 1px solid #eee;
}
.setting-item:last-child {
    border-bottom: none;
}
.form-switch .form-check-input {
    width: 3em;
    height: 1.5em;
    background-color: #e9ecef;
    border-color: #e9ecef;
}
.form-switch .form-check-input:checked {
    background-color: #da7756;
    border-color: #da7756;
}
.form-switch .form-check-input:focus {
    box-shadow: 0 0 0 0.25rem rgba(218, 119, 86, 0.25);
}
</style>