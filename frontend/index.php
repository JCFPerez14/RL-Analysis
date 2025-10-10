<?php
session_start();
include 'connections.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>NU Lipa - Welcome</title>
  <link rel="stylesheet" href="style.css">
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
    
    /* Hero Section */
    .hero {
      background: url('images/bg.jpg') no-repeat center center fixed;
      background-size: cover;
      position: relative;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .hero::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(41, 56, 85, 0.8);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
    }
    
    .hero-content {
      position: relative;
      z-index: 10;
      text-align: center;
      color: white;
      max-width: 800px;
      padding: 2rem;
    }
    
    .hero-title {
      font-size: 3.5rem;
      font-weight: 700;
      margin-bottom: 1rem;
      background: linear-gradient(135deg, #F1AC20, #ffd700);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    
    .hero-subtitle {
      font-size: 1.25rem;
      margin-bottom: 2rem;
      opacity: 0.9;
      font-weight: 400;
    }
    
    .hero-buttons {
      display: flex;
      gap: 1rem;
      justify-content: center;
      flex-wrap: wrap;
    }
    
    .btn-hero {
      padding: 1rem 2rem;
      border-radius: 12px;
      font-weight: 600;
      font-size: 1.125rem;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      transition: all 0.3s ease;
      border: none;
      cursor: pointer;
    }
    
    .btn-register {
      background: linear-gradient(135deg, #F1AC20, #ffd700);
      color: #293855;
    }
    
    .btn-register:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(241, 172, 32, 0.4);
      color: #293855;
    }
    
    .btn-login {
      background: transparent;
      color: white;
      border: 2px solid white;
    }
    
    .btn-login:hover {
      background: white;
      color: #293855;
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(255, 255, 255, 0.2);
    }
    
    .btn-dashboard {
      background: linear-gradient(135deg, #10b981, #059669);
      color: white;
    }
    
    .btn-dashboard:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
      color: white;
    }
    
    .btn-icon {
      width: 20px;
      height: 20px;
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
      
      .hero-title {
        font-size: 2.5rem;
      }
      
      .hero-subtitle {
        font-size: 1rem;
      }
      
      .hero-buttons {
        flex-direction: column;
        align-items: center;
      }
      
      .btn-hero {
        width: 100%;
        max-width: 300px;
        justify-content: center;
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
  
  
  <div class="header-actions">
    <?php if (isset($_SESSION['user'])): ?>
      <div class="user-profile-dropdown">
        <div class="user-profile" onclick="toggleDropdown()">A</div>
        <div class="dropdown-menu" id="profileDropdown">
          <div class="dropdown-header"><?= htmlspecialchars($_SESSION['user']['firstname']); ?></div>
          <div class="dropdown-divider"></div>
          <a href="logout.php" class="dropdown-item">
            <svg class="dropdown-icon" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd"/>
            </svg>
            Logout
          </a>
        </div>
      </div>
    <?php else: ?>
      <a href="login.php" class="nav-item">
        <svg class="nav-icon" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M3 3a1 1 0 011 1v12a1 1 0 11-2 0V4a1 1 0 011-1zm7.707 3.293a1 1 0 010 1.414L9.414 9H17a1 1 0 110 2H9.414l1.293 1.293a1 1 0 01-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0z" clip-rule="evenodd"/>
        </svg>
        Login
      </a>
    <?php endif; ?>
  </div>
</header>

<!-- Hero Section -->
<section class="hero">
  <div class="hero-content">
    <h1 class="hero-title">Welcome to NU Lipa</h1>
    <p class="hero-subtitle">Your gateway to student services, enrollment tracking, and analytics.</p>
    
    <div class="hero-buttons">
      <?php if (!isset($_SESSION['user'])): ?>
        <a href="register.php" class="btn-hero btn-register">
          <svg class="btn-icon" fill="currentColor" viewBox="0 0 20 20">
            <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"/>
          </svg>
          Register
        </a>
        <a href="login.php" class="btn-hero btn-login">
          <svg class="btn-icon" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M3 3a1 1 0 011 1v12a1 1 0 11-2 0V4a1 1 0 011-1zm7.707 3.293a1 1 0 010 1.414L9.414 9H17a1 1 0 110 2H9.414l1.293 1.293a1 1 0 01-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0z" clip-rule="evenodd"/>
          </svg>
          Login
        </a>
      <?php else: ?>
        <a href="https://onlineapp.nu-lipa.edu.ph/quest/home.php" class="btn-hero btn-register" target="_blank">
          <svg class="btn-icon" fill="currentColor" viewBox="0 0 20 20">
            <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"/>
          </svg>
          Continue Registration
        </a>
        <a href="courses.php" class="btn-hero btn-register">
          <svg class="btn-icon" fill="currentColor" viewBox="0 0 20 20">
            <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"/>
          </svg>
          Courses
        </a>
      <?php endif; ?>
    </div>
  </div>
</section>

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
