<?php
declare(strict_types=1);

require __DIR__ . '/../src/db.php';
$queries = require __DIR__ . '/../src/queries.php';

$queries = require __DIR__ . '/../src/queries.php';
$key = (string)($_GET['q'] ?? '');

if (!isset($queries[$key])) {
    http_response_code(400);
    echo "Невідомий запит.";
    exit;
}

$q = $queries[$key];
$sql = $q['sql'];
$paramsSpec = $q['params'];

$params = [];
foreach ($paramsSpec as $name => $type) {
    $raw = $_GET[$name] ?? null;

    if ($raw === null || $raw === '') {
        http_response_code(400);
        echo "Не задано обов'язковий параметр: " . htmlspecialchars($name);
        exit;
    }

    switch ($type) {
        case 'int':
            $params[":$name"] = (int)$raw;
            break;
        case 'date':
            // мінімальна валідація формату YYYY-MM-DD
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)$raw)) {
                http_response_code(400);
                echo "Некоректний формат дати для параметра: " . htmlspecialchars($name);
                exit;
            }
            $params[":$name"] = (string)$raw;
            break;
        default:
            $params[":$name"] = (string)$raw;
    }
}

try {
    $pdo = db();
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();
} catch (Throwable $e) {
    http_response_code(500);
    echo "Помилка виконання запиту: " . htmlspecialchars($e->getMessage());
    exit;
}

function h(string $s): string { return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }

?>
<!doctype html>
<html lang="uk">
<head>
  <meta charset="utf-8">
  <title>Результат запиту</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
    <a href="index.php" class="back">← Назад</a>
    <h1><?= h($q['title']) ?></h1>

    <div class="card">
      <div class="meta">
        <div><b>Запит:</b> <?= h($key) ?></div>
        <div><b>Рядків:</b> <?= count($rows) ?></div>
      </div>

      <?php if (count($rows) === 0): ?>
        <p>Немає даних для відображення.</p>
      <?php else: ?>
        <table>
          <thead>
            <tr>
              <?php foreach (array_keys($rows[0]) as $col): ?>
                <th><?= h((string)$col) ?></th>
              <?php endforeach; ?>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($rows as $r): ?>
              <tr>
                <?php foreach ($r as $val): ?>
                  <td><?= h((string)($val ?? '')) ?></td>
                <?php endforeach; ?>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>

    <details class="card">
      <summary>Показати SQL (для звіту)</summary>
      <pre><?= h(trim($sql)) ?></pre>
      <?php if (!empty($params)): ?>
        <pre><?= h(print_r($params, true)) ?></pre>
      <?php endif; ?>
    </details>
  </div>
</body>
</html>
