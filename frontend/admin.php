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
  <title>Students - AcademicsPro Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
  <style>
    body {
      background: #f8fafc;
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      color: #374151;
      margin: 0;
      padding: 0;
    }
    
    /* Header Styles */
    .header-bar {
      background: #ffffff;
      border-bottom: 1px solid #e5e7eb;
      padding: 1rem 2rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: sticky;
      top: 0;
      z-index: 100;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .logo {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      font-size: 1.5rem;
      font-weight: 700;
      color: #1f2937;
      text-decoration: none;
    }
    
    .logo-icon {
      width: 40px;
      height: 40px;
      background: linear-gradient(135deg, #001f54, #1d4ed8);
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: 700;
      font-size: 1.25rem;
    }
    
    .nav-menu {
      display: flex;
      gap: 2rem;
      align-items: center;
    }
    
    .nav-item {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.75rem 1rem;
      border-radius: 8px;
      text-decoration: none;
      color: #6b7280;
      font-weight: 500;
      transition: all 0.2s ease;
    }
    
    .nav-item.active {
      background: linear-gradient(135deg, #001f54, #1d4ed8);
      color: white;
    }
    
    .nav-item:hover:not(.active) {
      background: #f3f4f6;
      color: #374151;
    }
    
    .nav-icon {
      width: 20px;
      height: 20px;
    }

    /* Main Content */
    .main-content {
      padding: 2rem;
      max-width: 1200px;
      margin: 0 auto;
    }
    
    .page-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 2rem;
    }
    
    .page-title {
      font-size: 2rem;
      font-weight: 700;
      color: #1f2937;
      margin: 0;
    }
    
    .add-student-btn {
      background: linear-gradient(135deg, #001f54, #1d4ed8);
      color: white;
      border: none;
      padding: 0.75rem 1.5rem;
      border-radius: 8px;
      font-weight: 600;
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      transition: all 0.2s ease;
    }
    
    .add-student-btn:hover {
      transform: translateY(-1px);
      box-shadow: 0 4px 8px rgba(0, 31, 84, 0.3);
      color: white;
    }
    
    .plus-icon {
      width: 20px;
      height: 20px;
    }

    /* Filter Section */
    .filter-section {
      display: flex;
      align-items: center;
      gap: 1rem;
      margin-bottom: 2rem;
    }
    
    .filter-label {
      font-size: 0.875rem;
      font-weight: 600;
      color: #374151;
    }
    
    .filter-select {
      border: 2px solid #e5e7eb;
      border-radius: 8px;
      padding: 0.5rem 1rem;
      font-size: 0.875rem;
      background: white;
      transition: all 0.2s ease;
    }
    
    .filter-select:focus {
      border-color: #001f54;
      box-shadow: 0 0 0 3px rgba(0, 31, 84, 0.1);
      outline: none;
    }

    /* Table Styles */
    .students-table {
      background: white;
      border-radius: 12px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
      border: 1px solid #e5e7eb;
      overflow: hidden;
    }
    
    .table {
      margin: 0;
      border-collapse: collapse;
    }
    
    .table thead th {
      background: #f8fafc;
      border-bottom: 1px solid #e5e7eb;
      padding: 1rem;
      font-weight: 600;
      font-size: 0.875rem;
      color: #374151;
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }
    
    .table tbody td {
      padding: 1rem;
      border-bottom: 1px solid #f3f4f6;
      vertical-align: middle;
    }
    
    .table tbody tr:hover {
      background: #f9fafb;
    }
    
    .student-name {
      font-weight: 600;
      color: #1f2937;
    }
    
    .student-email {
      color: #6b7280;
      font-size: 0.875rem;
    }
    
    .student-program {
      color: #374151;
      font-weight: 500;
    }

    /* Status Badges */
    .status-badge {
      display: inline-flex;
      align-items: center;
      padding: 0.25rem 0.75rem;
      border-radius: 9999px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }
    
    .status-active {
      background: #d1fae5;
      color: #065f46;
    }
    
    .status-on-hold {
      background: #fef3c7;
      color: #92400e;
    }
    
    .status-inactive {
      background: #fee2e2;
      color: #991b1b;
    }

    /* View Link */
    .view-link {
      color: #001f54;
      text-decoration: none;
      font-weight: 500;
      font-size: 0.875rem;
      transition: color 0.2s ease;
    }
    
    .view-link:hover {
      color: #1d4ed8;
    }

    /* Alert Styles */
    .alert {
      padding: 0.875rem 1rem;
      border-radius: 8px;
      margin-bottom: 1.5rem;
      font-size: 0.875rem;
      border: none;
    }
    
    .alert-info {
      background: #eff6ff;
      color: #1e40af;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .header-bar {
        padding: 1rem;
        flex-wrap: wrap;
        gap: 1rem;
      }
      
      .nav-menu {
        gap: 1rem;
        order: 3;
        width: 100%;
        justify-content: center;
      }
      
      .main-content {
        padding: 1rem;
      }
      
      .page-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
      }
      
      .page-title {
        font-size: 1.5rem;
      }
      
      .table-responsive {
        font-size: 0.875rem;
      }
    }
  </style>
</head>
<body>

<!-- Header Bar -->
<header class="header-bar">
  <a href="index.php" class="logo">
    <div class="logo-icon">A</div>
    <span>AcademicsPro</span>
  </a>
  
  <nav class="nav-menu">
    <a href="enrollee.php" class="nav-item">
      <svg class="nav-icon" fill="currentColor" viewBox="0 0 20 20">
        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
      </svg>
      Dashboard
    </a>
    <a href="admin.php" class="nav-item active">
      <svg class="nav-icon" fill="currentColor" viewBox="0 0 20 20">
        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
      </svg>
      Students
    </a>
    <a href="courses.php" class="nav-item">
      <svg class="nav-icon" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V4a2 2 0 00-2-2H6zm1 2a1 1 0 000 2h6a1 1 0 100-2H7zm6 7a1 1 0 011 1v3a1 1 0 11-2 0v-3a1 1 0 011-1zm-3 3a1 1 0 100 2h.01a1 1 0 100-2H10zm-4 1a1 1 0 011-1h.01a1 1 0 110 2H7a1 1 0 01-1-1zm1-4a1 1 0 100 2h.01a1 1 0 100-2H7zm2 0a1 1 0 100 2h.01a1 1 0 100-2H9zm2 0a1 1 0 100 2h.01a1 1 0 100-2h-.01z"/>
      </svg>
      Courses
    </a>
  </nav>
</header>

<!-- Main Content -->
<main class="main-content">

  <!-- Page Header -->
  <div class="page-header">
    <h1 class="page-title">Students</h1>
    <a href="#" class="add-student-btn">
      <svg class="plus-icon" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
      </svg>
      Add Student
    </a>
  </div>

  <?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-info">
      <?= $_SESSION['message']; unset($_SESSION['message']); ?>
    </div>
  <?php endif; ?>

  <!-- Filter Section -->
  <div class="filter-section">
    <span class="filter-label">Filter by Program:</span>
    <form method="GET" style="display: inline-block;">
      <select name="program" class="filter-select" onchange="this.form.submit()">
        <option value="">All Programs</option>
        <?php while ($p = $programs->fetch_assoc()): ?>
          <option value="<?= htmlspecialchars($p['program']); ?>" 
            <?= ($filter_program === $p['program']) ? 'selected' : ''; ?>>
            <?= htmlspecialchars($p['program']); ?>
          </option>
        <?php endwhile; ?>
      </select>
    </form>
    <?php if (!empty($filter_program)): ?>
      <a href="admin.php" class="view-link">Reset</a>
    <?php endif; ?>
  </div>

  <!-- Students Table -->
  <div class="students-table">
    <table class="table">
      <thead>
        <tr>
          <th>NAME</th>
          <th>PROGRAM</th>
          <th>EMAIL</th>
          <th>STATUS</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td>
              <div class="student-name">
                <?= htmlspecialchars($row['firstname'] . " " . $row['middlename'] . " " . $row['lastname']); ?>
              </div>
            </td>
            <td>
              <div class="student-program">
                <?= htmlspecialchars($row['program'] ?? 'N/A'); ?>
              </div>
            </td>
            <td>
              <div class="student-email">
                <?= htmlspecialchars($row['email']); ?>
              </div>
            </td>
            <td>
              <?php 
                $status = $row['status'] ?? 'Not Enrolled';
                $statusClass = '';
                switch($status) {
                  case 'Enrolled':
                    $statusClass = 'status-active';
                    break;
                  case 'Not Enrolled':
                    $statusClass = 'status-inactive';
                    break;
                  default:
                    $statusClass = 'status-on-hold';
                }
              ?>
              <span class="status-badge <?= $statusClass ?>">
                <?= htmlspecialchars($status); ?>
              </span>
            </td>
            <td>
              <button class="view-link" data-bs-toggle="modal" data-bs-target="#studentModal<?= $row['id']; ?>">
                View
              </button>
            </td>
          </tr>

          <!-- Edit Modal -->
          <div class="modal fade" id="studentModal<?= $row['id']; ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
              <div class="modal-content">
                <form method="POST">
                  <div class="modal-header" style="background: linear-gradient(135deg, #001f54, #1d4ed8); color: white;">
                    <h5 class="modal-title">Edit Student - <?= htmlspecialchars($row['firstname'] . " " . $row['lastname']); ?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                    <input type="hidden" name="user_id" value="<?= $row['id']; ?>">
                    <div class="row g-3">
                      <div class="col-md-4">
                        <label class="form-label">First Name</label>
                        <input type="text" name="firstname" value="<?= htmlspecialchars($row['firstname']); ?>" class="form-control">
                      </div>
                      <div class="col-md-4">
                        <label class="form-label">Middle Name</label>
                        <input type="text" name="middlename" value="<?= htmlspecialchars($row['middlename']); ?>" class="form-control">
                      </div>
                      <div class="col-md-4">
                        <label class="form-label">Last Name</label>
                        <input type="text" name="lastname" value="<?= htmlspecialchars($row['lastname']); ?>" class="form-control">
                      </div>
                      <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($row['email']); ?>" class="form-control">
                      </div>
                      <div class="col-md-6">
                        <label class="form-label">Mobile</label>
                        <input type="text" name="mobile" value="<?= htmlspecialchars($row['mobile']); ?>" class="form-control">
                      </div>
                      <div class="col-md-6">
                        <label class="form-label">Program</label>
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
                      <div class="col-md-6">
                        <label class="form-label">Status</label>
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
      </tbody>
    </table>
  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
