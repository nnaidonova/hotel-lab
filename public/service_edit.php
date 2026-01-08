<?php
declare(strict_types=1);
require __DIR__ . '/../src/db.php';

$id = (int)($_GET['id'] ?? 0);
$pdo = db();

$stmt = $pdo->prepare("SELECT * FROM services WHERE service_id = :id");
$stmt->execute([':id' => $id]);
$row = $stmt->fetch();

if (!$row) {
    http_response_code(404);
    echo "Запис не знайдено.";
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string)($_POST['service_name'] ?? ''));
    $cat  = (string)($_POST['service_category'] ?? '');
    $chg  = isset($_POST['is_chargeable']) ? 1 : 0;

    if ($name === '') $errors[] = "Назва обов'язкова.";
    $allowedCats = ['HOUSEKEEPING','LAUNDRY','FOOD','ENTERTAINMENT','OTHER'];
    if (!in_array($cat, $allowedCats, true)) $errors[] = "Некоректна категорія.";

    if (!$errors) {
        $upd = $pdo->prepare("UPDATE services SET service_name=:n, service_category=:c, is_chargeable=:ch WHERE service_id=:id");
        $upd->execute([':n'=>$name, ':c'=>$cat, ':ch'=>$chg, ':id'=>$id]);
        header('Location: services.php');
        exit;
    }
}
?>
<!doctype html>
<html lang="uk">
<head>
  <meta charset="utf-8">
  <title>Редагувати послугу</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
  <a href="services.php">← Назад</a>
  <h1>Редагувати послугу #<?= (int)$row['service_id'] ?></h1>

  <div class="card">
    <?php foreach ($errors as $e): ?>
      <p style="color:#b00"><?= htmlspecialchars($e) ?></p>
    <?php endforeach; ?>

    <form method="post">
      <label>Назва</label>
      <input name="service_name" value="<?= htmlspecialchars((string)$row['service_name']) ?>" required>

      <label>Категорія</label>
      <select name="service_category" required>
        <?php foreach (['HOUSEKEEPING','LAUNDRY','FOOD','ENTERTAINMENT','OTHER'] as $c): ?>
          <option value="<?= $c ?>" <?= $row['service_category']===$c?'selected':'' ?>><?= $c ?></option>
        <?php endforeach; ?>
      </select>

      <label>
        <input type="checkbox" name="is_chargeable" <?= $row['is_chargeable'] ? 'checked' : '' ?>>
        Платна послуга
      </label>

      <button type="submit">Оновити</button>
    </form>
  </div>
</div>
</body>
</html>
