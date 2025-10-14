<?php 
session_start(); 
include 'connections.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register - NU Lipa</title>
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
    
    .btn-login {
      background: linear-gradient(135deg, #293855, #4165D5);
      color: white;
      border: none;
      padding: 0.75rem 1.5rem;
      border-radius: 8px;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.2s ease;
    }
    
    .btn-login:hover {
      transform: translateY(-1px);
      box-shadow: 0 4px 8px rgba(41, 56, 85, 0.3);
      color: white;
    }
    
    .btn-register {
      background: white;
      color: #293855;
      border: 2px solid #293855;
      padding: 0.75rem 1.5rem;
      border-radius: 8px;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.2s ease;
    }
    
    .btn-register:hover {
      background: #293855;
      color: white;
      transform: translateY(-1px);
    }

    /* Registration Form */
    .registration-container {
      padding: 2rem;
      max-width: 900px;
      margin: 0 auto;
    }
    
    .registration-card {
      background: #ffffff;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      border: 1px solid #e5e7eb;
      overflow: hidden;
    }
    
    .form-header {
      padding: 3rem 2rem 2rem;
      text-align: center;
      border-bottom: 1px solid #f3f4f6;
    }
    
    .form-title {
      font-size: 2.5rem;
      font-weight: 700;
      margin-bottom: 0.5rem;
      color: #1f2937;
    }
    
    .form-subtitle {
      font-size: 1.125rem;
      color: #6b7280;
      font-weight: 400;
    }

    /* Profile Picture Section */
    .profile-section {
      display: flex;
      justify-content: center;
      margin-bottom: 2rem;
    }
    
    .profile-picture {
      width: 120px;
      height: 120px;
      border: 3px solid #F1AC20;
      border-radius: 50%;
      background: #f9fafb;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.2s ease;
      position: relative;
      overflow: hidden;
    }
    
    .profile-picture:hover {
      border-color: #293855;
      transform: scale(1.05);
    }
    
    .profile-placeholder {
      width: 60px;
      height: 60px;
      background: #e5e7eb;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
    }
    
    .camera-icon {
      width: 24px;
      height: 24px;
      color: #293855;
      position: absolute;
      bottom: -8px;
      right: -8px;
      background: white;
      border-radius: 50%;
      padding: 4px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .profile-preview {
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 50%;
    }

    /* Form Content */
    .form-content {
      padding: 0 2rem 3rem;
    }
    
    .form-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1.5rem;
      margin-bottom: 1.5rem;
    }
    
    .form-grid.full-width {
      grid-template-columns: 1fr;
    }
    
    .form-group {
      display: flex;
      flex-direction: column;
    }
    
    .form-label {
      color: #374151;
      font-weight: 600;
      font-size: 0.875rem;
      margin-bottom: 0.5rem;
    }
    
    .form-control, .form-select {
      border: 2px solid #e5e7eb;
      border-radius: 8px;
      padding: 0.875rem 1rem;
      font-size: 0.875rem;
      transition: all 0.2s ease;
      background: #f9fafb;
    }
    
    .form-control:focus, .form-select:focus {
      border-color: #293855;
      box-shadow: 0 0 0 3px rgba(41, 56, 85, 0.1);
      outline: none;
      background: #ffffff;
    }
    
    .form-control:hover, .form-select:hover {
      border-color: #d1d5db;
      background: #ffffff;
    }
    
    .form-control::placeholder {
      color: #9ca3af;
    }
    
    .form-select {
      background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
      background-position: right 0.75rem center;
      background-repeat: no-repeat;
      background-size: 1rem;
      padding-right: 2.5rem;
    }
    
    .form-select:disabled {
      background-color: #f3f4f6;
      color: #9ca3af;
      cursor: not-allowed;
      opacity: 0.7;
    }

    /* Register Button */
    .register-button {
      background: linear-gradient(135deg, #293855, #4165D5);
      color: white;
      border: none;
      border-radius: 8px;
      padding: 1rem 3rem;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s ease;
      display: block;
      margin: 2rem auto 0;
      min-width: 200px;
    }
    
    .register-button:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(41, 56, 85, 0.3);
    }
    
    .register-button:disabled {
      opacity: 0.7;
      cursor: not-allowed;
      transform: none;
    }

    /* Hidden File Input */
    .file-input {
      display: none;
    }

    /* Loading States */
    .btn-loading {
      opacity: 0.7;
      cursor: not-allowed;
    }
    
    .spinner-border-sm {
      width: 1rem;
      height: 1rem;
    }

    /* Form Sections */
    .form-section {
      margin-bottom: 3rem;
      padding: 2rem;
      background: #f8fafc;
      border-radius: 12px;
      border: 1px solid #e5e7eb;
    }

    .section-header {
      margin-bottom: 2rem;
      text-align: center;
    }

    .section-title {
      font-size: 1.5rem;
      font-weight: 700;
      color: #1f2937;
      margin-bottom: 0.5rem;
    }

    .section-subtitle {
      color: #6b7280;
      font-size: 1rem;
      margin: 0;
    }

    /* Validation Section */
    .validation-section {
      background: #fef3c7;
      border-color: #f59e0b;
    }

    .validation-summary {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 1rem;
    }

    .validation-item {
      background: #ffffff;
      padding: 1rem;
      border-radius: 8px;
      border: 1px solid #e5e7eb;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .validation-item.complete {
      border-color: #10b981;
      background: #f0fdf4;
    }

    .validation-item.incomplete {
      border-color: #f59e0b;
      background: #fffbeb;
    }

    .validation-item h4 {
      font-size: 0.875rem;
      font-weight: 600;
      color: #374151;
      margin: 0 0 0.5rem 0;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .validation-item .icon {
      width: 16px;
      height: 16px;
    }

    .validation-item.complete .icon {
      color: #10b981;
    }

    .validation-item.incomplete .icon {
      color: #f59e0b;
    }

    .validation-item p {
      font-size: 0.75rem;
      color: #6b7280;
      margin: 0;
    }

    /* Terms Agreement */
    .terms-section {
      background: #f9fafb;
      border-color: #d1d5db;
    }

    .terms-agreement {
      text-align: center;
    }

    .checkbox-group {
      display: flex;
      align-items: flex-start;
      gap: 0.75rem;
      margin-bottom: 1rem;
    }

    .checkbox-group input[type="checkbox"] {
      width: 20px;
      height: 20px;
      margin-top: 0.125rem;
      accent-color: #293855;
    }

    .terms-label {
      font-size: 0.875rem;
      color: #374151;
      line-height: 1.5;
      cursor: pointer;
      text-align: left;
    }

    .terms-link {
      color: #293855;
      text-decoration: none;
      font-weight: 600;
    }

    .terms-link:hover {
      text-decoration: underline;
    }

    .terms-error {
      color: #dc2626;
      font-size: 0.875rem;
      font-weight: 500;
      margin-top: 0.5rem;
      padding: 0.75rem;
      background: #fef2f2;
      border: 1px solid #fecaca;
      border-radius: 8px;
    }

    /* Readonly field styling */
    .form-control[readonly] {
      background-color: #f3f4f6;
      color: #6b7280;
      cursor: not-allowed;
    }

    .form-control[readonly]:focus {
      border-color: #d1d5db;
      box-shadow: none;
    }

    /* Progress Bar Styles */
    .progress-container {
      margin: 2rem 0;
      padding: 0 2rem;
    }

    .progress-bar {
      width: 100%;
      height: 6px;
      background: #e5e7eb;
      border-radius: 3px;
      overflow: hidden;
      margin-bottom: 2rem;
      position: relative;
    }

    .progress-fill {
      height: 100%;
      background: linear-gradient(90deg, #293855, #4165D5);
      border-radius: 3px;
      transition: width 0.5s ease;
      position: relative;
    }

    .progress-fill::after {
      content: '';
      position: absolute;
      top: 0;
      right: 0;
      width: 20px;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3));
      animation: shimmer 2s infinite;
    }

    @keyframes shimmer {
      0% { transform: translateX(-20px); }
      100% { transform: translateX(20px); }
    }

    .progress-steps {
      display: flex;
      justify-content: space-between;
      position: relative;
    }

    .progress-step {
      display: flex;
      flex-direction: column;
      align-items: center;
      position: relative;
      flex: 1;
    }

    .progress-step:not(:last-child)::after {
      content: '';
      position: absolute;
      top: 20px;
      left: 50%;
      right: -50%;
      height: 2px;
      background: #e5e7eb;
      z-index: 1;
    }

    .progress-step.completed:not(:last-child)::after {
      background: #293855;
    }

    .step-number {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: #e5e7eb;
      color: #6b7280;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 0.875rem;
      margin-bottom: 0.5rem;
      position: relative;
      z-index: 2;
      transition: all 0.3s ease;
    }

    .progress-step.active .step-number {
      background: linear-gradient(135deg, #293855, #4165D5);
      color: white;
      box-shadow: 0 4px 8px rgba(41, 56, 85, 0.3);
      transform: scale(1.1);
    }

    .progress-step.completed .step-number {
      background: #10b981;
      color: white;
    }

    .step-label {
      font-size: 0.75rem;
      font-weight: 600;
      color: #6b7280;
      text-align: center;
      max-width: 80px;
      line-height: 1.2;
    }

    .progress-step.active .step-label {
      color: #293855;
    }

    .progress-step.completed .step-label {
      color: #10b981;
    }

    /* Step Navigation */
    .step-navigation {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 2rem;
      padding-top: 1.5rem;
      border-top: 1px solid #e5e7eb;
    }

    .btn-previous, .btn-next, .btn-submit {
      padding: 0.875rem 2rem;
      border-radius: 8px;
      font-weight: 600;
      font-size: 0.875rem;
      cursor: pointer;
      transition: all 0.2s ease;
      border: none;
      min-width: 120px;
    }

    .btn-previous {
      background: #ffffff;
      color: #6b7280;
      border: 2px solid #e5e7eb;
    }

    .btn-previous:hover {
      background: #f9fafb;
      border-color: #d1d5db;
      color: #374151;
    }

    .btn-next {
      background: linear-gradient(135deg, #293855, #4165D5);
      color: white;
    }

    .btn-next:hover {
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(41, 56, 85, 0.3);
    }

    .btn-submit {
      background: linear-gradient(135deg, #059669, #10b981);
      color: white;
      min-width: 150px;
    }

    .btn-submit:hover {
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
    }

    .btn-submit:disabled {
      opacity: 0.7;
      cursor: not-allowed;
      transform: none;
      box-shadow: none;
    }

    /* Step Section Styles */
    .step-section {
      animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
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
      
      .registration-container {
        padding: 1rem;
      }
      
      .form-header {
        padding: 2rem 1.5rem 1.5rem;
      }
      
      .form-title {
        font-size: 2rem;
      }
      
      .form-content {
        padding: 0 1.5rem 2rem;
      }
      
      .form-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
      }
      
      .profile-picture {
        width: 100px;
        height: 100px;
      }
      
      .register-button {
        width: 100%;
        margin-top: 1.5rem;
      }
      
      .form-section {
        padding: 1.5rem;
        margin-bottom: 2rem;
      }
      
      .section-title {
        font-size: 1.25rem;
      }
      
      .validation-summary {
        grid-template-columns: 1fr;
      }
      
      .checkbox-group {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
      }
      
      .terms-label {
        text-align: left;
      }
      
      .progress-container {
        padding: 0 1rem;
        margin: 1.5rem 0;
      }
      
      .step-label {
        font-size: 0.625rem;
        max-width: 60px;
      }
      
      .step-number {
        width: 32px;
        height: 32px;
        font-size: 0.75rem;
      }
      
      .step-navigation {
        flex-direction: column;
        gap: 1rem;
      }
      
      .btn-previous, .btn-next, .btn-submit {
        width: 100%;
        min-width: auto;
      }
    }
  </style>
</head>
<body class="bg-light">

<!-- Header Bar -->
<header class="header-bar">
  <a href="index.php" class="logo">
    <img src="images/National University Lipa.png" alt="NU Lipa" class="logo-image" style="width: 130px; height: 50px;">
  </a>
  

  <div class="header-actions">
    <a href="login.php" class="btn-login">Login</a>
    <a href="register.php" class="btn-register">Register</a>
            </div>
</header>

            <!-- Registration Form -->
<div class="registration-container">
  <div class="registration-card">
    <div class="form-header">
      <h1 class="form-title">Student Registration</h1>
      <p class="form-subtitle">Complete the form below to register as a new student.</p>
      
      <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger" style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
          <?php 
          switch($_GET['error']) {
            case 'email_exists':
              echo 'This email address is already registered. Please use a different email address.';
              break;
            case 'registration_failed':
              echo 'Registration failed. Please try again or contact support if the problem persists.';
              break;
            default:
              echo 'An error occurred during registration. Please try again.';
          }
          ?>
        </div>
      <?php endif; ?>
      
      <!-- Profile Picture Section -->
      <div class="profile-section">
        <div class="profile-picture" onclick="document.getElementById('photo').click()">
          <img id="preview" src="uploads/default.png" alt="Profile Preview" class="profile-preview" style="display: none;">
          <div class="profile-placeholder" id="placeholder">
            <svg class="camera-icon" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
            </svg>
                    </div>
                    </div>
        <input type="file" name="photo" id="photo" class="file-input" accept="image/*" onchange="previewImage(event)">
                    </div>
                    </div>

      <!-- Progress Bar -->
      <div class="progress-container">
        <div class="progress-bar">
          <div class="progress-fill" id="progressFill" style="width: 25%"></div>
        </div>
        <div class="progress-steps">
          <div class="progress-step active" data-step="1">
            <div class="step-number">1</div>
            <div class="step-label">Personal Details</div>
          </div>
          <div class="progress-step" data-step="2">
            <div class="step-number">2</div>
            <div class="step-label">Academic Info</div>
          </div>
          <div class="progress-step" data-step="3">
            <div class="step-number">3</div>
            <div class="step-label">Other Info</div>
          </div>
          <div class="progress-step" data-step="4">
            <div class="step-number">4</div>
            <div class="step-label">Review & Submit</div>
          </div>
        </div>
      </div>
    
    <div class="form-content">
      <form id="registrationForm" action="process_register.php" method="POST" enctype="multipart/form-data">
        
        <!-- SECTION 1: Personal Details -->
        <div class="form-section step-section" id="step1" data-step="1">
          <div class="section-header">
            <h3 class="section-title">Personal Details</h3>
            <p class="section-subtitle">Please provide your personal information</p>
          </div>
          
          <!-- Row 1: First Name & Middle Name -->
          <div class="form-grid">
            <div class="form-group">
              <label class="form-label">First Name</label>
              <input type="text" name="firstname" class="form-control" placeholder="Enter your first name" required>
            </div>
            <div class="form-group">
              <label class="form-label">Middle Name</label>
              <input type="text" name="middlename" class="form-control" placeholder="Enter your middle name">
            </div>
          </div>

          <!-- Row 2: Last Name & Email -->
          <div class="form-grid">
            <div class="form-group">
              <label class="form-label">Last Name</label>
              <input type="text" name="lastname" class="form-control" placeholder="Enter your last name" required>
            </div>
            <div class="form-group">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
            </div>
          </div>

          <!-- Row 3: Password & Confirm Password -->
          <div class="form-grid">
            <div class="form-group">
              <label class="form-label">Password</label>
              <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
              <small class="form-text">Must be at least 8 characters long</small>
            </div>
            <div class="form-group">
              <label class="form-label">Confirm Password</label>
              <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirm your password" required>
              <small class="form-text">Re-enter your password to confirm</small>
            </div>
          </div>

          <!-- Row 4: Date of Birth & Phone Number -->
          <div class="form-grid">
            <div class="form-group">
              <label class="form-label">Date of Birth</label>
              <input type="date" name="birth_date" class="form-control" required>
            </div>
            <div class="form-group">
              <label class="form-label">Phone Number</label>
              <input type="tel" name="mobile" id="mobile" class="form-control" placeholder="09XXXXXXXXX" maxlength="11" pattern="[0-9]{11}" title="Please enter exactly 11 digits" required>
            </div>
          </div>

          <!-- Row 5: Sex & Birthplace -->
          <div class="form-grid">
            <div class="form-group">
              <label class="form-label">Sex</label>
              <select name="sex" class="form-select" required>
                <option value="">Select Sex</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Prefer not to say">Prefer not to say</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Birthplace</label>
              <input type="text" name="birthplace" class="form-control" placeholder="Enter your birthplace" required>
            </div>
          </div>

          <!-- Row 5: Province & City/Municipality -->
          <div class="form-grid">
            <div class="form-group">
              <label class="form-label">Province</label>
              <select name="province" id="province" class="form-select" required onchange="updateCities()">
                <option value="">Loading provinces...</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">City/Municipality</label>
              <select name="city" id="city" class="form-select" required onchange="updateBarangays()" disabled>
                <option value="">Select City/Municipality</option>
              </select>
            </div>
          </div>

          <!-- Row 6: Barangay (Full Width) -->
          <div class="form-grid full-width">
            <div class="form-group">
              <label class="form-label">Barangay</label>
              <select name="barangay" id="barangay" class="form-select" required disabled>
                <option value="">Select Barangay</option>
              </select>
            </div>
          </div>
          
          <!-- Navigation Buttons -->
          <div class="step-navigation">
            <button type="button" class="btn-next" onclick="nextStep()">Next Step</button>
          </div>
        </div>

        <!-- SECTION 2: Academic Information -->
        <div class="form-section step-section" id="step2" data-step="2" style="display: none;">
          <div class="section-header">
            <h3 class="section-title">Academic Information</h3>
            <p class="section-subtitle">Please provide your academic details</p>
          </div>
          
          <!-- Row 1: Applying For & Academic Year -->
          <div class="form-grid">
            <div class="form-group">
              <label class="form-label">Applying For</label>
              <select name="applying_for" class="form-select" required>
                <option value="">Select Status</option>
                <option value="Freshman">Freshman</option>
                <option value="Transferee">Transferee</option>
                <option value="Cross Enrollee">Cross Enrollee</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Academic Year</label>
              <input type="text" name="academic_year" class="form-control" value="2025-2026" readonly>
            </div>
          </div>

          <!-- Row 2: Academic Term & First Choice Program -->
          <div class="form-grid">
            <div class="form-group">
              <label class="form-label">Academic Term</label>
              <select name="academic_term" class="form-select" required>
                <option value="">Select Term</option>
                <option value="1st Term">1st Term</option>
                <option value="2nd Term">2nd Term</option>
                <option value="3rd Term">3rd Term</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">First Choice Program</label>
              <select name="program" class="form-select" required>
                <option value="">Select Program</option>
                <option value="BSCS">BSCS</option>
                <option value="BSIT">BSIT</option>
                <option value="BSCE">BSCE</option>
                <option value="BSArch">BSArch</option>
                <option value="BSMT">BSMT</option>
                <option value="BSN">BSN</option>
                <option value="BSPsy">BSPsy</option>
                <option value="BSTM">BSTM</option>
                <option value="BSA">BSA</option>
                <option value="BSBA - MM">BSBA - MM</option>
                <option value="BSBA - Fin">BSBA - Fin</option>
              </select>
            </div>
          </div>

          <!-- Row 3: Strand & Second Choice Program -->
          <div class="form-grid">
            <div class="form-group">
              <label class="form-label">Previous Strand/Track</label>
              <select name="strand" class="form-select" required>
                <option value="">Select Strand/Track</option>
                <option value="STEM">STEM</option>
                <option value="HUMSS">HUMSS</option>
                <option value="GAS">GAS</option>
                <option value="ALS">ALS</option>
                <option value="Non-K12 Curriculum">Non-K12 Curriculum</option>
                <option value="TVL-Industrial">TVL-Industrial</option>
                <option value="TVL-AFA">TVL-AFA</option>
                <option value="TVL-EIM">TVL-EIM</option>
                <option value="TVL-EPAS">TVL-EPAS</option>
                <option value="TVL-SSP">TVL-SSP</option>
                <option value="TVL-SMAW">TVL-SMAW</option>
                <option value="ARTDES">ARTDES</option>
                <option value="TVL-ICT">TVL-ICT</option>
                <option value="TVL-HE">TVL-HE</option>
                <option value="ABM">ABM</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Second Choice Program</label>
              <select name="second_program" class="form-select">
                <option value="">Select Second Choice (Optional)</option>
                <option value="BSCS">BSCS</option>
                <option value="BSIT">BSIT</option>
                <option value="BSCE">BSCE</option>
                <option value="BSArch">BSArch</option>
                <option value="BSMT">BSMT</option>
                <option value="BSN">BSN</option>
                <option value="BSPsy">BSPsy</option>
                <option value="BSTM">BSTM</option>
                <option value="BSA">BSA</option>
                <option value="BSBA - MM">BSBA - MM</option>
                <option value="BSBA - Fin">BSBA - Fin</option>
              </select>
            </div>
          </div>

          <!-- Row 4: Previous School & School Type -->
          <div class="form-grid">
            <div class="form-group">
              <label class="form-label">Previous School Attended</label>
              <input type="text" name="previous_school" class="form-control" placeholder="Enter previous school name" required>
            </div>
            <div class="form-group">
              <label class="form-label">School Type</label>
              <select name="school_type" class="form-select" required>
                <option value="">Select School Type</option>
                <option value="Public">Public</option>
                <option value="Private">Private</option>
              </select>
            </div>
          </div>
          
          <!-- Navigation Buttons -->
          <div class="step-navigation">
            <button type="button" class="btn-previous" onclick="previousStep()">Previous</button>
            <button type="button" class="btn-next" onclick="nextStep()">Next Step</button>
          </div>
        </div>

        <!-- SECTION 3: Other Information -->
        <div class="form-section step-section" id="step3" data-step="3" style="display: none;">
          <div class="section-header">
            <h3 class="section-title">Other Information</h3>
            <p class="section-subtitle">Please provide additional information</p>
          </div>
          
          <!-- Row 1: Annual Family Income & Parent 1 Occupation -->
          <div class="form-grid">
            <div class="form-group">
              <label class="form-label">Annual Family Income</label>
              <select name="family_income" class="form-select" required>
                <option value="">Select Income Range</option>
                <option value="0-25,000">₱0 - ₱25,000</option>
                <option value="25,000-50,000">₱25,000 - ₱50,000</option>
                <option value="50,000-75,000">₱50,000 - ₱75,000</option>
                <option value="Above 75,000">Above ₱75,000</option>
                <option value="Prefer not to say">Prefer not to say</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Father's Occupation</label>
              <input type="text" name="father_occupation" class="form-control" placeholder="Enter occupation">
            </div>
          </div>

          <!-- Row 2: Mother's Occupation (Full Width) -->
          <div class="form-grid full-width">
            <div class="form-group">
              <label class="form-label">Mother's Occupation</label>
              <input type="text" name="mother_occupation" class="form-control" placeholder="Enter occupation (or N/A)">
            </div>
          </div>
          
          <!-- Navigation Buttons -->
          <div class="step-navigation">
            <button type="button" class="btn-previous" onclick="previousStep()">Previous</button>
            <button type="button" class="btn-next" onclick="nextStep()">Next Step</button>
          </div>
        </div>

        <!-- STEP 4: VALIDATION & TERMS -->
        <div class="form-section step-section validation-section" id="step4" data-step="4" style="display: none;">
          <div class="section-header">
            <h3 class="section-title">Review Your Information</h3>
            <p class="section-subtitle">Please review all the information you have provided</p>
          </div>
          
          <div class="validation-summary" id="validationSummary">
            <!-- Validation items will be populated by JavaScript -->
          </div>

          <!-- Terms Agreement -->
          <div class="terms-agreement">
            <div class="checkbox-group">
              <input type="checkbox" id="termsAgreement" name="terms_agreement" required>
              <label for="termsAgreement" class="terms-label">
                I agree to the <a href="#" class="terms-link">Terms and Conditions</a> and <a href="#" class="terms-link">Privacy Policy</a> of NU Lipa
              </label>
            </div>
            <div class="terms-error" id="termsError" style="display: none;">
              You must agree to the terms and conditions to proceed with registration.
            </div>
          </div>
          
          <!-- Navigation Buttons -->
          <div class="step-navigation">
            <button type="button" class="btn-previous" onclick="previousStep()">Previous</button>
            <button type="submit" class="btn-submit" id="registerBtn">
              <span id="btnText">Register</span>
              <span id="btnSpinner" class="spinner-border spinner-border-sm ms-2" style="display: none;"></span>
            </button>
          </div>
        </div>

        <!-- Hidden fields for compatibility -->
        <input type="hidden" name="nationality" value="Filipino">
        <input type="hidden" name="current_address" value="">
        <input type="hidden" name="province_code" id="province_code" value="">
        <input type="hidden" name="city_code" id="city_code" value="">
        <input type="hidden" name="barangay_code" id="barangay_code" value="">
            </form>
          </div>
        </div>
      </div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function(e) {
        const preview = document.getElementById('preview');
        const placeholder = document.getElementById('placeholder');
        
        preview.src = e.target.result;
        preview.style.display = 'block';
        placeholder.style.display = 'none';
      };
      reader.readAsDataURL(file);
    }
  }

  function toggleDropdown() {
    const dropdown = document.querySelector('.dropdown-menu');
    dropdown.classList.toggle('show');
  }

  // Close dropdown when clicking outside
  document.addEventListener('click', function(event) {
    const dropdown = document.querySelector('.dropdown-menu');
    const userProfile = document.querySelector('.user-profile');
    
    if (!userProfile.contains(event.target)) {
      dropdown.classList.remove('show');
    }
  });

  // PSA API Configuration
  const PSA_API_BASE = 'https://psgc.gitlab.io/api';
  
  // Cache for API data to avoid repeated requests
  let provincesCache = null;
  let citiesCache = {};
  let barangaysCache = {};

  // Initialize provinces dropdown from PSA API
  async function initializeProvinces() {
    const provinceSelect = document.getElementById('province');
    
    try {
      // Show loading state
      provinceSelect.innerHTML = '<option value="">Loading provinces...</option>';
      
      // Check cache first
      if (provincesCache) {
        populateProvinceOptions(provincesCache);
        return;
      }
      
      const response = await fetch(`${PSA_API_BASE}/provinces/`);
      
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      
      const data = await response.json();
      provincesCache = data;
      populateProvinceOptions(data);
      
    } catch (error) {
      console.error('Error loading provinces:', error);
      provinceSelect.innerHTML = '<option value="">Error loading provinces</option>';
    }
  }

  // Populate province options
  function populateProvinceOptions(provinces) {
    const provinceSelect = document.getElementById('province');
    provinceSelect.innerHTML = '<option value="">Select Province</option>';
    
    // Sort provinces alphabetically
    const sortedProvinces = provinces.sort((a, b) => a.name.localeCompare(b.name));
    
    sortedProvinces.forEach(province => {
      const option = document.createElement('option');
      option.value = province.name; // Use name instead of code
      option.textContent = province.name;
      option.setAttribute('data-code', province.code); // Store code as data attribute
      option.setAttribute('data-name', province.name);
      provinceSelect.appendChild(option);
    });
    
    // Add special option for NCR/Metro Manila cities (cities with empty provinceCode)
    const ncrOption = document.createElement('option');
    ncrOption.value = 'National Capital Region (NCR)'; // Use name as value
    ncrOption.textContent = 'National Capital Region (NCR)';
    ncrOption.setAttribute('data-code', ''); // Empty code for NCR
    ncrOption.setAttribute('data-name', 'National Capital Region (NCR)');
    provinceSelect.appendChild(ncrOption);
  }

  // Update cities based on selected province using PSA API
  async function updateCities() {
    console.log('updateCities function called');
    
    const provinceSelect = document.getElementById('province');
    const citySelect = document.getElementById('city');
    const barangaySelect = document.getElementById('barangay');
    
    console.log('Elements found:', {
      province: provinceSelect,
      city: citySelect,
      barangay: barangaySelect
    });
    
    // Clear city and barangay options
    citySelect.innerHTML = '<option value="">Loading cities...</option>';
    barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
    
    // Reset disabled states
    citySelect.disabled = true;
    barangaySelect.disabled = true;
    
    const selectedProvinceName = provinceSelect.value;
    const selectedProvinceCode = provinceSelect.selectedOptions[0]?.getAttribute('data-code');
    console.log('Selected province name:', selectedProvinceName);
    console.log('Selected province code:', selectedProvinceCode);
    
    if (!selectedProvinceName) {
      citySelect.innerHTML = '<option value="">Select City/Municipality</option>';
      return;
    }
    
    try {
      // Check cache first
      if (citiesCache[selectedProvinceCode]) {
        populateCityOptions(citiesCache[selectedProvinceCode]);
        citySelect.disabled = false;
        return;
      }
      
      console.log('Fetching cities from PSA API...');
      const response = await fetch(`${PSA_API_BASE}/cities-municipalities/`);
      
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      
      const allCities = await response.json();
      console.log('All cities received:', allCities.length);
      
      // Filter cities by province code (handle empty provinceCode for NCR cities)
      const provinceCities = allCities.filter(city => 
        city.provinceCode === selectedProvinceCode || 
        (selectedProvinceCode === '' && !city.provinceCode) // Handle NCR cities
      );
      console.log('Filtered cities for province:', provinceCities.length);
      
      // Cache the results
      citiesCache[selectedProvinceCode] = provinceCities;
      
      populateCityOptions(provinceCities);
      citySelect.disabled = false;
      
    } catch (error) {
      console.error('Error loading cities:', error);
      citySelect.innerHTML = '<option value="">Error loading cities</option>';
    }
  }


  // Populate city options
  function populateCityOptions(cities) {
    const citySelect = document.getElementById('city');
    citySelect.innerHTML = '<option value="">Select City/Municipality</option>';
    
    // Sort cities alphabetically
    const sortedCities = cities.sort((a, b) => a.name.localeCompare(b.name));
    
    sortedCities.forEach(city => {
      const option = document.createElement('option');
      option.value = city.name; // Use name instead of code
      option.textContent = city.name;
      option.setAttribute('data-code', city.code); // Store code as data attribute
      option.setAttribute('data-name', city.name);
      citySelect.appendChild(option);
    });
  }

  // Update barangays based on selected city using PSA API
  async function updateBarangays() {
    console.log('updateBarangays function called');
    
    const citySelect = document.getElementById('city');
    const barangaySelect = document.getElementById('barangay');
    
    // Clear barangay options
    barangaySelect.innerHTML = '<option value="">Loading barangays...</option>';
    barangaySelect.disabled = true;
    
    const selectedCityName = citySelect.value;
    const selectedCityCode = citySelect.selectedOptions[0]?.getAttribute('data-code');
    console.log('Selected city name:', selectedCityName);
    console.log('Selected city code:', selectedCityCode);
    
    if (!selectedCityName) {
      barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
      return;
    }
    
    try {
      // Check cache first
      if (barangaysCache[selectedCityCode]) {
        populateBarangayOptions(barangaysCache[selectedCityCode]);
        barangaySelect.disabled = false;
        return;
      }
      
      console.log('Fetching barangays from PSA API...');
      const response = await fetch(`${PSA_API_BASE}/barangays/`);
      
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      
      const allBarangays = await response.json();
      console.log('All barangays received:', allBarangays.length);
      
      // Filter barangays by city code (check both cityCode and municipalityCode)
      const cityBarangays = allBarangays.filter(barangay => 
        barangay.cityCode === selectedCityCode || 
        barangay.municipalityCode === selectedCityCode
      );
      console.log('Filtered barangays for city:', cityBarangays.length);
      
      // Cache the results
      barangaysCache[selectedCityCode] = cityBarangays;
      
      populateBarangayOptions(cityBarangays);
      barangaySelect.disabled = false;
      
    } catch (error) {
      console.error('Error loading barangays:', error);
      barangaySelect.innerHTML = '<option value="">Error loading barangays</option>';
    }
  }

  // Populate barangay options
  function populateBarangayOptions(barangays) {
    const barangaySelect = document.getElementById('barangay');
    barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
    
    // Sort barangays alphabetically
    const sortedBarangays = barangays.sort((a, b) => a.name.localeCompare(b.name));
    
    sortedBarangays.forEach(barangay => {
      const option = document.createElement('option');
      option.value = barangay.name; // Use name instead of code
      option.textContent = barangay.name;
      option.setAttribute('data-code', barangay.code); // Store code as data attribute
      option.setAttribute('data-name', barangay.name);
      barangaySelect.appendChild(option);
    });
  }


  // Phone number validation
  function validatePhoneNumber() {
    const phoneInput = document.getElementById('mobile');
    
    phoneInput.addEventListener('input', function(e) {
      // Remove any non-digit characters
      let value = e.target.value.replace(/[^0-9]/g, '');
      
      // Limit to 11 digits
      if (value.length > 11) {
        value = value.substring(0, 11);
      }
      
      e.target.value = value;
      
      // Add visual feedback for validation
      if (value.length === 11) {
        e.target.style.borderColor = '#10b981'; // Green for valid
      } else if (value.length > 0) {
        e.target.style.borderColor = '#f59e0b'; // Orange for incomplete
      } else {
        e.target.style.borderColor = '#e5e7eb'; // Default
      }
    });
    
    phoneInput.addEventListener('keypress', function(e) {
      // Allow only digits
      if (!/[0-9]/.test(e.key) && !['Backspace', 'Delete', 'Tab', 'Escape', 'Enter', 'Home', 'End', 'ArrowLeft', 'ArrowRight'].includes(e.key)) {
        e.preventDefault();
      }
    });
    
    phoneInput.addEventListener('paste', function(e) {
      // Handle paste events to filter out non-digits
      setTimeout(() => {
        let value = e.target.value.replace(/[^0-9]/g, '');
        if (value.length > 11) {
          value = value.substring(0, 11);
        }
        e.target.value = value;
      }, 0);
    });
  }

  // Step management variables
  let currentStep = 1;
  const totalSteps = 4;

  // Form validation and submission handler
  window.onload = async () => {
    // Initialize provinces dropdown from PSA API
    await initializeProvinces();
    
    // Initialize phone number validation
    validatePhoneNumber();
    
    // Initialize form validation
    initializeFormValidation();
    
    // Initialize step management
    initializeStepManagement();
    
    // Initialize location code tracking
    initializeLocationCodeTracking();
    
    // Form submission handler
    document.getElementById('registrationForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      // Check terms agreement
      const termsCheckbox = document.getElementById('termsAgreement');
      const termsError = document.getElementById('termsError');
      
      if (!termsCheckbox.checked) {
        termsError.style.display = 'block';
        termsCheckbox.focus();
        return;
      } else {
        termsError.style.display = 'none';
      }
      
      // Validate all sections
      const validationResult = validateAllSections();
      if (!validationResult.isValid) {
        alert('Please complete all required fields before submitting.');
        return;
      }
      
      // Validate phone number before submission
      const phoneInput = document.getElementById('mobile');
      if (phoneInput.value.length !== 11) {
        alert('Please enter a valid 11-digit phone number');
        phoneInput.focus();
        return;
      }
      
      // Show loading state and submit
      const registerBtn = document.getElementById('registerBtn');
      const btnText = document.getElementById('btnText');
      const btnSpinner = document.getElementById('btnSpinner');
      
      registerBtn.disabled = true;
      btnText.textContent = 'Processing...';
      btnSpinner.style.display = 'inline-block';
      
      // Submit the form
      this.submit();
    });
  }

  // Initialize form validation
  function initializeFormValidation() {
    // Add event listeners to form fields for real-time validation
    const formFields = document.querySelectorAll('#registrationForm input, #registrationForm select');
    
    formFields.forEach(field => {
      field.addEventListener('blur', updateValidationSummary);
      field.addEventListener('change', updateValidationSummary);
      field.addEventListener('input', updateValidationSummary);
    });
    
    // Initial validation update
    updateValidationSummary();
  }

  // Initialize location code tracking
  function initializeLocationCodeTracking() {
    // Track province selection
    document.getElementById('province').addEventListener('change', function() {
      const selectedOption = this.selectedOptions[0];
      const provinceCode = selectedOption ? selectedOption.getAttribute('data-code') : '';
      document.getElementById('province_code').value = provinceCode;
    });
    
    // Track city selection
    document.getElementById('city').addEventListener('change', function() {
      const selectedOption = this.selectedOptions[0];
      const cityCode = selectedOption ? selectedOption.getAttribute('data-code') : '';
      document.getElementById('city_code').value = cityCode;
    });
    
    // Track barangay selection
    document.getElementById('barangay').addEventListener('change', function() {
      const selectedOption = this.selectedOptions[0];
      const barangayCode = selectedOption ? selectedOption.getAttribute('data-code') : '';
      document.getElementById('barangay_code').value = barangayCode;
    });
  }

  // Update validation summary
  function updateValidationSummary() {
    const validationSummary = document.getElementById('validationSummary');
    const sections = [
      {
        title: 'Personal Details',
        fields: ['firstname', 'lastname', 'email', 'birth_date', 'mobile', 'sex', 'birthplace', 'province', 'city', 'barangay'],
        icon: '👤'
      },
      {
        title: 'Academic Information',
        fields: ['applying_for', 'academic_term', 'program', 'strand', 'previous_school', 'school_type'],
        icon: '🎓'
      },
      {
        title: 'Other Information',
        fields: ['family_income'],
        icon: '📋'
      }
    ];

    let validationHTML = '';
    
    sections.forEach(section => {
      const isComplete = section.fields.every(fieldName => {
        const field = document.querySelector(`[name="${fieldName}"]`);
        if (!field) return true;
        
        if (field.type === 'checkbox') {
          return field.checked;
        }
        
        return field.value.trim() !== '';
      });
      
      const status = isComplete ? 'complete' : 'incomplete';
      const statusIcon = isComplete ? '✅' : '⚠️';
      
      validationHTML += `
        <div class="validation-item ${status}">
          <h4>
            <span class="icon">${section.icon}</span>
            ${section.title}
          </h4>
          <p>${isComplete ? 'All required fields completed' : 'Some required fields are missing'}</p>
        </div>
      `;
    });
    
    validationSummary.innerHTML = validationHTML;
  }

  // Validate all sections
  function validateAllSections() {
    const requiredFields = [
      'firstname', 'lastname', 'email', 'birth_date', 'mobile', 'sex', 'birthplace',
      'province', 'city', 'barangay', 'applying_for', 'academic_term', 'program',
      'strand', 'previous_school', 'school_type', 'family_income'
    ];
    
    const missingFields = [];
    
    requiredFields.forEach(fieldName => {
      const field = document.querySelector(`[name="${fieldName}"]`);
      if (field && field.value.trim() === '') {
        missingFields.push(fieldName);
      }
    });
    
    return {
      isValid: missingFields.length === 0,
      missingFields: missingFields
    };
  }

  // Step Management Functions
  function initializeStepManagement() {
    updateProgressBar();
    updateStepVisibility();
  }

  function nextStep() {
    if (validateCurrentStep()) {
      if (currentStep < totalSteps) {
        currentStep++;
        updateProgressBar();
        updateStepVisibility();
        updateStepStatus();
        updateValidationSummary();
        
        // Scroll to top of form
        document.querySelector('.form-content').scrollIntoView({ 
          behavior: 'smooth', 
          block: 'start' 
        });
      }
    }
  }

  function previousStep() {
    if (currentStep > 1) {
      currentStep--;
      updateProgressBar();
      updateStepVisibility();
      updateStepStatus();
      
      // Scroll to top of form
      document.querySelector('.form-content').scrollIntoView({ 
        behavior: 'smooth', 
        block: 'start' 
      });
    }
  }

  function updateProgressBar() {
    const progressFill = document.getElementById('progressFill');
    const progressPercent = (currentStep / totalSteps) * 100;
    progressFill.style.width = `${progressPercent}%`;
  }

  function updateStepVisibility() {
    // Hide all steps
    document.querySelectorAll('.step-section').forEach(step => {
      step.style.display = 'none';
    });
    
    // Show current step
    const currentStepElement = document.getElementById(`step${currentStep}`);
    if (currentStepElement) {
      currentStepElement.style.display = 'block';
    }
  }

  function updateStepStatus() {
    // Update progress step indicators
    document.querySelectorAll('.progress-step').forEach((step, index) => {
      const stepNumber = index + 1;
      step.classList.remove('active', 'completed');
      
      if (stepNumber < currentStep) {
        step.classList.add('completed');
      } else if (stepNumber === currentStep) {
        step.classList.add('active');
      }
    });
  }

  function validateCurrentStep() {
    const stepFields = {
      1: ['firstname', 'lastname', 'email', 'birth_date', 'mobile', 'sex', 'birthplace', 'province', 'city', 'barangay'],
      2: ['applying_for', 'academic_term', 'program', 'strand', 'previous_school', 'school_type'],
      3: ['family_income']
    };
    
    const currentStepFields = stepFields[currentStep] || [];
    const missingFields = [];
    
    currentStepFields.forEach(fieldName => {
      const field = document.querySelector(`[name="${fieldName}"]`);
      if (field && field.value.trim() === '') {
        missingFields.push(fieldName);
      }
    });
    
    if (missingFields.length > 0) {
      alert(`Please complete all required fields in this step before proceeding.`);
      
      // Focus on first missing field
      const firstMissingField = document.querySelector(`[name="${missingFields[0]}"]`);
      if (firstMissingField) {
        firstMissingField.focus();
      }
      
      return false;
    }
    
    return true;
  }
</script>
</body>
</html>
