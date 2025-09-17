<?php
session_start();
include 'connections.php';

// Check if ID is provided and valid
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: courses.php"); // redirect to courses list or homepage
    exit();
}

$course_id = intval($_GET['id']);

// Fetch course details
$stmt = $conn->prepare("SELECT * FROM courses WHERE id = ?");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['message'] = "Course not found.";
    header("Location: courses.php");
    exit();
}

$course = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Course Details - <?= htmlspecialchars($course['course_name']); ?></title>
    <link rel="stylesheet" href="style.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
      .breadcrumb-item + .breadcrumb-item::before {
        color: #001f3f;
      }
      .course-header {
        border-bottom: 3px solid #FFD700;
        padding-bottom: 8px;
      }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg bg-navy px-3">
  <a class="navbar-brand fw-bold text-golden" href="index.php">National University - Lipa</a>
  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarRight" aria-controls="navbarRight" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse justify-content-end" id="navbarRight">
    <ul class="navbar-nav align-items-center">
      <li class="nav-item"><a class="nav-link fw-bold text-white" href="index.php">Home</a></li>
      <li class="nav-item"><a class="nav-link fw-bold text-white" href="courses.php">Courses</a></li>
      <?php if (isset($_SESSION['user'])): ?>
      <li class="nav-item dropdown">
        <a href="#" class="nav-link fw-bold dropdown-toggle text-white" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          <?= htmlspecialchars($_SESSION['user']['firstname']); ?>
        </a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
          <li><a class="dropdown-item">Email: <?= htmlspecialchars($_SESSION['user']['email']); ?></a></li>
          <li><a class="dropdown-item">Program: <?= htmlspecialchars($_SESSION['user']['program']); ?></a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
        </ul>
      </li>
      <?php else: ?>
      <li class="nav-item"><a class="nav-link fw-bold text-white" href="login.php">Login</a></li>
      <?php endif; ?>
    </ul>
  </div>
</nav>

<!-- Main Content -->
<div class="container my-5">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb bg-light px-3 py-2 rounded shadow-sm">
        <li class="breadcrumb-item"><a href="index.php" class="text-navy fw-semibold">Home</a></li>
        <li class="breadcrumb-item"><a href="courses.php" class="text-navy fw-semibold">Courses</a></li>
        <li class="breadcrumb-item active text-golden fw-bold" aria-current="page"><?= htmlspecialchars($course['course_name']); ?></li>
      </ol>
    </nav>

    <h2 class="course-header text-navy mb-4"><?= htmlspecialchars($course['course_name']); ?> <small class="text-muted">(<?= htmlspecialchars($course['course_code']); ?>)</small></h2>

    <div class="card border-navy shadow-sm">
        <div class="card-body">
            <p><strong>Program:</strong> <?= htmlspecialchars($course['program']); ?></p>
            <p><strong>Semester:</strong> <?= htmlspecialchars($course['semester']); ?></p>
            <p><strong>Units:</strong> <?= intval($course['units']); ?></p>
            <p><strong>Prerequisites:</strong> <?= htmlspecialchars($course['prerequisites'] ?: 'None'); ?></p>
            <hr>
            <h5>Description</h5>
            <p><?= nl2br(htmlspecialchars($course['description'])); ?></p>
            <a href="courses.php" class="btn btn-navy mt-3">Back to Courses</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
