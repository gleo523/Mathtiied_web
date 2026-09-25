<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/auth.php';
$teacherUser = current_user();
if (!$teacherUser || $teacherUser['role'] !== 'teacher') {
    header('Location: Teacher%20Dashboard/teacher-login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard | MathTrack LMS</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="Teacher Dashboard/css/base.css">
  <link rel="stylesheet" href="Teacher Dashboard/css/layout.css">
  <link rel="stylesheet" href="Teacher Dashboard/css/components.css">
  <link rel="stylesheet" href="Teacher Dashboard/css/dashboard.css">
  <link rel="stylesheet" href="Teacher Dashboard/css/responsive.css">
</head>
<body>

  <div class="app-shell">

    <!-- ============ SIDEBAR ============ -->
    <aside class="sidebar">
      <div class="sidebar__brand">
        <img src="Teacher Dashboard/images/logo_st_mary (2).png" alt="School Logo" class="sidebar__brand-logo">
        <span class="sidebar__brand-name">MathTrack</span>
      </div>

      <nav class="sidebar__nav">
        <span class="sidebar__section-label">Main</span>

        <a href="#dashboard" class="sidebar__link is-active" data-tab="dashboard">
          <svg class="sidebar__link-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/></svg>
          <span class="sidebar__link-label">Dashboard</span>
        </a>

        <a href="#students" class="sidebar__link" data-tab="students">
          <svg class="sidebar__link-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-7 8-7s8 2.6 8 7"/></svg>
          <span class="sidebar__link-label">Students</span>
        </a>

        <a href="#analytics" class="sidebar__link" data-tab="analytics">
          <svg class="sidebar__link-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M7 13l4-4 3 3 5-6"/></svg>
          <span class="sidebar__link-label">Learning Analytics</span>
        </a>

        <a href="#reviews" class="sidebar__link" data-tab="reviews">
          <svg class="sidebar__link-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
          <span class="sidebar__link-label">Review Modules</span>
        </a>
        <a href="content-editor.php" class="sidebar__link">
          <span class="sidebar__link-label">Content Editor</span>
        </a>
        <a href="wave-history.php" class="sidebar__link">
          <span class="sidebar__link-label">Wave History</span>
        </a>

        <span class="sidebar__section-label">Account</span>

        <a href="settings.php" class="sidebar__link">
          <svg class="sidebar__link-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.6V21a2 2 0 1 1-4 0v-.2a1.7 1.7 0 0 0-1-1.5 1.7 1.7 0 0 0-1.9.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.9 1.7 1.7 0 0 0-1.6-1H3a2 2 0 1 1 0-4h.2a1.7 1.7 0 0 0 1.5-1 1.7 1.7 0 0 0-.3-1.9l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.9.3H9a1.7 1.7 0 0 0 1-1.6V3a2 2 0 1 1 4 0v.2a1.7 1.7 0 0 0 1 1.6 1.7 1.7 0 0 0 1.9-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.9V9a1.7 1.7 0 0 0 1.6 1H21a2 2 0 1 1 0 4h-.2a1.7 1.7 0 0 0-1.6 1z"/></svg>
          <span class="sidebar__link-label">Settings</span>
        </a>

        <a href="change-password.php" class="sidebar__link">
          <svg class="sidebar__link-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
          <span class="sidebar__link-label">Change Password</span>
        </a>
      </nav>

      <div class="sidebar__footer">
        <a href="Teacher Dashboard/index.php" class="sidebar__link" onclick="return confirmLogout();">
          <svg class="sidebar__link-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></svg>
          <span class="sidebar__link-label">Logout</span>
        </a>
      </div>
    </aside>

    <!-- ============ TOPBAR ============ -->
    <header class="topbar">
      <div class="topbar__logos">
        <img src="Teacher Dashboard/images/school_logo.png" alt="School Logo" class="topbar__logo">
        <span class="topbar__logo-divider" aria-hidden="true"></span>
        <img src="Teacher Dashboard/images/ICS_logo.png" alt="Institute Logo" class="topbar__logo">
        <span class="topbar__logo-divider" aria-hidden="true"></span>
        <img src="Teacher Dashboard/images/logo_st_mary (2).png" alt="Partner School Logo" class="topbar__logo">
      </div>

      <div class="topbar__profile">
        <div class="topbar__profile-info">
          <p class="topbar__profile-name"><?= htmlspecialchars(trim(($teacherUser['first_name'] ?? '') . ' ' . ($teacherUser['last_name'] ?? '')) ?: $teacherUser['username'], ENT_QUOTES, 'UTF-8') ?></p>
          <p class="topbar__profile-role">Mathematics Teacher</p>
        </div>
        <img src="Teacher Dashboard/images/client.jpg" alt="Teacher Avatar" class="topbar__avatar">
      </div>
    </header>

    <!-- ============ MAIN CONTENT ============ -->
    <main class="main-content">

      <!-- ============ DASHBOARD PANEL ============ -->
      <div class="dashboard-panel is-active" id="panel-dashboard" data-panel="dashboard">

      <!-- Welcome Section -->
      <section class="welcome-banner">
        <div class="welcome-banner__content">
          <p class="welcome-banner__eyebrow">Good day!</p>
          <h1 class="welcome-banner__title">Welcome back, <?= htmlspecialchars(trim(($teacherUser['first_name'] ?? '') . ' ' . ($teacherUser['last_name'] ?? '')) ?: $teacherUser['username'], ENT_QUOTES, 'UTF-8') ?>!</h1>
          <p class="welcome-banner__subtitle">Review your assigned students, learning analytics, and modules.</p>
        </div>
        <a href="#students" class="btn btn--primary welcome-banner__action" data-tab="students">View Students</a>
        <svg class="welcome-banner__motif" viewBox="0 0 220 220" aria-hidden="true" focusable="false">
          <circle cx="110" cy="110" r="90" fill="none" stroke="#FFFFFF" stroke-width="1.5" stroke-dasharray="4 8"/>
          <circle cx="110" cy="110" r="60" fill="none" stroke="#FFFFFF" stroke-width="1.5" stroke-dasharray="4 8"/>
        </svg>
      </section>

      <!-- Summary Cards -->
      <section class="section">
        <div class="summary-row">

          <div class="summary-card">
            <div class="summary-card__icon">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="7" r="4"/><path d="M2 21c0-3.9 3.1-7 7-7s7 3.1 7 7"/><path d="M16 3.1a4 4 0 0 1 0 7.8"/><path d="M22 21c0-3-2.3-5.5-5.3-6.9"/></svg>
            </div>
            <div>
              <p class="summary-card__value" id="summary-total-students">0</p>
              <p class="summary-card__label">Total Students</p>
              <p class="summary-card__trend">Live assigned-student count</p>
            </div>
          </div>

          <div class="summary-card">
            <div class="summary-card__icon">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M7 13l4-4 3 3 5-6"/></svg>
            </div>
            <div>
              <p class="summary-card__value" id="summary-average-mastery">0%</p>
              <p class="summary-card__label">Average Mastery</p>
              <p class="summary-card__trend">Calculated from received game data</p>
            </div>
          </div>

          <div class="summary-card">
            <div class="summary-card__icon">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v4"/><path d="M12 17h.01"/><path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"/></svg>
            </div>
            <div>
              <p class="summary-card__value" id="summary-needing-attention">0</p>
              <p class="summary-card__label">Weak Topics Flagged</p>
              <p class="summary-card__trend">Based on current mastery records</p>
            </div>
          </div>

          <div class="summary-card">
            <div class="summary-card__icon">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
            </div>
            <div>
              <p class="summary-card__value" id="summary-active-modules">0</p>
              <p class="summary-card__label">Review Modules Generated</p>
              <p class="summary-card__trend">Live module count</p>
            </div>
          </div>

        </div>
      </section>

      </div>
      <!-- ============ END DASHBOARD PANEL ============ -->

      <!-- ============ STUDENTS PANEL ============ -->
      <div class="dashboard-panel" id="panel-students" data-panel="students">

        <div class="page-header">
          <div>
            <h1 class="page-header__title">Students</h1>
            <p class="page-header__subtitle">Progress overview across your assigned sections</p>
          </div>
          <a href="student-details.php" class="btn btn--ghost btn--sm">View Full Student Details</a>
        </div>

        <div class="section">
          <div class="card">
            <div class="card__header">
              <h2 class="card__title">Student Progress Overview</h2>
            </div>

            <div class="progress-overview__list" id="student-progress-list">

            </div>
          </div>
        </div>

      </div>
      <!-- ============ END STUDENTS PANEL ============ -->

      <!-- ============ LEARNING ANALYTICS PANEL ============ -->
      <div class="dashboard-panel" id="panel-analytics" data-panel="analytics">

        <div class="page-header">
          <div>
            <h1 class="page-header__title">Learning Analytics</h1>
            <p class="page-header__subtitle">Diagnostic Weak Topic Identification (DWTI) across your classes</p>
          </div>
        </div>

        <div class="section">
          <div class="card">
            <div class="card__header">
              <h2 class="card__title">DWTI Overview</h2>
            </div>

            <div class="dwti-overview" id="dwti-overview">
              <p class="form-hint">Loading live weak-topic data...</p>
            </div>
            <div class="card__header" style="margin-top:24px"><h2 class="card__title">Recent Activity</h2></div>
            <p id="analytics-summary" class="form-hint">Loading activity data...</p>
            <div id="attempts-over-time" class="dwti-overview"></div>
          </div>
        </div>

      </div>
      <!-- ============ END LEARNING ANALYTICS PANEL ============ -->

      <!-- ============ REVIEW MODULES PANEL ============ -->
      <div class="dashboard-panel" id="panel-reviews" data-panel="reviews">

        <div class="page-header">
          <div>
            <h1 class="page-header__title">Review Modules</h1>
            <p class="page-header__subtitle">Modules generated from flagged weak topics</p>
          </div>
          <a href="review-module.php" class="btn btn--primary btn--sm">Generate New</a>
        </div>

        <div class="section">
          <div class="card">
            <div class="card__header">
              <h2 class="card__title">Review Module Overview</h2>
            </div>

            <div class="review-module-list" id="review-module-list"></div>
          </div>
        </div>
      </div>
      <!-- ============ END REVIEW MODULES PANEL ============ -->

    </main>

  </div>

  <script>
    var validTabs = ["dashboard", "students", "analytics", "reviews"];

    function activateTab(tabName) {
      if (validTabs.indexOf(tabName) === -1) { return; }

      document.querySelectorAll(".dashboard-panel").forEach(function (panel) {
        panel.classList.toggle("is-active", panel.getAttribute("data-panel") === tabName);
      });

      document.querySelectorAll(".sidebar__link[data-tab]").forEach(function (link) {
        link.classList.toggle("is-active", link.getAttribute("data-tab") === tabName);
      });
    }

    document.querySelectorAll("[data-tab]").forEach(function (el) {
      el.addEventListener("click", function (e) {
        e.preventDefault();
        activateTab(this.getAttribute("data-tab"));
        history.replaceState(null, "", "#" + this.getAttribute("data-tab"));
      });
    });

    var initialTab = window.location.hash.replace("#", "");
    if (validTabs.indexOf(initialTab) !== -1) {
      activateTab(initialTab);
    }

    function confirmLogout() {
      return confirm("Are you sure you want to logout?");
    }

    function escapeHtml(value) {
      return String(value).replace(/[&<>"']/g, function (character) {
        return {"&":"&amp;","<":"&lt;",">":"&gt;","\"":"&quot;","'":"&#039;"}[character];
      });
    }

    function renderDashboard(data) {
      var summary = data.summary || {};
      document.getElementById("summary-total-students").textContent = summary.total_students || 0;
      document.getElementById("summary-average-mastery").textContent = (summary.average_mastery || 0) + "%";
      document.getElementById("summary-needing-attention").textContent = summary.students_needing_attention || 0;
      document.getElementById("summary-active-modules").textContent = summary.active_review_modules || 0;

      var list = document.getElementById("student-progress-list");
      if (!list) { return; }
      list.innerHTML = (data.students || []).map(function (student) {
        var mastery = Number(student.mastery) || 0;
        var statusClass = mastery < 50 ? "danger" : (mastery < 70 ? "warning" : "success");
        return '<div class="progress-row">' +
          '<div class="progress-row__student"><div class="avatar avatar--sm"></div><div>' +
          '<p class="progress-row__name"><a href="student-details.php?id=' + encodeURIComponent(student.id) + '">' + escapeHtml(student.name) + '</a></p>' +
          '<p class="progress-row__section">' + escapeHtml(student.section) + '</p></div></div>' +
          '<div class="progress-bar"><div class="progress-bar__fill progress-bar__fill--' + statusClass +
          '" style="width:' + mastery + '%"></div></div><span class="progress-row__value">' +
          mastery.toFixed(0) + '%</span></div>';
      }).join("") || "<p>No student game data has been received.</p>";

      document.getElementById("dwti-overview").innerHTML = (data.topics || []).map(function (topic) {
        var severity = String(topic.severity || "Low").toLowerCase();
        return '<div class="dwti-topic-card"><div class="dwti-topic-card__header">' +
          '<span class="dwti-topic-card__title">' + escapeHtml(topic.topic) + '</span>' +
          '<span class="badge badge--' + (severity === "high" ? "danger" : severity === "medium" ? "warning" : "neutral") + '">' +
          escapeHtml(topic.severity) + '</span></div><p class="dwti-topic-card__stat"><span class="dwti-topic-card__count">' +
          topic.students_flagged + '</span> students flagged</p></div>';
      }).join("") || "<p>No weak topics have been reported.</p>";
      var analytics = data.analytics || {};
      document.getElementById("analytics-summary").textContent =
        (analytics.activity_last_30_days || 0) + " student activity events in the last 30 days.";
      document.getElementById("attempts-over-time").innerHTML = (analytics.attempts_by_day || []).map(function (item) {
        return '<div class="dwti-topic-card"><div class="dwti-topic-card__header"><span class="dwti-topic-card__title">' +
          escapeHtml(item.day) + '</span><span class="badge badge--neutral">' + item.attempts + ' attempts</span></div>' +
          '<p class="dwti-topic-card__stat">Average score: ' + item.average_score + '%</p></div>';
      }).join("") || "<p>No attempts have been recorded in the last 14 days.</p>";

      document.getElementById("review-module-list").innerHTML = (data.review_modules || []).map(function (module) {
        return '<div class="review-module-item"><div class="review-module-item__body">' +
          '<p class="review-module-item__title">' + escapeHtml(module.title) + '</p><p class="review-module-item__meta">' +
          module.student_count + ' students · ' + escapeHtml(module.created_at || "Not dated") +
          '</p></div><select class="form-input module-status" data-module-id="' + module.id + '">' +
          '<option value="draft"' + (module.status === "draft" ? " selected" : "") + '>Draft</option>' +
          '<option value="published"' + (module.status === "published" ? " selected" : "") + '>Published</option>' +
          '<option value="completed"' + (module.status === "completed" ? " selected" : "") + '>Completed</option>' +
          '</select></div>';
      }).join("") || "<p>No review modules have been generated.</p>";
      document.querySelectorAll(".module-status").forEach(function (select) {
        select.addEventListener("change", function () {
          fetch("../api/review_modules.php", {method:"PATCH", headers:{"Content-Type":"application/json"},
            body:JSON.stringify({id:Number(this.getAttribute("data-module-id")), status:this.value})})
            .then(function (response) { if (!response.ok) { throw new Error("Unable to update module status."); } return response.json(); })
            .then(loadDashboardData).catch(function (error) { console.error(error); loadDashboardData(); });
        });
      });
    }

    function loadDashboardData() {
      fetch("../api/dashboard_data.php", {headers: {"Accept": "application/json"}})
        .then(function (response) {
          if (!response.ok) { throw new Error("Dashboard data request failed."); }
          return response.json();
        })
        .then(renderDashboard)
        .catch(function (error) { console.error(error); });
    }

    loadDashboardData();
    setInterval(loadDashboardData, 30000);
  </script>

</body>
</html>
