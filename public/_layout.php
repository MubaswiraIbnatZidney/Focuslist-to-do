<?php
$cfg = (require __DIR__ . '/../config.php');
$app = $cfg['app'];
$title = $title ?? $app['name'];
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= htmlspecialchars($title, ENT_QUOTES) ?></title>
<link rel="stylesheet" href="<?= htmlspecialchars($app['base_path'], ENT_QUOTES) ?>/assets/app.css">
</head>
<body>
  <div class="container">
    <div class="card">
      <div class="header">
        <div class="brand">
          <div class="logo" aria-hidden="true"></div>
          <h1><?= htmlspecialchars($app['name'], ENT_QUOTES) ?></h1>
        </div>
        <?php include __DIR__ . '/_nav.php'; ?>
      </div>
      <div class="content">
        <?= flash() ?>
        <?= $content ?? '' ?>
      </div>
    </div>
  </div>
<script src="<?= htmlspecialchars($app['base_path'], ENT_QUOTES) ?>/assets/app.js"></script>
</body>
</html>

