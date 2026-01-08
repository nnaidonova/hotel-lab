<?php
declare(strict_types=1);
require __DIR__ . '/../src/db.php';

$pdo = db();
$rows = $pdo->query("SELECT service_id, service_name, service_category, is_chargeable FROM services ORDER BY service_id ASC")->fetchAll();
?>
<!doctype html>
<html lang="uk">
<head>
  <meta charset="utf-8">
  <title>Services</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
  <h1>Послуги</h1>

  <div class="card">
    <a href="service_create.php">+ Додати послугу</a>
    &nbsp;|&nbsp;
    <a href="param_update.php">Параметричний UPDATE</a>
    &nbsp;|&nbsp;
    <a href="param_delete.php">Параметричний DELETE</a>
    &nbsp;|&nbsp;
    <a href="update_last_service.php">Змінити останню послугу</a>
  </div>

  <div class="card">
    <?php if (!$rows): ?>
      <p>Немає записів.</p>
    <?php else: ?>
      <table>
        <thead>
          <tr>
            <th>ID</th><th>Назва</th><th>Категорія</th><th>Платна</th><th>Дії</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?= htmlspecialchars((string)$r['service_id']) ?></td>
            <td><?= htmlspecialchars((string)$r['service_name']) ?></td>
            <td><?= htmlspecialchars((string)$r['service_category']) ?></td>
            <td><?= $r['is_chargeable'] ? 'Так' : 'Ні' ?></td>
            <td>
              <a href="service_edit.php?id=<?= (int)$r['service_id'] ?>">Редагувати</a>
              |
              <form action="service_delete.php" method="post" style="display:inline" onsubmit="return confirm('Видалити запис?');">
                <input type="hidden" name="id" value="<?= (int)$r['service_id'] ?>">
                <button type="submit">Видалити</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>

</div>
</body>
</html>
