<?php
declare(strict_types=1);
require_once __DIR__ . '/../../config/admin_guard.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Dashboard | Mathtified</title>
  <link rel="stylesheet" href="../css/base.css">
  <link rel="stylesheet" href="../css/layout.css">
  <link rel="stylesheet" href="../css/components.css">
  <link rel="stylesheet" href="../css/dashboard.css">
  <link rel="stylesheet" href="../css/responsive.css">
</head>
<body>
  <div class="app-shell">
    <aside class="sidebar">
      <div class="sidebar-brand">
        <img src="../images/logo_st_mary (2).png" alt="Client school logo" class="sidebar-brand__logo">
        <div class="sidebar-brand__text">
          <span class="sidebar-brand__title">MATHTIFIED</span>
          <span class="sidebar-brand__subtitle">Admin Portal</span>
        </div>
      </div>
      <nav class="sidebar-nav">
        <p class="sidebar-nav__label">Main Menu</p>
        <ul class="sidebar-nav__list">
          <li><a class="sidebar-nav__link is-active" href="dashboard.php"><span>Dashboard</span></a></li>
          <li><a class="sidebar-nav__link" href="authentication.php"><span>User Authentication Oversight</span></a></li>
          <li><a class="sidebar-nav__link" href="settings.php"><span>Settings</span></a></li>
          <li><a class="sidebar-nav__link" href="management.php"><span>Management</span></a></li>
        </ul>
        <div class="sidebar-nav__divider"></div>
        <ul class="sidebar-nav__list">
          <li><a class="sidebar-nav__link" href="../../api/logout.php" data-admin-logout><span>Logout</span></a></li>
        </ul>
      </nav>
    </aside>

    <div class="main-content">
      <header class="app-header">
        <div class="app-header__left">
          <button class="app-header__menu-toggle" type="button" aria-label="Toggle navigation menu">
            <svg viewBox="0 0 24 24"><path d="M3 6h18"/><path d="M3 12h18"/><path d="M3 18h18"/></svg>
          </button>
          <h1 class="app-header__title">Dashboard</h1>
        </div>
        <div class="app-header__right">
          <div class="app-header__profile">
            <img src="../images/admin.jpg" alt="Administrator profile photo" class="app-header__avatar">
            <div class="app-header__profile-text">
              <span class="app-header__profile-name"><?= htmlspecialchars(trim(($adminUser['first_name'] ?? '') . ' ' . ($adminUser['last_name'] ?? '')) ?: $adminUser['username'], ENT_QUOTES, 'UTF-8') ?></span>
              <span class="app-header__profile-role">System Administrator</span>
            </div>
          </div>
        </div>
      </header>

      <main class="page-body">
        <div class="page-heading">
          <p class="page-heading__eyebrow">Administration</p>
          <h2 class="page-heading__title">Dashboard</h2>
          <p class="page-heading__desc">Manage teachers, students, authentication records, and DWTI settings.</p>
        </div>

        <section class="dashboard-summary grid grid--3" aria-label="Administrator summary">
          <div class="card dashboard-summary-card" id="total-users">
            <span class="stat-card__value">—</span>
            <span class="stat-card__label">Total Users</span>
            <p class="dashboard-summary-card__desc">Students, teachers, and administrators</p>
          </div>
          <div class="card dashboard-summary-card" id="total-teachers">
            <span class="stat-card__value">—</span>
            <span class="stat-card__label">Teachers</span>
            <p class="dashboard-summary-card__desc">Teacher accounts managed by administrators</p>
          </div>
          <div class="card dashboard-summary-card" id="total-students">
            <span class="stat-card__value">—</span>
            <span class="stat-card__label">Students</span>
            <p class="dashboard-summary-card__desc">Students available for oversight</p>
          </div>
        </section>

        <section class="grid grid--2 dashboard-row" aria-label="Administrator records">
          <div class="card" id="authentication-overview">
            <div class="panel-header">
              <div>
                <h3 class="panel-header__title">Teacher Authentication Logs</h3>
                <p class="panel-header__subtitle">Recent teacher login and logout records</p>
              </div>
              <a class="panel-link" href="authentication.php">View all</a>
            </div>
            <div class="table-wrapper">
              <table class="data-table">
                <thead><tr><th>User</th><th>Event</th><th>Time</th></tr></thead>
                <tbody><tr><td colspan="3">Loading records...</td></tr></tbody>
              </table>
            </div>
          </div>

          <div class="card" id="students-overview">
            <div class="panel-header">
              <div>
                <h3 class="panel-header__title">Students by Teacher</h3>
                <p class="panel-header__subtitle">Current teacher assignments</p>
              </div>
              <div>
                <span class="badge badge-info" id="student-overview-count">0 students</span>
                <a class="panel-link" href="management.php">Manage</a>
              </div>
            </div>
            <div class="table-wrapper">
              <table class="data-table">
                <thead><tr><th>Student</th><th>Teacher</th><th>Section</th></tr></thead>
                <tbody><tr><td colspan="3">Loading records...</td></tr></tbody>
              </table>
            </div>
          </div>
        </section>
      </main>
    </div>
  </div>

  <script>
    function requestJson(url) {
      return fetch(url, {headers: {Accept: "application/json"}}).then(function (response) {
        return response.ok ? response.json() : Promise.reject(new Error("Admin data unavailable."));
      });
    }
    function displayName(record) {
      return [record.first_name, record.last_name].filter(Boolean).join(" ") || record.username || "Unknown";
    }
    Promise.allSettled([
      requestJson("../../api/admin/admin_summary.php"),
      requestJson("../../api/admin/admin_auth_logs.php"),
      requestJson("../../api/admin/admin_overview.php"),
      requestJson("../../api/admin/admin_teachers.php")
    ]).then(function (results) {
      var summary = results[0].status === "fulfilled" ? results[0].value : {};
      var logsData = results[1].status === "fulfilled" ? results[1].value : {};
      var overviewData = results[2].status === "fulfilled" ? results[2].value : {};
      var teachersData = results[3].status === "fulfilled" ? results[3].value : {};
      var logs = Array.isArray(logsData.logs) ? logsData.logs : [];
      var students = Array.isArray(overviewData.students) ? overviewData.students : [];
      var teachers = Array.isArray(teachersData.teachers) ? teachersData.teachers : [];
      document.querySelector("#total-users .stat-card__value").textContent = summary.total_users || 0;
      document.querySelector("#total-teachers .stat-card__value").textContent = summary.total_teachers ?? teachers.length;
      document.querySelector("#total-students .stat-card__value").textContent =
        summary.total_students ?? overviewData.total_students ?? students.length;
      document.getElementById("student-overview-count").textContent =
        (overviewData.total_students ?? students.length) + " students";

      var logBody = document.querySelector("#authentication-overview tbody");
      logBody.innerHTML = logs.length ? logs.slice(0, 5).map(function (log) {
        return "<tr><td>" + displayName(log) + "</td><td>" + log.event_type + "</td><td>" +
          new Date(log.occurred_at).toLocaleString() + "</td></tr>";
      }).join("") : '<tr><td colspan="3">No teacher authentication records.</td></tr>';

      var studentBody = document.querySelector("#students-overview tbody");
      studentBody.innerHTML = students.length ? students.slice(0, 10).map(function (student) {
        return "<tr><td>" + displayName(student) + "</td><td>" +
          (student.teacher_username || "Unassigned") + "</td><td>" +
          (student.section || "—") + "</td></tr>";
      }).join("") : '<tr><td colspan="3">No students have been registered.</td></tr>';
      if (results[1].status === "rejected") {
        logBody.innerHTML = '<tr><td colspan="3">Authentication logs are unavailable.</td></tr>';
      }
      if (results[2].status === "rejected") {
        studentBody.innerHTML = '<tr><td colspan="3">Student overview is unavailable.</td></tr>';
      }
    });
    document.querySelector("[data-admin-logout]").addEventListener("click", function (event) {
      event.preventDefault();
      fetch("../../api/logout.php").finally(function () { window.location.href = "../index.php"; });
    });
  </script>
</body>
</html>
