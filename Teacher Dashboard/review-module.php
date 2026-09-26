<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/auth.php';
$teacher = current_user();
if (!$teacher || $teacher['role'] !== 'teacher') {
    header('Location: teacher-login.php');
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Generate Review Module | MathTrack</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/layout.css">
  <link rel="stylesheet" href="css/components.css">
  <link rel="stylesheet" href="css/dashboard.css">
  <link rel="stylesheet" href="css/responsive.css">
  <style>
    .review-module-form { display: grid; gap: 1.25rem; }
    .review-module-form__field { display: grid; gap: .5rem; }
    .review-module-form__assignments { margin: 0; padding: 1rem; border: 1px solid var(--color-border, #d9dee8); border-radius: .75rem; }
    .review-module-form__assignments legend { padding: 0 .35rem; }
    .review-module-form__students { display: grid; gap: .65rem; margin-top: .5rem; }
    .review-module-form__student { display: flex; align-items: center; gap: .6rem; font-weight: 600; }
    .review-module-form__student input { width: 1.1rem; height: 1.1rem; accent-color: var(--color-primary, #3155b8); }
  </style>
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
        <a href="dashboard.php#students" class="sidebar__link"><span class="sidebar__link-label">Students</span></a>
        <a href="dashboard.php#analytics" class="sidebar__link"><span class="sidebar__link-label">Learning Analytics</span></a>
        <a href="dashboard.php#reviews" class="sidebar__link is-active"><span class="sidebar__link-label">Review Modules</span></a>
        <a href="content-editor.php" class="sidebar__link"><span class="sidebar__link-label">Content Editor</span></a>
        <a href="wave-history.php" class="sidebar__link"><span class="sidebar__link-label">Wave History</span></a>
        <span class="sidebar__section-label">Account</span>
        <a href="settings.php" class="sidebar__link"><span class="sidebar__link-label">Settings</span></a>
        <a href="change-password.php" class="sidebar__link"><span class="sidebar__link-label">Change Password</span></a>
      </nav>
      <div class="sidebar__footer">
        <a href="../api/logout.php" class="sidebar__link"><span class="sidebar__link-label">Logout</span></a>
      </div>
    </aside>
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
          <p class="topbar__profile-name"><?= htmlspecialchars(trim(($teacher['first_name'] ?? '') . ' ' . ($teacher['last_name'] ?? '')) ?: $teacher['username'], ENT_QUOTES, 'UTF-8') ?></p>
          <p class="topbar__profile-role">Mathematics Teacher</p>
        </div>
        <img src="images/client.jpg" alt="Teacher Avatar" class="topbar__avatar">
      </div>
    </header>
    <main class="main-content">
      <div class="page-header">
        <div>
          <h1 class="page-header__title">Generate Review Module</h1>
          <p class="page-header__subtitle">Choose a weak topic and assign targeted practice.</p>
        </div>
        <a href="dashboard.php#reviews" class="btn btn--ghost btn--sm">Back to Modules</a>
      </div>
      <section class="section">
        <div class="card review-module-card">
          <div class="card__header">
            <h2 class="card__title">Review Module Details</h2>
          </div>
          <form id="module-form" class="review-module-form">
        <div class="review-module-form__field">
          <label class="form-label" for="topic">Weak topic</label>
          <select id="topic" class="form-input" required></select>
          <p id="topic-help" class="form-help"></p>
        </div>
        <div class="review-module-form__field">
          <label class="form-label" for="title">Module title</label>
          <input id="title" class="form-input" maxlength="190" required>
        </div>
        <div class="review-module-form__field">
          <label class="form-label" for="instructions">Instructions</label>
          <textarea id="instructions" class="form-input" rows="5" placeholder="What should students review?"></textarea>
        </div>
        <div class="review-module-form__field">
          <label class="form-label" for="status">Save as</label>
          <select id="status" class="form-input"><option value="draft">Draft</option><option value="published">Published</option></select>
        </div>
        <fieldset class="review-module-form__assignments">
          <legend class="form-label">Assign to students</legend>
          <div id="students" class="review-module-form__students"></div>
        </fieldset>
        <p id="message" role="alert"></p>
        <button class="btn btn--primary" type="submit">Create module</button>
      </form>
        </div>
      </section>
    </main>
  </div>
<script>
  var form = document.getElementById("module-form"), topic = document.getElementById("topic");
  function esc(value) { return String(value).replace(/[&<>"']/g, function(c) { return {"&":"&amp;","<":"&lt;",">":"&gt;","\"":"&quot;","'":"&#039;"}[c]; }); }
  fetch("../api/review_modules.php").then(function(r) { return r.json(); }).then(function(data) {
    (data.weak_topics || []).forEach(function(item) {
      var option = document.createElement("option"); option.value = item.topic;
      option.textContent = item.topic + " (" + item.students_flagged + " students, " + item.mastery + "% average)";
      topic.appendChild(option);
    });
    if (!topic.options.length) { topic.innerHTML = "<option value=''>No weak topics available</option>"; }
    document.getElementById("topic-help").textContent = topic.options.length ? "Only topics flagged for your students are available." : "Students need reported mastery below 70% before a module can be created.";
    document.getElementById("students").innerHTML = (data.students || []).map(function(s) {
      return "<label class='review-module-form__student'><input type='checkbox' name='student_ids' value='" + s.id + "'> <span>" + esc(s.name) + " (" + esc(s.section) + ")</span></label>";
    }).join("") || "<p>No students are assigned to you.</p>";
  }).catch(function() { document.getElementById("message").textContent = "Unable to load module options."; });
  form.addEventListener("submit", function(e) {
    e.preventDefault(); var ids = Array.from(document.querySelectorAll("[name=student_ids]:checked")).map(function(i) { return Number(i.value); });
    fetch("../api/review_modules.php", {method:"POST", headers:{"Content-Type":"application/json"}, body:JSON.stringify({topic:topic.value,title:document.getElementById("title").value,instructions:document.getElementById("instructions").value,status:document.getElementById("status").value,student_ids:ids})})
      .then(function(r) { return r.json().then(function(d) { if (!r.ok) { throw new Error(d.error || "Could not create module."); } return d; }); })
      .then(function() { window.location.href = "dashboard.php#reviews"; })
      .catch(function(error) { document.getElementById("message").textContent = error.message; });
  });
</script>
</body>
</html>
