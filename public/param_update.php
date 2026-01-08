<?php
declare(strict_types=1);
require __DIR__ . '/../src/db.php';

$allowed = [
  'services' => [
    'pk' => 'service_id',
    'fields' => ['service_name','service_category','is_chargeable']
  ],
  // можете додати інші таблиці аналогічно:
  // 'buildings' => ['pk'=>'building_id','fields'=>['building_name','hotel_class_stars','floors_count','address']]
];

$msg = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $table = (string)($_POST['table'] ?? '');
    $field = (string)($_POST['field'] ?? '');
    $id    = (int)($_POST['id'] ?? 0);
    $value = (string)($_POST['value'] ?? '');

    if (!isset($allowed[$table])) {
        $err = 'Таблиця не дозволена.';
    } elseif (!in_array($field, $allowed[$table]['fields'], true)) {
        $err = 'Поле не дозволене.';
    } elseif ($id <= 0) {
        $err = 'Некоректний ID.';
    } else {
        $pk = $allowed[$table]['pk'];
        $sql = "UPDATE `$table` SET `$field` = :val WHERE `$pk` = :id";
        $pdo = db();
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':val' => $value, ':id' => $id]);
        $msg = "Оновлено рядків: " . $stmt->rowCount();
    }
}

$tableKeys = array_keys($allowed);
$defaultTable = $tableKeys[0];
?>
<!doctype html>
<html lang="uk">
<head>
  <meta charset="utf-8">
  <title>Параметричний UPDATE</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
  <a href="services.php">← Назад</a>
  <h1>Параметричний UPDATE (зміна будь-якого поля)</h1>

  <div class="card">
    <?php if ($err): ?><p style="color:#b00"><?= htmlspecialchars($err) ?></p><?php endif; ?>
    <?php if ($msg): ?><p style="color:#060"><?= htmlspecialchars($msg) ?></p><?php endif; ?>

    <form method="post">
      <label>Таблиця</label>
      <select name="table" required>
        <?php foreach ($allowed as $t => $_): ?>
          <option value="<?= htmlspecialchars($t) ?>"><?= htmlspecialchars($t) ?></option>
        <?php endforeach; ?>
      </select>

      <label>Поле</label>
      <select name="field" required>
        <?php foreach ($allowed[$defaultTable]['fields'] as $f): ?>
          <option value="<?= htmlspecialchars($f) ?>"><?= htmlspecialchars($f) ?></option>
        <?php endforeach; ?>
      </select>

      <label>ID (PK)</label>
      <input type="number" name="id" min="1" required>

      <label>Нове значення</label>
      <input name="value" required>

      <button type="submit">Оновити</button>
    </form>

    <p class="note">Для розширення додайте таблиці/поля у whitelist вгорі файлу.</p>
  </div>
</div>
</body>
</html>
