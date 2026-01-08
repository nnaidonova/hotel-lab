<?php
declare(strict_types=1);
?>
<!doctype html>
<html lang="uk">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Hotel Complex — Головне меню</title>
  <link rel="stylesheet" href="style.css">
  <style>
    /* якщо у вас style.css вже гарний — можна цей блок прибрати */
    .grid { display: grid; gap: 12px; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); }
    .card a { display:block; padding: 10px 12px; text-decoration:none; }
    .card a:hover { opacity: .9; }
    .muted { opacity: .75; font-size: 0.95em; }
    .title { display:flex; justify-content:space-between; align-items:baseline; gap:12px; flex-wrap:wrap; }
    .badge { padding: 4px 10px; border-radius: 999px; background: rgba(0,0,0,.06); }
    ul { margin: 8px 0 0 18px; }
  </style>
</head>

<body>
<div class="container">
  <div class="title">
    <h1>Hotel Complex — Головне меню</h1>
    <span class="badge">Lab: CRUD + SQL</span>
  </div>

  <p class="muted">
    Тут зібрані всі сторінки для керування даними (INSERT/UPDATE/DELETE) та демонстрації запитів.
  </p>

  <div class="grid">

    <div class="card">
      <h2>Послуги (CRUD)</h2>
      <ul>
        <li><a href="services.php">Перегляд списку послуг</a></li>
        <li><a href="service_create.php">Додати послугу (INSERT)</a></li>
        <li><a href="param_update.php">Параметричний UPDATE (зміна будь-якого поля)</a></li>
        <li><a href="param_delete.php">Параметричний DELETE (видалення за параметром)</a></li>
        <li><a href="update_last_service.php">Змінити останній введений запис</a></li>
      </ul>
    </div>

    <div class="card">
      <h2>Запити (SELECT)</h2>
      <p class="muted">Якщо у вас є сторінка перегляду запитів.</p>
      <ul>
        <li><a href="index_queries.php">Перегляд результатів запитів (якщо файл існує)</a></li>
        <li><a href="run.php">Виконання обраного запиту (якщо використовуєте run.php напряму)</a></li>
      </ul>
      <p class="muted">
        Якщо цих файлів немає у вашому проєкті — просто видаліть ці пункти.
      </p>
    </div>

    <div class="card">
      <h2>Адміністрування</h2>
      <ul>
        <li><a href="http://localhost:8081" target="_blank" rel="noreferrer">phpMyAdmin (8081)</a></li>
      </ul>
      <p class="muted">Відкриється в новій вкладці.</p>
    </div>

  </div>

  <div class="card" style="margin-top:14px;">
    <h2>Підказка</h2>
    <p class="muted">
      Якщо якась сторінка видає 404 — перевірте, що файл лежить саме в <code>public/</code>.
    </p>
  </div>
</div>
</body>
</html>
