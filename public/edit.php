<?php
declare(strict_types=1);
require __DIR__ . '/../db.php';
require __DIR__ . '/../helpers.php';
$cfg = (require __DIR__ . '/../config.php');

$id = max(0, (int)($_GET['id'] ?? 0));
$st = $pdo->prepare('SELECT id, title, is_done FROM tasks WHERE id = :id');
$st->execute([':id' => $id]);
$task = $st->fetch();

$title = 'Edit Task · ' . $cfg['app']['name'];
ob_start(); ?>

<?php if (!$task): ?>
  <div class="flash">⚠️ Task not found.</div>
  <a class="btn btn--ghost" href="<?= e(base_url('index.php')) ?>">
    <span class="btn__icon" aria-hidden="true">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M10 19l-7-7 7-7M3 12h18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </span>
    <span class="btn__label">Back to list</span>
  </a>
<?php else: ?>
  <form class="controls" method="post" action="<?= e(base_url('../actions.php')) ?>">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <input type="hidden" name="_action" value="update">
    <input type="hidden" name="id" value="<?= (int)$task['id'] ?>">
    <input type="hidden" name="_back" value="<?= e(base_url('index.php')) ?>">

    <input class="input" type="text" name="title" value="<?= e($task['title']) ?>" maxlength="255" required>

    <button class="btn btn--primary">
      <span class="btn__icon" aria-hidden="true">
        <!-- save/check -->
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
          <path d="M20 7l-9 9-4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </span>
      <span class="btn__label">Save</span>
    </button>

    <a class="btn btn--ghost" href="<?= e(base_url('index.php')) ?>">
      <span class="btn__icon" aria-hidden="true">
        <!-- x/cancel -->
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
          <path d="M6 6l12 12M6 18L18 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
      </span>
      <span class="btn__label">Cancel</span>
    </a>
  </form>

  <form method="post" action="<?= e(base_url('../actions.php')) ?>" onsubmit="return confirm('Delete this task?');" class="controls">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <input type="hidden" name="_action" value="delete">
    <input type="hidden" name="id" value="<?= (int)$task['id'] ?>">
    <input type="hidden" name="_back" value="<?= e(base_url('index.php')) ?>">
    <button class="btn btn--danger">
      <span class="btn__icon" aria-hidden="true">
        <!-- trash -->
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
          <path d="M3 6h18M8 6V4h8v2m-1 0v13a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2V6h10Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </span>
      <span class="btn__label">Delete Task</span>
    </button>
  </form>
<?php endif; ?>

<?php
$content = ob_get_clean();
include __DIR__ . '/_layout.php';
