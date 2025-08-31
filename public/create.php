<?php
require __DIR__ . '/../helpers.php';
$cfg = (require __DIR__ . '/../config.php');
$title = 'Add Task · ' . $cfg['app']['name'];

ob_start(); ?>
<form class="controls" method="post" action="<?= e(base_url('../actions.php')) ?>">
  <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
  <input type="hidden" name="_action" value="create">
  <input type="hidden" name="_back" value="<?= e(base_url('index.php')) ?>">

  <input class="input" type="text" name="title" placeholder="What do you need to do?" maxlength="255" required>

  <button class="btn btn--primary">
    <span class="btn__icon" aria-hidden="true">
      <!-- plus -->
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
    </span>
    <span class="btn__label">Add Task</span>
  </button>

  <a class="btn btn--ghost" href="<?= e(base_url('index.php')) ?>">
    <span class="btn__icon" aria-hidden="true">
      <!-- back/arrow-left -->
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M10 19l-7-7 7-7M3 12h18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </span>
    <span class="btn__label">Back</span>
  </a>
</form>
<?php
$content = ob_get_clean();
include __DIR__ . '/_layout.php';
