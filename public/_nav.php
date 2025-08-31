<?php
$cfg = (require __DIR__ . '/../config.php');
$base = rtrim($cfg['app']['base_path'], '/');
$active = basename($_SERVER['SCRIPT_NAME']);
function nav($href, $label, $activeFile){
  $isActive = basename($href) === $activeFile ? 'active' : '';
  return '<a class="'.$isActive.'" href="'.$href.'">'.htmlspecialchars($label, ENT_QUOTES).'</a>';
}
?>
<nav class="nav" aria-label="Primary">
  <?= nav($base.'/index.php', 'Tasks', $active) ?>
  <?= nav($base.'/create.php', 'Add Task', $active) ?>
  <?= nav($base.'/stats.php', 'Stats', $active) ?>
</nav>
