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
    try {
        $user_id = intval($_POST['user_id']);
        
        // Log the update attempt for debugging
        error_log("Admin update attempt - User ID: $user_id, POST data: " . print_r($_POST, true));
        
        // Validate required fields
        if (empty($user_id)) {
            throw new Exception("Invalid user ID");
        }

        // Collect and sanitize fields
        $status = trim($_POST['status'] ?? '');
        $program = trim($_POST['program'] ?? '');
        $firstname = trim($_POST['firstname'] ?? '');
        $middlename = trim($_POST['middlename'] ?? '');
        $lastname = trim($_POST['lastname'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $mobile = trim($_POST['mobile'] ?? '');

        // Validate required fields
        if (empty($firstname) || empty($lastname) || empty($email)) {
            throw new Exception("Please fill in all required fields (First Name, Last Name, Email)");
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Please enter a valid email address");
        }

        // Start transaction
        $conn->begin_transaction();

        // First, verify the user exists and is a student
        $verify_user = $conn->prepare("SELECT id, email, role, status FROM users WHERE id = ?");
        $verify_user->bind_param("i", $user_id);
        $verify_user->execute();
        $user_result = $verify_user->get_result();
        $user_exists = $user_result->num_rows > 0;
        
        // Log verification result for debugging
        if ($user_result->num_rows > 0) {
            $user_row = $user_result->fetch_assoc();
            error_log("User verification - ID: $user_id, Email: " . $user_row['email'] . ", Role: " . $user_row['role'] . ", Status: " . $user_row['status']);
            
            // Check if user is a student
            if ($user_row['role'] !== 'student') {
                throw new Exception("Cannot edit admin users. Only student records can be edited.");
            }
        } else {
            error_log("User verification - ID: $user_id, User not found");
            throw new Exception("Student not found or invalid user ID");
        }
        $verify_user->close();

        // Update users table (status and email)
        $stmt = $conn->prepare("UPDATE users SET status = ?, email = ? WHERE id = ? AND role = 'student'");
        $stmt->bind_param("ssi", $status, $email, $user_id);
        $stmt->execute();
        
        // Log the update attempt for debugging
        error_log("Update attempt - User ID: $user_id, Status: $status, Email: $email, Affected rows: " . $stmt->affected_rows);
        
        if ($stmt->error) {
            error_log("SQL Error during users table update: " . $stmt->error);
            throw new Exception("Database error during user update: " . $stmt->error);
        }
        $stmt->close();

        // Check if user_info record exists
        $check = $conn->prepare("SELECT id FROM user_info WHERE user_id = ?");
        $check->bind_param("i", $user_id);
        $check->execute();
        $user_info_exists = $check->get_result()->num_rows > 0;
        $check->close();

        if ($user_info_exists) {
            // Update existing user_info
            $stmt2 = $conn->prepare("UPDATE user_info SET 
                firstname = ?, middlename = ?, lastname = ?, program = ?, mobile = ? 
                WHERE user_id = ?");
            $stmt2->bind_param("sssssi", $firstname, $middlename, $lastname, $program, $mobile, $user_id);
            $stmt2->execute();
            
            // Log the update attempt for debugging
            error_log("User_info update attempt - User ID: $user_id, Affected rows: " . $stmt2->affected_rows);
            
            if ($stmt2->error) {
                error_log("SQL Error during user_info table update: " . $stmt2->error);
                throw new Exception("Database error during student information update: " . $stmt2->error);
            }
            $stmt2->close();
        } else {
            // Insert new user_info record
            $stmt2 = $conn->prepare("INSERT INTO user_info (user_id, firstname, middlename, lastname, program, mobile) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt2->bind_param("isssss", $user_id, $firstname, $middlename, $lastname, $program, $mobile);
            $stmt2->execute();
            
            // Log the insert attempt for debugging
            error_log("User_info insert attempt - User ID: $user_id, Affected rows: " . $stmt2->affected_rows);
            
            if ($stmt2->error) {
                error_log("SQL Error during user_info table insert: " . $stmt2->error);
                throw new Exception("Database error during student information creation: " . $stmt2->error);
            }
            $stmt2->close();
        }

        // Commit transaction
        $conn->commit();
        
        $_SESSION['message'] = "✅ Student updated successfully!";
        $_SESSION['message_type'] = 'success';
        
    } catch (Exception $e) {
        // Rollback transaction on error
        if (isset($conn)) {
            $conn->rollback();
        }
        $_SESSION['message'] = "❌ Error: " . $e->getMessage();
        $_SESSION['message_type'] = 'danger';
        
        // Log detailed error for debugging
        error_log("Admin update error: " . $e->getMessage());
        error_log("User ID: " . ($_POST['user_id'] ?? 'not set'));
        error_log("POST data: " . print_r($_POST, true));
        error_log("Database connection status: " . ($conn ? 'Connected' : 'Not connected'));
    }

    header("Location: admin.php?updated=" . time());
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
$query = "SELECT users.id as user_id, users.email, users.status, 
          COALESCE(user_info.firstname, '') as firstname,
          COALESCE(user_info.middlename, '') as middlename,
          COALESCE(user_info.lastname, '') as lastname,
          COALESCE(user_info.program, '') as program,
          COALESCE(user_info.mobile, '') as mobile,
          COALESCE(user_info.nationality, '') as nationality,
          COALESCE(user_info.sex, '') as sex,
          COALESCE(user_info.academic_year, '') as academic_year,
          COALESCE(user_info.academic_term, '') as academic_term,
          COALESCE(user_info.applying_for, '') as applying_for,
          COALESCE(user_info.strand, '') as strand,
          COALESCE(user_info.second_program, '') as second_program,
          COALESCE(user_info.family_income, '') as family_income,
          COALESCE(user_info.father_occupation, '') as father_occupation,
          COALESCE(user_info.mother_occupation, '') as mother_occupation,
          COALESCE(user_info.birthplace, '') as birthplace,
          COALESCE(user_info.city, '') as city,
          COALESCE(user_info.province, '') as province,
          COALESCE(user_info.current_address, '') as current_address
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
  <title>Students - NU Lipa Admin</title>
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
      background: linear-gradient(135deg, #293855, #4165D5);
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
      background: linear-gradient(135deg, #293855, #4165D5);
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
    
    .header-actions {
      display: flex;
      align-items: center;
      gap: 1rem;
    }
    
    .notification-btn {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: #f3f4f6;
      border: none;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #6b7280;
      font-size: 1.25rem;
      cursor: pointer;
      transition: all 0.2s ease;
    }
    
    .notification-btn:hover {
      background: #e5e7eb;
      color: #374151;
      transform: scale(1.05);
    }
    
    .user-profile-dropdown {
      position: relative;
    }
    
    .user-profile {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: linear-gradient(135deg, #293855, #4165D5);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: 600;
      font-size: 0.875rem;
      cursor: pointer;
      transition: all 0.2s ease;
    }
    
    .user-profile:hover {
      transform: scale(1.05);
      box-shadow: 0 4px 8px rgba(41, 56, 85, 0.3);
    }
    
    .dropdown-menu {
      position: absolute;
      top: 100%;
      right: 0;
      background: #ffffff;
      border: 1px solid #e5e7eb;
      border-radius: 8px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      min-width: 200px;
      z-index: 1000;
      display: none;
      margin-top: 0.5rem;
    }
    
    .dropdown-menu.show {
      display: block;
    }
    
    .dropdown-header {
      padding: 0.75rem 1rem;
      font-size: 0.875rem;
      font-weight: 600;
      color: #374151;
      border-bottom: 1px solid #e5e7eb;
    }
    
    .dropdown-divider {
      height: 1px;
      background: #e5e7eb;
      margin: 0.5rem 0;
    }
    
    .dropdown-item {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      padding: 0.75rem 1rem;
      color: #374151;
      text-decoration: none;
      font-size: 0.875rem;
      transition: all 0.2s ease;
    }
    
    .dropdown-item:hover {
      background: #f3f4f6;
      color: #1f2937;
    }
    
    .dropdown-icon {
      width: 16px;
      height: 16px;
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
      border-color: #293855;
      box-shadow: 0 0 0 3px rgba(41, 56, 85, 0.1);
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
      background: #C3E8C9;
      color: #293855;
    }
    
    .status-on-hold {
      background: #F1AC20;
      color: white;
    }
    
    .status-inactive {
      background: #fee2e2;
      color: #991b1b;
    }

    /* View Link */
    .view-link {
      color: #293855;
      text-decoration: none;
      font-weight: 500;
      font-size: 0.875rem;
      transition: color 0.2s ease;
    }
    
    .view-link:hover {
      color: #4165D5;
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
    <img src="images/National University Lipa.png" alt="NU Lipa" class="logo-image" style="width: 130px; height: 50px;">
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
  
  <div class="header-actions">
    <button class="notification-btn">🔔</button>
    <div class="user-profile-dropdown">
      <div class="user-profile" onclick="toggleDropdown()">A</div>
      <div class="dropdown-menu" id="profileDropdown">
        <div class="dropdown-header">Administrator</div>
        <div class="dropdown-divider"></div>
        <a href="logout.php" class="dropdown-item">
          <svg class="dropdown-icon" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd"/>
          </svg>
          Logout
        </a>
      </div>
    </div>
  </div>
</header>

<!-- Main Content -->
<main class="main-content">

  <?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-<?= $_SESSION['message_type'] ?? 'info' ?> alert-dismissible fade show" role="alert">
      <?= $_SESSION['message']; unset($_SESSION['message'], $_SESSION['message_type']); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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
              <button class="view-link" data-bs-toggle="modal" data-bs-target="#studentModal<?= $row['user_id']; ?>">
                View
              </button>
            </td>
          </tr>

          <!-- Professional Edit Modal -->
          <div class="modal fade" id="studentModal<?= $row['user_id']; ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
              <div class="modal-content" style="border: none; border-radius: 12px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);">
                <form method="POST" id="studentForm<?= $row['user_id']; ?>">
                  <div class="modal-header" style="background: linear-gradient(135deg, #293855, #4165D5); color: white; border-radius: 12px 12px 0 0; border-bottom: none; padding: 1.5rem 2rem;">
                    <div class="d-flex align-items-center gap-3">
                      <div class="student-avatar" style="width: 50px; height: 50px; border-radius: 50%; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 700;">
                        <?= strtoupper(substr($row['firstname'] ?? 'U', 0, 1)) ?>
                      </div>
                      <div>
                        <h5 class="modal-title mb-1" style="font-weight: 600; font-size: 1.25rem;">Edit Student Details</h5>
                        <p class="mb-0" style="opacity: 0.9; font-size: 0.875rem;"><?= htmlspecialchars($row['firstname'] . " " . $row['lastname']); ?></p>
                      </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  
                  <div class="modal-body" style="padding: 2rem;">
                    <input type="hidden" name="user_id" value="<?= $row['user_id']; ?>">
                    
                    <!-- Overview Cards -->
                    <div class="row mb-4">
                      <div class="col-md-3">
                        <div class="overview-card" style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px; padding: 1rem; text-align: center;">
                          <div style="color: #0369a1; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">📧 Email Status</div>
                          <div style="color: #0c4a6e; font-weight: 600; font-size: 0.875rem;"><?= !empty($row['email']) ? 'Verified' : 'Not Set' ?></div>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="overview-card" style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 1rem; text-align: center;">
                          <div style="color: #166534; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">🎓 Program</div>
                          <div style="color: #14532d; font-weight: 600; font-size: 0.875rem;"><?= htmlspecialchars($row['program'] ?? 'Not Set') ?></div>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="overview-card" style="background: #fefce8; border: 1px solid #fde047; border-radius: 8px; padding: 1rem; text-align: center;">
                          <div style="color: #a16207; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">📱 Mobile</div>
                          <div style="color: #713f12; font-weight: 600; font-size: 0.875rem;"><?= htmlspecialchars($row['mobile'] ?? 'Not Set') ?></div>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="overview-card" style="background: #fdf2f8; border: 1px solid #f9a8d4; border-radius: 8px; padding: 1rem; text-align: center;">
                          <div style="color: #be185d; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">📋 Status</div>
                          <div style="color: #831843; font-weight: 600; font-size: 0.875rem;"><?= htmlspecialchars($row['status'] ?? 'Not Set') ?></div>
                        </div>
                      </div>
                    </div>
                    
                    <!-- Form Sections -->
                    <div class="row g-4">
                      <!-- Personal Information -->
                      <div class="col-12">
                        <h6 class="section-title" style="color: #374151; font-weight: 600; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 2px solid #e5e7eb; display: flex; align-items: center; gap: 0.5rem;">
                          <span style="margin-right: 0.5rem;">👤</span>Personal Information
                        </h6>
                        <div class="row g-3">
                          <div class="col-md-4">
                            <label class="form-label" style="font-weight: 600; color: #374151;">First Name *</label>
                            <input type="text" name="firstname" value="<?= htmlspecialchars($row['firstname'] ?? ''); ?>" class="form-control" style="border: 1px solid #d1d5db; border-radius: 6px; padding: 0.75rem;" required>
                          </div>
                          <div class="col-md-4">
                            <label class="form-label" style="font-weight: 600; color: #374151;">Middle Name</label>
                            <input type="text" name="middlename" value="<?= htmlspecialchars($row['middlename'] ?? ''); ?>" class="form-control" style="border: 1px solid #d1d5db; border-radius: 6px; padding: 0.75rem;">
                          </div>
                          <div class="col-md-4">
                            <label class="form-label" style="font-weight: 600; color: #374151;">Last Name *</label>
                            <input type="text" name="lastname" value="<?= htmlspecialchars($row['lastname'] ?? ''); ?>" class="form-control" style="border: 1px solid #d1d5db; border-radius: 6px; padding: 0.75rem;" required>
                          </div>
                        </div>
                      </div>
                      
                      <!-- Contact Information -->
                      <div class="col-12">
                        <h6 class="section-title" style="color: #374151; font-weight: 600; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 2px solid #e5e7eb; display: flex; align-items: center; gap: 0.5rem;">
                          <span style="margin-right: 0.5rem;">📞</span>Contact Information
                        </h6>
                        <div class="row g-3">
                          <div class="col-md-6">
                            <label class="form-label" style="font-weight: 600; color: #374151;">Email Address *</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($row['email'] ?? ''); ?>" class="form-control" style="border: 1px solid #d1d5db; border-radius: 6px; padding: 0.75rem;" required>
                          </div>
                          <div class="col-md-6">
                            <label class="form-label" style="font-weight: 600; color: #374151;">Mobile Number</label>
                            <input type="text" name="mobile" value="<?= htmlspecialchars($row['mobile'] ?? ''); ?>" class="form-control" style="border: 1px solid #d1d5db; border-radius: 6px; padding: 0.75rem;" placeholder="+63 XXX XXX XXXX">
                          </div>
                        </div>
                      </div>
                      
                      <!-- Academic Information -->
                      <div class="col-12">
                        <h6 class="section-title" style="color: #374151; font-weight: 600; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 2px solid #e5e7eb; display: flex; align-items: center; gap: 0.5rem;">
                          <span style="margin-right: 0.5rem;">🎓</span>Academic Information
                        </h6>
                        <div class="row g-3">
                          <div class="col-md-6">
                            <label class="form-label" style="font-weight: 600; color: #374151;">Program *</label>
                            <select name="program" class="form-select" style="border: 1px solid #d1d5db; border-radius: 6px; padding: 0.75rem;" required>
                              <option value="">-- Select Program --</option>
                              <option value="BSCS" <?= (isset($row['program']) && $row['program'] === 'BSCS') ? 'selected' : ''; ?>>Bachelor of Science in Computer Science</option>
                              <option value="BSIT" <?= (isset($row['program']) && $row['program'] === 'BSIT') ? 'selected' : ''; ?>>Bachelor of Science in Information Technology</option>
                              <option value="BSCE" <?= (isset($row['program']) && $row['program'] === 'BSCE') ? 'selected' : ''; ?>>Bachelor of Science in Civil Engineering</option>
                              <option value="BSArch" <?= (isset($row['program']) && $row['program'] === 'BSArch') ? 'selected' : ''; ?>>Bachelor of Science in Architecture</option>
                              <option value="BSMT" <?= (isset($row['program']) && $row['program'] === 'BSMT') ? 'selected' : ''; ?>>Bachelor of Science in Medical Technology</option>
                              <option value="BSN" <?= (isset($row['program']) && $row['program'] === 'BSN') ? 'selected' : ''; ?>>Bachelor of Science in Nursing</option>
                              <option value="BSPYS" <?= (isset($row['program']) && $row['program'] === 'BSPYS') ? 'selected' : ''; ?>>Bachelor of Science in Psychology</option>
                              <option value="BSTM" <?= (isset($row['program']) && $row['program'] === 'BSTM') ? 'selected' : ''; ?>>Bachelor of Science in Tourism Management</option>
                              <option value="BSA - Marketing" <?= (isset($row['program']) && $row['program'] === 'BSA - Marketing') ? 'selected' : ''; ?>>Bachelor of Science in Accountancy - Marketing</option>
                              <option value="BSA - Financial Management" <?= (isset($row['program']) && $row['program'] === 'BSA - Financial Management') ? 'selected' : ''; ?>>Bachelor of Science in Accountancy - Financial Management</option>
                            </select>
                          </div>
                          <div class="col-md-6">
                            <label class="form-label" style="font-weight: 600; color: #374151;">Enrollment Status *</label>
                            <select name="status" class="form-select" style="border: 1px solid #d1d5db; border-radius: 6px; padding: 0.75rem;" required>
                              <option value="Enrolled" <?= ($row['status'] ?? '') === 'Enrolled' ? 'selected' : ''; ?>>✅ Enrolled - Active student</option>
                              <option value="Not Enrolled" <?= ($row['status'] ?? '') === 'Not Enrolled' ? 'selected' : ''; ?>>❌ Not Enrolled - Inactive student</option>
                            </select>
                          </div>
                        </div>
                        <div class="col-12 mt-3">
                          <div class="alert alert-info" style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; padding: 0.75rem; border-radius: 6px; font-size: 0.875rem;">
                            <strong>Note:</strong> Changing enrollment status affects student access to academic resources and services.
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e5e7eb; padding: 1.5rem 2rem; border-radius: 0 0 12px 12px;">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="padding: 0.75rem 1.5rem; border-radius: 6px; font-weight: 500;">
                      Cancel
                    </button>
                    <button type="submit" name="update_user" class="btn btn-primary" style="background: linear-gradient(135deg, #293855, #4165D5); border: none; padding: 0.75rem 2rem; border-radius: 6px; font-weight: 600; transition: all 0.2s ease;">
                      💾 Save Changes
                    </button>
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

<script>
// Dropdown toggle functionality
function toggleDropdown() {
  const dropdown = document.getElementById('profileDropdown');
  dropdown.classList.toggle('show');
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
  const dropdown = document.getElementById('profileDropdown');
  const profile = document.querySelector('.user-profile');
  
  if (!profile.contains(event.target) && !dropdown.contains(event.target)) {
    dropdown.classList.remove('show');
  }
});

// Enhanced form functionality and validation
document.addEventListener('DOMContentLoaded', function() {
    // Form validation and submission handling
    const forms = document.querySelectorAll('form[id^="studentForm"]');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            // Validate required fields
            const requiredFields = form.querySelectorAll('input[required], select[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.style.borderColor = '#ef4444';
                    isValid = false;
                } else {
                    field.style.borderColor = '#10b981';
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields before saving.');
                return false;
            }
            
            // Add loading state to submit button
            const submitBtn = form.querySelector('button[name="update_user"]');
            if (submitBtn) {
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
                submitBtn.disabled = true;
            }
        });
    });
    
    // Real-time validation feedback
    const inputs = document.querySelectorAll('input, select');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.hasAttribute('required') && !this.value.trim()) {
                this.style.borderColor = '#ef4444';
            } else if (this.value.trim()) {
                this.style.borderColor = '#10b981';
            }
        });
        
        input.addEventListener('input', function() {
            if (this.style.borderColor === 'rgb(239, 68, 68)') {
                this.style.borderColor = '#d1d5db';
            }
        });
    });
    
    // Email format validation
    const emailInputs = document.querySelectorAll('input[type="email"]');
    emailInputs.forEach(input => {
        input.addEventListener('blur', function() {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (this.value && !emailRegex.test(this.value)) {
                this.style.borderColor = '#ef4444';
                if (!this.nextElementSibling || !this.nextElementSibling.classList.contains('invalid-feedback')) {
                    const feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    feedback.textContent = 'Please enter a valid email address';
                    this.parentNode.appendChild(feedback);
                }
            } else {
                this.style.borderColor = '#10b981';
                const feedback = this.parentNode.querySelector('.invalid-feedback');
                if (feedback) {
                    feedback.remove();
                }
            }
        });
    });
    
    // Mobile number formatting
    const mobileInputs = document.querySelectorAll('input[name="mobile"]');
    mobileInputs.forEach(input => {
        input.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.length > 0) {
                if (value.startsWith('63')) {
                    value = '+' + value;
                } else if (!value.startsWith('+')) {
                    value = '+63' + value;
                }
            }
            this.value = value;
        });
    });
    
    // Auto-save draft functionality
    forms.forEach(form => {
        const inputs = form.querySelectorAll('input, select');
        const formId = form.id;
        
        // Load saved draft
        const savedData = localStorage.getItem(`draft_${formId}`);
        if (savedData) {
            const data = JSON.parse(savedData);
            Object.keys(data).forEach(name => {
                const input = form.querySelector(`[name="${name}"]`);
                if (input) {
                    input.value = data[name];
                }
            });
        }
        
        // Save draft on input change
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                const formData = new FormData(form);
                const data = {};
                for (let [key, value] of formData.entries()) {
                    data[key] = value;
                }
                localStorage.setItem(`draft_${formId}`, JSON.stringify(data));
            });
        });
        
        // Clear draft on successful submission
        form.addEventListener('submit', function() {
            setTimeout(() => {
                localStorage.removeItem(`draft_${formId}`);
            }, 1000);
        });
    });
    
    // Add confirmation for status changes
    const statusSelects = document.querySelectorAll('select[name="status"]');
    statusSelects.forEach(select => {
        const originalValue = select.value;
        
        select.addEventListener('change', function() {
            if (this.value !== originalValue) {
                const statusText = this.value === 'Enrolled' ? 'enrolled' : 'not enrolled';
                if (!confirm(`Are you sure you want to change this student's status to ${statusText}?`)) {
                    this.value = originalValue;
                    return;
                }
            }
        });
    });
});
</script>

</body>
</html>
