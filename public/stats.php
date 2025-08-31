<?php
declare(strict_types=1);
require __DIR__ . '/../db.php';
require __DIR__ . '/../helpers.php';
$cfg = (require __DIR__ . '/../config.php');

$title = 'Stats · ' . $cfg['app']['name'];

$total  = (int)$pdo->query('SELECT COUNT(*) FROM tasks')->fetchColumn();
$done   = (int)$pdo->query('SELECT COUNT(*) FROM tasks WHERE is_done=1')->fetchColumn();
$open   = $total - $done;
$recent = $pdo->query('SELECT id, title, is_done, created_at FROM tasks ORDER BY created_at DESC LIMIT 5')->fetchAll();

ob_start(); ?>
<div class="badges">
  <div class="badge"><strong><?= $total ?></strong> total tasks</div>
  <div class="badge"><strong><?= $open ?></strong> open</div>
  <div class="badge"><strong><?= $done ?></strong> done</div>
</div>

<h2 style="margin:18px 0 8px 0;font-size:16px">Recently added</h2>
<ul class="list">
  <?php if (!$recent): ?>
    <li class="empty">No recent tasks.</li>
  <?php else: foreach ($recent as $t): ?>
    <li class="item">
      <div class="tick <?= $t['is_done'] ? 'done' : '' ?>"><?= $t['is_done'] ? '✔' : '' ?></div>
      <div class="title <?= $t['is_done'] ? 'done' : '' ?>"><?= e($t['title']) ?></div>
      <div class="meta"><?= date('M j, Y · H:i', strtotime($t['created_at'])) ?></div>
      <a class="btn ghost" href="<?= e(base_url('edit.php?id='.(int)$t['id'])) ?>">Edit</a>
    </li>
  <?php endforeach; endif; ?>
</ul>
<?php
$content = ob_get_clean();
include __DIR__ . '/_layout.php';
