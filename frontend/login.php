<?php
session_start();
include 'connections.php';

// Redirect logged-in users
if (isset($_SESSION['user'])) {
    if ($_SESSION['user']['role'] === 'admin') {
        header("Location: admin.php");
    } else {
        header("Location: index.php");
    }
    exit();
}

// Capture error message if any (from URL query string)
$error = '';
if (isset($_GET['error'])) {
    $error = htmlspecialchars($_GET['error']);
}

// Capture success message if any (from URL query string)
$success = '';
if (isset($_GET['success'])) {
    $success = htmlspecialchars($_GET['success']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Login - NU Lipa</title>
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
      min-height: 100vh;
      display: flex;
      flex-direction: column;
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

    /* Main Content */
    .main-content {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem;
    }

    /* Login Card */
    .login-container {
      width: 100%;
      max-width: 400px;
    }
    
    .login-card {
      background: #ffffff;
      border-radius: 12px;
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
      border: 1px solid #e5e7eb;
      padding: 2.5rem;
    }
    
    .login-header {
      text-align: center;
      margin-bottom: 2rem;
    }
    
    .login-title {
      font-size: 1.875rem;
      font-weight: 700;
      color: #1f2937;
      margin-bottom: 0.5rem;
    }
    
    .login-subtitle {
      font-size: 1rem;
      color: #6b7280;
      font-weight: 400;
    }

    /* Form Styles */
    .form-group {
      margin-bottom: 1.5rem;
    }
    
    .form-label {
      color: #374151;
      font-weight: 600;
      font-size: 0.875rem;
      margin-bottom: 0.5rem;
      display: block;
    }
    
    .form-control {
      border: 2px solid #e5e7eb;
      border-radius: 8px;
      padding: 0.875rem 1rem;
      font-size: 0.875rem;
      transition: all 0.2s ease;
      background: #ffffff;
      width: 100%;
      box-sizing: border-box;
    }
    
    .form-control:focus {
      border-color: #293855;
      box-shadow: 0 0 0 3px rgba(41, 56, 85, 0.1);
      outline: none;
    }
    
    .form-control:hover {
      border-color: #d1d5db;
    }
    
    .form-control::placeholder {
      color: #9ca3af;
    }

    /* Form Options */
    .form-options {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.5rem;
    }
    
    .remember-me {
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    
    .remember-me input[type="checkbox"] {
      width: 16px;
      height: 16px;
      accent-color: #293855;
    }
    
    .remember-me label {
      font-size: 0.875rem;
      color: #6b7280;
      cursor: pointer;
    }
    
    .forgot-password {
      color: #293855;
      text-decoration: none;
      font-size: 0.875rem;
      font-weight: 500;
      transition: color 0.2s ease;
    }
    
    .forgot-password:hover {
      color: #4165D5;
    }

    /* Login Button */
    .login-button {
      background: linear-gradient(135deg, #293855, #4165D5);
      color: white;
      border: none;
      border-radius: 8px;
      padding: 0.875rem 1.5rem;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s ease;
      width: 100%;
      margin-bottom: 1.5rem;
    }
    
    .login-button:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(41, 56, 85, 0.3);
    }
    
    .login-button:disabled {
      opacity: 0.7;
      cursor: not-allowed;
      transform: none;
    }

    /* Registration Link */
    .register-link {
      text-align: center;
      color: #6b7280;
      font-size: 0.875rem;
    }
    
    .register-link a {
      color: #293855;
      text-decoration: none;
      font-weight: 600;
      transition: color 0.2s ease;
    }
    
    .register-link a:hover {
      color: #4165D5;
    }

    /* Alert Styles */
    .alert {
      padding: 0.875rem 1rem;
      border-radius: 8px;
      margin-bottom: 1.5rem;
      font-size: 0.875rem;
    }
    
    .alert-danger {
      background: #fef2f2;
      border: 1px solid #fecaca;
      color: #dc2626;
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
      
      .login-card {
        padding: 2rem;
      }
      
      .login-title {
        font-size: 1.5rem;
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
    <a href="login.php" class="btn-login">Login</a>
    <a href="register.php" class="btn-register">Register</a>
  </div>
</header>

<!-- Main Content -->
<main class="main-content">
  <div class="login-container">
    <div class="login-card">
      <div class="login-header">
        <h1 class="login-title">Welcome Back</h1>
        <p class="login-subtitle">Sign in to continue to your dashboard.</p>
      </div>

      <?php if (!empty($error)): ?>
        <div class="alert alert-danger" style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
          <?php echo $error; ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($success) && $success == '1'): ?>
        <div class="alert alert-success" style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
          ✅ Registration successful! You can now log in with your credentials.
        </div>
      <?php endif; ?>

      <form method="POST" action="process_login.php">
        <div class="form-group">
          <label class="form-label">Email address</label>
          <input type="email" name="email" class="form-control" placeholder="Enter your email" required />
        </div>

        <div class="form-group">
          <label class="form-label">Password</label>
          <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required />
        </div>

        <div class="form-options">
          <div class="remember-me">
            <input type="checkbox" id="remember" name="remember">
            <label for="remember">Remember me</label>
          </div>
          <a href="#" class="forgot-password">Forgot your password?</a>
        </div>

        <button type="submit" class="login-button">Login</button>

        <div class="register-link">
          Don't have an account? <a href="register.php">Register here</a>
        </div>
      </form>
    </div>
  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Form submission handler
  document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const loginButton = document.querySelector('.login-button');
    
    form.addEventListener('submit', function(e) {
      loginButton.disabled = true;
      loginButton.textContent = 'Logging in...';
    });
  });
</script>

</body>
</html>
