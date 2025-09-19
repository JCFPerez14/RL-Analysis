<?php
session_start();
include 'connections.php';

// Fetch all courses
$query = "SELECT * FROM courses ORDER BY course_name ASC";
$result = $conn->query($query);
if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Courses - National University Lipa</title>
  <link rel="stylesheet" href="style.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<!-- Navbar -->
<?php include 'nav.php'; ?>

<div class="container my-5">
  <h2 class="text-navy mb-4 text-center">📚 Available Courses</h2>

  <?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-info text-center">
      <?= $_SESSION['message']; unset($_SESSION['message']); ?>
    </div>
  <?php endif; ?>

  <?php if ($result->num_rows === 0): ?>
    <p class="text-center text-muted">No courses found.</p>
  <?php else: ?>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
      <?php while ($course = $result->fetch_assoc()): ?>
        <div class="col">
          <div class="card border-navy shadow-sm h-100">
            <div class="card-body d-flex flex-column">
              <h5 class="card-title text-navy fw-bold"><?= htmlspecialchars($course['course_name']); ?></h5>
              <h6 class="card-subtitle mb-2 text-muted"><?= htmlspecialchars($course['course_code']); ?></h6>
              <p class="card-text flex-grow-1">
                <?= htmlspecialchars(substr($course['description'], 0, 120)); ?><?= strlen($course['description']) > 120 ? '...' : ''; ?>
              </p>
              <a href="details.php?id=<?= $course['id']; ?>" class="btn btn-navy mt-auto align-self-start">View Details</a>
            </div>
            <div class="card-footer text-muted small">
              Program: <?= htmlspecialchars($course['program']); ?> | Semester: <?= htmlspecialchars($course['semester']); ?>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
