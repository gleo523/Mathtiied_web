<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Registration | MathTrack LMS</title>

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
        <p class="auth-card__eyebrow">Student Access</p>
        <h1 class="auth-card__title">Start your learning journey</h1>
        <p class="auth-card__subtitle">
          Already registered? <a href="student-login.php">Log in instead</a>
        </p>
      </div>

      <div class="alert alert--success auth-success" id="register-success">
        Registration successful! Redirecting to the login page&hellip;
      </div>
      <div class="alert alert--error auth-success" id="register-error"></div>

      <form class="auth-form" id="student-register-form" action="../../api/register_student.php" method="post">
        <div class="auth-form__row">
          <div class="form-group">
            <label class="form-label" for="student-first-name">First Name</label>
            <input type="text" id="student-first-name" name="first_name" class="form-input" placeholder="Maria" required>
          </div>
          <div class="form-group">
            <label class="form-label" for="student-last-name">Last Name</label>
            <input type="text" id="student-last-name" name="last_name" class="form-input" placeholder="Santos" required>
          </div>
        </div>

        <div class="auth-form__row">
          <div class="form-group">
            <label class="form-label" for="student-lrn">LRN / Student ID</label>
            <input type="text" id="student-lrn" name="student_id" class="form-input" placeholder="e.g. 136245100123" required>
          </div>
          <div class="form-group">
            <label class="form-label" for="student-section">Section</label>
            <input type="text" id="student-section" name="section" class="form-input" placeholder="e.g. Grade 6 - Diamond" required>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="student-email">Email Address</label>
          <input type="email" id="student-email" name="email" class="form-input" placeholder="you@student.edu.ph" required>
        </div>

        <div class="auth-form__row">
          <div class="form-group">
            <label class="form-label" for="student-password">Password</label>
            <input type="password" id="student-password" name="password" class="form-input" placeholder="Create a password" required>
          </div>
          <div class="form-group">
            <label class="form-label" for="student-confirm-password">Confirm Password</label>
            <input type="password" id="student-confirm-password" name="confirm_password" class="form-input" placeholder="Re-enter password" required>
          </div>
        </div>

        <div class="auth-form__options">
          <label class="auth-form__checkbox">
            <input type="checkbox" name="terms" required>
            I agree to the Terms &amp; Privacy Policy
          </label>
        </div>

        <button type="submit" class="btn btn--primary btn--full" id="student-register-submit">Create Account</button>

      </form>

    </div>
  </main>

  <script>
    document.getElementById("student-register-form").addEventListener("submit", function (e) {
      e.preventDefault();

      var submitBtn = document.getElementById("student-register-submit");
      var successMsg = document.getElementById("register-success");
      var errorMsg = document.getElementById("register-error");

      submitBtn.disabled = true;
      submitBtn.textContent = "Creating account...";
      errorMsg.style.display = "none";

      fetch(this.action, {
        method: "POST",
        body: new FormData(this),
        headers: {"Accept": "application/json"}
      })
        .then(function (response) {
          return response.json().then(function (data) {
            if (!response.ok) { throw new Error(data.error || "Registration failed."); }
            return data;
          });
        })
        .then(function (data) {
          localStorage.setItem("mathtified_student", JSON.stringify({
            user_id: data.user_id,
            username: data.username,
            student_id: data.student_id,
            game_identity: data.game_identity
          }));
          successMsg.style.display = "flex";
          var handoff = new URLSearchParams(window.location.search).get("handoff_id");
          setTimeout(function () {
            window.location.href = "student-login.php" + (handoff ? "?handoff_id=" + encodeURIComponent(handoff) : "");
          }, 1200);
        })
        .catch(function (error) {
          errorMsg.textContent = error.message;
          errorMsg.style.display = "flex";
          submitBtn.disabled = false;
          submitBtn.textContent = "Create Account";
        });
    });
  </script>

</body>
</html>