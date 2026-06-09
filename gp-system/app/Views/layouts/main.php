<!DOCTYPE html>
<html>
<head>
    <title><?= $title ?? 'My App' ?></title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<?= view('layouts/header') ?>

<div class="page-wrapper">
    <?= view('layouts/sidebar') ?>

    <main class="main-content">
        <?= $this->renderSection('content') ?>
    </main>
</div>

<?= view('layouts/footer') ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>