<?php
header("Content-Type: application/json");
require __DIR__ . '/../../vendor/autoload.php';

use App\DatabaseManager;
use App\EventService;
use App\TokenManager;

$databaseManager = new DatabaseManager();
$eventService = new EventService($databaseManager);
$tokenManager = new TokenManager($databaseManager);

try {
    $tokenData = $tokenManager->validateToken();
    $user_id = $tokenData->user_id;
    $user_role = $tokenData->role;
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(["status" => $e->getCode(), "message" => $e->getMessage()]);
    exit();
}

if ($user_role !== 'admin') {
    http_response_code(403);
    echo json_encode(["status" => "error", "message" => "Not authorized to create events"]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid JSON data: " . json_last_error_msg()]);
    exit();
}

if (!isset($data['id']) || !isset($data['title']) || !isset($data['start_date']) || !isset($data['end_date'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid data"]);
    exit();
}

$id = intval($data['id']);

$title = $data['title'];
try {
    $start_date = DateTime::createFromFormat('Y-m-d H:i:s', $data['start_date']) ?: Datetime::createFromFormat('Y-m-d H:i:s', $data['start_date'] . ' 00:00:00');
    $end_date = DateTime::createFromFormat('Y-m-d H:i:s', $data['end_date']) ?: Datetime::createFromFormat('Y-m-d H:i:s', $data['end_date'] . ' 00:00:00');

    if (!$start_date || !$end_date) {
        throw new Exception("Invalid date format");
    }

    if ($start_date > $end_date) {
        throw new Exception("Start time cannot be greater than end time");
    }

    $start_date = $start_date->format('Y-m-d H:i:s');
    $end_date = $end_date->format('Y-m-d H:i:s');
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    exit();
}

$content = isset($data['content']) ? $data['content'] : null;

$result = $eventService->updateEvent($id, $title, $start_date, $end_date, $content);

if ($result['status'] !== 'success') {
    http_response_code(500);
}
echo json_encode($result);