
<body>

<?= view('layouts/header') ?>

<div class="page-wrapper">
    <?= view('layouts/sidebar') ?>

    <main class="main-content">
        <?= $this->renderSection('content') ?>
    </main>
</div>

<?= view('layouts/footer') ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"  crossorigin="anonymous"></script>
<script>if(localStorage.getItem("daynight-theme")==="carbon"){document.documentElement.classList.add("carbon");}</script>
<script src="assets/js/theme.js"></script>
</body>
</html>