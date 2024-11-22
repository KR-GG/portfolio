<?php
header("Content-Type: application/json");
require __DIR__ . '/../../vendor/autoload.php';

use App\DatabaseManager;
use App\IdeaService;
use App\TokenManager;

$databaseManager = new DatabaseManager();
$tokenManager = new TokenManager($databaseManager);
$ideaService = new IdeaService($databaseManager);

try {
    $tokenData = $tokenManager->validateToken();
    $user_id = $tokenData->user_id;
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(["status" => $e->getCode(), "message" => $e->getMessage()]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Invalid request method: " . $_SERVER['REQUEST_METHOD']]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

// Check if JSON data is parsed correctly
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid JSON data: " . json_last_error_msg()]);
    exit();
}

if (!isset($data['title']) || !isset($data['content'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid data"]);
    exit();
}

$response = $ideaService->saveIdea($data['title'], $data['content'], $user_id);
if ($response['status'] !== 'success') {
    http_response_code(500);
}
echo json_encode($response);