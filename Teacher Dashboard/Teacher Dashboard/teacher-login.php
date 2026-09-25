<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Teacher Login | MathTrack LMS</title>

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
        <img src="images/logo_st_mary (2).png" alt="Partner School Logo" class="auth-card__logo">
      </div>

      <div class="auth-card__header">
        <p class="auth-card__eyebrow">Teacher Access</p>
        <h1 class="auth-card__title">Welcome back, Teacher</h1>
        <p class="auth-card__subtitle">
          Log in to continue managing your classes.
          Not a teacher? <a href="student-login.php">Student login</a>
        </p>
      </div>

      <div class="alert alert--success auth-success" id="login-success">
        Login successful. Redirecting to your dashboard&hellip;
      </div>

      <form class="auth-form" id="teacher-login-form" action="#" method="post">

        <div class="form-group">
          <label class="form-label" for="teacher-email">Email Address</label>
          <input type="email" id="teacher-email" name="email" class="form-input" placeholder="you@school.edu.ph" required>
        </div>

        <div class="form-group">
          <label class="form-label" for="teacher-password">Password</label>
          <input type="password" id="teacher-password" name="password" class="form-input" placeholder="Enter your password" required>
        </div>

        <div class="auth-form__options">
          <label class="auth-form__checkbox">
            <input type="checkbox" name="remember">
            Remember me
          </label>
          <a href="#" class="auth-form__forgot">Forgot password?</a>
        </div>

        <button type="submit" class="btn btn--primary btn--full" id="teacher-login-submit">Log In</button>

      </form>

      <p class="auth-form__footer-note">
        Don't have an account yet? <a href="teacher-register.php">Register here</a>
      </p>

    </div>
  </main>

  <script>
    
    document.getElementById("teacher-login-form").addEventListener("submit", async function (e) {
      e.preventDefault();

      var submitBtn = document.getElementById("teacher-login-submit");
      var successMsg = document.getElementById("login-success");

      submitBtn.disabled = true;
      submitBtn.textContent = "Logging in...";
      const response = await fetch("../../api/login.php", {
        method: "POST",
        body: new FormData(e.currentTarget),
        headers: { "Accept": "application/json" }
      });
      const data = await response.json();
      if (!response.ok || data.role !== "teacher") {
        submitBtn.disabled = false;
        submitBtn.textContent = "Log In";
        successMsg.textContent = data.error || "A teacher account is required.";
        successMsg.style.display = "flex";
        return;
      }
      successMsg.style.display = "flex";
      window.location.href = "../dashboard.php";
    });
  </script>

</body>
</html>