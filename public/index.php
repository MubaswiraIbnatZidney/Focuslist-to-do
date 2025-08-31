<?php
declare(strict_types=1);
require __DIR__ . '/../db.php';
require __DIR__ . '/../helpers.php';
$cfg = (require __DIR__ . '/../config.php');

$title = 'Tasks · ' . $cfg['app']['name'];

/* ---- SAFE INPUTS ---- */
$q = trim($_GET['q'] ?? '');

$allowedSorts = ['new','old','az','za','open','done'];
$sortParam    = $_GET['sort'] ?? 'new';
$sort         = in_array($sortParam, $allowedSorts, true) ? $sortParam : 'new';

$page = max(1, (int)($_GET['page'] ?? 1));
$per  = (int)$cfg['app']['items_per_page'];

/* ---- FILTERS / WHERE ---- */
$where  = [];
$params = [];
if ($q !== '') { $where[] = 'title LIKE :q'; $params[':q'] = "%$q%"; }
if ($sort === 'open') { $where[] = 'is_done = 0'; }
if ($sort === 'done') { $where[] = 'is_done = 1'; }
$W = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

/* ---- ORDER ---- */
$order = match($sort){
  'old' => 'created_at ASC',
  'az'  => 'title ASC',
  'za'  => 'title DESC',
  default => 'created_at DESC'
};

/* ---- TOTAL / PAGINATION ---- */
$st = $pdo->prepare("SELECT COUNT(*) FROM tasks $W");
$st->execute($params);
$total = (int)$st->fetchColumn();

[$page, $pages, $offset] = paginate($total, $page, $per);

/* ---- FETCH PAGE ---- */
$sql = "SELECT id, title, is_done, created_at
        FROM tasks $W ORDER BY $order LIMIT :lim OFFSET :off";
$st2 = $pdo->prepare($sql);
foreach ($params as $k=>$v) $st2->bindValue($k, $v);
$st2->bindValue(':lim', $per, PDO::PARAM_INT);
$st2->bindValue(':off', $offset, PDO::PARAM_INT);
$st2->execute();
$tasks = $st2->fetchAll();

/* ---- STATS ---- */
$allCount  = (int)($pdo->query('SELECT COUNT(*) FROM tasks')->fetchColumn() ?: 0);
$openCount = (int)($pdo->query('SELECT COUNT(*) FROM tasks WHERE is_done=0')->fetchColumn() ?: 0);
$doneCount = $allCount - $openCount;

/* ---- VIEW ---- */
ob_start();
?>
<div class="badges" aria-label="Task stats">
  <div class="badge"><strong><?= $allCount ?></strong>&nbsp;total</div>
  <div class="badge"><strong><?= $openCount ?></strong>&nbsp;open</div>
  <div class="badge"><strong><?= $doneCount ?></strong>&nbsp;done</div>
</div>

<form class="controls" method="get" action="">
  <input class="input" type="search" name="q" value="<?= e($q) ?>" placeholder="Search tasks…">
  <select class="select" name="sort" title="Sort">
    <option value="new"  <?= $sort==='new'?'selected':''  ?>>Newest</option>
    <option value="old"  <?= $sort==='old'?'selected':''  ?>>Oldest</option>
    <option value="az"   <?= $sort==='az'?'selected':''   ?>>A → Z</option>
    <option value="za"   <?= $sort==='za'?'selected':''   ?>>Z → A</option>
    <option value="open" <?= $sort==='open'?'selected':'' ?>>Only Open</option>
    <option value="done" <?= $sort==='done'?'selected':'' ?>>Only Done</option>
  </select>
  <button class="btn btn--secondary">
    <span class="btn__icon" aria-hidden="true">
      <!-- filter icon -->
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M3 6h18M6 12h12M10 18h4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
    </span>
    <span class="btn__label">Apply</span>
  </button>
  <a class="btn btn--primary" href="<?= e(base_url('create.php')) ?>">
    <span class="btn__icon" aria-hidden="true">
      <!-- plus icon -->
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
    </span>
    <span class="btn__label">Add Task</span>
  </a>
</form>

<ul class="list" role="list">
  <?php if (!$tasks): ?>
    <li class="empty">No tasks found. Try removing filters or add a new task ✨</li>
  <?php endif; ?>

  <?php foreach ($tasks as $t): ?>
    <li class="item">
      <form method="post" action="<?= e(base_url('../actions.php')) ?>">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="_action" value="toggle">
        <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
        <input type="hidden" name="_back" value="<?= e($_SERVER['REQUEST_URI']) ?>">
        <button class="tick <?= $t['is_done'] ? 'done' : '' ?>" title="Toggle done" aria-label="Toggle done">
          <?= $t['is_done'] ? '✔' : '' ?>
        </button>
      </form>

      <div class="title <?= $t['is_done'] ? 'done' : '' ?>"><?= e($t['title']) ?></div>
      <div class="meta"><?= date('M j, Y · H:i', strtotime($t['created_at'])) ?></div>

      <div class="row">
        <a class="btn btn--secondary btn--sm" href="<?= e(base_url('edit.php?id='.(int)$t['id'])) ?>" title="Edit">
          <span class="btn__icon" aria-hidden="true">
            <!-- edit/pencil -->
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
              <path d="M12 20h9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
              <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
            </svg>
          </span>
          <span class="btn__label">Edit</span>
        </a>
        <form method="post" action="<?= e(base_url('../actions.php')) ?>" onsubmit="return confirm('Delete this task?');" style="display:inline">
          <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
          <input type="hidden" name="_action" value="delete">
          <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
          <input type="hidden" name="_back" value="<?= e($_SERVER['REQUEST_URI']) ?>">
          <button class="btn btn--danger btn--sm">
            <span class="btn__icon" aria-hidden="true">
              <!-- trash -->
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                <path d="M3 6h18M8 6V4h8v2m-1 0v13a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2V6h10Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </span>
            <span class="btn__label">Delete</span>
          </button>
        </form>
      </div>
    </li>
  <?php endforeach; ?>
</ul>

<?php if ($pages > 1): ?>
  <div class="controls" style="justify-content:flex-end">
    <?php for ($p=1;$p<=$pages;$p++):
      $qs = $_GET; $qs['page'] = $p; $url = '?'.http_build_query($qs); ?>
      <a class="btn <?= $p===$page?'btn--primary':'btn--ghost' ?>" href="<?= e($url) ?>"><?= $p ?></a>
    <?php endfor; ?>
  </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
include __DIR__ . '/_layout.php';
