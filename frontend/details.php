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
    <title>Course Details - NU Lipa</title>
    <link rel="stylesheet" href="style.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
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
        box-shadow: 0 4px 8px rgba(0, 31, 84, 0.3);
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
        color: #1f2937;
        border-bottom: 1px solid #f3f4f6;
      }
      
      .dropdown-divider {
        height: 1px;
        background: #e5e7eb;
        margin: 0;
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
        background: #f9fafb;
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
      
      .breadcrumb {
        background: #ffffff;
        border-radius: 8px;
        padding: 1rem 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border: 1px solid #e5e7eb;
      }
      
      .breadcrumb-item + .breadcrumb-item::before {
        color: #9ca3af;
      }
      
      .breadcrumb-item a {
        color: #293855;
        text-decoration: none;
        font-weight: 500;
      }
      
      .breadcrumb-item.active {
        color: #F1AC20;
        font-weight: 600;
      }
      
      .course-header {
        background: linear-gradient(135deg, #293855, #4165D5);
        color: white;
        padding: 2rem;
        border-radius: 12px;
        margin-bottom: 2rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      }
      
      .course-title {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
      }
      
      .course-code {
        font-size: 1.125rem;
        opacity: 0.9;
        font-weight: 500;
      }
      
      .course-details-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border: 1px solid #e5e7eb;
        margin-bottom: 2rem;
      }
      
      .detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
      }
      
      .detail-item {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
      }
      
      .detail-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.05em;
      }
      
      .detail-value {
        font-size: 1.125rem;
        font-weight: 600;
        color: #1f2937;
      }
      
      .description-section {
        border-top: 1px solid #e5e7eb;
        padding-top: 1.5rem;
      }
      
      .section-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 1rem;
      }
      
      .description-text {
        font-size: 1rem;
        line-height: 1.6;
        color: #374151;
      }
      
      .back-button {
        background: linear-gradient(135deg, #293855, #4165D5);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
      }
      
      .back-button:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 31, 84, 0.3);
        color: white;
      }
      
      .back-icon {
        width: 16px;
        height: 16px;
      }
      
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
        
        .course-header {
          padding: 1.5rem;
        }
        
        .course-title {
          font-size: 1.5rem;
        }
        
        .detail-grid {
          grid-template-columns: 1fr;
          gap: 1rem;
        }
        
        .course-details-card {
          padding: 1.5rem;
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
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Home</a></li>
      <li class="breadcrumb-item"><a href="courses.php">Courses</a></li>
      <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($course['course_name']); ?></li>
    </ol>
  </nav>

  <div class="course-header">
    <h1 class="course-title"><?= htmlspecialchars($course['course_name']); ?></h1>
    <div class="course-code"><?= htmlspecialchars($course['course_code']); ?></div>
  </div>

  <div class="course-details-card">
    <div class="detail-grid">
      <div class="detail-item">
        <div class="detail-label">Program</div>
        <div class="detail-value"><?= htmlspecialchars($course['program']); ?></div>
      </div>
      <div class="detail-item">
        <div class="detail-label">Semester</div>
        <div class="detail-value"><?= htmlspecialchars($course['semester']); ?></div>
      </div>
      <div class="detail-item">
        <div class="detail-label">Units</div>
        <div class="detail-value"><?= intval($course['units']); ?></div>
      </div>
      <div class="detail-item">
        <div class="detail-label">Prerequisites</div>
        <div class="detail-value"><?= htmlspecialchars($course['prerequisites'] ?: 'None'); ?></div>
      </div>
    </div>
    
    <div class="description-section">
      <h2 class="section-title">Course Description</h2>
      <p class="description-text"><?= nl2br(htmlspecialchars($course['description'])); ?></p>
    </div>
    
    <a href="courses.php" class="back-button">
      <svg class="back-icon" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
      </svg>
      Back to Courses
    </a>
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
</script>
</body>
</html>
