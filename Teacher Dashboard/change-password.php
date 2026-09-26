<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/auth.php';
if (!current_user() || current_user()['role'] !== 'teacher') {
    header('Location: teacher-login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Change Password | MathTrack LMS</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/layout.css">
  <link rel="stylesheet" href="css/components.css">
  <link rel="stylesheet" href="css/settings.css">
  <link rel="stylesheet" href="css/responsive.css">
</head>
<body>

  <div class="app-shell">

    <!-- ============ SIDEBAR ============ -->
    <aside class="sidebar">
      <div class="sidebar__brand">
        <img src="images/logo_st_mary (2).png" alt="School Logo" class="sidebar__brand-logo">
        <span class="sidebar__brand-name">MathTrack</span>
      </div>

      <nav class="sidebar__nav">
        <span class="sidebar__section-label">Main</span>

        <a href="dashboard.php#dashboard" class="sidebar__link">
          <svg class="sidebar__link-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/></svg>
          <span class="sidebar__link-label">Dashboard</span>
        </a>

        <a href="dashboard.php#students" class="sidebar__link">
          <svg class="sidebar__link-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-7 8-7s8 2.6 8 7"/></svg>
          <span class="sidebar__link-label">Students</span>
        </a>

        <a href="dashboard.php#analytics" class="sidebar__link">
          <svg class="sidebar__link-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M7 13l4-4 3 3 5-6"/></svg>
          <span class="sidebar__link-label">Learning Analytics</span>
        </a>

        <a href="dashboard.php#reviews" class="sidebar__link">
          <svg class="sidebar__link-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
          <span class="sidebar__link-label">Review Modules</span>
        </a>

        <span class="sidebar__section-label">Account</span>

        <a href="settings.php" class="sidebar__link">
          <svg class="sidebar__link-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.6V21a2 2 0 1 1-4 0v-.2a1.7 1.7 0 0 0-1-1.5 1.7 1.7 0 0 0-1.9.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.9 1.7 1.7 0 0 0-1.6-1H3a2 2 0 1 1 0-4h.2a1.7 1.7 0 0 0 1.5-1 1.7 1.7 0 0 0-.3-1.9l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.9.3H9a1.7 1.7 0 0 0 1-1.6V3a2 2 0 1 1 4 0v.2a1.7 1.7 0 0 0 1 1.6 1.7 1.7 0 0 0 1.9-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.9V9a1.7 1.7 0 0 0 1.6 1H21a2 2 0 1 1 0 4h-.2a1.7 1.7 0 0 0-1.6 1z"/></svg>
          <span class="sidebar__link-label">Settings</span>
        </a>

        <a href="change-password.php" class="sidebar__link is-active">
          <svg class="sidebar__link-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
          <span class="sidebar__link-label">Change Password</span>
        </a>
      </nav>

      <div class="sidebar__footer">
        <a href="../api/logout.php" class="sidebar__link" onclick="return confirmLogout();">
          <svg class="sidebar__link-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></svg>
          <span class="sidebar__link-label">Logout</span>
        </a>
      </div>
    </aside>

    <!-- ============ TOPBAR ============ -->
    <header class="topbar">
      <div class="topbar__logos">
        <img src="images/school_logo.png" alt="School Logo" class="topbar__logo">
        <span class="topbar__logo-divider" aria-hidden="true"></span>
        <img src="images/ICS_logo.png" alt="Institute Logo" class="topbar__logo">
        <span class="topbar__logo-divider" aria-hidden="true"></span>
        <img src="images/logo_st_mary (2).png" alt="Partner School Logo" class="topbar__logo">
      </div>

      <div class="topbar__profile">
        <div class="topbar__profile-info">
          <p class="topbar__profile-name"><?= htmlspecialchars(trim(($teacherUser['first_name'] ?? '') . ' ' . ($teacherUser['last_name'] ?? '')) ?: $teacherUser['username'], ENT_QUOTES, 'UTF-8') ?></p>
          <p class="topbar__profile-role">Mathematics Teacher</p>
        </div>
        <img src="images/client.jpg" alt="Teacher Avatar" class="topbar__avatar">
      </div>
    </header>

    <!-- ============ MAIN CONTENT ============ -->
    <main class="main-content">

      <div class="page-header">
        <div>
          <h1 class="page-header__title">Change Password</h1>
          <p class="page-header__subtitle">Update your account password to keep your account secure</p>
        </div>
      </div>

      <div class="grid" style="grid-template-columns: minmax(0, 480px); justify-content: center;">
        <section class="card">
          <div class="card__header">
            <h2 class="card__title">Password Update Form</h2>
          </div>

          <div class="alert alert--info" style="margin-bottom: var(--space-6);">
            Use at least 8 characters, including a number and a symbol, for a stronger password.
          </div>

          <form id="password-form">

            <div class="form-group">
              <label class="form-label" for="current-password">Current Password</label>
              <input type="password" id="current-password" name="current_password" class="form-input" placeholder="Enter your current password" required>
            </div>

            <div class="form-group">
              <label class="form-label" for="new-password">New Password</label>
              <input type="password" id="new-password" name="new_password" class="form-input" placeholder="Enter your new password" required>
              <p class="form-hint">Minimum 8 characters, with a number and a symbol.</p>
            </div>

            <div class="form-group">
              <label class="form-label" for="confirm-new-password">Confirm New Password</label>
              <input type="password" id="confirm-new-password" name="confirm_new_password" class="form-input" placeholder="Re-enter your new password" required>
            </div>

            <div class="settings-section__footer">
              <button type="button" class="btn btn--ghost">Cancel</button>
              <button type="submit" class="btn btn--primary">Update Password</button>
            </div>

          </form>
        </section>
      </div>

    </main>

  </div>

  <script>
    document.getElementById("password-form").addEventListener("submit", async function (event) {
      event.preventDefault();
      const form = event.currentTarget;
      const values = Object.fromEntries(new FormData(form));
      if (values.new_password !== values.confirm_new_password) {
        alert("New passwords do not match.");
        return;
      }
      try {
        const response = await fetch("../api/change_password.php", {method:"POST", headers:{"Content-Type":"application/json",Accept:"application/json"}, body:JSON.stringify(values)});
        const data = await response.json();
        if (!response.ok) throw new Error(data.error || "Password update failed.");
        alert("Password updated successfully.");
        form.reset();
      } catch (error) {
        alert(error.message);
      }
    });
    function confirmLogout() {
      return confirm("Are you sure you want to logout?");
    }
  </script>

</body>
</html>