<!DOCTYPE html>
<html>
<head>
    <title><?= $title ?? 'My App' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-7Un+XjYcjR1YqQXsb++Nw7aLdA/kxz4F2cY4jygsRbaxjOGdKZ0+NmkP3B12/az8" crossorigin="anonymous">
</head>
<body>

<?= view('layouts/header') ?>
<?= view('layouts/sidebar') ?>

<?= $this->renderSection('content') ?>

<?= view('layouts/footer') ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>