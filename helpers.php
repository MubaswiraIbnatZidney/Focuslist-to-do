<?php
// helpers.php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

function e(string $v): string {
  return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function csrf_token(): string {
  if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
  return $_SESSION['csrf_token'];
}
function csrf_check(): void {
  $ok = isset($_POST['csrf']) && hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf']);
  if (!$ok) { http_response_code(419); exit('CSRF token mismatch.'); }
}
function base_url(string $path = ''): string {
  $cfg = (require __DIR__ . '/config.php');
  return rtrim($cfg['app']['base_path'], '/') . '/' . ltrim($path, '/');
}
function flash(?string $msg = null, string $type = 'ok'): ?string {
  if ($msg !== null) { $_SESSION['flash'][$type] = $msg; return null; }
  $messages = $_SESSION['flash'] ?? [];
  unset($_SESSION['flash']);
  $out = '';
  foreach ($messages as $t => $m) {
    $cls = $t === 'ok' ? 'flash-ok' : 'flash';
    $icon = $t === 'ok' ? '✅' : '⚠️';
    $out .= "<div class=\"$cls\">$icon " . e($m) . "</div>";
  }
  return $out;
}
function paginate(int $total, int $page, int $per): array {
  $pages = max(1, (int)ceil($total / $per));
  $page = max(1, min($page, $pages));
  $offset = ($page - 1) * $per;
  return [$page, $pages, $offset];
}
