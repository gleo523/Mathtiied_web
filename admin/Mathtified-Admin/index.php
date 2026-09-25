<?php
declare(strict_types=1);
require_once __DIR__ . '/../../config/auth.php';
if (isset($_GET['logout'])) {
    start_auth_session();
    $_SESSION = [];
    session_destroy();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login | Mathtified Admin Portal</title>
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/layout.css">
  <link rel="stylesheet" href="css/components.css">
  <link rel="stylesheet" href="css/login.css">
  <link rel="stylesheet" href="css/responsive.css">
</head>
<body>

  <main class="login-page">

    <!-- Left Panel: Branding, Introduction, Background Image -->
    <section class="login-visual" aria-label="Mathtified Admin Portal introduction">
      <div class="login-visual__content">

        <div class="login-visual__brand">
          <span class="login-visual__name">MATHTIFIED</span>
          <span class="login-visual__portal">Admin Portal</span>
        </div>

        <div class="login-visual__message">
          <h1 class="login-visual__title">Smarter Administration. Better Oversight.</h1>
          <p class="login-visual__desc">Manage authentication, monitor system performance, and oversee learning reports through one centralized Mathtified Admin Portal.</p>
        </div>

        <div class="login-visual__footer">
          <div class="login-visual__logos">
            <img src="images/school_logo.png" alt="School logo" class="login-visual__logo">
            <img src="images/ICS_logo.png" alt="Institute logo" class="login-visual__logo">
          </div>
          <p class="login-visual__copyright">&copy; 2026 Mathtified. All Rights Reserved.</p>
        </div>

      </div>
    </section>

    <!-- Right Panel: Administrator Login -->
    <section class="login-form-area" aria-label="Administrator login">
      <div class="login-card">

        <div class="login-brand">
          <img src="images/logo_st_mary (2).png" alt="Client school logo" class="login-brand__logo">
          <span class="login-brand__name">MATHTIFIED</span>
          <span class="login-brand__portal">Admin Portal</span>
        </div>

        <div class="login-heading">
          <h2 class="login-heading__title">Administrator Access</h2>
          <p class="login-heading__desc">Sign in to access and manage the Mathtified administration portal.</p>
        </div>

        <form id="admin-login-form">
          <div class="form-group">
            <label class="form-label" for="email">Email Address</label>
            <input
              class="form-input"
              type="email"
              id="email"
              name="email"
              placeholder="Enter your email address"
              autocomplete="username"
              required
            >
          </div>

          <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <input
              class="form-input"
              type="password"
              id="password"
              name="password"
              placeholder="Enter your password"
              autocomplete="current-password"
              required
            >
          </div>

          <div class="login-options">
            <label class="form-check" for="remember">
              <input type="checkbox" id="remember" name="remember">
              <span>Remember Me</span>
            </label>
            <a class="login-forgot" href="#">Forgot Password?</a>
          </div>

          <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>
        <p id="login-message" class="login-footer" role="alert"></p>

        <p class="login-footer">Authorized administrators only.</p>
      </div>
    </section>

  </main>

  <script>
    document.getElementById("admin-login-form").addEventListener("submit", async function (event) {
      event.preventDefault();
      const message = document.getElementById("login-message");
      const response = await fetch("../../api/login.php", {
        method: "POST",
        body: new FormData(event.currentTarget),
        headers: { "Accept": "application/json" }
      });
      const data = await response.json();
      if (!response.ok || data.role !== "admin") {
        message.textContent = data.error || "An admin account is required.";
        return;
      }
      window.location.href = "pages/dashboard.php";
    });
  </script>
</body>
</html>
