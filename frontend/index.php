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
  <style>
    .bg-navy { background-color: #001f3f !important; }
    .text-golden { color: #FFD700 !important; }
    .hero {
      background: url('images/bg.jpg') no-repeat center center/cover;
      height: 90vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .hero-overlay {
      background: rgba(0,0,0,0.6);
      padding: 40px;
      border-radius: 12px;
      text-align: center;
      color: white;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg bg-navy px-3">
  <a class="navbar-brand fw-bold text-golden" href="index.php">National University - Lipa</a>

  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
    <ul class="navbar-nav align-items-center">

      <!-- Always visible -->
      <li class="nav-item">
        <a class="nav-link text-golden fw-bold" href="index.php">Home</a>
      </li>
      
      <!--
      <li class="nav-item">
        <a class="nav-link text-white fw-bold" href="heatmap.php"></a>
      </li>
      -->

      <!-- ✅ Visible only to admins -->
      <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
        <li class="nav-item">
          <a class="nav-link text-white fw-bold" href="enrollee.php">Dashboard</a>
        </li>
        
        <li class="nav-item">
          <a class="nav-link text-white fw-bold" href="admin.php">Admin Panel</a>
        </li>
      <?php endif; ?>

      <!-- User Dropdown -->
      <li class="nav-item dropdown ms-3">
        <a href="#" class="nav-link dropdown-toggle text-white fw-bold" id="userDropdown" data-bs-toggle="dropdown">
          <?php echo isset($_SESSION['user']) ? $_SESSION['user']['firstname'] : "User"; ?>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <?php if (isset($_SESSION['user'])): ?>
            <li><a class="dropdown-item">Email: <?php echo $_SESSION['user']['email']; ?></a></li>
            <li><a class="dropdown-item">Program: <?php echo $_SESSION['user']['program']; ?></a></li>
            <li><a class="dropdown-item">Role: <?php echo $_SESSION['user']['role']; ?></a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
          <?php else: ?>
            <li><a class="dropdown-item" href="login.php">Login</a></li>
            <li><a class="dropdown-item" href="register.php">Register</a></li>
          <?php endif; ?>
        </ul>
      </li>
    </ul>
  </div>
</nav>

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
