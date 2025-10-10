<?php
session_start();
include 'connections.php';

// Restrict access to logged-in users only (admin and students)
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'student'])) {
    // Clear any existing session data
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}

// Additional security: regenerate session ID to prevent session fixation
if (!isset($_SESSION['authenticated'])) {
    session_regenerate_id(true);
    $_SESSION['authenticated'] = true;
}

// Fetch all courses with enhanced information
$query = "SELECT * FROM courses ORDER BY course_name ASC";
$result = $conn->query($query);
if (!$result) {
    die("Query failed: " . $conn->error);
}

// Get course statistics
$total_courses = $result->num_rows;
$programs_query = "SELECT DISTINCT program FROM courses WHERE program IS NOT NULL ORDER BY program ASC";
$programs_result = $conn->query($programs_query);
$programs = [];
if ($programs_result->num_rows > 0) {
    while($row = $programs_result->fetch_assoc()) {
        $programs[] = $row['program'];
    }
}

// Get semester statistics
$semester_query = "SELECT semester, COUNT(*) as count FROM courses GROUP BY semester ORDER BY semester ASC";
$semester_result = $conn->query($semester_query);
$semester_stats = [];
if ($semester_result->num_rows > 0) {
    while($row = $semester_result->fetch_assoc()) {
        $semester_stats[$row['semester']] = $row['count'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NU Lipa - Courses Management</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
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
      max-width: 1400px;
      margin: 0 auto;
    }
    
    .dashboard-title {
      font-size: 2rem;
      font-weight: 700;
      color: #1f2937;
      margin-bottom: 0.5rem;
    }
    
    .dashboard-subtitle {
      color: #6b7280;
      font-size: 1rem;
      margin-bottom: 2rem;
    }
    
    /* Statistics Cards */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 1.5rem;
      margin-bottom: 2rem;
    }
    
    .stat-card {
      background: #ffffff;
      border-radius: 12px;
      padding: 1.5rem;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
      border: 1px solid #e5e7eb;
      transition: all 0.2s ease;
    }
    
    .stat-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    .stat-header {
      display: flex;
      align-items: center;
      gap: 1rem;
      margin-bottom: 1rem;
    }
    
    .stat-icon {
      width: 48px;
      height: 48px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
      font-weight: 600;
    }
    
    .stat-icon.primary {
      background: linear-gradient(135deg, #dbeafe, #93c5fd);
      color: #1e40af;
    }
    
    .stat-icon.secondary {
      background: linear-gradient(135deg, #dcfce7, #bbf7d0);
      color: #166534;
    }
    
    .stat-icon.accent {
      background: linear-gradient(135deg, #fef3c7, #fcd34d);
      color: #92400e;
    }
    
    .stat-content {
      flex: 1;
    }
    
    .stat-value {
      font-size: 2rem;
      font-weight: 700;
      color: #1f2937;
      line-height: 1;
    }
    
    .stat-label {
      font-size: 0.875rem;
      color: #6b7280;
      font-weight: 500;
      margin-top: 0.25rem;
    }
    
    /* Filter Section */
    .filter-section {
      background: #ffffff;
      border-radius: 12px;
      padding: 1.5rem;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
      border: 1px solid #e5e7eb;
      margin-bottom: 2rem;
    }
    
    .filter-row {
      display: flex;
      gap: 1rem;
      align-items: center;
      flex-wrap: wrap;
    }
    
    .filter-dropdown {
      background: #ffffff;
      border: 1px solid #d1d5db;
      border-radius: 8px;
      padding: 0.75rem 1rem;
      font-size: 0.875rem;
      color: #374151;
      cursor: pointer;
      transition: all 0.2s ease;
      min-width: 150px;
    }
    
    .filter-dropdown:hover {
      border-color: #293855;
    }
    
    .filter-dropdown:focus {
      outline: none;
      border-color: #293855;
      box-shadow: 0 0 0 3px rgba(41, 56, 85, 0.1);
    }
    
    .search-input {
      background: #ffffff;
      border: 1px solid #d1d5db;
      border-radius: 8px;
      padding: 0.75rem 1rem;
      font-size: 0.875rem;
      color: #374151;
      transition: all 0.2s ease;
      min-width: 250px;
    }
    
    .search-input:hover {
      border-color: #293855;
    }
    
    .search-input:focus {
      outline: none;
      border-color: #293855;
      box-shadow: 0 0 0 3px rgba(41, 56, 85, 0.1);
    }
    
    /* Courses Grid */
    .courses-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
      gap: 1.5rem;
    }
    
    .course-card {
      background: #ffffff;
      border-radius: 12px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
      border: 1px solid #e5e7eb;
      transition: all 0.2s ease;
      overflow: hidden;
    }
    
    .course-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }
    
    .course-header {
      padding: 1.5rem 1.5rem 1rem;
      border-bottom: 1px solid #f3f4f6;
    }
    
    .course-code {
      display: inline-block;
      background: linear-gradient(135deg, #293855, #4165D5);
      color: white;
      padding: 0.25rem 0.75rem;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      margin-bottom: 0.75rem;
    }
    
    .course-title {
      font-size: 1.25rem;
      font-weight: 700;
      color: #1f2937;
      margin-bottom: 0.5rem;
      line-height: 1.3;
    }
    
    .course-program {
      color: #6b7280;
      font-size: 0.875rem;
      font-weight: 500;
    }
    
    .course-body {
      padding: 1rem 1.5rem;
    }
    
    .course-description {
      color: #4b5563;
      font-size: 0.875rem;
      line-height: 1.6;
      margin-bottom: 1rem;
    }
    
    .course-meta {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1rem 1.5rem;
      background: #f8fafc;
      border-top: 1px solid #f3f4f6;
    }
    
    .semester-badge {
      background: linear-gradient(135deg, #dcfce7, #bbf7d0);
      color: #166534;
      padding: 0.25rem 0.75rem;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
    }
    
    .view-details-btn {
      background: linear-gradient(135deg, #293855, #4165D5);
      color: white;
      border: none;
      padding: 0.5rem 1rem;
      border-radius: 8px;
      font-size: 0.875rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s ease;
      text-decoration: none;
      display: inline-block;
    }
    
    .view-details-btn:hover {
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(41, 56, 85, 0.3);
      color: white;
    }
    
    /* Empty State */
    .empty-state {
      text-align: center;
      padding: 4rem 2rem;
      color: #6b7280;
    }
    
    .empty-state-icon {
      font-size: 4rem;
      margin-bottom: 1rem;
      opacity: 0.5;
    }
    
    .empty-state-title {
      font-size: 1.25rem;
      font-weight: 600;
      margin-bottom: 0.5rem;
    }
    
    .empty-state-description {
      font-size: 0.875rem;
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
      
      .nav-item {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
      }
      
      .main-content {
        padding: 1rem;
      }
      
      .stats-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
      }
      
      .filter-row {
        flex-direction: column;
        align-items: stretch;
      }
      
      .filter-dropdown,
      .search-input {
        min-width: auto;
        width: 100%;
      }
      
      .courses-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
      }
      
      .dashboard-title {
        font-size: 1.5rem;
      }
    }
  </style>
</head>
<body>
  <!-- Header Bar -->
  <header class="header-bar">
    <a href="#" class="logo">
      <img src="images/National University Lipa.png" alt="NU Lipa" class="logo-image" style="width: 130px; height: 50px;">
    </a>
    
    <nav class="nav-menu">
      <?php if ($_SESSION['user']['role'] === 'admin'): ?>
        <a href="enrollee.php" class="nav-item">
          <svg class="nav-icon" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
          </svg>
          Dashboard
        </a>
        <a href="admin.php" class="nav-item">
          <svg class="nav-icon" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
          </svg>
          Students
        </a>
      <?php else: ?>
        <a href="index.php" class="nav-item">
          <svg class="nav-icon" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
          </svg>
          Home
        </a>
      <?php endif; ?>
      <a href="courses.php" class="nav-item active">
        <svg class="nav-icon" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V4a2 2 0 00-2-2H6zm1 2a1 1 0 000 2h6a1 1 0 100-2H7zm6 7a1 1 0 011 1v3a1 1 0 11-2 0v-3a1 1 0 011-1zm-3 3a1 1 0 100 2h.01a1 1 0 100-2H10zm-4 1a1 1 0 011-1h.01a1 1 0 110 2H7a1 1 0 01-1-1zm1-4a1 1 0 100 2h.01a1 1 0 100-2H7zm2 0a1 1 0 100 2h.01a1 1 0 100-2H9zm2 0a1 1 0 100 2h.01a1 1 0 100-2h-.01z"/>
        </svg>
        Courses
      </a>
    </nav>
    
    <div class="header-actions">
      <div class="user-profile-dropdown">
        <div class="user-profile" onclick="toggleDropdown()"><?= strtoupper(substr($_SESSION['user']['firstname'] ?? 'U', 0, 1)) ?></div>
        <div class="dropdown-menu" id="profileDropdown">
          <div class="dropdown-header"><?= $_SESSION['user']['role'] === 'admin' ? 'Administrator' : 'Student' ?></div>
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
    <div class="dashboard-title">Course Catalog</div>
    <div class="dashboard-subtitle">
      <?php if ($_SESSION['user']['role'] === 'admin'): ?>
        Comprehensive overview of all available courses and academic programs
      <?php else: ?>
        Explore all available courses and academic programs at National University Lipa
      <?php endif; ?>
    </div>
    
    <!-- Statistics Cards -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-header">
          <div class="stat-icon primary">📚</div>
          <div class="stat-content">
            <div class="stat-value"><?php echo $total_courses; ?></div>
            <div class="stat-label">Total Courses</div>
          </div>
        </div>
      </div>
      
      <div class="stat-card">
        <div class="stat-header">
          <div class="stat-icon secondary">🎓</div>
          <div class="stat-content">
            <div class="stat-value"><?php echo count($programs); ?></div>
            <div class="stat-label">Academic Programs</div>
          </div>
        </div>
      </div>
      
      <div class="stat-card">
        <div class="stat-header">
          <div class="stat-icon accent">📅</div>
          <div class="stat-content">
            <div class="stat-value"><?php echo count($semester_stats); ?></div>
            <div class="stat-label">Semester Levels</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
      <div class="filter-row">
        <input type="text" class="search-input" id="courseSearch" placeholder="Search courses by name or code...">
        <select class="filter-dropdown" id="programFilter">
          <option value="all">All Programs</option>
          <?php foreach($programs as $program): ?>
            <option value="<?php echo htmlspecialchars($program); ?>"><?php echo htmlspecialchars($program); ?></option>
          <?php endforeach; ?>
        </select>
        <select class="filter-dropdown" id="semesterFilter">
          <option value="all">All Semesters</option>
          <?php foreach($semester_stats as $semester => $count): ?>
            <option value="<?php echo htmlspecialchars($semester); ?>"><?php echo htmlspecialchars($semester); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <!-- Courses Grid -->
    <div class="courses-grid" id="coursesGrid">
      <?php if ($result->num_rows === 0): ?>
        <div class="empty-state">
          <div class="empty-state-icon">📚</div>
          <div class="empty-state-title">No Courses Found</div>
          <div class="empty-state-description">There are currently no courses available in the system.</div>
        </div>
      <?php else: ?>
        <?php 
        // Reset result pointer
        $result->data_seek(0);
        while ($course = $result->fetch_assoc()): 
        ?>
          <div class="course-card" data-program="<?php echo htmlspecialchars($course['program']); ?>" data-semester="<?php echo htmlspecialchars($course['semester']); ?>" data-name="<?php echo strtolower(htmlspecialchars($course['course_name'])); ?>" data-code="<?php echo strtolower(htmlspecialchars($course['course_code'])); ?>">
            <div class="course-header">
              <div class="course-code"><?php echo htmlspecialchars($course['course_code']); ?></div>
              <h3 class="course-title"><?php echo htmlspecialchars($course['course_name']); ?></h3>
              <div class="course-program"><?php echo htmlspecialchars($course['program']); ?></div>
            </div>
            
            <div class="course-body">
              <p class="course-description">
                <?php 
                $description = $course['description'] ?: 'This course provides comprehensive knowledge and practical skills essential for academic and professional development. Students will engage with fundamental concepts and advanced methodologies relevant to their field of study.';
                echo htmlspecialchars($description);
                ?>
              </p>
            </div>
            
            <div class="course-meta">
              <span class="semester-badge"><?php echo htmlspecialchars($course['semester']); ?> Semester</span>
              <a href="details.php?id=<?php echo $course['id']; ?>" class="view-details-btn">View Details</a>
            </div>
          </div>
        <?php endwhile; ?>
      <?php endif; ?>
    </div>
  </main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Filter and search functionality
  const courseSearch = document.getElementById('courseSearch');
  const programFilter = document.getElementById('programFilter');
  const semesterFilter = document.getElementById('semesterFilter');
  const coursesGrid = document.getElementById('coursesGrid');

  function filterCourses() {
    const searchTerm = courseSearch.value.toLowerCase();
    const programValue = programFilter.value;
    const semesterValue = semesterFilter.value;

    const courseCards = coursesGrid.querySelectorAll('.course-card');
    let visibleCount = 0;

    courseCards.forEach(card => {
      const courseName = card.dataset.name;
      const courseCode = card.dataset.code;
      const program = card.dataset.program;
      const semester = card.dataset.semester;

      const matchesSearch = courseName.includes(searchTerm) || courseCode.includes(searchTerm);
      const matchesProgram = programValue === 'all' || program === programValue;
      const matchesSemester = semesterValue === 'all' || semester === semesterValue;

      if (matchesSearch && matchesProgram && matchesSemester) {
        card.style.display = 'block';
        visibleCount++;
      } else {
        card.style.display = 'none';
      }
    });

    // Show empty state if no courses match
    const existingEmptyState = coursesGrid.querySelector('.empty-state');
    if (visibleCount === 0 && !existingEmptyState) {
      const emptyState = document.createElement('div');
      emptyState.className = 'empty-state';
      emptyState.innerHTML = `
        <div class="empty-state-icon">🔍</div>
        <div class="empty-state-title">No Courses Match Your Search</div>
        <div class="empty-state-description">Try adjusting your search criteria or filters.</div>
      `;
      coursesGrid.appendChild(emptyState);
    } else if (visibleCount > 0 && existingEmptyState) {
      existingEmptyState.remove();
    }
  }

  // Add event listeners
  courseSearch.addEventListener('input', filterCourses);
  programFilter.addEventListener('change', filterCourses);
  semesterFilter.addEventListener('change', filterCourses);

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

  // Initialize filters
  filterCourses();
</script>
</body>
</html>
