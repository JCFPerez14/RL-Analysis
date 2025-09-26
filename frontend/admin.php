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

    // Collect fields
    $status = $_POST['status'] ?? '';
    $program = $_POST['program'] ?? '';
    $second_program = $_POST['second_program'] ?? '';
    $firstname = $_POST['firstname'] ?? '';
    $middlename = $_POST['middlename'] ?? '';
    $lastname = $_POST['lastname'] ?? '';
    $email = trim($_POST['email'] ?? '');
    $mobile = $_POST['mobile'] ?? '';

    // ✅ Always update status
    $stmtStatus = $conn->prepare("UPDATE users SET status=? WHERE id=? AND role='student'");
    $stmtStatus->bind_param("si", $status, $user_id);
    $stmtStatus->execute();
    $stmtStatus->close();

    // ✅ Update email only if not empty and unique
    if (!empty($email)) {
        $check = $conn->prepare("SELECT id FROM users WHERE email=? AND id<>? AND role='student'");
        $check->bind_param("si", $email, $user_id);
        $check->execute();
        $check->store_result();

        if ($check->num_rows === 0) {
            $stmtEmail = $conn->prepare("UPDATE users SET email=? WHERE id=? AND role='student'");
            $stmtEmail->bind_param("si", $email, $user_id);
            $stmtEmail->execute();
            $stmtEmail->close();
        } else {
            $_SESSION['message'] = "⚠️ Email already exists. No changes made to email.";
        }
        $check->close();
    }

    // ✅ Update user_info table
    $stmt2 = $conn->prepare("UPDATE user_info 
        SET firstname=?, middlename=?, lastname=?, program=?, second_program=?, mobile=? 
        WHERE user_id=?");
    $stmt2->bind_param("ssssssi", $firstname, $middlename, $lastname, $program, $second_program, $mobile, $user_id);
    $stmt2->execute();
    $stmt2->close();

    if (!isset($_SESSION['message'])) {
        $_SESSION['message'] = "✅ Student updated successfully.";
    }

    header("Location: admin.php");
    exit();
}

// ✅ Fetch distinct programs for filter dropdown (students only)
$programs = $conn->query("SELECT DISTINCT program 
                          FROM user_info 
                          INNER JOIN users ON user_info.user_id = users.id
                          WHERE users.role='student' AND program IS NOT NULL AND program<>'' 
                          ORDER BY program ASC");

// ✅ Apply filter if set
$filter_program = $_GET['program'] ?? '';
$query = "SELECT users.id, users.email, users.status, user_info.* 
          FROM users 
          LEFT JOIN user_info ON users.id=user_info.user_id
          WHERE users.role='student'";

if (!empty($filter_program)) {
    $stmt = $conn->prepare($query . " AND user_info.program=?");
    $stmt->bind_param("s", $filter_program);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
} else {
    $result = $conn->query($query);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - NU Lipa</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .bg-navy { background-color: #001f3f !important; }
    .text-golden { color: #FFD700 !important; }
    .student-card { transition: transform 0.2s; }
    .student-card:hover { transform: scale(1.02); }
    .card-body { display: flex; flex-direction: column; }
  </style>
</head>
<body class="bg-light">

<?php include 'nav.php'; ?>

<div class="container mt-5">
  <h2 class="text-center fw-bold text-navy mb-4">📋 Student Management</h2>

  <?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-info text-center">
      <?= $_SESSION['message']; unset($_SESSION['message']); ?>
    </div>
  <?php endif; ?>

  <!-- ✅ Filter by Course -->
  <form method="GET" class="mb-4 d-flex justify-content-center">
    <div class="input-group" style="max-width: 400px;">
      <label class="input-group-text">Filter by Program</label>
      <select name="program" class="form-select" onchange="this.form.submit()">
        <option value="">All Programs</option>
        <?php while ($p = $programs->fetch_assoc()): ?>
          <option value="<?= htmlspecialchars($p['program']); ?>" 
            <?= ($filter_program === $p['program']) ? 'selected' : ''; ?>>
            <?= htmlspecialchars($p['program']); ?>
          </option>
        <?php endwhile; ?>
      </select>
      <?php if (!empty($filter_program)): ?>
        <a href="admin.php" class="btn btn-secondary">Reset</a>
      <?php endif; ?>
    </div>
  </form>

  <!-- Student Cards -->
  <div class="row g-4">
    <?php while ($row = $result->fetch_assoc()): ?>
      <div class="col-md-4 d-flex">
        <div class="card student-card shadow-sm h-100 w-100">
          <div class="card-body">
            <h5 class="card-title">
              <?= htmlspecialchars($row['firstname'] . " " . $row['middlename'] . " " . $row['lastname']); ?>
            </h5>
            <p class="card-text mb-1"><strong>Email:</strong> <?= htmlspecialchars($row['email']); ?></p>
            <p class="card-text mb-1"><strong>Mobile:</strong> <?= htmlspecialchars($row['mobile']); ?></p>
            <p class="card-text mb-1"><strong>Program:</strong> <?= htmlspecialchars($row['program']); ?></p>
            <p class="card-text mb-1"><strong>Status:</strong> <?= htmlspecialchars($row['status']); ?></p>
            
            <div class="mt-auto">
              <!-- Quick Update (status only) -->
              <form method="POST" class="d-flex flex-column">
                <input type="hidden" name="user_id" value="<?= $row['user_id']; ?>">
                <select name="status" class="form-select form-select-sm mb-2">
                  <option value="Enrolled" <?= $row['status'] === 'Enrolled' ? 'selected' : ''; ?>>Enrolled</option>
                  <option value="Not Enrolled" <?= $row['status'] === 'Not Enrolled' ? 'selected' : ''; ?>>Not Enrolled</option>
                 
                </select>
                <button type="submit" name="update_user" class="btn btn-primary btn-sm">Update</button>
              </form>

              <!-- Full Edit Modal -->
              <button class="btn btn-outline-secondary btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#studentModal<?= $row['user_id']; ?>">
                View / Edit Details
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Full Edit Modal -->
      <div class="modal fade" id="studentModal<?= $row['user_id']; ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
          <div class="modal-content">
            <form method="POST">
              <div class="modal-header bg-navy text-golden">
                <h5 class="modal-title">Edit Student - <?= htmlspecialchars($row['firstname'] . " " . $row['lastname']); ?></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <input type="hidden" name="user_id" value="<?= $row['user_id']; ?>">
                <div class="row g-3">
                  <div class="col-md-4">
                    <label>First Name</label>
                    <input type="text" name="firstname" value="<?= htmlspecialchars($row['firstname']); ?>" class="form-control">
                  </div>
                  <div class="col-md-4">
                    <label>Middle Name</label>
                    <input type="text" name="middlename" value="<?= htmlspecialchars($row['middlename']); ?>" class="form-control">
                  </div>
                  <div class="col-md-4">
                    <label>Last Name</label>
                    <input type="text" name="lastname" value="<?= htmlspecialchars($row['lastname']); ?>" class="form-control">
                  </div>
                  <div class="col-md-6">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($row['email']); ?>" class="form-control">
                  </div>
                  <div class="col-md-6">
                    <label>Mobile</label>
                    <input type="text" name="mobile" value="<?= htmlspecialchars($row['mobile']); ?>" class="form-control">
                  </div>

                  <!-- REPLACED: Program input -> DROPDOWN -->
                  <div class="col-md-6">
                    <label>Program</label>
                    <select name="program" class="form-select">
                      <option value="">-- Select Program --</option>
                      <option value="BSCS" <?= (isset($row['program']) && $row['program'] === 'BSCS') ? 'selected' : ''; ?>>BSCS</option>
                      <option value="BSIT" <?= (isset($row['program']) && $row['program'] === 'BSIT') ? 'selected' : ''; ?>>BSIT</option>
                      <option value="BSCE" <?= (isset($row['program']) && $row['program'] === 'BSCE') ? 'selected' : ''; ?>>BSCE</option>
                      <option value="BSArch" <?= (isset($row['program']) && $row['program'] === 'BSArch') ? 'selected' : ''; ?>>BSArch</option>
                      <option value="BSMT" <?= (isset($row['program']) && $row['program'] === 'BSMT') ? 'selected' : ''; ?>>BSMT</option>
                      <option value="BSN" <?= (isset($row['program']) && $row['program'] === 'BSN') ? 'selected' : ''; ?>>BSN</option>
                      <option value="BSPYS" <?= (isset($row['program']) && $row['program'] === 'BSPYS') ? 'selected' : ''; ?>>BSPYS</option>
                      <option value="BSTM" <?= (isset($row['program']) && $row['program'] === 'BSTM') ? 'selected' : ''; ?>>BSTM</option>
                      <option value="BSA - Marketing" <?= (isset($row['program']) && $row['program'] === 'BSA - Marketing') ? 'selected' : ''; ?>>BSA - Marketing</option>
                      <option value="BSA - Financial Management" <?= (isset($row['program']) && $row['program'] === 'BSA - Financial Management') ? 'selected' : ''; ?>>BSA - Financial Management</option>
                    </select>
                  </div>

                  <!-- REPLACED: Second Program input -> DROPDOWN -->
                  <div class="col-md-6">
                    <label>Second Program</label>
                    <select name="second_program" class="form-select">
                      <option value="">-- Select Second Program --</option>
                      <option value="BSCS" <?= (isset($row['second_program']) && $row['second_program'] === 'BSCS') ? 'selected' : ''; ?>>BSCS</option>
                      <option value="BSIT" <?= (isset($row['second_program']) && $row['second_program'] === 'BSIT') ? 'selected' : ''; ?>>BSIT</option>
                      <option value="BSCE" <?= (isset($row['second_program']) && $row['second_program'] === 'BSCE') ? 'selected' : ''; ?>>BSCE</option>
                      <option value="BSArch" <?= (isset($row['second_program']) && $row['second_program'] === 'BSArch') ? 'selected' : ''; ?>>BSArch</option>
                      <option value="BSMT" <?= (isset($row['second_program']) && $row['second_program'] === 'BSMT') ? 'selected' : ''; ?>>BSMT</option>
                      <option value="BSN" <?= (isset($row['second_program']) && $row['second_program'] === 'BSN') ? 'selected' : ''; ?>>BSN</option>
                      <option value="BSPYS" <?= (isset($row['second_program']) && $row['second_program'] === 'BSPYS') ? 'selected' : ''; ?>>BSPYS</option>
                      <option value="BSTM" <?= (isset($row['second_program']) && $row['second_program'] === 'BSTM') ? 'selected' : ''; ?>>BSTM</option>
                      <option value="BSA - Marketing" <?= (isset($row['second_program']) && $row['second_program'] === 'BSA - Marketing') ? 'selected' : ''; ?>>BSA - Marketing</option>
                      <option value="BSA - Financial Management" <?= (isset($row['second_program']) && $row['second_program'] === 'BSA - Financial Management') ? 'selected' : ''; ?>>BSA - Financial Management</option>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <label>Status</label>
                    <select name="status" class="form-select">
                      <option value="Enrolled" <?= $row['status'] === 'Enrolled' ? 'selected' : ''; ?>>Enrolled</option>
                      <option value="Not Enrolled" <?= $row['status'] === 'Not Enrolled' ? 'selected' : ''; ?>>Not Enrolled</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="submit" name="update_user" class="btn btn-primary">Save Changes</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              </div>
            </form>
          </div>
        </div>
      </div>

    <?php endwhile; ?>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
