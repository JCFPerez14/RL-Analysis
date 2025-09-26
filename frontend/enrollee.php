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
  <title>Enrollee Statistics</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      background: #f0f2f5;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .dashboard-container {
      display: grid;
      grid-template-columns: 1fr 2fr;
      gap: 20px;
      padding: 20px;
      max-width: 1200px;
      margin: 20px auto;
    }
    .left-panel, .right-panel {
      background: white;
      border-radius: 12px;
      padding: 20px;
      box-shadow: 0 3px 6px rgba(0,0,0,0.1);
      display: flex;
      flex-direction: column;
    }
    .card-box {
      background: #001f54;
      color: #ffcc00;
      font-weight: bold;
      text-align: center;
      border-radius: 10px;
      padding: 20px;
      font-size: 20px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    .chart-container, .progress-box, .student-table-box {
      margin-top: 10px;
    }
    .chart-container {
      flex-grow: 1;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .table thead {
      background: #001f54;
      color: #ffcc00;
      font-weight: bold;
    }
    .table tbody tr:nth-child(even) {
      background: #f8f9fa;
    }
    
    /* Fixed table styling */
    .student-table-box {
      display: flex;
      flex-direction: column;
      height: 500px;
    }
    .filter-box {
      flex-shrink: 0;
      background: white;
      padding: 10px 0;
      border-bottom: 2px solid #ddd;
      position: sticky;
      top: 0;
      z-index: 10;
    }
    .slider-label {
      text-align: center;
      font-weight: bold;
      margin-bottom: 5px;
      color: #001f54;
    }
    input[type=range] {
      width: 100%;
    }
    .tab-content {
      flex-grow: 1;
      overflow-y: auto;
    }
    table {
      margin: 0;
      table-layout: fixed;
      width: 100%;
    }
    /* Fixed column widths */
    .student-table th:nth-child(1),
    .student-table td:nth-child(1) {
      width: 50px; /* ID */
    }
    .student-table th:nth-child(2),
    .student-table td:nth-child(2) {
      width: 180px; /* Name */
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .student-table th:nth-child(3),
    .student-table td:nth-child(3) {
      width: 200px; /* Email */
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .student-table th:nth-child(4),
    .student-table td:nth-child(4) {
      width: 120px; /* Program */
    }
    .student-table th:nth-child(5),
    .student-table td:nth-child(5) {
      width: 120px; /* Mobile */
    }
    .student-table th:nth-child(6),
    .student-table td:nth-child(6) {
      width: 100px; /* Likelihood */
      text-align: center;
    }
    .header-title {
      color: #001f54;
      border-bottom: 2px solid #ffcc00;
      padding-bottom: 10px;
      margin-bottom: 15px;
    }
    .status-filter {
      margin-bottom: 15px;
    }
    .status-badge {
      padding: 5px 10px;
      border-radius: 10px;
      font-weight: bold;
    }
    .status-enrolled {
      background-color: #d4edda;
      color: #155724;
    }
    .status-not-enrolled {
      font-size: 0.6em;
      background-color: #f8d7da;
      color: #721c24;
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
      <h6 class="fw-bold">Most Likely to Enroll / Not Enroll</h6>
      <div class="progress mb-2">
        <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $enrolled_percent; ?>%">
          Enrolled <?php echo $enrolled_percent; ?>%
        </div>
      </div>
      <div class="progress">
        <div class="progress-bar bg-danger" role="progressbar" style="width: <?php echo $not_enrolled_percent; ?>%">
          Not Enrolled <?php echo $not_enrolled_percent; ?>%
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
              <select id="comparisonOperator" class="form-select d-inline w-auto">
                <option value="gte" selected>≥</option>
                <option value="lte">≤</option>
              </select>
              <span id="minValLabel">70%</span>
            </div>
            <input type="range" min="0" max="100" value="70" id="minSlider">
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

      <h6 class="fw-bold mt-3">Student Details</h6>
      
      <div class="tab-content">
        <table class="table table-bordered table-sm student-table">
          <thead>
            <tr>
              <th>ID</th>
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
                while($row = $students->fetch_assoc()) {
                    $likelihood = isset($row['likelihood']) ? $row['likelihood'] : 0;
                    echo "<tr data-status='".$row['status']."' data-likelihood='$likelihood'>";
                    echo "<td>".$row['id']."</td>";
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
  const comparisonOperator = document.getElementById('comparisonOperator');

  function filterStudents() {
    const minVal = parseInt(minSlider.value);
    const operator = comparisonOperator.value;
    const statusVal = statusFilter.value;
    minValLabel.textContent = minVal + '%';

    document.querySelectorAll('.student-table tbody tr').forEach(row => {
      const likelihood = parseInt(row.dataset.likelihood);
      const status = row.dataset.status;

      let likelihoodMatch = false;
      if (operator === 'gte') {
        likelihoodMatch = likelihood >= minVal;
      } else if (operator === 'lte') {
        likelihoodMatch = likelihood <= minVal;
      }

      const statusMatch = statusVal === 'all' || status === statusVal;
      row.style.display = (likelihoodMatch && statusMatch) ? '' : 'none';
    });
  }

  minSlider.addEventListener('input', filterStudents);
  statusFilter.addEventListener('change', filterStudents);
  comparisonOperator.addEventListener('change', filterStudents);

  // Initialize with default filter
  filterStudents();
</script>
</body>
</html>
