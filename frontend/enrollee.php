<?php
session_start();
include 'connections.php';

// Fetch enrolled vs not enrolled students (students only)
$enrolled = $conn->query("SELECT COUNT(*) AS total FROM users WHERE status='Enrolled' AND role='student'")->fetch_assoc()['total'];
$not_enrolled = $conn->query("SELECT COUNT(*) AS total FROM users WHERE status='Not Enrolled' AND role='student'")->fetch_assoc()['total'];
$total_students = $enrolled + $not_enrolled;

// Percentages
$enrolled_percent = $total_students > 0 ? round(($enrolled / $total_students) * 100, 2) : 0;
$not_enrolled_percent = $total_students > 0 ? round(($not_enrolled / $total_students) * 100, 2) : 0;

// Fetch all students with their likelihood from user_info
$students = $conn->query("
    SELECT users.id, users.email, users.status, 
           user_info.firstname, user_info.middlename, user_info.lastname,
           user_info.program, user_info.mobile, user_info.nationality, user_info.likelihood
    FROM users
    LEFT JOIN user_info ON users.id = user_info.user_id
    WHERE users.role = 'student'
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Enrollee Statistics</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      background: #ffffff;
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      color: #374151;
      margin: 0;
      padding: 0;
    }
    .dashboard-container {
      display: grid;
      grid-template-columns: 1fr 2fr;
      gap: 1rem;
      padding: 1rem;
      max-width: 1200px;
      margin: 0 auto;
    }
    .left-panel, .right-panel {
      background: #ffffff;
      border-radius: 6px;
      padding: 1rem;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
      border: 1px solid #e5e7eb;
    }
    .card-box {
      background: linear-gradient(135deg, #3b82f6, #1d4ed8);
      color: white;
      font-weight: 600;
      text-align: center;
      border-radius: 6px;
      padding: 1rem;
      margin-bottom: 1rem;
    }
    .card-box div:first-child {
      font-size: 0.875rem;
      margin-bottom: 0.5rem;
    }
    .card-box div:last-child {
      font-size: 2rem;
    }
    .chart-container {
      height: 300px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .progress-box {
      margin-bottom: 1rem;
      background: #f8fafc;
      border-radius: 8px;
      padding: 1rem;
      border: 1px solid #e2e8f0;
    }
    .progress-box h6 {
      font-size: 0.875rem;
      margin-bottom: 1rem;
      color: #1f2937;
      text-align: center;
      font-weight: 600;
    }
    .stats-card {
      background: #ffffff;
      border-radius: 6px;
      padding: 0.75rem;
      margin-bottom: 0.5rem;
      box-shadow: 0 1px 3px rgba(0,0,0,0.05);
      border-left: 4px solid transparent;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .stats-card.enrolled {
      border-left-color: #10b981;
      background: linear-gradient(90deg, #ecfdf5 0%, #ffffff 100%);
    }
    .stats-card.not-enrolled {
      border-left-color: #ef4444;
      background: linear-gradient(90deg, #fef2f2 0%, #ffffff 100%);
    }
    .stats-label {
      font-size: 0.75rem;
      font-weight: 500;
      color: #6b7280;
    }
    .stats-value {
      font-size: 1.25rem;
      font-weight: 700;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    .stats-value.enrolled {
      color: #10b981;
    }
    .stats-value.not-enrolled {
      color: #ef4444;
    }
    .stats-icon {
      width: 20px;
      height: 20px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.75rem;
      font-weight: bold;
      color: white;
    }
    .stats-icon.enrolled {
      background: #10b981;
    }
    .stats-icon.not-enrolled {
      background: #ef4444;
    }
    .progress {
      height: 12px;
      margin-bottom: 0.75rem;
      background: #f1f5f9;
      border-radius: 6px;
      overflow: hidden;
      box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
    }
    .progress-bar {
      border-radius: 6px;
      transition: width 0.3s ease;
      position: relative;
      overflow: hidden;
    }
    .progress-bar::after {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(90deg, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0.1) 50%, rgba(255,255,255,0.2) 100%);
      animation: shimmer 2s infinite;
    }
    @keyframes shimmer {
      0% { transform: translateX(-100%); }
      100% { transform: translateX(100%); }
    }
    .student-table-box {
      display: flex;
      flex-direction: column;
      height: calc(100vh - 200px);
      min-height: 400px;
      background: #ffffff;
      border-radius: 6px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
      border: 1px solid #e5e7eb;
    }
    .filter-box {
      flex-shrink: 0;
      background: #f9fafb;
      padding: 0.75rem;
      border-bottom: 1px solid #e5e7eb;
      border-radius: 6px 6px 0 0;
    }
    .slider-label {
      font-weight: 500;
      margin-bottom: 0.5rem;
      color: #4b5563;
      font-size: 0.75rem;
    }
    .comparison-buttons {
      display: flex;
      gap: 0.25rem;
      margin-bottom: 0.5rem;
    }
    .comparison-btn {
      padding: 0.25rem 0.5rem;
      border: 1px solid #d1d5db;
      background: #ffffff;
      border-radius: 4px;
      font-size: 0.75rem;
      cursor: pointer;
      transition: all 0.2s ease;
    }
    .comparison-btn.active {
      background: #3b82f6;
      color: white;
      border-color: #3b82f6;
    }
    .comparison-btn:hover {
      border-color: #3b82f6;
    }
    .slider-container {
      position: relative;
      margin-bottom: 0.25rem;
    }
    .slider-value {
      display: inline-block;
      background: #3b82f6;
      color: white;
      padding: 0.125rem 0.375rem;
      border-radius: 3px;
      font-size: 0.6875rem;
      font-weight: 500;
      margin-left: 0.5rem;
    }
    input[type=range] {
      width: 100%;
      height: 4px;
      background: #d1d5db;
      border-radius: 2px;
      outline: none;
    }
    input[type=range]::-webkit-slider-thumb {
      -webkit-appearance: none;
      width: 12px;
      height: 12px;
      background: #3b82f6;
      border-radius: 50%;
      cursor: pointer;
    }
    .form-select {
      border: 1px solid #d1d5db;
      border-radius: 4px;
      padding: 0.375rem 0.5rem;
      font-size: 0.75rem;
      color: #4b5563;
    }
    .form-select:focus {
      border-color: #3b82f6;
    }
    .tab-content {
      flex-grow: 1;
      overflow-y: auto;
      padding: 0.75rem;
    }
    .table {
      font-size: 0.875rem;
      border: none;
      margin: 0;
      table-layout: fixed;
      width: 100%;
      border-collapse: separate;
      border-spacing: 0;
    }
    .table thead {
      background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
      color: #374151;
      font-weight: 600;
      text-transform: uppercase;
      font-size: 0.75rem;
      letter-spacing: 0.05em;
      position: sticky;
      top: 0;
      z-index: 10;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .table thead th {
      border: none;
      padding: 1rem 0.75rem;
      white-space: nowrap;
      border-bottom: 2px solid #e5e7eb;
      position: relative;
    }
    .table thead th::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      height: 2px;
      background: linear-gradient(90deg, #3b82f6, #1d4ed8);
      opacity: 0;
      transition: opacity 0.3s ease;
    }
    .table thead th:hover::after {
      opacity: 1;
    }
    .table tbody tr {
      border-bottom: 1px solid #f3f4f6;
      transition: all 0.2s ease;
    }
    .table tbody tr:hover {
      background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 50%);
      transform: translateY(-1px);
    }
    .table tbody tr:last-child {
      border-bottom: none;
    }
    .table td {
      padding: 1rem 0.75rem;
      vertical-align: middle;
      border: none;
      position: relative;
    }
    .table td::before {
      content: '';
      position: absolute;
      left: 0;
      top: 0;
      bottom: 0;
      width: 3px;
      background: transparent;
      transition: background-color 0.3s ease;
    }
    .table tbody tr:hover td::before {
      background: #3b82f6;
    }
    .student-table th:nth-child(1), .student-table td:nth-child(1) { 
      width: 60px; 
      text-align: center;
      font-weight: 600;
    }
    .student-table th:nth-child(2), .student-table td:nth-child(2) { 
      width: 22%; 
      min-width: 180px; 
    }
    .student-table th:nth-child(3), .student-table td:nth-child(3) { 
      width: 25%; 
      min-width: 200px; 
    }
    .student-table th:nth-child(4), .student-table td:nth-child(4) { 
      width: 18%; 
      min-width: 120px; 
    }
    .student-table th:nth-child(5), .student-table td:nth-child(5) { 
      width: 15%; 
      min-width: 120px; 
    }
    .student-table th:nth-child(6), .student-table td:nth-child(6) { 
      width: 100px; 
      text-align: center;
    }
    .student-table th:nth-child(7), .student-table td:nth-child(7) { 
      width: 90px; 
      text-align: center;
      font-weight: 600;
    }
    .student-table td {
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      line-height: 1.5;
    }
    .student-table td:nth-child(1) {
      font-weight: 600;
      color: #6b7280;
    }
    .student-table td:nth-child(7) {
      font-size: 0.875rem;
      font-weight: 700;
    }
    .tab-content {
      overflow-x: auto;
    }
    .status-badge {
      padding: 0.375rem 0.75rem;
      border-radius: 20px;
      font-weight: 600;
      font-size: 0.75rem;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      text-transform: uppercase;
      letter-spacing: 0.025em;
      box-shadow: 0 1px 2px rgba(0,0,0,0.1);
      transition: all 0.2s ease;
    }
    .status-badge:hover {
      transform: translateY(-1px);
      box-shadow: 0 2px 4px rgba(0,0,0,0.15);
    }
    .status-enrolled {
      background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
      color: #166534;
      border: 1px solid #86efac;
    }
    .status-not-enrolled {
      background: linear-gradient(135deg, #fef2f2 0%, #fecaca 100%);
      color: #991b1b;
      border: 1px solid #fca5a5;
    }
    @media (max-width: 768px) {
      .dashboard-container {
        grid-template-columns: 1fr;
        gap: 0.5rem;
        padding: 0.5rem;
      }
      .left-panel, .right-panel {
        padding: 0.75rem;
      }
      .student-table-box {
        height: calc(100vh - 300px);
      }
    }
  </style>
</head>
<body>
<?php include 'nav.php'; ?>

<div class="dashboard-container">
  <!-- LEFT PANEL -->
  <div class="left-panel">
    <div class="card-box">
      <div>Total Students</div>
      <div style="font-size: 32px;"><?php echo $total_students; ?></div>
    </div>
    <div class="chart-container">
      <canvas id="enrollmentChart"></canvas>
    </div>
  </div>

  <!-- RIGHT PANEL -->
  <div class="right-panel">
    <div class="progress-box">
      <h6 class="fw-bold">Enrollment Statistics</h6>
      
      <div class="stats-card enrolled">
        <div>
          <div class="stats-label">Enrolled Students</div>
          <div class="stats-value enrolled">
            <span class="stats-icon enrolled">✓</span>
            <?php echo $enrolled; ?> (<?php echo $enrolled_percent; ?>%)
          </div>
        </div>
      </div>
      
      <div class="stats-card not-enrolled">
        <div>
          <div class="stats-label">Not Enrolled Students</div>
          <div class="stats-value not-enrolled">
            <span class="stats-icon not-enrolled">✗</span>
            <?php echo $not_enrolled; ?> (<?php echo $not_enrolled_percent; ?>%)
          </div>
        </div>
      </div>
      
      <div style="margin-top: 1rem;">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <span class="text-success fw-semibold" style="font-size: 0.875rem;">Enrolled <?php echo $enrolled_percent; ?>%</span>
          <span class="text-danger fw-semibold" style="font-size: 0.875rem;">Not Enrolled <?php echo $not_enrolled_percent; ?>%</span>
        </div>
        <div class="progress mb-2">
          <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $enrolled_percent; ?>%"></div>
        </div>
        <div class="progress">
          <div class="progress-bar bg-danger" role="progressbar" style="width: <?php echo $not_enrolled_percent; ?>%"></div>
        </div>
      </div>
    </div>

    <div class="student-table-box">
      <!-- Filter Controls -->
      <div class="filter-box">
        <div class="row">
          <div class="col-md-6">
            <div class="slider-label">
              Show students with likelihood 
              <span class="slider-value" id="minValLabel">70%</span>
            </div>
            <div class="comparison-buttons">
              <button class="comparison-btn active" data-operator="gte">≥ Greater than or equal</button>
              <button class="comparison-btn" data-operator="lte">≤ Less than or equal</button>
            </div>
            <div class="slider-container">
              <input type="range" min="0" max="100" value="70" id="minSlider">
            </div>
          </div>
          <div class="col-md-6">
            <div class="slider-label">Filter by Status</div>
            <select class="form-select" id="statusFilter">
              <option value="all">All Students</option>
              <option value="Enrolled">Enrolled Only</option>
              <option value="Not Enrolled">Not Enrolled Only</option>
            </select>
          </div>
        </div>
      </div>

      <h6 class="fw-bold mt-3 mb-3" style="color: #1f2937; font-size: 1rem; border-bottom: 2px solid #e5e7eb; padding-bottom: 0.5rem;">Student Details</h6>
      
      <div class="tab-content">
        <table class="table table-bordered table-sm student-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Name</th>
              <th>Email</th>
              <th>Program</th>
              <th>Mobile</th>
              <th>Status</th>
              <th>Likelihood</th>
            </tr>
          </thead>
          <tbody>
            <?php
            if ($students->num_rows > 0) {
                $counter = 1;
                while($row = $students->fetch_assoc()) {
                    $likelihood = isset($row['likelihood']) ? $row['likelihood'] : 0;
                    echo "<tr data-status='".$row['status']."' data-likelihood='$likelihood'>";
                    echo "<td>".$counter++."</td>";
                    $name = $row['firstname'] . ' ' . $row['middlename'] . ' ' . $row['lastname'];
                    echo "<td>".htmlspecialchars($name)."</td>";
                    echo "<td>".htmlspecialchars($row['email'])."</td>";
                    echo "<td>".htmlspecialchars($row['program'])."</td>";
                    echo "<td>".htmlspecialchars($row['mobile'])."</td>";
                    $status_class = $row['status'] == 'Enrolled' ? 'status-enrolled' : 'status-not-enrolled';
                    echo "<td><span class='status-badge $status_class'>".$row['status']."</span></td>";
                    echo "<td>".$likelihood."%</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>No students found</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Pie chart
  const ctx = document.getElementById('enrollmentChart').getContext('2d');
  new Chart(ctx, {
    type: 'pie',
    data: {
      labels: ['Enrolled', 'Not Enrolled'],
      datasets: [{
        data: [<?php echo $enrolled; ?>, <?php echo $not_enrolled; ?>],
        backgroundColor: ['#001f54', '#ffcc00']
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: 'bottom'
        }
      }
    }
  });

  // Filter elements
  const minSlider = document.getElementById('minSlider');
  const minValLabel = document.getElementById('minValLabel');
  const statusFilter = document.getElementById('statusFilter');
  const comparisonButtons = document.querySelectorAll('.comparison-btn');
  let currentOperator = 'gte';

  // Handle comparison button clicks
  comparisonButtons.forEach(button => {
    button.addEventListener('click', function() {
      comparisonButtons.forEach(btn => btn.classList.remove('active'));
      this.classList.add('active');
      currentOperator = this.dataset.operator;
      filterStudents();
    });
  });

  function filterStudents() {
    const minVal = parseInt(minSlider.value);
    const statusVal = statusFilter.value;
    minValLabel.textContent = minVal + '%';

    document.querySelectorAll('.student-table tbody tr').forEach(row => {
      const likelihood = parseInt(row.dataset.likelihood);
      const status = row.dataset.status;

      let likelihoodMatch = false;
      if (currentOperator === 'gte') {
        likelihoodMatch = likelihood >= minVal;
      } else if (currentOperator === 'lte') {
        likelihoodMatch = likelihood <= minVal;
      }

      const statusMatch = statusVal === 'all' || status === statusVal;
      row.style.display = (likelihoodMatch && statusMatch) ? '' : 'none';
    });
  }

  minSlider.addEventListener('input', filterStudents);
  statusFilter.addEventListener('change', filterStudents);

  // Initialize with default filter
  filterStudents();
</script>
</body>
</html>
