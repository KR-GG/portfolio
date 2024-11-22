<?php
header("Content-Type: application/json");
require __DIR__ . '/../../vendor/autoload.php';

use App\DatabaseManager;
use App\EventService;

$databaseManager = new DatabaseManager();
$eventService = new EventService($databaseManager);

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
    exit();
}

$result = $eventService->getAllEvents();

if ($result['status'] !== 'success') {
    http_response_code(500);
}
echo json_encode($result);