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
    $user_role = $tokenData->role;
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(["status" => $e->getCode(), "message" => $e->getMessage()]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
    exit();
}

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "ID is required"]);
    exit();
}

$id = intval($_GET['id']);
$response = $ideaService->getIdeaById($id);

if ($response['status'] !== 'success') {
    http_response_code(404);
    echo json_encode(["status" => "error", "message" => "Idea not found"]);
    exit();
}

$idea = $response['message'];

if ($idea['user_id'] !== $user_id && $user_role !== 'admin') {
    http_response_code(403);
    echo json_encode(["status" => "error", "message" => "Not authorized to delete this idea"]);
    exit();
}

$result = $ideaService->deleteIdea($id);
if ($result['status'] === 'success') {
    echo json_encode(["status" => "success", "message" => "Idea deleted successfully"]);
} else {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Failed to delete idea"]);
}