<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/config.php';

// Configurações de página inicial
$pageTitle = SITE_NAME . " - Sua Landing Page";
$pageDescription = "Landing page de marketing digital para captação de leads";

// Headers e componentes
include_once __DIR__ . '/includes/header.php';
?>

<main>
    <?php include_once __DIR__ . '/components/hero-section.php'; ?>
    <?php include_once __DIR__ . '/components/features-section.php'; ?>
    <?php include_once __DIR__ . '/components/testimonials.php'; ?>
    <?php include_once __DIR__ . '/components/cta-section.php'; ?>
</main>

<?php
include_once __DIR__ . '/includes/footer.php';
?>