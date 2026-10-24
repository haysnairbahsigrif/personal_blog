<?php

declare(strict_types=1);
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle       = 'Tentang';
$pageDescription = 'Tentang ' . SITE_NAME;
require __DIR__ . '/includes/header.php';
?>

<section class="container">
    <div class="about-content">
        <h1>Tentang <em><?= e(SITE_NAME) ?></em></h1>
        <p class="lead">
            Sebuah ruang kecil di internet tempat aku menuangkan pikiran,
            cerita, dan imajinasi — sebagian nyata, sebagian fiksi.
        </p>
        <p>
            Di sini kamu akan menemukan tulisan personal tentang kehidupan sehari-hari,
            juga cerita-cerita pendek yang lahir dari iseng dan rasa ingin tahu.
            Tidak ada jadwal tetap — aku menulis ketika ada yang ingin dikatakan.
        </p>
        <p>
            Kalau kamu menemukan sesuatu yang resonan, atau ingin berdiskusi,
            jangan ragu untuk meninggalkan jejak. Selamat membaca.
        </p>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>