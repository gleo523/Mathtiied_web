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
  <title>Settings | MathTrack LMS</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="Teacher Dashboard/css/base.css">
  <link rel="stylesheet" href="Teacher Dashboard/css/layout.css">
  <link rel="stylesheet" href="Teacher Dashboard/css/components.css">
  <link rel="stylesheet" href="Teacher Dashboard/css/settings.css">
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

        <a href="settings.php" class="sidebar__link is-active">
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

      <div class="page-header">
        <div>
          <h1 class="page-header__title">Settings</h1>
          <p class="page-header__subtitle">Manage your profile, school information, and preferences</p>
        </div>
      </div>

      <div class="settings-layout">

        <!-- Section jump nav -->
        <nav class="settings-nav">
          <a href="#teacher-profile" class="settings-nav__link is-active">Teacher Profile</a>
          <a href="#personal-information" class="settings-nav__link">Personal Information</a>
          <a href="#school-information" class="settings-nav__link">School Information</a>
          <a href="#preferences" class="settings-nav__link">Preferences</a>
          <a href="#change-password" class="settings-nav__link">Change Password</a>
        </nav>

        <div class="settings-sections">

          <!-- Teacher Profile -->
          <section id="teacher-profile" class="card settings-section">
            <div class="card__header">
              <h2 class="card__title">Teacher Profile</h2>
            </div>

            <div class="profile-editor">
              <div class="profile-editor__avatar-wrap">
                <img id="profile-photo" src="images/client.jpg" alt="Teacher Avatar" class="avatar avatar--lg">
              </div>
              <div class="profile-editor__meta">
                <h3 id="profile-name" class="profile-editor__name">Teacher Profile</h3>
                <p id="profile-role" class="profile-editor__role">Teacher</p>
              </div>
              <div class="profile-editor__upload">
                <label class="btn btn--ghost btn--sm" for="profile-photo-input">Change Photo</label>
                <input type="file" id="profile-photo-input" name="profile_photo" accept="image/jpeg,image/png,image/webp" hidden>
              </div>
            </div>
          </section>

          <!-- Personal Information -->
          <section id="personal-information" class="card settings-section">
            <div class="card__header">
              <h2 class="card__title">Personal Information</h2>
            </div>
            <p class="settings-section__desc">Update your personal contact details.</p>

            <form id="personal-form" action="#" method="post">
              <div class="settings-form-grid">
                <div class="form-group">
                  <label class="form-label" for="pi-first-name">First Name</label>
                  <input type="text" id="pi-first-name" name="first_name" class="form-input" required>
                </div>
                <div class="form-group">
                  <label class="form-label" for="pi-last-name">Last Name</label>
                  <input type="text" id="pi-last-name" name="last_name" class="form-input" required>
                </div>
                <div class="form-group">
                  <label class="form-label" for="pi-email">Email Address</label>
                  <input type="email" id="pi-email" name="email" class="form-input" required>
                </div>
                <div class="form-group">
                  <label class="form-label" for="pi-phone">Phone Number</label>
                  <input type="tel" id="pi-phone" name="phone" class="form-input">
                </div>
              </div>

              <div class="settings-section__footer">
                <button type="button" class="btn btn--ghost">Cancel</button>
                <button type="submit" class="btn btn--primary">Save Changes</button>
              </div>
            </form>
          </section>

          <!-- School Information -->
          <section id="school-information" class="card settings-section">
            <div class="card__header">
              <h2 class="card__title">School Information</h2>
            </div>
            <p class="settings-section__desc">Details about your assigned school and class sections.</p>

            <form id="school-form" action="#" method="post">
              <div class="settings-form-grid">
                <div class="form-group form-group--full">
                  <label class="form-label" for="si-school-name">School Name</label>
                  <input type="text" id="si-school-name" name="school" class="form-input">
                </div>
                <div class="form-group">
                  <label class="form-label" for="si-department">Department</label>
                  <input type="text" id="si-department" name="department" class="form-input">
                </div>
                <div class="form-group">
                  <label class="form-label" for="si-grade-level">Grade Level Handled</label>
                  <input type="text" id="si-grade-level" name="grade_level" class="form-input">
                </div>
                <div class="form-group form-group--full">
                  <label class="form-label" for="si-sections">Sections Handled</label>
                  <input type="text" id="si-sections" name="sections" class="form-input">
                </div>
              </div>
              <div class="settings-section__footer">
                <button type="button" class="btn btn--ghost">Cancel</button>
                <button type="submit" class="btn btn--primary">Save Changes</button>
              </div>
            </form>
          </section>

          <!-- Preferences -->
          <section id="preferences" class="card settings-section">
            <div class="card__header">
              <h2 class="card__title">Preferences</h2>
            </div>
            <p class="settings-section__desc">Control how MathTrack notifies and displays information to you.</p>

            <div class="preference-list">

              <div class="preference-row">
                <div>
                  <p class="preference-row__label">Email Notifications</p>
                  <p class="preference-row__desc">Receive an email when a new DWTI report is generated.</p>
                </div>
                <label class="switch">
                  <input type="checkbox" id="email-notifications" name="email_notifications" value="1">
                  <span class="switch__track"></span>
                </label>
              </div>

              <div class="preference-row">
                <div>
                  <p class="preference-row__label">Progress Summary</p>
                  <p class="preference-row__desc">Get a weekly digest of class-wide mastery trends.</p>
                </div>
                <label class="switch">
                  <input type="checkbox" id="progress-summary" name="progress_summary" value="1">
                  <span class="switch__track"></span>
                </label>
              </div>

              <div class="settings-section__footer">
                <button type="button" id="save-preferences" class="btn btn--primary">Save Preferences</button>
              </div>
            </div>
          </section>
        </div>
      </div>

    </main>

  </div>

  <script>
    function confirmLogout() {
      return confirm("Are you sure you want to logout?");
    }

    const profilePhoto = document.getElementById('profile-photo');
    const photoInput = document.getElementById('profile-photo-input');
    const profileFields = {
      first_name: document.getElementById('pi-first-name'),
      last_name: document.getElementById('pi-last-name'),
      email: document.getElementById('pi-email'),
      phone: document.getElementById('pi-phone'),
      school: document.getElementById('si-school-name'),
      department: document.getElementById('si-department'),
      grade_level: document.getElementById('si-grade-level'),
      sections: document.getElementById('si-sections')
    };

    function setProfile(profile) {
      Object.keys(profileFields).forEach((key) => {
        profileFields[key].value = profile[key] || '';
      });
      document.getElementById('email-notifications').checked = Number(profile.email_notifications) === 1;
      document.getElementById('progress-summary').checked = Number(profile.progress_summary) === 1;
      const name = [profile.first_name, profile.last_name].filter(Boolean).join(' ') || 'Teacher Profile';
      document.getElementById('profile-name').textContent = name;
      document.getElementById('profile-role').textContent =
        [profile.grade_level, profile.department].filter(Boolean).join(' · ') || 'Teacher';
      if (profile.photo_url) {
        profilePhoto.src = profile.photo_url;
        document.querySelector('.topbar__avatar').src = profile.photo_url;
      }
    }

    async function loadProfile() {
      const response = await fetch('../api/teacher_profile.php', {headers: {Accept: 'application/json'}});
      const data = await response.json();
      if (!response.ok) throw new Error(data.error || 'Unable to load profile.');
      setProfile(data.profile || {});
    }

    async function saveProfile(event) {
      event.preventDefault();
      const formData = new FormData();
      Object.keys(profileFields).forEach((key) => formData.append(key, profileFields[key].value.trim()));
      formData.append('email_notifications', document.getElementById('email-notifications').checked ? '1' : '0');
      formData.append('progress_summary', document.getElementById('progress-summary').checked ? '1' : '0');
      if (photoInput.files[0]) formData.append('profile_photo', photoInput.files[0]);
      const response = await fetch('../api/teacher_profile.php', {method: 'POST', body: formData, headers: {Accept: 'application/json'}});
      const data = await response.json();
      if (!response.ok) throw new Error(data.error || 'Unable to save profile.');
      setProfile(data.profile || {});
      alert('Profile saved successfully.');
    }

    document.getElementById('personal-form').addEventListener('submit', saveProfile);
    document.getElementById('school-form').addEventListener('submit', saveProfile);
    document.getElementById('save-preferences').addEventListener('click', saveProfile);
    photoInput.addEventListener('change', () => {
      if (photoInput.files[0]) profilePhoto.src = URL.createObjectURL(photoInput.files[0]);
    });
    loadProfile().catch((error) => alert(error.message));
  </script>

</body>
</html>