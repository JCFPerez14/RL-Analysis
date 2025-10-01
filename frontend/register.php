<?php 
session_start(); 
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
    /* Navbar */
    .bg-navy { background-color: #001f3f; }
    .text-golden { color: #FFD700; }
    .text-navy { color: #001f3f; }
    .border-navy { border-color: #001f3f; }

    /* Hero Section */
    .hero {
      background: url('images/bg.jpg') no-repeat center center fixed;
      background-size: cover;
      min-height: 100vh;
      display: flex;
      align-items: flex-start;
    }
    .form-scroll {
      max-height: 83vh;
      overflow-y: auto;
      padding-right: 10px;
      padding-left: 10px;
    }

    /* Section Titles */
    .section-title {
      color: #001f3f;
      border-bottom: 2px solid #FFD700;
      padding-bottom: 5px;
      margin-bottom: 15px;
      font-weight: bold;
    }

    /* Progress Bar */
    .progress-container {
      position: sticky;
      top: 0;
      z-index: 1000;
      background: #fff;
      padding: 10px 0;
    }
    .progress-bar {
      height: 20px;
      background-color: #FFD700;
      color: #001f3f;
      font-weight: bold;
      text-align: center;
      line-height: 20px;
    }
    .step-section { display: none; }
    .step-section.active { display: block; }
  </style>
</head>
<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg bg-navy px-3 shadow-sm">
  <a class="navbar-brand fw-bold text-golden" href="index.php">National University - Lipa</a>
  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
    <ul class="navbar-nav">
      <li class="nav-item dropdown">
        <a href="#" class="nav-link dropdown-toggle text-golden" id="userDropdown" data-bs-toggle="dropdown">
          <?php echo isset($_SESSION['user']) ? htmlspecialchars($_SESSION['user']['firstname']) : "User"; ?>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <?php if (isset($_SESSION['user'])): ?>
            <li><a class="dropdown-item">Email: <?php echo htmlspecialchars($_SESSION['user']['email']); ?></a></li>
            <li><a class="dropdown-item">Program: <?php echo htmlspecialchars($_SESSION['user']['program']); ?></a></li>
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
<section class="hero py-5">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-10 col-md-12">
        <div class="card shadow-lg rounded-3 border-0">
          <div class="progress-container px-4">
            <div class="progress">
              <div id="formProgress" class="progress-bar" style="width: 33%;">Step 1 of 3</div>
            </div>
          </div>
          <div class="card-body p-4 form-scroll">
            <h2 class="text-center text-navy fw-bold mb-4">Registration Form</h2>

            <!-- Registration Form -->
            <form id="multiStepForm" action="process_register.php" method="POST" enctype="multipart/form-data">
              <div class="row justify-content-center">
                <div class="col-md-8">

                  <!-- STEP 1: Basic Information -->
                  <div class="step-section active" id="step-1">
                    <h5 class="section-title">Basic Information</h5>
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
                    <div class="d-flex justify-content-end">
                      <button type="button" class="btn btn-primary" onclick="nextStep(1)">Next</button>
                    </div>
                  </div>

                  <!-- STEP 2: Academic Information -->
                  <div class="step-section" id="step-2">
                    <h5 class="section-title mt-4">Academic Information</h5>
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
                    <div class="mb-3">
                      <label class="form-label text-navy">Third Choice of Program</label>
                      <select name="third_program" class="form-select border-navy">
                        <option value="">-- Select Third Choice Program (Optional) --</option>
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
                    <div class="d-flex justify-content-between">
                      <button type="button" class="btn btn-secondary" onclick="prevStep(2)">Back</button>
                      <button type="button" class="btn btn-primary" onclick="nextStep(2)">Next</button>
                    </div>
                  </div>

                  <!-- STEP 3: Additional Information -->
                  <div class="step-section" id="step-3">
                    <h5 class="section-title mt-4">Additional Information</h5>
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
                    <div class="d-flex justify-content-between">
                      <button type="button" class="btn btn-secondary" onclick="prevStep(3)">Back</button>
                      <button type="submit" class="btn btn-success fw-bold" id="registerBtn">
                        <span id="btnText">Register & Predict Enrollment</span>
                        <span id="btnSpinner" class="spinner-border spinner-border-sm ms-2" style="display: none;"></span>
                      </button>
                    </div>
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

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  let currentStep = 1;

  function updateProgress(step) {
    const progress = document.getElementById("formProgress");
    const percent = (step / 3) * 100;
    progress.style.width = percent + "%";
    progress.textContent = "Step " + step + " of 3";
  }

  function toggleRequired(step, enable) {
    const inputs = document.querySelectorAll("#step-" + step + " input, #step-" + step + " select, #step-" + step + " textarea");
    inputs.forEach(input => {
      if (enable) {
        if (input.dataset.originalRequired === "true") input.setAttribute("required", "required");
      } else {
        if (input.hasAttribute("required")) {
          input.dataset.originalRequired = "true";
          input.removeAttribute("required");
        }
      }
    });
  }

  function nextStep(step) {
    const currentSection = document.getElementById("step-" + step);
    const inputs = currentSection.querySelectorAll("input, select, textarea");

    // Validate required fields in current section
    for (let input of inputs) {
      if (input.hasAttribute("required") && !input.value) {
        alert("Please fill out all required fields before proceeding.");
        input.focus();
        return;
      }
    }

    // Hide current, show next
    toggleRequired(step, false);
    document.getElementById("step-" + step).classList.remove("active");
    document.getElementById("step-" + (step + 1)).classList.add("active");
    toggleRequired(step + 1, true);

    currentStep = step + 1;
    updateProgress(currentStep);
  }

  function prevStep(step) {
    toggleRequired(step, false);
    document.getElementById("step-" + step).classList.remove("active");
    document.getElementById("step-" + (step - 1)).classList.add("active");
    toggleRequired(step - 1, true);

    currentStep = step - 1;
    updateProgress(currentStep);
  }

  function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function() {
      document.getElementById('preview').src = reader.result;
    }
    reader.readAsDataURL(event.target.files[0]);
  }

  // Init: only step 1 required
  window.onload = () => {
    toggleRequired(1, true);
    toggleRequired(2, false);
    toggleRequired(3, false);
    
    // Add form submit handler
    document.getElementById('multiStepForm').addEventListener('submit', function(e) {
      const registerBtn = document.getElementById('registerBtn');
      const btnText = document.getElementById('btnText');
      const btnSpinner = document.getElementById('btnSpinner');
      
      // Show loading state
      registerBtn.disabled = true;
      btnText.textContent = 'Processing & Predicting...';
      btnSpinner.style.display = 'inline-block';
    });
  }
</script>
</body>
</html>
