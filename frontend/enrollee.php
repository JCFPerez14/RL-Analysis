<?php
session_start();
include 'connections.php';

// Fetch enrolled vs not enrolled students
$enrolled = $conn->query("SELECT COUNT(*) AS total FROM users WHERE status='Enrolled'")->fetch_assoc()['total'];
$not_enrolled = $conn->query("SELECT COUNT(*) AS total FROM users WHERE status='Not Enrolled'")->fetch_assoc()['total'];
$total_students = $enrolled + $not_enrolled;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Enrollee Statistics</title>
  <link rel="stylesheet" href="style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg bg-navy px-3">
  <a class="navbar-brand fw-bold text-golden" href="index.php">National University - Lipa</a>
  <div class="collapse navbar-collapse justify-content-end">
    <ul class="navbar-nav align-items-center">
      <li class="nav-item"><a class="nav-link text-white fw-bold" href="index.php">Home</a></li>
      <li class="nav-item"><a class="nav-link text-golden fw-bold" href="enrollee.php">Dashboard</a></li>
      <li class="nav-item"><a class="nav-link text-white fw-bold" href="admin.php">Admin Panel</a></li>

      <li class="nav-item dropdown ms-3">
        <a href="#" class="nav-link dropdown-toggle text-white fw-bold" data-bs-toggle="dropdown">
          <?php echo isset($_SESSION['user']) ? $_SESSION['user']['firstname'] : "User"; ?>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <?php if (isset($_SESSION['user'])): ?>
            <li><a class="dropdown-item">Email: <?php echo $_SESSION['user']['email']; ?></a></li>
            <li><a class="dropdown-item">Program: <?php echo $_SESSION['user']['program']; ?></a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
          <?php else: ?>
            <li><a class="dropdown-item" href="login.php">Login</a></li>
          <?php endif; ?>
        </ul>
      </li>
    </ul>
  </div>
</nav>

<!-- Hero Section -->
<section class="hero">
  <div class="hero-overlay container mt-5">
    
    <!-- Total Students Counter -->
    <div class="row mb-4">
      <div class="col-md-4 offset-md-4">
        <div class="card text-center shadow-lg border-0" style="background-color:#001f3f; color:#FFD700;">
          <div class="card-body">
            <h5 class="fw-bold">Total Students</h5>
            <h2 class="fw-bold"><?= $total_students ?></h2>
          </div>
        </div>
      </div>
    </div>

    <!-- Chart Card -->
    <div class="card shadow-lg p-4">
      <h3 class="text-center text-navy mb-4">Enrolled vs Not Enrolled Students</h3>
      <canvas id="enrollChart"></canvas>
    </div>
  </div>
</section>

<script>
const ctx = document.getElementById('enrollChart').getContext('2d');
new Chart(ctx, {
  type: 'pie',
  data: {
    labels: ['Enrolled', 'Not Enrolled'],
    datasets: [{
      data: [<?= $enrolled ?>, <?= $not_enrolled ?>],
      backgroundColor: ['#001f3f', '#FFD700'], // navy & golden
    }]
  },
  options: {
    responsive: true,
    plugins: {
      legend: { position: 'bottom' }
    }
  }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
