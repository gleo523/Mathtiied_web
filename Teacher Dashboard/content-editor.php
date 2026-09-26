<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/auth.php';
if (!current_user() || current_user()['role'] !== 'teacher') {
    header('Location: teacher-login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Content Editor | MathTrack</title>
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/layout.css">
  <link rel="stylesheet" href="css/components.css">
  <link rel="stylesheet" href="css/responsive.css">
  <style>
    .editor-grid { display:grid; grid-template-columns:220px 1fr; gap:24px; align-items:start; }
    .editor-toolbar { display:flex; gap:8px; flex-wrap:wrap; margin:0 0 16px; }
    .question-card { border:1px solid var(--color-border); border-radius:12px; padding:16px; margin:12px 0; background:#fff; }
    .question-card__heading { display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:12px; }
    .choice-row { display:grid; grid-template-columns:1fr auto; gap:8px; margin:8px 0; }
    .editor-help { margin:4px 0 20px; color:var(--color-text-muted); font-size:var(--fs-sm); }
    .editor-actions { display:flex; gap:8px; flex-wrap:wrap; margin-top:16px; }
    .item-selector { margin-bottom:16px; }
    .editor-status { min-height:24px; margin-top:12px; }
    @media (max-width:800px) { .editor-grid { grid-template-columns:1fr; } }
  </style>
</head>
<body>
<div class="app-shell">
  <aside class="sidebar">
    <div class="sidebar__brand"><img src="images/logo_st_mary (2).png" alt="School Logo" class="sidebar__brand-logo"><span class="sidebar__brand-name">MathTrack</span></div>
    <nav class="sidebar__nav">
      <span class="sidebar__section-label">Main</span>
      <a href="dashboard.php" class="sidebar__link"><span class="sidebar__link-label">Dashboard</span></a>
      <a href="dashboard.php#students" class="sidebar__link"><span class="sidebar__link-label">Students</span></a>
      <a href="dashboard.php#analytics" class="sidebar__link"><span class="sidebar__link-label">Learning Analytics</span></a>
      <a href="dashboard.php#reviews" class="sidebar__link"><span class="sidebar__link-label">Review Modules</span></a>
      <a href="content-editor.php" class="sidebar__link is-active"><span class="sidebar__link-label">Content Editor</span></a>
      <a href="wave-history.php" class="sidebar__link"><span class="sidebar__link-label">Wave History</span></a>
      <span class="sidebar__section-label">Account</span>
      <a href="settings.php" class="sidebar__link"><span class="sidebar__link-label">Settings</span></a>
      <a href="change-password.php" class="sidebar__link"><span class="sidebar__link-label">Change Password</span></a>
    </nav>
    <div class="sidebar__footer"><a href="../api/logout.php" class="sidebar__link"><span class="sidebar__link-label">Logout</span></a></div>
  </aside>
  <main class="main-content">
    <div class="page-header"><div><h1 class="page-header__title">Learning Content</h1><p class="page-header__subtitle">Update the questions and explanations students see in the game.</p></div></div>
    <section class="card">
      <p class="editor-help">Choose a lesson, load its content, make your changes, then select Save content.</p>
      <div class="editor-grid">
        <div>
          <label class="form-label" for="part">Lesson</label>
          <select id="part" class="form-input">
            <option value="part1">Lesson 1: Tessellation</option><option value="part2">Lesson 2: Numbers and Algebra</option>
            <option value="part3">Lesson 3: Ratio and Proportion</option><option value="part4">Lesson 4: Measurement and Geometry</option>
            <option value="part5">Lesson 5: Geometry, Algebra, and Data</option>
          </select>
          <label class="form-label" for="type">What would you like to edit?</label>
          <select id="type" class="form-input"><option value="questions">Questions</option><option value="explanations">Explanations</option><option value="why_check">Answer explanations</option></select>
          <label class="form-label" for="item">Item to edit</label>
          <select id="item" class="form-input item-selector" disabled><option>No items loaded</option></select>
          <div class="editor-actions"><button id="load" class="btn btn--ghost btn--sm" type="button">Load content</button><button id="save" class="btn btn--primary btn--sm" type="button">Save content</button></div>
          <p id="status" class="editor-status" role="status"></p>
        </div>
        <div>
          <div class="editor-toolbar">
            <button id="add-question" class="btn btn--ghost btn--sm" type="button">Add item</button>
          </div>
          <div id="visual-editor" aria-live="polite"></div>
        </div>
      </div>
    </section>
  </main>
</div>
<script>
const part = document.getElementById('part'), type = document.getElementById('type'), itemSelector = document.getElementById('item'), status = document.getElementById('status'), visual = document.getElementById('visual-editor');
let model = [];
let selectedIndex = 0;
function setStatus(text, error) { status.textContent = text; status.style.color = error ? '#b42318' : '#16794c'; }
function esc(value) { return String(value ?? '').replace(/[&<>"']/g, function(c) { return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[c]; }); }
function renderVisual() {
  const rows = Array.isArray(model) ? model : [model];
  if (!rows.length) {
    itemSelector.disabled = true;
    itemSelector.innerHTML = '<option>No items loaded</option>';
    visual.innerHTML = '<p class="form-hint">No content yet. Select Add item to begin.</p>';
    return;
  }
  selectedIndex = Math.min(Math.max(selectedIndex, 0), rows.length - 1);
  itemSelector.disabled = false;
  itemSelector.innerHTML = rows.map(function(_, index) {
    return '<option value="' + index + '"' + (index === selectedIndex ? ' selected' : '') + '>Item ' + (index + 1) + '</option>';
  }).join('');
  const current = rows[selectedIndex];
  const question = typeof current === 'object' && current !== null ? current : {question: current};
  visual.innerHTML = (function() {
    const choices = Array.isArray(question.choices) ? question.choices : [];
    return '<div class="question-card" data-index="' + selectedIndex + '">' +
      '<div class="question-card__heading"><strong>Item ' + (selectedIndex + 1) + '</strong><button type="button" class="btn btn--ghost btn--sm remove-question">Remove item</button></div>' +
      '<div class="form-group"><label class="form-label">Question or explanation</label><textarea class="form-input field-question" rows="3">' + esc(question.question || question.prompt || question.text || '') + '</textarea></div>' +
      '<div class="form-group"><label class="form-label">Answer / notes</label><textarea class="form-input field-answer" rows="2">' + esc(question.answer || question.explanation || question.correct_answer || '') + '</textarea></div>' +
      '<label class="form-label">Choices</label><div class="choices">' + choices.map(function(choice, choiceIndex) {
        return '<div class="choice-row"><input class="form-input field-choice" value="' + esc(choice) + '"><button type="button" class="btn btn--ghost btn--sm remove-choice">Remove</button></div>';
      }).join('') + '</div><button type="button" class="btn btn--ghost btn--sm add-choice">Add choice</button></div>';
  })();
}
function syncModel() {
  const card = visual.querySelector('.question-card');
  if (!card || !Array.isArray(model)) return;
  const old = model[selectedIndex] || {};
  const item = Object.assign({}, typeof old === 'object' && old !== null ? old : {});
  item.question = card.querySelector('.field-question').value;
  item.answer = card.querySelector('.field-answer').value;
  item.choices = Array.from(card.querySelectorAll('.field-choice')).map(function(input) { return input.value; }).filter(Boolean);
  model[selectedIndex] = item;
}
async function loadContent() {
  setStatus('Loading...', false);
  const response = await fetch('../api/get_content.php?part_id=' + encodeURIComponent(part.value) + '&type=' + type.value);
  const data = await response.json();
  if (!response.ok) throw new Error(data.error || 'Could not load content.');
  model = data; renderVisual(); setStatus('Content loaded.', false);
}
async function saveContent() {
  syncModel();
  const response = await fetch('../api/save_content.php', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({part_id:part.value,type:type.value,content:model})});
  const data = await response.json(); if (!response.ok) throw new Error(data.error || 'Could not save content.'); setStatus('Saved successfully.', false);
}
document.getElementById('load').addEventListener('click', () => loadContent().catch(error => setStatus(error.message, true)));
document.getElementById('save').addEventListener('click', () => saveContent().catch(error => setStatus(error.message, true)));
itemSelector.addEventListener('change', function() { syncModel(); selectedIndex = Number(itemSelector.value); renderVisual(); });
document.getElementById('add-question').addEventListener('click', function() { syncModel(); if (!Array.isArray(model)) model = []; model.push({question:'', answer:'', choices:['']}); selectedIndex = model.length - 1; renderVisual(); });
visual.addEventListener('click', function(event) {
  const card = event.target.closest('.question-card'); if (!card) return;
  if (event.target.classList.contains('remove-question')) { syncModel(); model.splice(Number(card.dataset.index), 1); selectedIndex = Math.max(0, selectedIndex - 1); renderVisual(); }
  if (event.target.classList.contains('add-choice')) { syncModel(); model[Number(card.dataset.index)].choices.push(''); renderVisual(); }
  if (event.target.classList.contains('remove-choice')) { syncModel(); const choices = model[Number(card.dataset.index)].choices; choices.splice(Array.from(card.querySelectorAll('.choice-row')).indexOf(event.target.closest('.choice-row')), 1); renderVisual(); }
});
loadContent().catch(error => setStatus(error.message, true));
</script>
</body>
</html>
