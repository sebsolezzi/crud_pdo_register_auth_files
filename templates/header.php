<?php
  session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FotoTeca</title>
  <link rel="stylesheet" href="css/bootstrap.min.css">
</head>

<body>
  <nav class="navbar navbar-expand-lg bg-info" data-bs-theme="dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="/">FotoTeca</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarColor01">
        <ul class="navbar-nav me-auto">
          <li class="nav-item">
            <a class="nav-link" href="/">Home

            </a>
          </li>
          <?php if (isset($_SESSION['id'])): ?>
            <li class="nav-item">
              <a class="nav-link" href="/create.php">Nuevo Post</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/logout.php">Logout</a>
            </li>
          <?php endif; ?>
          <li class="nav-item">
            <a class="nav-link" href="/about.php">About</a>
          </li>

        </ul>
      </div>
    </div>
  </nav>