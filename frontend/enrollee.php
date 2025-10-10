<?php
session_start();
include 'connections.php';

// Restrict access to admin only
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

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

// Fetch program distribution
$program_stats = $conn->query("
    SELECT program, COUNT(*) as count 
    FROM user_info ui
    JOIN users u ON ui.user_id = u.id
    WHERE u.role = 'student' AND program IS NOT NULL
    GROUP BY program
");

// Calculate likelihood distribution
$likelihood_stats = $conn->query("
    SELECT 
        CASE 
            WHEN likelihood >= 80 THEN 'high'
            WHEN likelihood >= 50 THEN 'medium'
            ELSE 'low'
        END as category,
        COUNT(*) as count
    FROM user_info ui
    JOIN users u ON ui.user_id = u.id
    WHERE u.role = 'student' AND likelihood IS NOT NULL
    GROUP BY 
        CASE 
            WHEN likelihood >= 80 THEN 'high'
            WHEN likelihood >= 50 THEN 'medium'
            ELSE 'low'
        END
");

// Prepare program data for chart
$program_labels = [];
$program_data = [];
$program_colors = ['#001f54', '#1d4ed8', '#3b82f6', '#60a5fa', '#93c5fd'];

if ($program_stats->num_rows > 0) {
    while($row = $program_stats->fetch_assoc()) {
        $program_labels[] = $row['program'];
        $program_data[] = $row['count'];
    }
}

// Prepare likelihood data
$likelihood_counts = ['high' => 0, 'medium' => 0, 'low' => 0];
if ($likelihood_stats->num_rows > 0) {
    while($row = $likelihood_stats->fetch_assoc()) {
        $likelihood_counts[$row['category']] = $row['count'];
    }
}

// Fetch detailed student data for filtering
$all_students_data = $conn->query("
    SELECT users.id, users.status, user_info.program, user_info.likelihood
    FROM users
    LEFT JOIN user_info ON users.id = user_info.user_id
    WHERE users.role = 'student'
");

$student_data = [];
while($row = $all_students_data->fetch_assoc()) {
    $student_data[] = $row;
}

// Prepare enrollment data by program
$enrollment_by_program = [];
foreach($program_labels as $program) {
    $enrolled_in_program = $conn->query("
        SELECT COUNT(*) as enrolled_count
        FROM users u
        JOIN user_info ui ON u.id = ui.user_id
        WHERE u.role = 'student' AND u.status = 'Enrolled' AND ui.program = '$program'
    ")->fetch_assoc()['enrolled_count'];
    
    $not_enrolled_in_program = $conn->query("
        SELECT COUNT(*) as not_enrolled_count
        FROM users u
        JOIN user_info ui ON u.id = ui.user_id
        WHERE u.role = 'student' AND u.status = 'Not Enrolled' AND ui.program = '$program'
    ")->fetch_assoc()['not_enrolled_count'];
    
    $enrollment_by_program[$program] = [
        'enrolled' => $enrolled_in_program,
        'not_enrolled' => $not_enrolled_in_program
    ];
}

// Define program groups by schools
$program_groups = [
    'SACE' => ['BSCS', 'BSArch', 'BSCE', 'BSIT'],
    'SABM' => ['BSTM', 'BSA', 'BSBA - Fin', 'BSBA - MM'],
    'SAHS' => ['BSN', 'BSMT', 'BSPsy']
];

// Calculate enrollment by school groups
$enrollment_by_school = [];
foreach($program_groups as $school => $programs) {
    $school_enrolled = 0;
    $school_not_enrolled = 0;
    
    foreach($programs as $program) {
        if(isset($enrollment_by_program[$program])) {
            $school_enrolled += $enrollment_by_program[$program]['enrolled'];
            $school_not_enrolled += $enrollment_by_program[$program]['not_enrolled'];
        }
    }
    
    $enrollment_by_school[$school] = [
        'enrolled' => $school_enrolled,
        'not_enrolled' => $school_not_enrolled,
        'total' => $school_enrolled + $school_not_enrolled
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NU Lipa - Admin Dashboard</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    
    .dashboard-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
      gap: 2rem;
      margin-bottom: 2rem;
    }
    
    .dashboard-card {
      background: #ffffff;
      border-radius: 12px;
      padding: 1.5rem;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
      border: 1px solid #e5e7eb;
    }
    
    .card-title {
      font-size: 1.125rem;
      font-weight: 600;
      color: #1f2937;
      margin-bottom: 1.5rem;
    }
    /* Donut Chart Styles */
    .donut-chart-container {
      position: relative;
      height: 250px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 1.5rem;
    }
    
    .donut-center-text {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      text-align: center;
      z-index: 10;
    }
    
    .donut-center-number {
      font-size: 2.5rem;
      font-weight: 700;
      color: #1f2937;
      line-height: 1;
    }
    
    .donut-center-label {
      font-size: 0.875rem;
      color: #6b7280;
      font-weight: 500;
    }
    
    .chart-legend {
      display: flex;
      justify-content: center;
      gap: 2rem;
      margin-top: 1rem;
    }
    
    .legend-item {
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    
    .legend-dot {
      width: 12px;
      height: 12px;
      border-radius: 50%;
    }
    
    .legend-dot.enrolled {
      background: #293855;
    }
    
    .legend-dot.not-enrolled {
      background: #F1AC20;
    }
    
    .legend-text {
      font-size: 0.875rem;
      color: #6b7280;
      font-weight: 500;
    }
    
    .stats-label {
      font-size: 0.875rem;
      color: #6b7280;
      font-weight: 500;
    }
    
    .stats-value {
      font-size: 1.25rem;
      font-weight: 700;
      color: #1f2937;
    }
    
    .stats-bar {
      width: 100%;
      height: 8px;
      background: #f3f4f6;
      border-radius: 4px;
      overflow: hidden;
      margin-bottom: 0.5rem;
    }
    
    .stats-bar-fill {
      height: 100%;
      border-radius: 4px;
      transition: width 0.3s ease;
    }
    
    .stats-bar-fill.enrolled {
      background: linear-gradient(90deg, #293855, #4165D5);
    }
    
    .stats-bar-fill.not-enrolled {
      background: #F1AC20;
    }
    
    /* New Chart Container Styles */
    .chart-container {
      position: relative;
      height: 250px;
      margin-bottom: 1.5rem;
    }
    
    /* Enhanced Statistics Grid */
    .stats-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1rem;
      margin-bottom: 1.5rem;
    }
    
    .stat-card {
      display: flex;
      align-items: center;
      gap: 1rem;
      padding: 1rem;
      background: #f8fafc;
      border-radius: 8px;
      border: 1px solid #e5e7eb;
      transition: all 0.2s ease;
    }
    
    .stat-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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
    
    .stat-icon.enrolled {
      background: linear-gradient(135deg, #dcfce7, #bbf7d0);
      color: #166534;
    }
    
    .stat-icon.not-enrolled {
      background: linear-gradient(135deg, #fef3c7, #fcd34d);
      color: #92400e;
    }
    
    .stat-content {
      flex: 1;
    }
    
    .stat-value {
      font-size: 1.5rem;
      font-weight: 700;
      color: #1f2937;
      line-height: 1;
    }
    
    .stat-label {
      font-size: 0.75rem;
      color: #6b7280;
      font-weight: 500;
      margin-top: 0.25rem;
    }
    
    .stat-percentage {
      font-size: 0.875rem;
      font-weight: 600;
      color: #059669;
      margin-top: 0.25rem;
    }
    
    /* Enhanced Enrollment Bar */
    .enrollment-bar {
      width: 100%;
      height: 12px;
      background: #f3f4f6;
      border-radius: 6px;
      overflow: hidden;
      display: flex;
    }
    
    .enrollment-segment {
      height: 100%;
      transition: width 0.3s ease;
    }
    
    .enrollment-segment.enrolled {
      background: linear-gradient(90deg, #293855, #4165D5);
    }
    
    .enrollment-segment.not-enrolled {
      background: #F1AC20;
    }
    
    /* Likelihood Summary */
    .likelihood-summary {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
      gap: 1rem;
      margin-top: 1rem;
    }
    
    .summary-item {
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 1rem;
      background: #f8fafc;
      border-radius: 8px;
      border: 1px solid #e5e7eb;
    }
    
    .summary-label {
      font-size: 0.75rem;
      color: #6b7280;
      font-weight: 500;
      text-align: center;
      margin-bottom: 0.5rem;
    }
    
    .summary-value {
      font-size: 1.5rem;
      font-weight: 700;
      color: #1f2937;
    }
    
    /* Chart Filter Styles */
    .chart-filter {
      margin-bottom: 1rem;
      display: flex;
      justify-content: flex-end;
    }
    
    .chart-filter .filter-dropdown {
      min-width: 140px;
      background: #ffffff;
      border: 1px solid #d1d5db;
      border-radius: 8px;
      padding: 0.5rem 1rem;
      font-size: 0.875rem;
      color: #374151;
      cursor: pointer;
      transition: all 0.2s ease;
    }
    
    .chart-filter .filter-dropdown:hover {
      border-color: #001f54;
    }
    
    .chart-filter .filter-dropdown:focus {
      outline: none;
      border-color: #001f54;
      box-shadow: 0 0 0 3px rgba(0, 31, 84, 0.1);
    }
    /* Student Table Styles */
    .students-table-card {
      grid-column: 1 / -1;
      background: #ffffff;
      border-radius: 12px;
      padding: 1.5rem;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
      border: 1px solid #e5e7eb;
    }
    
    .table-filters {
      display: flex;
      gap: 1rem;
      margin-bottom: 1.5rem;
      flex-wrap: wrap;
    }
    
    .filter-dropdown {
      background: #ffffff;
      border: 1px solid #d1d5db;
      border-radius: 8px;
      padding: 0.5rem 1rem;
      font-size: 0.875rem;
      color: #374151;
      cursor: pointer;
      transition: all 0.2s ease;
      min-width: 120px;
    }
    
    .filter-dropdown:hover {
      border-color: #293855;
    }
    
    .filter-dropdown:focus {
      outline: none;
      border-color: #293855;
      box-shadow: 0 0 0 3px rgba(41, 56, 85, 0.1);
    }
    /* Enhanced Table Styles */
    .modern-table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0;
      background: #ffffff;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .modern-table thead {
      background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    }
    
    .modern-table th {
      padding: 1rem;
      text-align: left;
      font-weight: 600;
      font-size: 0.875rem;
      color: #374151;
      border-bottom: 2px solid #e5e7eb;
      position: relative;
      cursor: pointer;
      user-select: none;
      transition: all 0.2s ease;
    }
    
    .modern-table th:hover {
      background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
    }
    
    .modern-table th.sortable {
      padding-right: 2rem;
    }
    
    .modern-table td {
      padding: 1rem;
      border-bottom: 1px solid #f3f4f6;
      font-size: 0.875rem;
      color: #374151;
      vertical-align: middle;
    }
    
    .modern-table tbody tr {
      transition: all 0.2s ease;
    }
    
    .modern-table tbody tr:hover {
      background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 50%);
    }
    
    .modern-table tbody tr:last-child td {
      border-bottom: none;
    }
    
    /* Status Badges */
    .status-pill {
      display: inline-flex;
      align-items: center;
      padding: 0.25rem 0.75rem;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.025em;
    }
    
    .status-pill.enrolled {
      background: #dcfce7;
      color: #166534;
      border: 1px solid #86efac;
    }
    
    .status-pill.not-enrolled {
      background: #fef2f2;
      color: #991b1b;
      border: 1px solid #fca5a5;
    }
    
    .status-pill.pending {
      background: #fef3c7;
      color: #92400e;
      border: 1px solid #fcd34d;
    }
    
    .status-pill.applied {
      background: #dbeafe;
      color: #1e40af;
      border: 1px solid #93c5fd;
    }
    
    .status-pill.accepted {
      background: #dcfce7;
      color: #166534;
      border: 1px solid #86efac;
    }
    
    /* Likelihood Progress Bars */
    .likelihood-container {
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }
    
    .likelihood-progress {
      flex: 1;
      height: 8px;
      background: #f3f4f6;
      border-radius: 4px;
      overflow: hidden;
    }
    
    .likelihood-progress-fill {
      height: 100%;
      background: linear-gradient(90deg, #293855, #4165D5);
      border-radius: 4px;
      transition: width 0.3s ease;
    }
    
    .likelihood-text {
      font-weight: 600;
      color: #1f2937;
      min-width: 45px;
      text-align: right;
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
      cursor: pointer;
      user-select: none;
      transition: all 0.2s ease;
    }
    .table thead th:hover {
      background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
      transform: translateY(-1px);
    }
    .table thead th.sortable {
      padding-right: 2rem;
    }
    .sort-indicator {
      position: absolute;
      right: 0.5rem;
      top: 50%;
      transform: translateY(-50%);
      font-size: 0.75rem;
      color: #9ca3af;
      transition: all 0.2s ease;
    }
    .sort-indicator.active {
      color: #3b82f6;
    }
    .sort-indicator.asc::after {
      content: '▲';
    }
    .sort-indicator.desc::after {
      content: '▼';
    }
    .sort-indicator.inactive::after {
      content: '⇅';
    }
    .table thead th::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      height: 2px;
      background: linear-gradient(90deg, #4165D5, #293855);
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
      background: #4165D5;
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
      
      .dashboard-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
      }
      
      .stats-grid {
        grid-template-columns: 1fr;
      }
      
      .likelihood-summary {
        grid-template-columns: 1fr;
      }
      
      .dashboard-title {
        font-size: 1.5rem;
      }
      
      .donut-chart-container {
        height: 200px;
      }
      
      .donut-center-number {
        font-size: 2rem;
      }
      
      .chart-legend {
        flex-direction: column;
        gap: 0.5rem;
        align-items: center;
      }
      
      .table-filters {
        flex-direction: column;
        gap: 0.75rem;
      }
      
      .filter-dropdown {
        width: 100%;
      }
      
      .modern-table {
        font-size: 0.75rem;
      }
      
      .modern-table th,
      .modern-table td {
        padding: 0.75rem 0.5rem;
      }
      
      .likelihood-container {
        flex-direction: column;
        gap: 0.5rem;
        align-items: flex-start;
      }
      
      .likelihood-progress {
        width: 100%;
      }
      
      .likelihood-text {
        min-width: auto;
        text-align: left;
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
      <a href="enrollee.php" class="nav-item active">
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
    <div class="dashboard-title">Admin Dashboard</div>
    <div class="dashboard-subtitle">Welcome back, Administrator. Here's an overview of your school's activities.</div>
    
    <div class="dashboard-grid">
      <!-- Enrollment Status Card -->
      <div class="dashboard-card">
        <div class="card-title">Enrollment Status</div>
        <div class="chart-filter">
          <select class="filter-dropdown" id="enrollmentFilter">
            <option value="all">All Status</option>
            <option value="Enrolled">Enrolled Only</option>
            <option value="Not Enrolled">Not Enrolled Only</option>
          </select>
        </div>
        <div class="donut-chart-container">
          <canvas id="enrollmentChart"></canvas>
          <div class="donut-center-text">
            <div class="donut-center-number" id="enrollmentTotal"><?php echo $total_students; ?></div>
            <div class="donut-center-label">Total Students</div>
          </div>
        </div>
        <div class="chart-legend" id="enrollmentLegend">
          <div class="legend-item">
            <div class="legend-dot enrolled"></div>
            <span class="legend-text">Enrolled (<?php echo $enrolled; ?>)</span>
          </div>
          <div class="legend-item">
            <div class="legend-dot not-enrolled"></div>
            <span class="legend-text">Not Enrolled (<?php echo $not_enrolled; ?>)</span>
          </div>
        </div>
      </div>
      
      <!-- Program Distribution Card -->
      <div class="dashboard-card">
        <div class="card-title">Program Distribution</div>
        <div class="chart-filter">
          <select class="filter-dropdown" id="programChartFilter">
            <option value="all">All Programs</option>
            <option value="SACE">SACE (BSCS, BSArch, BSCE, BSIT)</option>
            <option value="SABM">SABM (BSTM, BSA, BSBA - Fin, BSBA - MM)</option>
            <option value="SAHS">SAHS (BSN, BSMT, BSPsy)</option>
          </select>
        </div>
        <div class="chart-container">
          <canvas id="programChart"></canvas>
        </div>
      </div>
      
      
      
      <!-- Likelihood Distribution Card -->
      <div class="dashboard-card">
        <div class="card-title">Enrollment Likelihood Distribution</div>
        <div class="chart-filter">
          <select class="filter-dropdown" id="likelihoodFilter">
            <option value="all">All Levels</option>
            <option value="high">High Only (>80%)</option>
            <option value="medium">Medium Only (50-80%)</option>
            <option value="low">Low Only (<50%)</option>
          </select>
        </div>
        <div class="chart-container">
          <canvas id="likelihoodChart"></canvas>
        </div>
        <div class="likelihood-summary">
          <div class="summary-item">
            <span class="summary-label">High Likelihood (>80%)</span>
            <span class="summary-value" id="highLikelihood">0</span>
          </div>
          <div class="summary-item">
            <span class="summary-label">Medium Likelihood (50-80%)</span>
            <span class="summary-value" id="mediumLikelihood">0</span>
          </div>
          <div class="summary-item">
            <span class="summary-label">Low Likelihood (<50%)</span>
            <span class="summary-value" id="lowLikelihood">0</span>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Students Likely to Enroll Table -->
    <div class="students-table-card">
      <div class="card-title">Students Likely to Enroll</div>
      
      <!-- Filter Controls -->
      <div class="table-filters">
        <select class="filter-dropdown" id="statusFilter">
          <option value="all">Status</option>
          <option value="Enrolled">Enrolled</option>
          <option value="Not Enrolled">Not Enrolled</option>
        </select>
        <select class="filter-dropdown" id="programFilter">
          <option value="all">Program</option>
          <option value="BSCS">BSCS</option>
          <option value="BSTM">BSTM</option>
        </select>
      </div>
      
      <table class="modern-table">
        <thead>
          <tr>
            <th class="sortable" data-sort="text">Name <span class="sort-indicator inactive"></span></th>
            <th class="sortable" data-sort="text">Program <span class="sort-indicator inactive"></span></th>
            <th class="sortable" data-sort="text">Status <span class="sort-indicator inactive"></span></th>
            <th class="sortable" data-sort="number">Likelihood <span class="sort-indicator inactive"></span></th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ($students->num_rows > 0) {
              $counter = 1;
              while($row = $students->fetch_assoc()) {
                  $likelihood = isset($row['likelihood']) ? $row['likelihood'] : 0;
                  echo "<tr data-status='".$row['status']."' data-program='".$row['program']."' data-likelihood='$likelihood'>";
                  $name = $row['firstname'] . ' ' . $row['middlename'] . ' ' . $row['lastname'];
                  echo "<td><div><strong>".htmlspecialchars($name)."</strong></div><div style='color: #6b7280; font-size: 0.75rem;'>".htmlspecialchars($row['email'])."</div></td>";
                  echo "<td>".htmlspecialchars($row['program'])."</td>";
                  $status_class = strtolower(str_replace(' ', '-', $row['status']));
                  echo "<td><span class='status-pill $status_class'>".$row['status']."</span></td>";
                  echo "<td><div class='likelihood-container'><div class='likelihood-progress'><div class='likelihood-progress-fill' style='width: ".$likelihood."%'></div></div><span class='likelihood-text'>".$likelihood."%</span></div></td>";
                  echo "</tr>";
              }
          } else {
              echo "<tr><td colspan='4'>No students found</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Donut chart for enrollment status
  const enrollmentCtx = document.getElementById('enrollmentChart').getContext('2d');
  const enrollmentChart = new Chart(enrollmentCtx, {
    type: 'doughnut',
    data: {
      labels: ['Enrolled', 'Not Enrolled'],
      datasets: [{
        data: [<?php echo $enrolled; ?>, <?php echo $not_enrolled; ?>],
        backgroundColor: ['#293855', '#F1AC20'],
        borderWidth: 0,
        cutout: '70%'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false
        }
      }
    }
  });

  // Program distribution chart
  const programCtx = document.getElementById('programChart').getContext('2d');
  const programChart = new Chart(programCtx, {
    type: 'pie',
    data: {
      labels: <?php echo json_encode($program_labels); ?>,
      datasets: [{
        data: <?php echo json_encode($program_data); ?>,
        backgroundColor: <?php echo json_encode(array_slice($program_colors, 0, count($program_data))); ?>,
        borderWidth: 2,
        borderColor: '#ffffff',
        hoverBorderWidth: 3,
        hoverBorderColor: '#f3f4f6'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            padding: 20,
            usePointStyle: true,
            font: {
              size: 12,
              weight: '500'
            }
          }
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              const total = context.dataset.data.reduce((a, b) => a + b, 0);
              const percentage = ((context.parsed / total) * 100).toFixed(1);
              return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
            }
          }
        }
      }
    }
  });

  // Likelihood distribution chart
  const likelihoodCtx = document.getElementById('likelihoodChart').getContext('2d');
  const likelihoodChart = new Chart(likelihoodCtx, {
    type: 'bar',
    data: {
      labels: ['High (>80%)', 'Medium (50-80%)', 'Low (<50%)'],
      datasets: [{
        data: [<?php echo $likelihood_counts['high']; ?>, <?php echo $likelihood_counts['medium']; ?>, <?php echo $likelihood_counts['low']; ?>],
        backgroundColor: ['#dcfce7', '#fef3c7', '#fef2f2'],
        borderColor: ['#86efac', '#fcd34d', '#fca5a5'],
        borderWidth: 2,
        borderRadius: 8,
        borderSkipped: false
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false
        },
        tooltip: {
          backgroundColor: '#1f2937',
          titleColor: '#ffffff',
          bodyColor: '#ffffff',
          borderColor: '#374151',
          borderWidth: 1,
          callbacks: {
            title: function(context) {
              return 'Enrollment Likelihood';
            },
            label: function(context) {
              return context.label + ': ' + context.parsed.y + ' students';
            }
          }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          grid: {
            color: '#f3f4f6',
            drawBorder: false
          },
          ticks: {
            color: '#6b7280',
            font: {
              size: 12
            }
          }
        },
        x: {
          grid: {
            display: false
          },
          ticks: {
            color: '#6b7280',
            font: {
              size: 11
            }
          }
        }
      }
    }
  });

  // Update likelihood summary values
  document.getElementById('highLikelihood').textContent = <?php echo $likelihood_counts['high']; ?>;
  document.getElementById('mediumLikelihood').textContent = <?php echo $likelihood_counts['medium']; ?>;
  document.getElementById('lowLikelihood').textContent = <?php echo $likelihood_counts['low']; ?>;

  // Store original data for filtering
  const originalData = {
    enrollment: {
      enrolled: <?php echo $enrolled; ?>,
      notEnrolled: <?php echo $not_enrolled; ?>,
      total: <?php echo $total_students; ?>
    },
    programs: {
      labels: <?php echo json_encode($program_labels); ?>,
      data: <?php echo json_encode($program_data); ?>,
      colors: <?php echo json_encode(array_slice($program_colors, 0, count($program_data))); ?>
    },
    likelihood: {
      high: <?php echo $likelihood_counts['high']; ?>,
      medium: <?php echo $likelihood_counts['medium']; ?>,
      low: <?php echo $likelihood_counts['low']; ?>
    },
    enrollmentByProgram: <?php echo json_encode($enrollment_by_program); ?>,
    enrollmentBySchool: <?php echo json_encode($enrollment_by_school); ?>,
    programGroups: <?php echo json_encode($program_groups); ?>,
    studentData: <?php echo json_encode($student_data); ?>
  };

  // Filter functions
  function filterEnrollmentChart() {
    const filter = document.getElementById('enrollmentFilter').value;
    let enrolled, notEnrolled, total;
    
    if (filter === 'all') {
      enrolled = originalData.enrollment.enrolled;
      notEnrolled = originalData.enrollment.notEnrolled;
      total = originalData.enrollment.total;
    } else {
      // For specific status, show only that status
      if (filter === 'Enrolled') {
        enrolled = originalData.enrollment.enrolled;
        notEnrolled = 0;
        total = enrolled;
      } else {
        enrolled = 0;
        notEnrolled = originalData.enrollment.notEnrolled;
        total = notEnrolled;
      }
    }
    
    enrollmentChart.data.datasets[0].data = [enrolled, notEnrolled];
    enrollmentChart.update();
    
    document.getElementById('enrollmentTotal').textContent = total;
    document.querySelector('#enrollmentLegend .legend-item:first-child .legend-text').textContent = `Enrolled (${enrolled})`;
    document.querySelector('#enrollmentLegend .legend-item:last-child .legend-text').textContent = `Not Enrolled (${notEnrolled})`;
  }

  function filterProgramChart() {
    const filter = document.getElementById('programChartFilter').value;
    
    if (filter === 'all') {
      programChart.data.labels = originalData.programs.labels;
      programChart.data.datasets[0].data = originalData.programs.data;
      programChart.data.datasets[0].backgroundColor = originalData.programs.colors;
    } else {
      // Show programs grouped by school
      const schoolPrograms = originalData.programGroups[filter];
      const schoolLabels = [];
      const schoolData = [];
      const schoolColors = [];
      
      schoolPrograms.forEach(program => {
        const programIndex = originalData.programs.labels.indexOf(program);
        if (programIndex !== -1) {
          schoolLabels.push(program);
          schoolData.push(originalData.programs.data[programIndex]);
          schoolColors.push(originalData.programs.colors[programIndex]);
        }
      });
      
      programChart.data.labels = schoolLabels;
      programChart.data.datasets[0].data = schoolData;
      programChart.data.datasets[0].backgroundColor = schoolColors;
    }
    
    programChart.update();
  }

  function filterLikelihoodChart() {
    const filter = document.getElementById('likelihoodFilter').value;
    let high, medium, low;
    
    if (filter === 'all') {
      high = originalData.likelihood.high;
      medium = originalData.likelihood.medium;
      low = originalData.likelihood.low;
    } else {
      high = filter === 'high' ? originalData.likelihood.high : 0;
      medium = filter === 'medium' ? originalData.likelihood.medium : 0;
      low = filter === 'low' ? originalData.likelihood.low : 0;
    }
    
    likelihoodChart.data.datasets[0].data = [high, medium, low];
    likelihoodChart.update();
    
    document.getElementById('highLikelihood').textContent = high;
    document.getElementById('mediumLikelihood').textContent = medium;
    document.getElementById('lowLikelihood').textContent = low;
  }

  // Add event listeners for filters
  document.getElementById('enrollmentFilter').addEventListener('change', filterEnrollmentChart);
  document.getElementById('programChartFilter').addEventListener('change', filterProgramChart);
  document.getElementById('likelihoodFilter').addEventListener('change', filterLikelihoodChart);

  // Filter elements
  const statusFilter = document.getElementById('statusFilter');
  const programFilter = document.getElementById('programFilter');

  function filterStudents() {
    const statusVal = statusFilter.value;
    const programVal = programFilter.value;

    document.querySelectorAll('.modern-table tbody tr').forEach(row => {
      const status = row.dataset.status;
      const program = row.dataset.program;

      const statusMatch = statusVal === 'all' || status === statusVal;
      const programMatch = programVal === 'all' || program === programVal;

      row.style.display = (statusMatch && programMatch) ? '' : 'none';
    });
  }

  statusFilter.addEventListener('change', filterStudents);
  programFilter.addEventListener('change', filterStudents);

  // Initialize with default filter
  filterStudents();

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

  // Table sorting functionality
  let currentSortColumn = null;
  let currentSortDirection = 'asc';

  document.querySelectorAll('.sortable').forEach(header => {
    header.addEventListener('click', function() {
      const columnIndex = Array.from(this.parentNode.children).indexOf(this);
      const sortType = this.dataset.sort;
      const indicator = this.querySelector('.sort-indicator');

      // Reset all other indicators
      document.querySelectorAll('.sort-indicator').forEach(ind => {
        ind.className = 'sort-indicator inactive';
      });

      // Determine sort direction
      if (currentSortColumn === columnIndex) {
        currentSortDirection = currentSortDirection === 'asc' ? 'desc' : 'asc';
      } else {
        currentSortDirection = 'asc';
      }

      // Update current column indicator
      indicator.className = `sort-indicator active ${currentSortDirection}`;
      currentSortColumn = columnIndex;

      // Sort the table
      sortTable(columnIndex, sortType, currentSortDirection);
    });
  });

  function sortTable(columnIndex, sortType, direction) {
    const tbody = document.querySelector('.modern-table tbody');
    const rows = Array.from(tbody.querySelectorAll('tr:not([style*="display: none"])'));

    rows.sort((a, b) => {
      const aCell = a.cells[columnIndex];
      const bCell = b.cells[columnIndex];
      
      let aValue, bValue;

      if (sortType === 'number') {
        // For likelihood column
        aValue = parseFloat(aCell.querySelector('.likelihood-text').textContent.replace('%', '')) || 0;
        bValue = parseFloat(bCell.querySelector('.likelihood-text').textContent.replace('%', '')) || 0;
      } else {
        // For text columns
        aValue = aCell.textContent.trim().toLowerCase();
        bValue = bCell.textContent.trim().toLowerCase();
      }

      if (direction === 'asc') {
        return aValue > bValue ? 1 : aValue < bValue ? -1 : 0;
      } else {
        return aValue < bValue ? 1 : aValue > bValue ? -1 : 0;
      }
    });

    // Clear tbody and append sorted rows
    tbody.innerHTML = '';
    rows.forEach(row => {
      tbody.appendChild(row);
    });
  }
</script>
</body>
</html>
