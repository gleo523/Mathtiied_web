<?php
declare(strict_types=1);
require_once __DIR__ . '/../../../config/admin_guard.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Management | Mathtified</title>
  <link rel="stylesheet" href="../css/base.css">
  <link rel="stylesheet" href="../css/layout.css">
  <link rel="stylesheet" href="../css/components.css">
  <link rel="stylesheet" href="../css/responsive.css">
  <style>
    .management-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:var(--space-6); }
    .management-list { max-height:340px; overflow:auto; margin-top:var(--space-5); }
    .management-list li { padding:var(--space-3) 0; border-bottom:1px solid var(--color-border); overflow-wrap:anywhere; }
    @media (max-width: 840px) { .management-grid { grid-template-columns:1fr; } }
  </style>
</head>
<body>
<div class="app-shell">
  <aside class="sidebar"><div class="sidebar-brand"><img src="../images/logo_st_mary (2).png" alt="School logo" class="sidebar-brand__logo"><div class="sidebar-brand__text"><span class="sidebar-brand__title">MATHTIFIED</span><span class="sidebar-brand__subtitle">Admin Portal</span></div></div><nav class="sidebar-nav"><p class="sidebar-nav__label">Main Menu</p><ul class="sidebar-nav__list"><li><a class="sidebar-nav__link" href="dashboard.php"><span>Dashboard</span></a></li><li><a class="sidebar-nav__link" href="authentication.php"><span>Authentication Oversight</span></a></li><li><a class="sidebar-nav__link" href="settings.php"><span>Settings</span></a></li><li><a class="sidebar-nav__link is-active" href="management.php"><span>Management</span></a></li></ul><div class="sidebar-nav__divider"></div><ul class="sidebar-nav__list"><li><a class="sidebar-nav__link" href="../../../api/logout.php"><span>Logout</span></a></li></ul></nav></aside>
  <div class="main-content"><header class="app-header"><div class="app-header__left"><h1 class="app-header__title">Management</h1></div><div class="app-header__right"><div class="app-header__profile"><img src="../images/admin.jpg" alt="Administrator" class="app-header__avatar"><div class="app-header__profile-text"><span class="app-header__profile-name"><?= htmlspecialchars($adminUser['username'], ENT_QUOTES, 'UTF-8') ?></span><span class="app-header__profile-role">System Administrator</span></div></div></div></header>
  <main class="page-body"><div class="page-heading"><p class="page-heading__eyebrow">Administration</p><h2 class="page-heading__title">Management</h2><p class="page-heading__desc">Create teacher accounts and review teacher assignments and authentication history.</p></div>
  <div class="management-grid">
    <section class="card"><h3>Teacher Accounts</h3><form id="teacher-form"><div class="form-row"><div class="form-group"><label class="form-label">First name</label><input class="form-input" name="first_name" required></div><div class="form-group"><label class="form-label">Last name</label><input class="form-input" name="last_name" required></div></div><div class="form-group"><label class="form-label">Username</label><input class="form-input" name="username" required></div><div class="form-group"><label class="form-label">Email</label><input class="form-input" name="email" type="email" required></div><div class="form-group"><label class="form-label">Temporary password</label><input class="form-input" name="password" type="password" minlength="8" required></div><button class="btn btn-primary" type="submit">Create teacher</button><p id="teacher-status" role="status"></p></form><ul id="teachers" class="management-list"></ul></section>
    <section class="card"><h3>Students by Teacher</h3><p class="form-hint">Students without a teacher assignment are shown as unassigned.</p><ul id="students" class="management-list"></ul></section>
    <section class="card"><h3>Teacher Authentication Logs</h3><ul id="logs" class="management-list"></ul></section>
  </div></main></div>
</div>
<script>
async function json(url,options){var response=await fetch(url,options);var data=await response.json();if(!response.ok)throw new Error(data.error||"Request failed.");return data;}
async function refresh(){var results=await Promise.all([json("../../../api/admin/admin_teachers.php"),json("../../../api/admin/admin_auth_logs.php"),json("../../../api/admin/admin_overview.php"),json("../../../api/admin/admin_assignments.php")]);var teachers=results[3].teachers||[];document.getElementById("teachers").innerHTML=(results[0].teachers||[]).map(function(x){return "<li><strong>"+x.first_name+" "+x.last_name+"</strong><br>"+x.username+" · "+x.email+"<br><button type=\"button\" class=\"btn btn-primary reset-teacher\" data-user-id=\""+x.id+"\">Reset password</button></li>";}).join("")||"<li>No teachers.</li>";document.getElementById("logs").innerHTML=(results[1].logs||[]).slice(0,100).map(function(x){return "<li>"+x.username+" · "+x.event_type+" · "+new Date(x.occurred_at).toLocaleString()+"</li>";}).join("")||"<li>No logs.</li>";document.getElementById("students").innerHTML=(results[2].students||[]).map(function(x){var options='<option value="0">Unassigned</option>'+teachers.map(function(t){return '<option value="'+t.id+'" '+(String(t.username)===String(x.teacher_username)?'selected':'')+'>'+t.username+'</option>';}).join('');return "<li><strong>"+(x.first_name||"")+" "+(x.last_name||"")+"</strong><br><select class=\"form-input student-assignment\" data-student-id=\""+x.id+"\">"+options+"</select><small>"+(x.section||"No section")+"</small></li>";}).join("")||"<li>No students.</li>";document.querySelectorAll(".student-assignment").forEach(function(select){select.addEventListener("change",async function(){try{await json("../../../api/admin/admin_assignments.php",{method:"POST",headers:{"Content-Type":"application/json",Accept:"application/json"},body:JSON.stringify({student_id:this.dataset.studentId,teacher_id:this.value})});}catch(error){alert(error.message);await refresh();}});});document.querySelectorAll(".reset-teacher").forEach(function(button){button.addEventListener("click",async function(){var password=window.prompt("Enter a temporary password (at least 8 characters):");if(!password)return;try{await json("../../../api/admin/admin_reset_password.php",{method:"POST",headers:{"Content-Type":"application/json",Accept:"application/json"},body:JSON.stringify({user_id:this.dataset.userId,password:password})});alert("Teacher password reset.");}catch(error){alert(error.message);}});});}document.getElementById("teacher-form").addEventListener("submit",async function(event){event.preventDefault();var status=document.getElementById("teacher-status");try{await json("../../../api/admin/admin_teachers.php",{method:"POST",headers:{"Content-Type":"application/json",Accept:"application/json"},body:JSON.stringify(Object.fromEntries(new FormData(event.currentTarget)))});event.currentTarget.reset();status.textContent="Teacher account created.";await refresh();}catch(error){status.textContent=error.message;}});refresh().catch(function(error){document.querySelectorAll(".management-list").forEach(function(list){list.innerHTML="<li>"+error.message+"</li>";});});
</script>
</body>
</html>
