<?php
declare(strict_types=1);
require __DIR__ . '/../src/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

$id = (int)($_POST['id'] ?? 0);
$pdo = db();
$stmt = $pdo->prepare("DELETE FROM services WHERE service_id = :id");
$stmt->execute([':id' => $id]);

header('Location: services.php');
exit;
