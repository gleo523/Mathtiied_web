<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Details | MathTrack LMS</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/layout.css">
  <link rel="stylesheet" href="css/components.css">
  <link rel="stylesheet" href="css/students.css">
  <link rel="stylesheet" href="css/responsive.css">
</head>
<body>
  <div class="app-shell">
    <aside class="sidebar">
      <div class="sidebar__brand">
        <img src="images/logo_st_mary (2).png" alt="School Logo" class="sidebar__brand-logo">
        <span class="sidebar__brand-name">MathTrack</span>
      </div>
      <nav class="sidebar__nav">
        <span class="sidebar__section-label">Main</span>
        <a href="dashboard.php#dashboard" class="sidebar__link"><span class="sidebar__link-label">Dashboard</span></a>
        <a href="dashboard.php#students" class="sidebar__link is-active"><span class="sidebar__link-label">Students</span></a>
        <a href="dashboard.php#analytics" class="sidebar__link"><span class="sidebar__link-label">Learning Analytics</span></a>
        <a href="dashboard.php#reviews" class="sidebar__link"><span class="sidebar__link-label">Review Modules</span></a>
        <span class="sidebar__section-label">Account</span>
        <a href="settings.php" class="sidebar__link"><span class="sidebar__link-label">Settings</span></a>
        <a href="change-password.php" class="sidebar__link"><span class="sidebar__link-label">Change Password</span></a>
      </nav>
      <div class="sidebar__footer"><a href="../api/logout.php" class="sidebar__link"><span class="sidebar__link-label">Logout</span></a></div>
    </aside>

    <header class="topbar">
      <div class="topbar__logos">
        <img src="images/school_logo.png" alt="School Logo" class="topbar__logo">
        <img src="images/ICS_logo.png" alt="Institute Logo" class="topbar__logo">
        <img src="images/logo_st_mary (2).png" alt="Partner School Logo" class="topbar__logo">
      </div>
      <div class="topbar__profile">
        <div class="topbar__profile-info"><p class="topbar__profile-name">Teacher Portal</p><p class="topbar__profile-role">Student record</p></div>
        <img src="images/client.jpg" alt="Teacher Avatar" class="topbar__avatar">
      </div>
    </header>

    <main class="main-content">
      <div class="page-header">
        <div><h1 class="page-header__title">Student Details</h1><p class="page-header__subtitle" id="student-subtitle">Loading student record...</p></div>
        <a href="dashboard.php#students" class="btn btn--ghost btn--sm">&larr; Back to Dashboard</a>
      </div>

      <section class="student-profile">
        <img src="images/student 1.jpeg" alt="Student Avatar" class="student-profile__avatar">
        <div class="student-profile__details">
          <h2 class="student-profile__name" id="student-name">Loading...</h2>
          <div class="student-profile__meta" id="student-meta"></div>
        </div>
      </section>

      <div class="student-detail-grid">
        <div class="student-detail-main">
          <div class="card">
            <div class="card__header"><h2 class="card__title">Progress History</h2></div>
            <div class="table-wrapper"><table class="data-table"><thead><tr><th>Topic</th><th>Assessment</th><th>Score</th><th>Date</th></tr></thead><tbody id="attempts-list"></tbody></table></div>
          </div>
          <div class="card">
            <div class="card__header"><h2 class="card__title">Activity Logs</h2></div>
            <div class="activity-timeline" id="activities-list"></div>
          </div>
        </div>
        <div class="student-detail-side">
          <div class="card">
            <div class="card__header"><h2 class="card__title">Mastery Level</h2></div>
            <div class="mastery-panel">
              <div class="mastery-panel__ring-wrap"><div class="mastery-ring" id="mastery-ring"><div class="mastery-ring__inner" id="mastery-value">0%</div></div></div>
              <p class="mastery-panel__label" id="mastery-label">No assessment data</p>
              <p class="mastery-panel__desc">Calculated from submitted game data.</p>
              <div class="mastery-panel__breakdown" id="mastery-list"></div>
            </div>
          </div>
          <div class="card"><div class="card__header"><h2 class="card__title">Weak Topics (DWTI)</h2></div><div class="weak-topics-list" id="weak-topics-list"></div></div>
          <div class="card"><div class="card__header"><h2 class="card__title">Generated Review Modules</h2></div><div class="review-module-compact-list" id="modules-list"></div></div>
        </div>
      </div>
    </main>
  </div>

  <script>
    function escapeHtml(value) {
      return String(value ?? "").replace(/[&<>"']/g, function (character) {
        return {"&":"&amp;","<":"&lt;",">":"&gt;","\"":"&quot;","'":"&#039;"}[character];
      });
    }
    function formatDate(value) {
      return value ? new Date(value.replace(" ", "T")).toLocaleString() : "No date";
    }
    function scoreClass(score) {
      return score < 50 ? "danger" : (score < 70 ? "warning" : "success");
    }
    function renderStudent(data) {
      var student = data.student;
      document.getElementById("student-name").textContent = student.name;
      document.getElementById("student-subtitle").textContent = (student.section || "Unassigned") + " · Live game record";
      document.getElementById("student-meta").innerHTML =
        "<span class=\"student-profile__meta-item\">ID: " + escapeHtml(student.school_number || student.username) + "</span>" +
        "<span class=\"student-profile__meta-item\">" + escapeHtml(student.section || "Unassigned") + "</span>" +
        (student.email ? "<span class=\"student-profile__meta-item\">" + escapeHtml(student.email) + "</span>" : "");

      var mastery = Number(student.mastery) || 0;
      document.getElementById("mastery-value").textContent = mastery.toFixed(0) + "%";
      document.getElementById("mastery-ring").style.setProperty("--mastery", mastery);
      document.getElementById("mastery-label").textContent = mastery >= 70 ? "On track" : "Needs support";

      document.getElementById("attempts-list").innerHTML = (data.attempts || []).map(function (attempt) {
        var score = Number(attempt.score) || 0;
        var max = Number(attempt.max_score) || 100;
        var percent = max ? (score / max) * 100 : 0;
        return "<tr><td>" + escapeHtml(attempt.topic) + "</td><td>" + escapeHtml(attempt.assessment) +
          "</td><td><span class=\"badge badge--" + scoreClass(percent) + "\">" + percent.toFixed(0) +
          "%</span></td><td>" + escapeHtml(formatDate(attempt.attempted_at)) + "</td></tr>";
      }).join("") || "<tr><td colspan=\"4\">No game attempts have been submitted.</td></tr>";

      document.getElementById("mastery-list").innerHTML = (data.mastery || []).map(function (item) {
        return "<div class=\"mastery-panel__breakdown-row\"><span>" + escapeHtml(item.topic) +
          "</span><div class=\"progress-bar\"><div class=\"progress-bar__fill progress-bar__fill--" +
          scoreClass(item.mastery) + "\" style=\"width:" + item.mastery + "%\"></div></div></div>";
      }).join("") || "<p>No topic mastery data yet.</p>";

      document.getElementById("weak-topics-list").innerHTML = (data.weak_topics || []).map(function (item) {
        return "<span class=\"topic-tag\">" + escapeHtml(item.topic) + "</span>";
      }).join("") || "<span>No weak topics detected.</span>";

      document.getElementById("activities-list").innerHTML = (data.activities || []).map(function (item) {
        return "<div class=\"activity-item\"><div><p class=\"activity-item__title\">" + escapeHtml(item.title) +
          "</p><p class=\"activity-item__desc\">" + escapeHtml(item.description || "") + "</p><p class=\"activity-item__time\">" +
          escapeHtml(formatDate(item.occurred_at)) + "</p></div></div>";
      }).join("") || "<p>No activity recorded.</p>";

      document.getElementById("modules-list").innerHTML = (data.modules || []).map(function (item) {
        return "<div class=\"review-module-compact\"><div><p class=\"review-module-compact__title\">" +
          escapeHtml(item.title) + "</p><p class=\"review-module-compact__meta\">" + escapeHtml(item.status) +
          " · " + escapeHtml(formatDate(item.assigned_at)) + "</p></div></div>";
      }).join("") || "<p>No review modules assigned.</p>";
    }
    var studentId = new URLSearchParams(window.location.search).get("id");
    if (!studentId) {
      document.getElementById("student-subtitle").textContent = "Select a student from the dashboard.";
    } else {
      fetch("../api/student_details.php?id=" + encodeURIComponent(studentId), {headers: {"Accept": "application/json"}})
        .then(function (response) { if (!response.ok) { throw new Error("Student record unavailable."); } return response.json(); })
        .then(renderStudent)
        .catch(function (error) { document.getElementById("student-subtitle").textContent = error.message; });
    }
  </script>
</body>
</html>
