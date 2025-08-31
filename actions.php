<?php
// actions.php
declare(strict_types=1);
require __DIR__ . '/db.php';
require __DIR__ . '/helpers.php';

csrf_check();
$action = $_POST['_action'] ?? '';
$back   = $_POST['_back'] ?? base_url('index.php');

try {
  if ($action === 'create') {
    $title = trim($_POST['title'] ?? '');
    if ($title === '' || mb_strlen($title) > 255) throw new RuntimeException('Title is required (max 255).');
    $stmt = $pdo->prepare('INSERT INTO tasks (title) VALUES (:t)');
    $stmt->execute([':t' => $title]);
    flash('Task added.');
  } elseif ($action === 'update') {
    $id = (int)($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    if ($id <= 0 || $title === '' || mb_strlen($title) > 255) throw new RuntimeException('Invalid update.');
    $stmt = $pdo->prepare('UPDATE tasks SET title = :t WHERE id = :id');
    $stmt->execute([':t' => $title, ':id' => $id]);
    flash('Task updated.');
  } elseif ($action === 'toggle') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) throw new RuntimeException('Invalid toggle.');
    $stmt = $pdo->prepare('UPDATE tasks SET is_done = CASE WHEN is_done=1 THEN 0 ELSE 1 END WHERE id = :id');
    $stmt->execute([':id' => $id]);
    flash('Task status changed.');
  } elseif ($action === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) throw new RuntimeException('Invalid delete.');
    $stmt = $pdo->prepare('DELETE FROM tasks WHERE id = :id');
    $stmt->execute([':id' => $id]);
    flash('Task deleted.');
  } else {
    throw new RuntimeException('Unknown action.');
  }
} catch (Throwable $e) {
  flash($e->getMessage(), 'err');
}
header('Location: ' . $back);
exit;
