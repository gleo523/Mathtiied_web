<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/auth.php';
if (!current_user() || current_user()['role'] !== 'teacher') {
    header('Location: teacher-login.php');
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Wave History | MathTrack</title>
  <link rel="stylesheet" href="css/base.css"><link rel="stylesheet" href="css/layout.css"><link rel="stylesheet" href="css/components.css"><link rel="stylesheet" href="css/responsive.css">
  <style>.history-chart{display:flex;align-items:end;gap:10px;min-height:260px;padding:24px;border-bottom:1px solid #ddd}.bar{width:28px;background:#3478f6;min-height:3px;position:relative}.bar span{position:absolute;bottom:100%;font-size:11px;white-space:nowrap}.history-legend{margin-top:16px}</style>
</head>
<body><div class="app-shell"><aside class="sidebar"><div class="sidebar__brand"><img src="images/logo_st_mary (2).png" alt="School Logo" class="sidebar__brand-logo"><span class="sidebar__brand-name">MathTrack</span></div><nav class="sidebar__nav"><a href="dashboard.php" class="sidebar__link"><span class="sidebar__link-label">Dashboard</span></a><a href="content-editor.php" class="sidebar__link"><span class="sidebar__link-label">Content Editor</span></a><a href="wave-history.php" class="sidebar__link is-active"><span class="sidebar__link-label">Wave History</span></a></nav></aside><main class="main-content"><div class="page-header"><div><h1 class="page-header__title">Wave Score History</h1><p class="page-header__subtitle">Review per-wave DWTI scores for assigned students.</p></div></div><section class="card"><label class="form-label" for="student">Student</label><select id="student" class="form-input"></select><div id="chart" class="history-chart"><p>Select a student.</p></div><div id="legend" class="history-legend"></div></section></main></div>
<script>
const student = document.getElementById('student'), chart = document.getElementById('chart'), legend = document.getElementById('legend');
async function loadStudents(){const r=await fetch('../api/wave_history.php');const d=await r.json();if(!r.ok)throw Error(d.error||'Unable to load students');student.innerHTML='<option value="">Choose a student</option>'+(d.students||[]).map(s=>'<option value="'+s.id+'">'+(s.first_name||'')+' '+(s.last_name||'')+' ('+s.username+')</option>').join('');}
async function loadScores(){if(!student.value){chart.innerHTML='<p>Select a student.</p>';legend.textContent='';return;}const r=await fetch('../api/wave_history.php?student_id='+student.value);const d=await r.json();if(!r.ok)throw Error(d.error||'Unable to load scores');const scores=d.scores||[];chart.innerHTML=scores.length?scores.map(s=>'<div class="bar" style="height:'+Math.max(3,Number(s.score)*220)+'px" title="'+s.topic+' wave '+s.wave_number+'"><span>'+s.topic+' W'+s.wave_number+' '+(Number(s.score)*100).toFixed(0)+'%</span></div>').join(''):'<p>No wave scores recorded.</p>';legend.textContent=scores.length+' wave records';}
student.addEventListener('change',()=>loadScores().catch(e=>legend.textContent=e.message));loadStudents().catch(e=>legend.textContent=e.message);
</script></body></html>
