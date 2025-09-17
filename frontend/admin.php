<?php
session_start();
include 'connections.php';

// Restrict access to admin only
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Handle updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $user_id = intval($_POST['user_id']);

    // Collect fields (adjust as per your schema)
    $status = $_POST['status'] ?? '';
    $program = $_POST['program'] ?? '';
    $second_program = $_POST['second_program'] ?? '';
    $sex = $_POST['sex'] ?? '';
    $family_income = $_POST['family_income'] ?? '';
    $father_occupation = $_POST['father_occupation'] ?? '';
    $mother_occupation = $_POST['mother_occupation'] ?? '';
    $birthplace = $_POST['birthplace'] ?? '';
    $city = $_POST['city'] ?? '';
    $province = $_POST['province'] ?? '';
    $current_address = $_POST['current_address'] ?? '';
    $mobile = $_POST['mobile'] ?? '';
    $nationality = $_POST['nationality'] ?? '';
    $academic_year = $_POST['academic_year'] ?? '';
    $academic_term = $_POST['academic_term'] ?? '';
    $applying_for = $_POST['applying_for'] ?? '';
    $strand = $_POST['strand'] ?? '';
    $firstname = $_POST['firstname'] ?? '';
    $middlename = $_POST['middlename'] ?? '';
    $lastname = $_POST['lastname'] ?? '';

    // Update users table for status only (since program moved to user_info)
    $stmt1 = $conn->prepare("
        UPDATE users 
        SET status = ?
        WHERE id = ?
    ");
    if ($stmt1) {
        $stmt1->bind_param("si", $status, $user_id);
        $stmt1->execute();
        $stmt1->close();
    } else {
        $_SESSION['message'] = "Failed to prepare users update: " . $conn->error;
    }

    // Update user_info table for profile details including program fields
    $stmt2 = $conn->prepare("
        UPDATE user_info 
        SET firstname = ?, lastname = ?, mobile = ?, current_address = ?, sex = ?, family_income = ?, father_occupation = ?, mother_occupation = ?, birthplace = ?, city = ?, province = ?, nationality = ?, academic_year = ?, academic_term = ?, applying_for = ?, strand = ?, middlename = ?, program = ?, second_program = ?
        WHERE user_id = ?
    ");
    if ($stmt2) {
        $stmt2->bind_param(
            "sssssssssssssssssssssi",
            $firstname, $lastname, $mobile, $current_address, $sex, $family_income,
            $father_occupation, $mother_occupation, $birthplace, $city, $province,
            $nationality, $academic_year, $academic_term, $applying_for, $strand,
            $middlename, $program, $second_program, $user_id
        );
        if ($stmt2->execute()) {
            $_SESSION['message'] = "User updated successfully.";
        } else {
            $_SESSION['message'] = "Update failed: " . $stmt2->error;
        }
        $stmt2->close();
    } else {
        $_SESSION['message'] = "Failed to prepare user_info update: " . $conn->error;
    }

    header("Location: admin.php");
    exit();
}

// Fetch all users joined with user_info to display
$query = "
    SELECT users.*, user_info.firstname, user_info.middlename, user_info.lastname, user_info.mobile, user_info.current_address, 
           user_info.sex, user_info.family_income, user_info.father_occupation, user_info.mother_occupation, user_info.birthplace, 
           user_info.city, user_info.province, user_info.nationality, user_info.academic_year, user_info.academic_term, user_info.applying_for, user_info.strand,
           user_info.program, user_info.second_program
    FROM users
    LEFT JOIN user_info ON users.id = user_info.user_id
";
$result = $conn->query($query);
if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Dashboard - NU Lipa</title>
  <link rel="stylesheet" href="style.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    .bg-navy { background-color: #001f3f !important; }
    .text-golden { color: #FFD700 !important; }
    .table thead { background-color: #001f3f; color: #FFD700; }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg bg-navy px-3">
  <a class="navbar-brand fw-bold text-golden" href="index.php">National University - Lipa</a>
  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarRight" aria-controls="navbarRight" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse justify-content-end" id="navbarRight">
    <ul class="navbar-nav align-items-center">
      <li class="nav-item"><a class="nav-link fw-bold text-white" href="index.php">Home</a></li>
      <li class="nav-item"><a class="nav-link fw-bold text-white" href="enrollee.php">Dashboard</a></li>
      <li class="nav-item"><a class="nav-link fw-bold text-golden" href="admin_panel.php">Admin Panel</a></li>
      <li class="nav-item dropdown">
        <a href="#" class="nav-link fw-bold dropdown-toggle text-white" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          <?= isset($_SESSION['user']) ? htmlspecialchars($_SESSION['user']['firstname']) : "User"; ?>
        </a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
          <?php if (isset($_SESSION['user'])): ?>
            <li><a class="dropdown-item">Email: <?= htmlspecialchars($_SESSION['user']['email']); ?></a></li>
            <li><a class="dropdown-item">Program: <?= htmlspecialchars($_SESSION['user_info']['program'] ?? ''); ?></a></li>
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

<div class="container mt-5">
  <h2 class="text-center text-navy fw-bold mb-4">📋 Student Management</h2>

  <?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-info text-center">
      <?= $_SESSION['message']; unset($_SESSION['message']); ?>
    </div>
  <?php endif; ?>

  <div class="table-responsive">
    <table class="table table-bordered table-striped align-middle">
      <thead>
        <tr>
          <th>ID</th>
          <th>Full Name</th>
          <th>Email</th>
          <th>Mobile</th>
          <th>Nationality</th>
          <th>Sex</th>
          <th>Academic Year</th>
          <th>Term</th>
          <th>Applying For</th>
          <th>Strand</th>
          <th>Program</th>
          <th>Second Program</th>
          <th>Status</th>
          <th>Address</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <form method="POST">
              <td><?= $row['id']; ?></td>
              <td><?= htmlspecialchars(trim($row['firstname'] . " " . $row['middlename'] . " " . $row['lastname'])); ?></td>
              <td><?= htmlspecialchars($row['email']); ?></td>
              <td><input type="text" name="mobile" value="<?= htmlspecialchars($row['mobile']); ?>" class="form-control form-control-sm"></td>
              <td><input type="text" name="nationality" value="<?= htmlspecialchars($row['nationality']); ?>" class="form-control form-control-sm"></td>
              <td>
                <select name="sex" class="form-select form-select-sm">
                  <option value="Male" <?= $row['sex'] === 'Male' ? 'selected' : ''; ?>>Male</option>
                  <option value="Female" <?= $row['sex'] === 'Female' ? 'selected' : ''; ?>>Female</option>
                  <option value="Prefer not to say" <?= $row['sex'] === 'Prefer not to say' ? 'selected' : ''; ?>>Prefer not to say</option>
                </select>
              </td>
              <td><input type="text" name="academic_year" value="<?= htmlspecialchars($row['academic_year']); ?>" class="form-control form-control-sm"></td>
              <td><input type="text" name="academic_term" value="<?= htmlspecialchars($row['academic_term']); ?>" class="form-control form-control-sm"></td>
              <td><input type="text" name="applying_for" value="<?= htmlspecialchars($row['applying_for']); ?>" class="form-control form-control-sm"></td>
              <td><input type="text" name="strand" value="<?= htmlspecialchars($row['strand']); ?>" class="form-control form-control-sm"></td>
              <td>
                <select name="program" class="form-select form-select-sm">
                  <option value="BSCS" <?= $row['program'] === 'BSCS' ? 'selected' : ''; ?>>BSCS</option>
                  <option value="BSIT" <?= $row['program'] === 'BSIT' ? 'selected' : ''; ?>>BSIT</option>
                  <option value="BSCE" <?= $row['program'] === 'BSCE' ? 'selected' : ''; ?>>BSCE</option>
                  <option value="BSArch" <?= $row['program'] === 'BSArch' ? 'selected' : ''; ?>>BSArch</option>
                  <option value="BSMT" <?= $row['program'] === 'BSMT' ? 'selected' : ''; ?>>BSMT</option>
                  <option value="BSN" <?= $row['program'] === 'BSN' ? 'selected' : ''; ?>>BSN</option>
                  <option value="BSPYS" <?= $row['program'] === 'BSPYS' ? 'selected' : ''; ?>>BSPYS</option>
                  <option value="BSTM" <?= $row['program'] === 'BSTM' ? 'selected' : ''; ?>>BSTM</option>
                  <option value="BSA - Marketing" <?= $row['program'] === 'BSA - Marketing' ? 'selected' : ''; ?>>BSA - Marketing</option>
                  <option value="BSA - Financial Management" <?= $row['program'] === 'BSA - Financial Management' ? 'selected' : ''; ?>>BSA - Financial Management</option>
                </select>
              </td>
              <td>
                <select name="second_program" class="form-select form-select-sm">
                  <option value="">None</option>
                  <option value="BSCS" <?= $row['second_program'] === 'BSCS' ? 'selected' : ''; ?>>BSCS</option>
                  <option value="BSIT" <?= $row['second_program'] === 'BSIT' ? 'selected' : ''; ?>>BSIT</option>
                  <option value="BSCE" <?= $row['second_program'] === 'BSCE' ? 'selected' : ''; ?>>BSCE</option>
                  <option value="BSArch" <?= $row['second_program'] === 'BSArch' ? 'selected' : ''; ?>>BSArch</option>
                  <option value="BSMT" <?= $row['second_program'] === 'BSMT' ? 'selected' : ''; ?>>BSMT</option>
                  <option value="BSN" <?= $row['second_program'] === 'BSN' ? 'selected' : ''; ?>>BSN</option>
                  <option value="BSPYS" <?= $row['second_program'] === 'BSPYS' ? 'selected' : ''; ?>>BSPYS</option>
                  <option value="BSTM" <?= $row['second_program'] === 'BSTM' ? 'selected' : ''; ?>>BSTM</option>
                  <option value="BSA - Marketing" <?= $row['second_program'] === 'BSA - Marketing' ? 'selected' : ''; ?>>BSA - Marketing</option>
                  <option value="BSA - Financial Management" <?= $row['second_program'] === 'BSA - Financial Management' ? 'selected' : ''; ?>>BSA - Financial Management</option>
                </select>
              </td>
              <td>
                <select name="status" class="form-select form-select-sm">
                  <option value="Enrolled" <?= $row['status'] === 'Enrolled' ? 'selected' : ''; ?>>Enrolled</option>
                  <option value="Not Enrolled" <?= $row['status'] === 'Not Enrolled' ? 'selected' : ''; ?>>Not Enrolled</option>
                  <option value="Pending" <?= $row['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                  <option value="Dropped" <?= $row['status'] === 'Dropped' ? 'selected' : ''; ?>>Dropped</option>
                </select>
              </td>
              <td><input type="text" name="current_address" value="<?= htmlspecialchars($row['current_address']); ?>" class="form-control form-control-sm"></td>
              <td>
                <input type="hidden" name="user_id" value="<?= $row['id']; ?>" />
                <button type="submit" name="update_user" class="btn btn-sm btn-primary">Save</button>
              </td>
            </form>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
