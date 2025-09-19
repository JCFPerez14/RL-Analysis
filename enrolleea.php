<?php
session_start();
include 'connections.php';

// Fetch enrolled vs not enrolled students
$enrolled = $conn->query("SELECT COUNT(*) AS total FROM users WHERE status='Enrolled'")->fetch_assoc()['total'];
$not_enrolled = $conn->query("SELECT COUNT(*) AS total FROM users WHERE status='Not Enrolled'")->fetch_assoc()['total'];
$total_students = $enrolled + $not_enrolled;

// Percentages
$enrolled_percent = $total_students > 0 ? round(($enrolled / $total_students) * 100, 2) : 0;
$not_enrolled_percent = $total_students > 0 ? round(($not_enrolled / $total_students) * 100, 2) : 0;

// Fetch enrolled students
$students_enrolled = $conn->query("
    SELECT users.id, users.email, users.status, 
           user_info.firstname, user_info.middlename, user_info.lastname,
           user_info.program, user_info.mobile, user_info.nationality
    FROM users
    LEFT JOIN user_info ON users.id = user_info.user_id
    WHERE users.status = 'Enrolled'
");

// Fetch not enrolled students
$students_not = $conn->query("
    SELECT users.id, users.email, users.status, 
           user_info.firstname, user_info.middlename, user_info.lastname,
           user_info.program, user_info.mobile, user_info.nationality
    FROM users
    LEFT JOIN user_info ON users.id = user_info.user_id
    WHERE users.status = 'Not Enrolled'
");
?>
<?php
session_start();
include 'connections.php';

// Fetch enrolled vs not enrolled students
$enrolled = $conn->query("SELECT COUNT(*) AS total FROM users WHERE status='Enrolled'")->fetch_assoc()['total'];
$not_enrolled = $conn->query("SELECT COUNT(*) AS total FROM users WHERE status='Not Enrolled'")->fetch_assoc()['total'];
$total_students = $enrolled + $not_enrolled;

// Percentages
$enrolled_percent = $total_students > 0 ? round(($enrolled / $total_students) * 100, 2) : 0;
$not_enrolled_percent = $total_students > 0 ? round(($not_enrolled / $total_students) * 100, 2) : 0;

// Fetch enrolled students
$students_enrolled = $conn->query("
    SELECT users.id, users.email, users.status, 
           user_info.firstname, user_info.middlename, user_info.lastname,
           user_info.program, user_info.mobile, user_info.nationality
    FROM users
    LEFT JOIN user_info ON users.id = user_info.user_id
    WHERE users.status = 'Enrolled'
");

// Fetch not enrolled students
$students_not = $conn->query("
    SELECT users.id, users.email, users.status, 
           user_info.firstname, user_info.middlename, user_info.lastname,
           user_info.program, user_info.mobile, user_info.nationality
    FROM users
    LEFT JOIN user_info ON users.id = user_info.user_id
    WHERE users.status = 'Not Enrolled'
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Enrollee Statistics</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    :root {
      --primary: #001f54;
      --accent: #ffcc00;
      --light-bg: #f0f2f5;
      --card-bg: #ffffff;
      --text-dark: #2d3748;
      --text-light: #718096;
      --success: #48bb78;
      --warning: #ecc94b;
      --danger: #f56565;
      --info: #4299e1;
    }
    
    body {
      background: var(--light-bg);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: var(--text-dark);
    }
    
    .dashboard-container {
      display: grid;
      grid-template-columns: 1fr 3fr;
      gap: 20px;
      padding: 20px;
      max-width: 1400px;
      margin: 20px auto;
    }
    
    .left-panel {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }
    
    .right-panel {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 20px;
    }
    
    .card {
      background: var(--card-bg);
      border-radius: 12px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.04);
      padding: 20px;
      transition: transform 0.2s;
    }
    
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
    }
    
    .card-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
      padding-bottom: 10px;
      border-bottom: 1px solid #e2e8f0;
    }
    
    .card-title {
      font-size: 16px;
      font-weight: 600;
      color: var(--text-dark);
      margin: 0;
    }
    
    .card-icon {
      width: 40px;
      height: 40px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: var(--primary);
      color: white;
    }
    
    .stat-number {
      font-size: 24px;
      font-weight: 700;
      color: var(--primary);
      margin: 10px 0;
    }
    
    .stat-label {
      font-size: 14px;
      color: var(--text-light);
    }
    
    .progress-container {
      margin: 15px 0;
    }
    
    .progress {
      height: 8px;
      border-radius: 4px;
      background: #e2e8f0;
      overflow: hidden;
      margin-bottom: 5px;
    }
    
    .progress-bar {
      border-radius: 4px;
    }
    
    .progress-label {
      display: flex;
      justify-content: space-between;
      font-size: 12px;
      color: var(--text-light);
    }
    
    .sales-share {
      background: var(--primary);
      color: white;
      padding: 25px;
      border-radius: 12px;
    }
    
    .sales-share .stat-number {
      color: var(--accent);
      font-size: 32px;
    }
    
    .sales-share .stat-label {
      color: rgba(255, 255, 255, 0.8);
    }
    
    .chart-container {
      height: 200px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .tab-container {
      background: var(--card-bg);
      border-radius: 12px;
      padding: 20px;
      grid-column: 1 / -1;
    }
    
    .filter-box {
      background: white;
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 15px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }
    
    .slider-label {
      text-align: center;
      font-weight: 600;
      margin-bottom: 10px;
      color: var(--primary);
    }
    
    input[type=range] {
      width: 100%;
      height: 5px;
      -webkit-appearance: none;
      background: #e2e8f0;
      border-radius: 5px;
      outline: none;
    }
    
    input[type=range]::-webkit-slider-thumb {
      -webkit-appearance: none;
      width: 20px;
      height: 20px;
      border-radius: 50%;
      background: var(--primary);
      cursor: pointer;
    }
    
    .student-table {
      margin-top: 15px;
      width: 100%;
      border-collapse: collapse;
    }
    
    .student-table thead {
      background: var(--primary);
      color: var(--accent);
    }
    
    .student-table th {
      padding: 12px 15px;
      text-align: left;
      font-weight: 600;
    }
    
    .student-table td {
      padding: 12px 15px;
      border-bottom: 1px solid #e2e8f0;
    }
    
    .student-table tbody tr:hover {
      background: #f8f9fa;
    }
    
    .nav-tabs .nav-link {
      color: var(--text-light);
      font-weight: 600;
      border: none;
      padding: 10px 15px;
    }
    
    .nav-tabs .nav-link.active {
      color: var(--primary);
      border-bottom: 3px solid var(--primary);
      background: transparent;
    }
    
    .percentage-badge {
      padding: 4px 8px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 600;
    }
    
    .percentage-high {
      background: #c6f6d5;
      color: #22543d;
    }
    
    .percentage-medium {
      background: #feebc8;
      color: #744210;
    }
    
    .percentage-low {
      background: #fed7d7;
      color: #742a2a;
    }
    
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 15px;
      margin-top: 15px;
    }
    
    .stat-item {
      text-align: center;
      padding: 15px;
      background: #f8f9fa;
      border-radius: 8px;
    }
    
    .stat-value {
      font-size: 20px;
      font-weight: 700;
      color: var(--primary);
    }
    
    .stat-name {
      font-size: 14px;
      color: var(--text-light);
    }
  </style>
</head>
<body>
<?php include 'nav.php'; ?>

<div class="dashboard-container">
  <!-- LEFT PANEL -->
  <div class="left-panel">
    <!-- Sales Share Card -->
    <div class="sales-share">
      <div class="stat-label">Sales Share</div>
      <div class="stat-number">890,344 Sales</div>
    </div>
    
    <!-- Top Authors Card -->
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Top Authors</h3>
        <div class="card-icon">
          <i class="fas fa-crown"></i>
        </div>
      </div>
      <div class="stat-label">HTML/CSS/US, Python</div>
    </div>
    
    <!-- Popular Authors Card -->
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Popular Authors</h3>
        <div class="card-icon">
          <i class="fas fa-fire"></i>
        </div>
      </div>
      <div class="stat-label">HTML, VueJS, Laravel</div>
    </div>
    
    <!-- Distribution Stats -->
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Distribution</h3>
        <div class="card-icon">
          <i class="fas fa-chart-pie"></i>
        </div>
      </div>
      <div class="stats-grid">
        <div class="stat-item">
          <div class="stat-value">48%</div>
          <div class="stat-name">SNT</div>
        </div>
        <div class="stat-item">
          <div class="stat-value">20%</div>
          <div class="stat-name">REX</div>
        </div>
        <div class="stat-item">
          <div class="stat-value">32%</div>
          <div class="stat-name">SAP</div>
        </div>
      </div>
    </div>
    
    <!-- New Products Card -->
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">New Products</h3>
        <div class="card-icon">
          <i class="fas fa-gift"></i>
        </div>
      </div>
      <div class="stat-label">HTML/CSS/US, Python</div>
    </div>
  </div>

  <!-- RIGHT PANEL -->
  <div class="right-panel">
    <!-- Weekly Bestsellers Card -->
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Weekly Bestsellers</h3>
        <div class="card-icon">
          <i class="fas fa-trophy"></i>
        </div>
      </div>
      <div class="stat-label">HTML/CSS/US, Python</div>
    </div>
    
    <!-- Milestones Card -->
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Milestones</h3>
        <div class="card-icon">
          <i class="fas fa-flag"></i>
        </div>
      </div>
      <div class="stat-number">890,344 Sales</div>
    </div>
    
    <!-- HR Solutions Cards -->
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Man&Flower SaaS</h3>
        <div class="card-icon">
          <i class="fas fa-briefcase"></i>
        </div>
      </div>
      <div class="stat-label">HR Solutions</div>
      <div class="progress-container">
        <div class="progress-label">
          <span>Progress</span>
          <span>46%</span>
        </div>
        <div class="progress">
          <div class="progress-bar bg-info" role="progressbar" style="width: 46%"></div>
        </div>
      </div>
    </div>
    
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Building Studio</h3>
        <div class="card-icon">
          <i class="fas fa-building"></i>
        </div>
      </div>
      <div class="stat-label">HR Solutions</div>
      <div class="progress-container">
        <div class="progress-label">
          <span>Progress</span>
          <span>87%</span>
        </div>
        <div class="progress">
          <div class="progress-bar bg-success" role="progressbar" style="width: 87%"></div>
        </div>
      </div>
    </div>
    
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Foxy Solutions</h3>
        <div class="card-icon">
          <i class="fas fa-paw"></i>
        </div>
      </div>
      <div class="stat-label">HR Solutions</div>
      <div class="progress-container">
        <div class="progress-label">
          <span>Progress</span>
          <span>53%</span>
        </div>
        <div class="progress">
          <div class="progress-bar bg-warning" role="progressbar" style="width: 53%"></div>
        </div>
      </div>
    </div>
    
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">MyStreams</h3>
        <div class="card-icon">
          <i class="fas fa-stream"></i>
        </div>
      </div>
      <div class="stat-label">HR Solutions</div>
      <div class="progress-container">
        <div class="progress-label">
          <span>Progress</span>
          <span>92%</span>
        </div>
        <div class="progress">
          <div class="progress-bar bg-primary" role="progressbar" style="width: 92%"></div>
        </div>
      </div>
    </div>
    
    <!-- Sales Stats Cards -->
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Sale</h3>
        <div class="card-icon">
          <i class="fas fa-shopping-cart"></i>
        </div>
      </div>
      <div class="stat-number">$650</div>
    </div>
    
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Commission</h3>
        <div class="card-icon">
          <i class="fas fa-money-bill-wave"></i>
        </div>
      </div>
      <div class="stat-number">$2,040</div>
    </div>
    
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Refers</h3>
        <div class="card-icon">
          <i class="fas fa-user-friends"></i>
        </div>
      </div>
      <div class="stat-number">8,926</div>
    </div>
    
    <!-- Time Filter -->
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Time Range</h3>
        <div class="card-icon">
          <i class="fas fa-calendar"></i>
        </div>
      </div>
      <div class="d-flex justify-content-between mt-3">
        <button class="btn btn-outline-primary btn-sm">Day</button>
        <button class="btn btn-primary btn-sm">Week</button>
        <button class="btn btn-outline-primary btn-sm">Month</button>
      </div>
    </div>
  </div>
</div>

<!-- Student Data Section -->
<div class="dashboard-container">
  <div class="tab-container">
    <ul class="nav nav-tabs" id="myTab" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="enrolled-tab" data-bs-toggle="tab" data-bs-target="#enrolled" type="button" role="tab">Enrolled Students</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="notenrolled-tab" data-bs-toggle="tab" data-bs-target="#notenrolled" type="button" role="tab">Not Enrolled</button>
      </li>
    </ul>
    
    <div class="filter-box">
      <div class="slider-label">Show students with likelihood ≥ <span id="minValLabel">70%</span></div>
      <input type="range" min="0" max="100" value="70" id="minSlider">
    </div>
    
    <div class="tab-content" id="myTabContent">
      <!-- Enrolled -->
      <div class="tab-pane fade show active" id="enrolled" role="tabpanel">
        <table class="table student-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Program</th>
              <th>Mobile</th>
              <th>Likelihood</th>
            </tr>
          </thead>
          <tbody>
            <tr data-likelihood="89">
              <td>3</td>
              <td>ago afaef aefd</td>
              <td>gagocrer@gmail.com</td>
              <td>BSIT</td>
              <td>0495043954</td>
              <td><span class="percentage-badge percentage-high">89%</span></td>
            </tr>
            <tr data-likelihood="74">
              <td>5</td>
              <td>Seth Andrei Recomono Dimanno</td>
              <td>dimannosr@students.nu-lipa.edu.ph</td>
              <td>BSA - Marketing</td>
              <td>09753327815</td>
              <td><span class="percentage-badge percentage-medium">74%</span></td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <!-- Not Enrolled -->
      <div class="tab-pane fade" id="notenrolled" role="tabpanel">
        <table class="table student-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Program</th>
              <th>Mobile</th>
              <th>Likelihood</th>
            </tr>
          </thead>
          <tbody>
            <tr data-likelihood="35">
              <td>7</td>
              <td>John Michael Smith</td>
              <td>john.smith@example.com</td>
              <td>BS Computer Science</td>
              <td>09123456789</td>
              <td><span class="percentage-badge percentage-low">35%</span></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Single slider filter
  const minSlider = document.getElementById('minSlider');
  const minValLabel = document.getElementById('minValLabel');

  function filterStudents() {
    const minVal = parseInt(minSlider.value);
    minValLabel.textContent = minVal + '%';

    document.querySelectorAll('.student-table tbody tr').forEach(row => {
      const likelihood = parseInt(row.dataset.likelihood);
      row.style.display = (likelihood >= minVal) ? '' : 'none';
    });
  }

  minSlider.addEventListener('input', filterStudents);
  // Initialize with 70% filter
  filterStudents();
</script>
</body>
</html>