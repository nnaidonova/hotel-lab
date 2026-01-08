<?php
declare(strict_types=1);
require __DIR__ . '/../src/db.php';

$msg = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newName = trim((string)($_POST['service_name'] ?? ''));
    if ($newName === '') {
        $err = "Назва обов'язкова.";
    } else {
        $pdo = db();
        $stmt = $pdo->prepare("
            UPDATE services
            SET service_name = :n
            ORDER BY service_id DESC
            LIMIT 1
        ");
        $stmt->execute([':n' => $newName]);
        $msg = "Оновлено рядків: " . $stmt->rowCount();
    }
}
?>
<!doctype html>
<html lang="uk">
<head>
  <meta charset="utf-8">
  <title>Змінити останній запис</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
  <a href="services.php">← Назад</a>
  <h1>Зміна останнього введеного запису (services)</h1>

  <div class="card">
    <?php if ($err): ?><p style="color:#b00"><?= htmlspecialchars($err) ?></p><?php endif; ?>
    <?php if ($msg): ?><p style="color:#060"><?= htmlspecialchars($msg) ?></p><?php endif; ?>

    <form method="post">
      <label>Нова назва для останньої послуги</label>
      <input name="service_name" required>
      <button type="submit">Застосувати</button>
    </form>

    <details style="margin-top:12px;">
      <summary>SQL, який виконується (для звіту)</summary>
      <pre>UPDATE services SET service_name = ? ORDER BY service_id DESC LIMIT 1;</pre>
    </details>
  </div>
</div>
</body>
</html>
