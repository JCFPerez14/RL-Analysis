<?php
session_start();
include 'connections.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>NU Portal</title>
  <link rel="stylesheet" href="style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
<?php include 'nav.php'; ?>

<!-- Hero Section -->
<section class="hero">
  <div class="hero-overlay">
    <h1 class="fw-bold text-golden">Welcome to National University - Lipa</h1>
    <p class="fs-5">Your gateway to student services, enrollment tracking, and analytics.</p>
    <?php if (!isset($_SESSION['user'])): ?>
      <a href="register.php" class="btn btn-warning btn-lg me-2">Register</a>
      <a href="login.php" class="btn btn-primary btn-lg">Login</a>
    <?php else: ?>
      <a href="details.php" class="btn btn-success btn-lg">View Course Details</a>
    <?php endif; ?>
  </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
