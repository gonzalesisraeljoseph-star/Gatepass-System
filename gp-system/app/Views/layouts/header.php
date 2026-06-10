<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= $title ?? 'My App' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <nav id="navbar-header" class="navbar navbar-expand-lg ">
      <div class="container-fluid">
        <a class="navbar-brand" href="#">Navbar</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="#">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Features</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Pricing</a>
        </li>
        <li class="nav-item">
          <a class="nav-link disabled" aria-disabled="true">Disabled</a>
        </li>
      </ul>
      <div class="theme-toggle">
            <button class="theme-btn" id="btn-snow" onclick="setTheme('snow')" aria-label="Light mode">
                <i class="fa-solid fa-sun" aria-hidden="true"></i>
                Light
            </button>
            <button class="theme-btn" id="btn-carbon" onclick="setTheme('carbon')" aria-label="Dark mode">
                <i class="fa-solid fa-moon" aria-hidden="true"></i>
                Dark
            </button>
    </div>
    </div>
    
  </div>
   
</nav>