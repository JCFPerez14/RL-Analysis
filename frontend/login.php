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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Login - National University - Lipa</title>
  <link rel="stylesheet" href="style.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navy-bg px-3">
  <a class="navbar-brand fw-bold text-warning" href="index.php">National University - Lipa</a>
</nav>

<section class="hero">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-5">
        <div class="card shadow-lg p-4 rounded-3">
          <h2 class="text-center text-primary mb-4">Login</h2>

          <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
          <?php endif; ?>

          <form method="POST" action="process_login.php">
            <div class="mb-3">
              <label class="form-label">Email Address</label>
              <input type="email" name="email" class="form-control" required />
            </div>

            <div class="mb-3 position-relative">
              <label class="form-label">Password</label>
              <div class="input-group">
                <input type="password" id="password" name="password" class="form-control" required />
                <button type="button" class="btn btn-outline-secondary" id="togglePassword">Show</button>
              </div>
            </div>

            <div class="d-grid mb-2">
              <button type="submit" class="btn btn-primary btn-lg">Login</button>
            </div>

            <div class="d-grid">
              <a href="register.php" class="btn btn-outline-primary btn-lg">Register</a>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  const togglePassword = document.querySelector("#togglePassword");
  const password = document.querySelector("#password");

  togglePassword.addEventListener("click", function () {
    const type = password.getAttribute("type") === "password" ? "text" : "password";
    password.setAttribute("type", type);
    this.textContent = type === "password" ? "Show" : "Hide";
  });
</script>

</body>
</html>
