<?php
header("Content-Type: application/json");
require __DIR__ . '/../../vendor/autoload.php';

use App\DatabaseManager;
use App\TokenManager;
use App\IdeaService;

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

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id']) || !isset($data['title']) || !isset($data['content'])) {
    echo json_encode(["status" => "error", "message" => "Invalid data"]);
    exit();
}

$id = intval($data['id']);
$response = $ideaService->getIdeaById($id);

if ($response['status'] !== 'success') {
    http_response_code(404);
    echo json_encode(["status" => "error", "message" => "Idea not found"]);
    exit();
}

$idea = $response['message'];

if ($idea['user_id'] !== $user_id) {
    http_response_code(403);
    echo json_encode(["status" => "error", "message" => "Not authorized to update this idea"]);
    exit();
}

$result = $ideaService->updateIdea($id, $data['title'], $data['content']);
if ($result['status'] === 'success') {
    echo json_encode(["status" => "success", "message" => "Idea updated successfully"]);
} else {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Failed to update idea"]);
}