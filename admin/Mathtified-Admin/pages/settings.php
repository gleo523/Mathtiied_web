<?php
declare(strict_types=1);
require_once __DIR__ . '/../../../config/admin_guard.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Settings | Mathtified</title>
  <link rel="stylesheet" href="../css/base.css">
  <link rel="stylesheet" href="../css/layout.css">
  <link rel="stylesheet" href="../css/components.css">
  <link rel="stylesheet" href="../css/responsive.css">
</head>
<body>
<div class="app-shell">
  <aside class="sidebar"><div class="sidebar-brand"><img src="../images/logo_st_mary (2).png" alt="School logo" class="sidebar-brand__logo"><div class="sidebar-brand__text"><span class="sidebar-brand__title">MATHTIFIED</span><span class="sidebar-brand__subtitle">Admin Portal</span></div></div><nav class="sidebar-nav"><p class="sidebar-nav__label">Main Menu</p><ul class="sidebar-nav__list"><li><a class="sidebar-nav__link" href="dashboard.php"><span>Dashboard</span></a></li><li><a class="sidebar-nav__link" href="authentication.php"><span>Authentication Oversight</span></a></li><li><a class="sidebar-nav__link is-active" href="settings.php"><span>Settings</span></a></li><li><a class="sidebar-nav__link" href="management.php"><span>Management</span></a></li></ul><div class="sidebar-nav__divider"></div><ul class="sidebar-nav__list"><li><a class="sidebar-nav__link" href="../../../api/logout.php"><span>Logout</span></a></li></ul></nav></aside>
  <div class="main-content"><header class="app-header"><div class="app-header__left"><h1 class="app-header__title">Settings</h1></div><div class="app-header__right"><div class="app-header__profile"><img src="../images/admin.jpg" alt="Administrator" class="app-header__avatar"><div class="app-header__profile-text"><span class="app-header__profile-name"><?= htmlspecialchars($adminUser['username'], ENT_QUOTES, 'UTF-8') ?></span><span class="app-header__profile-role">System Administrator</span></div></div></div></header>
  <main class="page-body"><div class="page-heading"><p class="page-heading__eyebrow">System Configuration</p><h2 class="page-heading__title">DWTI Review Settings</h2><p class="page-heading__desc">Configure the threshold used to flag topics for review.</p></div>
  <section class="card"><form id="settings-form"><div class="form-group"><label class="form-label" for="threshold">Review threshold (0 to 1)</label><input class="form-input" id="threshold" type="number" min="0" max="1" step="0.01" required><p class="form-hint">Students below this mastery value can be considered for review.</p></div><div class="form-actions"><button class="btn btn-primary" type="submit">Save threshold</button><span id="status" role="status"></span></div></form></section></main></div>
</div>
<script>
function load(){fetch("../../../api/admin/admin_settings.php",{headers:{Accept:"application/json"}}).then(function(r){return r.ok?r.json():Promise.reject(new Error("Settings unavailable."));}).then(function(data){var item=(data.settings||[]).find(function(x){return x.setting_key==="dwti_review_threshold";});document.getElementById("threshold").value=item?item.setting_value:"0.70";}).catch(function(e){document.getElementById("status").textContent=e.message;});}
document.getElementById("settings-form").addEventListener("submit",function(e){e.preventDefault();fetch("../../../api/admin/admin_settings.php",{method:"POST",headers:{"Content-Type":"application/json",Accept:"application/json"},body:JSON.stringify({dwti_review_threshold:Number(document.getElementById("threshold").value)})}).then(function(r){return r.ok?r.json():r.json().then(function(x){throw new Error(x.error||"Save failed.");});}).then(function(){document.getElementById("status").textContent="Saved.";}).catch(function(e){document.getElementById("status").textContent=e.message;});});load();
</script>
</body>
</html>
