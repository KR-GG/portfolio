<?php
header("Content-Type: application/json");
require __DIR__ . '/../../vendor/autoload.php';

use App\DatabaseManager;
use App\IdeaService;

$databaseManager = new DatabaseManager();
$ideaService = new IdeaService($databaseManager);

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Invalid request method: " . $_SERVER['REQUEST_METHOD']]);
    exit();
}

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "ID is required"]);
    exit();
}

$id = intval($_GET['id']);

$result = $ideaService->getIdeaById($id);
if ($result['status'] !== 'success') {
    http_response_code(500);
}
echo json_encode($result);