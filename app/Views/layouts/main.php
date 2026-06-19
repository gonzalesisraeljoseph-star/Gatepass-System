<!DOCTYPE html>
<html>
<head>
    <title><?= $title ?? 'My App' ?></title>
</head>
<body>

<?= view('layouts/header') ?>
<?= view('layouts/sidebar') ?>

<?= $this->renderSection('content') ?>

<?= view('layouts/footer') ?>

</body>
</html>