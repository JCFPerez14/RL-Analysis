<?php session_start(); 
include 'connections.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register - National University - Lipa</title>
  <link rel="stylesheet" href="style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    /* Don't force a static body padding here — we'll set it only if the navbar is fixed/sticky */
    .hero { padding-top: 20px; }
    .hero-overlay { margin-top: 10px; }

    /* Ensure preview image fits nicely */
    #preview { max-width: 150px; max-height: 150px; object-fit: cover; }

    /* Safety: make sure navbar is on top if fixed */
    .navbar { z-index: 1040; }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg bg-navy px-3">
  <a class="navbar-brand fw-bold text-golden" href="index.php">National University - Lipa</a>
  <div class="collapse navbar-collapse justify-content-end">
    <ul class="navbar-nav">
      <li class="nav-item dropdown">
        <a href="#" class="nav-link dropdown-toggle text-golden" id="userDropdown" data-bs-toggle="dropdown">
          <?php echo isset($_SESSION['user']) ? $_SESSION['user']['firstname'] : "User"; ?>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <?php if (isset($_SESSION['user'])): ?>
            <li><a class="dropdown-item">Email: <?php echo $_SESSION['user']['email']; ?></a></li>
            <li><a class="dropdown-item">Program: <?php echo $_SESSION['user']['program']; ?></a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
          <?php else: ?>
            <li><a class="dropdown-item" href="login.php">Login</a></li>
          <?php endif; ?>
        </ul>
      </li>
    </ul>
  </div>
</nav>

<!-- Hero Section -->
<section class="hero">
  <div class="hero-overlay container mt-5">
    <div class="row justify-content-center">
      <div class="col-lg-10 col-md-12">
        <div class="card shadow-lg rounded-3 border-0">
          <div class="card-body p-4">
            <h2 class="text-center text-navy fw-bold mb-4">Registration Form</h2>
            
<form action="process_register.php" method="POST" enctype="multipart/form-data">
  <div class="row justify-content-center">
    <div class="col-md-8">

      <!-- Basic Information -->
      <h5 class="text-navy border-bottom pb-2 mb-3">Basic Information</h5>
      <div class="mb-3">
        <label class="form-label text-navy">First Name</label>
        <input type="text" name="firstname" class="form-control border-navy" required>
      </div>
      <div class="mb-3">
        <label class="form-label text-navy">Middle Name</label>
        <input type="text" name="middlename" class="form-control border-navy">
      </div>
      <div class="mb-3">
        <label class="form-label text-navy">Last Name</label>
        <input type="text" name="lastname" class="form-control border-navy" required>
      </div>
      <div class="mb-3">
        <label class="form-label text-navy">Email Address</label>
        <input type="email" name="email" class="form-control border-navy" required>
      </div>
      <div class="mb-3">
        <label class="form-label text-navy">Password</label>
        <input type="password" name="password" class="form-control border-navy" required>
      </div>
      <div class="mb-3">
        <label class="form-label text-navy">Mobile Number</label>
        <input type="text" name="mobile" class="form-control border-navy" required>
      </div>
      <div class="mb-3">
        <label class="form-label text-navy">Nationality</label>
        <input type="text" name="nationality" class="form-control border-navy" required>
      </div>
      <div class="mb-3">
  <label class="form-label text-navy">Sex</label>
  <select name="sex" class="form-select border-navy" required>
    <option value="">Select</option>
    <option value="Male">Male</option>
    <option value="Female">Female</option>
    <option value="Prefer not to say">Prefer not to say</option>
  </select>
</div>


      <!-- Academic Information -->
      <hr class="my-4">
      <h5 class="text-navy border-bottom pb-2 mb-3">Academic Information</h5>
      <div class="mb-3">
        <label class="form-label text-navy">Academic Year</label>
        <input type="text" name="academic_year" class="form-control border-navy" value="2025-2026" required>
      </div>
      <div class="mb-3">
        <label class="form-label text-navy">Academic Term</label>
        <select name="academic_term" class="form-select border-navy" required>
          <option value="1st Semester">1st Semester</option>
          <option value="2nd Semester">2nd Semester</option>
          <option value="Summer">Summer</option>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label text-navy">Applying For</label>
        <select name="applying_for" class="form-select border-navy" required>
          <option value="Freshman">Freshman</option>
          <option value="Transferee">Transferee</option>
          <option value="Cross Enrollee">Cross Enrollee</option>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label text-navy">Course Strand</label>
        <input type="text" name="strand" class="form-control border-navy">
      </div>
      <div class="mb-3">
        <label class="form-label text-navy">Academic Program</label>
        <select name="program" class="form-select border-navy" required>
          <option value="">-- Select Program --</option>
          <option value="BSCS">BSCS</option>
          <option value="BSIT">BSIT</option>
          <option value="BSCE">BSCE</option>
          <option value="BSArch">BSArch</option>
          <option value="BSMT">BSMT</option>
          <option value="BSN">BSN</option>
          <option value="BSPYS">BSPYS</option>
          <option value="BSTM">BSTM</option>
          <option value="BSA - Marketing">BSA - Marketing</option>
          <option value="BSA - Financial Management">BSA - Financial Management</option>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label text-navy">Second Choice of Program</label>
        <select name="second_program" class="form-select border-navy" required>
          <option value="">-- Select Second Choice Program --</option>
          <option value="BSCS">BSCS</option>
          <option value="BSIT">BSIT</option>
          <option value="BSCE">BSCE</option>
          <option value="BSArch">BSArch</option>
          <option value="BSMT">BSMT</option>
          <option value="BSN">BSN</option>
          <option value="BSPYS">BSPYS</option>
          <option value="BSTM">BSTM</option>
          <option value="BSA - Marketing">BSA - Marketing</option>
          <option value="BSA - Financial Management">BSA - Financial Management</option>
        </select>
      </div>

      <!-- Additional Information -->
        <hr class="my-4">
      <h5 class="text-navy border-bottom pb-2 mb-3">Additional Information</h5>
      <div class="mb-3">
        <label class="form-label text-navy">Family Annual Income</label>
        <input type="text" name="family_income" class="form-control border-navy" required>
      </div>
      <div class="mb-3">
        <label class="form-label text-navy">Father's Occupation</label>
        <input type="text" name="father_occupation" class="form-control border-navy">
      </div>
      <div class="mb-3">
        <label class="form-label text-navy">Mother's Occupation</label>
        <input type="text" name="mother_occupation" class="form-control border-navy">
      </div>
      <div class="mb-3">
        <label class="form-label text-navy">Birthplace</label>
        <input type="text" name="birthplace" class="form-control border-navy">
      </div>
      <div class="mb-3">
        <label class="form-label text-navy">City</label>
        <input type="text" name="city" class="form-control border-navy">
      </div>
      <div class="mb-3">
        <label class="form-label text-navy">Province</label>
        <input type="text" name="province" class="form-control border-navy">
      </div>
      <div class="mb-3">
        <label class="form-label text-navy">Currently Residing At</label>
        <input type="text" name="current_address" class="form-control border-navy">
      </div>
      <div class="mb-3 text-center">
        <label class="form-label text-navy">Profile Picture</label>
        <div class="mb-2">
    <img id="preview" src="uploads/default.png" alt="Preview" class="img-thumbnail" style="max-width:150px; max-height:150px;">
  </div>
  <input type="file" name="photo" class="form-control border-navy" accept="image/*" onchange="previewImage(event)">
</div>


      <!-- Submit -->
      <div class="d-grid mt-4">
        <button type="submit" class="btn btn-primary btn-lg fw-bold">Register</button>
      </div>
    </div>
  </div>
</form>


          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Before closing body -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // If navbar is fixed or sticky, add top padding equal to its height so content doesn't sit underneath.
  (function () {
    const nav = document.querySelector('.navbar');
    function adjustBodyPadding() {
      if (!nav) return;
      const pos = window.getComputedStyle(nav).position;
      if (pos === 'fixed' || pos === 'sticky') {
        document.body.style.paddingTop = nav.offsetHeight + 'px';
      } else {
        document.body.style.paddingTop = '';
      }
    }
    window.addEventListener('load', adjustBodyPadding);
    window.addEventListener('resize', adjustBodyPadding);
    // also run shortly after in case fonts/images change sizes
    setTimeout(adjustBodyPadding, 300);
  })();
</script>
<script src="script.js"></script>
</body>
</html>


