<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>MathTrack LMS | Grade 6 Mathematics Learning System</title>

  <!-- Fonts: Sora for display headings, Inter for body/data text -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/layout.css">
  <link rel="stylesheet" href="css/components.css">
  <link rel="stylesheet" href="css/responsive.css">
</head>
<body class="portal-body">

  <!-- ============ TOP BRAND BAR ============ -->
  <header class="portal-topbar">
    <div class="portal-topbar__brands">
      <img src="images/school_logo.png" alt="School Logo" class="portal-topbar__logo">
      <span class="portal-topbar__divider" aria-hidden="true"></span>
      <img src="images/ICS_logo.png" alt="Institute Logo" class="portal-topbar__logo">
      <span class="portal-topbar__divider" aria-hidden="true"></span>
      <img src="images/logo_st_mary (2).png" alt="Partner School Logo" class="portal-topbar__logo">
    </div>
    <p class="portal-topbar__label">Grade 6 Mathematics Learning System</p>
  </header>

  <!-- ============ HERO ============ -->
  <main class="portal-hero">

    <!-- Subtle mathematical motif: compass arc + construction grid, sits behind the copy -->
    <svg class="portal-hero__motif" viewBox="0 0 960 640" aria-hidden="true" focusable="false">
      <defs>
        <pattern id="gridPattern" width="40" height="40" patternUnits="userSpaceOnUse">
          <path d="M 40 0 L 0 0 0 40" fill="none" stroke="var(--color-accent-soft)" stroke-width="1"/>
        </pattern>
      </defs>
      <rect width="960" height="640" fill="url(#gridPattern)" />
      <circle cx="760" cy="160" r="150" fill="none" stroke="var(--color-primary)" stroke-width="1.5" stroke-dasharray="4 6" opacity="0.35"/>
      <path d="M 760 160 L 900 60" stroke="var(--color-primary)" stroke-width="2" opacity="0.4"/>
      <path d="M 760 160 L 900 260" stroke="var(--color-primary)" stroke-width="2" opacity="0.4"/>
      <circle cx="760" cy="160" r="4" fill="var(--color-primary)" opacity="0.6"/>
    </svg>

    <div class="portal-hero__content">
      <p class="portal-hero__eyebrow">Diagnostic &amp; Weak Topic Identification (DWTI)</p>
      <h1 class="portal-hero__title">Where every student's<br class="portal-hero__title-break">next lesson is the right one.</h1>
      <p class="portal-hero__subtitle">
        A learning system built for Grade 6 Mathematics — tracking mastery, surfacing
        weak topics, and generating targeted review modules for every learner.
      </p>

      <!-- ============ ROLE SELECTION CARDS ============ -->
      <div class="portal-role-grid" role="group" aria-label="Choose your portal to continue">

        <article class="role-card">
          <div class="role-card__icon" aria-hidden="true">
            <svg viewBox="0 0 48 48" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M6 14 L24 6 L42 14 L24 22 Z"/>
              <path d="M14 20 v10 c0 4 20 4 20 0 v-10"/>
              <path d="M42 14 v12"/>
            </svg>
          </div>
          <h2 class="role-card__title">Teacher Portal</h2>
          <p class="role-card__desc">
            Monitor class progress, review DWTI reports, and manage generated
            review modules for your students.
          </p>
          <div class="role-card__actions">
            <a href="teacher-login.php" class="btn btn--primary" aria-label="Log in as a teacher">Log In</a>
            <a href="teacher-register.php" class="btn btn--ghost" aria-label="Register as a teacher">Register</a>
          </div>
        </article>

        <article class="role-card">
          <div class="role-card__icon" aria-hidden="true">
            <svg viewBox="0 0 48 48" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="24" cy="16" r="8"/>
              <path d="M8 42 c0 -10 7.2 -16 16 -16 s16 6 16 16"/>
            </svg>
          </div>
          <h2 class="role-card__title">Student Portal</h2>
          <p class="role-card__desc">
            Access your activities, track your mastery level, and follow your
            personalized review modules.
          </p>
          <div class="role-card__actions">
            <a href="student-login.php" class="btn btn--primary" aria-label="Log in as a student">Log In</a>
            <a href="student-register.php" class="btn btn--ghost" aria-label="Register as a student">Register</a>
          </div>
        </article>

      </div>
    </div>
  </main>

  <!-- ============ FOOTER ============ -->
  <footer class="portal-footer">
    <p>&copy; 2026 MathTrack — Grade 6 Mathematics Learning System. Built for classroom use.</p>
  </footer>

</body>
</html>