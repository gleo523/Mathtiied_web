<?php
declare(strict_types=1);
require_once __DIR__ . '/../../config/admin_guard.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Authentication Oversight | Mathtified</title>
  <link rel="stylesheet" href="../css/base.css">
  <link rel="stylesheet" href="../css/layout.css">
  <link rel="stylesheet" href="../css/components.css">
  <link rel="stylesheet" href="../css/responsive.css">
</head>
<body>
<div class="app-shell">
  <aside class="sidebar"><div class="sidebar-brand"><img src="../images/logo_st_mary (2).png" alt="School logo" class="sidebar-brand__logo"><div class="sidebar-brand__text"><span class="sidebar-brand__title">MATHTIFIED</span><span class="sidebar-brand__subtitle">Admin Portal</span></div></div><nav class="sidebar-nav"><p class="sidebar-nav__label">Main Menu</p><ul class="sidebar-nav__list"><li><a class="sidebar-nav__link" href="dashboard.php"><span>Dashboard</span></a></li><li><a class="sidebar-nav__link is-active" href="authentication.php"><span>Authentication Oversight</span></a></li><li><a class="sidebar-nav__link" href="settings.php"><span>Settings</span></a></li><li><a class="sidebar-nav__link" href="management.php"><span>Management</span></a></li></ul><div class="sidebar-nav__divider"></div><ul class="sidebar-nav__list"><li><a class="sidebar-nav__link" href="../../api/logout.php"><span>Logout</span></a></li></ul></nav></aside>
  <div class="main-content"><header class="app-header"><div class="app-header__left"><h1 class="app-header__title">Authentication Oversight</h1></div><div class="app-header__right"><div class="app-header__profile"><img src="../images/admin.jpg" alt="Administrator" class="app-header__avatar"><div class="app-header__profile-text"><span class="app-header__profile-name"><?= htmlspecialchars($adminUser['username'], ENT_QUOTES, 'UTF-8') ?></span><span class="app-header__profile-role">System Administrator</span></div></div></div></header>
  <main class="page-body"><div class="page-heading"><p class="page-heading__eyebrow">Access Records</p><h2 class="page-heading__title">Teacher Authentication Logs</h2><p class="page-heading__desc">Review recorded teacher login and logout events. The system does not currently have a separate approval-request workflow.</p></div>
  <section class="card"><div class="table-wrapper"><table class="data-table" id="auth-log-table"><thead><tr><th>User</th><th>Username</th><th>Event</th><th>IP Address</th><th>Recorded At</th></tr></thead><tbody><tr><td colspan="5">Loading records...</td></tr></tbody></table></div></section></main></div>
</div>
<script>
fetch("../../api/admin/admin_auth_logs.php",{headers:{Accept:"application/json"}}).then(function(r){return r.ok?r.json():Promise.reject(new Error("Authentication logs unavailable."));}).then(function(data){var rows=data.logs||[];var body=document.querySelector("#auth-log-table tbody");body.innerHTML=rows.length?rows.map(function(row){return "<tr><td>"+([row.first_name,row.last_name].filter(Boolean).join(" ")||row.username)+"</td><td>"+row.username+"</td><td>"+row.event_type+"</td><td>"+(row.ip_address||"—")+"</td><td>"+new Date(row.occurred_at).toLocaleString()+"</td></tr>";}).join(""):'<tr><td colspan="5">No teacher authentication records.</td></tr>';}).catch(function(){document.querySelector("#auth-log-table tbody").innerHTML='<tr><td colspan="5">Unable to load authentication records.</td></tr>';});
</script>
</body>
</html>
