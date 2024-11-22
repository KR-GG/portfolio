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

if (!isset($_GET['event_id'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Event ID is required"]);
    exit();
}

$event_id = intval($_GET['event_id']);

$result = $eventService->getEventById($event_id);
if ($result['status'] !== 'success') {
    http_response_code(500);
}
echo json_encode($result);