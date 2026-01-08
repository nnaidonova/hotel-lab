<?php
declare(strict_types=1);
$queries = require __DIR__ . '/../src/queries.php';
?>
<!doctype html>
<html lang="uk">
<head>
  <meta charset="utf-8">
  <title>Hotel Complex — SQL Viewer</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
    <h1>Перегляд результатів запитів (MySQL)</h1>

    <form method="get" action="run.php" class="card">
      <label for="q"><b>Оберіть запит:</b></label>
      <select id="q" name="q" required>
        <option value="" selected disabled>— оберіть —</option>
        <?php foreach ($queries as $key => $q): ?>
          <option value="<?= htmlspecialchars($key) ?>"><?= htmlspecialchars($q['title']) ?></option>
        <?php endforeach; ?>
      </select>

      <h2>Параметри (заповнюйте тільки якщо вони потрібні)</h2>

      <div class="grid">
        <div>
          <label>Зірки (stars)</label>
          <input type="number" name="stars" min="2" max="5" placeholder="Напр. 5">
        </div>
        <div>
          <label>Місткість (capacity)</label>
          <input type="number" name="capacity" min="1" max="6" placeholder="Напр. 2">
        </div>
        <div>
          <label>Період від (d1)</label>
          <input type="date" name="d1">
        </div>
        <div>
          <label>Період до (d2)</label>
          <input type="date" name="d2">
        </div>
        <div>
          <label>Мін. людей (min_people)</label>
          <input type="number" name="min_people" min="1" placeholder="Напр. 5">
        </div>
      </div>

      <button type="submit">Виконати запит</button>
    </form>

    <p class="note">
      Примітка: цей інтерфейс запускає лише “вбудовані” запити (білий список), щоб не виконувати довільний SQL з браузера.
    </p>
  </div>
</body>
</html>
