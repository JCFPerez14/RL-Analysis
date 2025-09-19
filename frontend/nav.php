<?php
include 'connections.php';

// Detect current page file (index.php, enrollee.php, admin.php)
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar navbar-expand-lg bg-navy px-3">
  <a class="navbar-brand fw-bold text-golden" href="index.php">National University - Lipa</a>

  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
    <ul class="navbar-nav align-items-center">

      <!-- Always visible -->
      <li class="nav-item">
        <a class="nav-link fw-bold <?php echo ($current_page == 'index.php') ? 'text-golden' : 'text-white'; ?>" 
           href="index.php">Home</a>
      </li>

      <!-- ✅ Extra menu only for admin -->
      <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
        <li class="nav-item">
          <a class="nav-link fw-bold <?php echo ($current_page == 'enrollee.php') ? 'text-golden' : 'text-white'; ?>" 
             href="enrollee.php">Dashboard</a>
        </li>
        <li class="nav-item">
          <a class="nav-link fw-bold <?php echo ($current_page == 'admin.php') ? 'text-golden' : 'text-white'; ?>" 
             href="admin.php">Admin Panel</a>
        </li>
      <?php endif; ?>

      <!-- User Dropdown -->
      <li class="nav-item dropdown ms-3">
        <a href="#" class="nav-link dropdown-toggle text-white fw-bold" id="userDropdown" data-bs-toggle="dropdown">
          <?php echo isset($_SESSION['user']) ? $_SESSION['user']['firstname'] : "Account"; ?>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <?php if (isset($_SESSION['user'])): ?>
            <li><a class="dropdown-item">Email: <?php echo $_SESSION['user']['email']; ?></a></li>
            <li><a class="dropdown-item">Role: <?php echo ucfirst($_SESSION['user']['role']); ?></a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
          <?php else: ?>
            <li><a class="dropdown-item" href="login.php">Login</a></li>
            <li><a class="dropdown-item" href="register.php">Register</a></li>
          <?php endif; ?>
        </ul>
      </li>
    </ul>
  </div>
</nav>
