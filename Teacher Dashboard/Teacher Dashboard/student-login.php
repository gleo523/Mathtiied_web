<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Login | MathTrack LMS</title>

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
        <h1 class="auth-card__title">Welcome back!</h1>
        <p class="auth-card__subtitle">
          Log in to continue your lessons.
          Are you a teacher? <a href="teacher-login.php">Teacher login</a>
        </p>
      </div>

      <div class="alert alert--success auth-success" id="login-success">
        Login successful. Redirecting&hellip;
      </div>

      <form class="auth-form" id="student-login-form" action="#" method="post">

        <div class="form-group">
          <label class="form-label" for="student-id">Student ID / Email</label>
          <input type="text" id="student-id" name="student_id" class="form-input" placeholder="Enter your Student ID or email" required>
        </div>

        <div class="form-group">
          <label class="form-label" for="student-password">Password</label>
          <input type="password" id="student-password" name="password" class="form-input" placeholder="Enter your password" required>
        </div>

        <div class="auth-form__options">
          <label class="auth-form__checkbox">
            <input type="checkbox" name="remember">
            Remember me
          </label>
          <a href="#" class="auth-form__forgot">Forgot password?</a>
        </div>

        <button type="submit" class="btn btn--primary btn--full" id="student-login-submit">Log In</button>

      </form>

      <p class="auth-form__footer-note">
        Don't have an account yet? <a id="student-register-link" href="student-register.php">Register here</a>
      </p>

    </div>
  </main>

  <script>
    var handoffId = new URLSearchParams(window.location.search).get("handoff_id") || "";
    if (handoffId) {
      document.getElementById("student-register-link").href =
        "student-register.php?handoff_id=" + encodeURIComponent(handoffId);
    }
    document.getElementById("student-login-form").addEventListener("submit", async function (e) {
      e.preventDefault();

      var submitBtn = document.getElementById("student-login-submit");
      var successMsg = document.getElementById("login-success");

      submitBtn.disabled = true;
      submitBtn.textContent = "Logging in...";
      successMsg.style.display = "none";
      try {
        const response = await fetch("../../api/login.php", {
          method: "POST",
          body: new FormData(e.currentTarget),
          credentials: "same-origin",
          headers: {"Accept": "application/json"}
        });
        const data = await response.json();
        if (!response.ok || data.role !== "student") {
          throw new Error(data.error || "A student account is required.");
        }
        localStorage.setItem("mathtified_student", JSON.stringify({
          user_id: data.user_id,
          username: data.username,
          student_id: data.student_id || data.username,
          game_identity: data.game_identity
        }));
        if (handoffId) {
          const handoffResponse = await fetch("../../api/authenticate_game_handoff.php", {
            method: "POST",
            credentials: "same-origin",
            headers: {
              "Content-Type": "application/json",
              "Accept": "application/json"
            },
            body: JSON.stringify({handoff_id: handoffId})
          });
          const handoffData = await handoffResponse.json();
          if (!handoffResponse.ok) {
            throw new Error(handoffData.error || "Could not connect the game.");
          }
        }
        successMsg.textContent = "Login successful.";
        successMsg.style.display = "flex";
        setTimeout(function () { window.location.href = "index.php"; }, 900);
      } catch (error) {
        successMsg.textContent = error.message;
        successMsg.style.display = "flex";
        submitBtn.disabled = false;
        submitBtn.textContent = "Log In";
      }
    });
  </script>

</body>
</html>