<?php
declare(strict_types=1);
require __DIR__ . '/../src/db.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string)($_POST['service_name'] ?? ''));
    $cat  = (string)($_POST['service_category'] ?? '');
    $chg  = isset($_POST['is_chargeable']) ? 1 : 0;

    if ($name === '') $errors[] = "Назва обов'язкова.";

    $allowedCats = ['HOUSEKEEPING','LAUNDRY','FOOD','ENTERTAINMENT','OTHER'];
    if (!in_array($cat, $allowedCats, true)) $errors[] = "Некоректна категорія.";

    if (!$errors) {
        $pdo = db();
        $stmt = $pdo->prepare("INSERT INTO services(service_name, service_category, is_chargeable) VALUES (:n,:c,:ch)");
        $stmt->execute([':n'=>$name, ':c'=>$cat, ':ch'=>$chg]);
        header('Location: services.php');
        exit;
    }
}
?>
<!doctype html>
<html lang="uk">
<head>
  <meta charset="utf-8">
  <title>Додати послугу</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
  <a href="services.php">← Назад</a>
  <h1>Додати послугу</h1>

  <div class="card">
    <?php foreach ($errors as $e): ?>
      <p style="color:#b00"><?= htmlspecialchars($e) ?></p>
    <?php endforeach; ?>

    <form method="post">
      <label>Назва</label>
      <input name="service_name" required>

      <label>Категорія</label>
      <select name="service_category" required>
        <option value="HOUSEKEEPING">HOUSEKEEPING</option>
        <option value="LAUNDRY">LAUNDRY</option>
        <option value="FOOD">FOOD</option>
        <option value="ENTERTAINMENT">ENTERTAINMENT</option>
        <option value="OTHER">OTHER</option>
      </select>

      <label>
        <input type="checkbox" name="is_chargeable" checked>
        Платна послуга
      </label>

      <button type="submit">Зберегти</button>
    </form>
  </div>
</div>
</body>
</html>
