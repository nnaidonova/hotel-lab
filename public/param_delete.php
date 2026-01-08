<?php
declare(strict_types=1);
require __DIR__ . '/../src/db.php';

$allowed = [
  'services' => ['pk' => 'service_id'],
  // додайте інші таблиці при потребі:
  // 'buildings' => ['pk'=>'building_id'],
];

$msg = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $table = (string)($_POST['table'] ?? '');
    $id    = (int)($_POST['id'] ?? 0);

    if (!isset($allowed[$table])) {
        $err = 'Таблиця не дозволена.';
    } elseif ($id <= 0) {
        $err = 'Некоректний ID.';
    } else {
        $pk = $allowed[$table]['pk'];
        $sql = "DELETE FROM `$table` WHERE `$pk` = :id";
        $pdo = db();
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $msg = "Видалено рядків: " . $stmt->rowCount();
    }
}
?>
<!doctype html>
<html lang="uk">
<head>
  <meta charset="utf-8">
  <title>Параметричний DELETE</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
  <a href="services.php">← Назад</a>
  <h1>Параметричний DELETE</h1>

  <div class="card">
    <?php if ($err): ?><p style="color:#b00"><?= htmlspecialchars($err) ?></p><?php endif; ?>
    <?php if ($msg): ?><p style="color:#060"><?= htmlspecialchars($msg) ?></p><?php endif; ?>

    <form method="post" onsubmit="return confirm('Точно видалити запис?');">
      <label>Таблиця</label>
      <select name="table" required>
        <?php foreach ($allowed as $t => $_): ?>
          <option value="<?= htmlspecialchars($t) ?>"><?= htmlspecialchars($t) ?></option>
        <?php endforeach; ?>
      </select>

      <label>ID (PK)</label>
      <input type="number" name="id" min="1" required>

      <button type="submit">Видалити</button>
    </form>
  </div>
</div>
</body>
</html>
