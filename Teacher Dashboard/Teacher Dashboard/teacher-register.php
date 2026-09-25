<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Teacher Registration | MathTrack LMS</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/layout.css">
  <link rel="stylesheet" href="css/components.css">
  <link rel="stylesheet" href="css/login.css">
  <link rel="stylesheet" href="css/responsive.css">
</head>
<body>

  <main class="auth-page">
    <div class="auth-card">

      <div class="auth-card__logos">
        <img src="images/school_logo.png" alt="School Logo" class="auth-card__logo">
        <span class="auth-card__logo-divider" aria-hidden="true"></span>
        <img src="images/ICS_logo.png" alt="Institute Logo" class="auth-card__logo">
        <span class="auth-card__logo-divider" aria-hidden="true"></span>
        <img src="images/logo_st_mary (2).png " alt="Partner School Logo" class="auth-card__logo">
      </div>

      <div class="auth-card__header">
        <p class="auth-card__eyebrow">Teacher Access</p>
        <h1 class="auth-card__title">Create your teacher account</h1>
        <p class="auth-card__subtitle">
          Already registered? <a href="teacher-login.php">Log in instead</a>
        </p>
      </div>

      <div class="alert alert--success auth-success" id="register-success">
        Registration successful! Redirecting to the login page&hellip;
      </div>

      <form class="auth-form" id="teacher-register-form" action="#" method="post">

        <div class="auth-form__row">
          <div class="form-group">
            <label class="form-label" for="first-name">First Name</label>
            <input type="text" id="first-name" name="first_name" class="form-input" placeholder="Juan" required>
          </div>
          <div class="form-group">
            <label class="form-label" for="last-name">Last Name</label>
            <input type="text" id="last-name" name="last_name" class="form-input" placeholder="Dela Cruz" required>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="school-name">School Name</label>
          <input type="text" id="school-name" name="school_name" class="form-input" placeholder="e.g. Cabanatuan Central School" required>
        </div>

        <div class="form-group">
          <label class="form-label" for="teacher-email">Email Address</label>
          <input type="email" id="teacher-email" name="email" class="form-input" placeholder="you@school.edu.ph" required>
        </div>

        <div class="auth-form__row">
          <div class="form-group">
            <label class="form-label" for="teacher-password">Password</label>
            <input type="password" id="teacher-password" name="password" class="form-input" placeholder="Create a password" required>
          </div>
          <div class="form-group">
            <label class="form-label" for="teacher-confirm-password">Confirm Password</label>
            <input type="password" id="teacher-confirm-password" name="confirm_password" class="form-input" placeholder="Re-enter password" required>
          </div>
        </div>

        <div class="auth-form__options">
          <label class="auth-form__checkbox">
            <input type="checkbox" name="terms" required>
            I agree to the Terms &amp; Privacy Policy
          </label>
        </div>

        <button type="submit" class="btn btn--primary btn--full" id="teacher-register-submit">Create Account</button>

      </form>

    </div>
  </main>

  <script>
    document.getElementById("teacher-register-form").addEventListener("submit", async function (e) {
      e.preventDefault();

      var submitBtn = document.getElementById("teacher-register-submit");
      var successMsg = document.getElementById("register-success");

      submitBtn.disabled = true;
      submitBtn.textContent = "Creating account...";
      try {
        const response = await fetch("../../api/register_teacher.php", {method:"POST", body:new FormData(e.currentTarget), headers:{Accept:"application/json"}});
        const data = await response.json();
        if (!response.ok) throw new Error(data.error || "Registration failed.");
        successMsg.textContent = "Registration successful. Your username is " + data.username + ". Redirecting...";
        successMsg.style.display = "flex";
        setTimeout(function () { window.location.href = "teacher-login.php"; }, 1200);
      } catch (error) {
        successMsg.textContent = error.message;
        successMsg.style.display = "flex";
        submitBtn.disabled = false;
        submitBtn.textContent = "Create Account";
      }
    });
  </script>

</body>
</html>