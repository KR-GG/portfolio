<?php
header("Content-Type: application/json");
require __DIR__ . '/../../vendor/autoload.php';

use App\DatabaseManager;
use App\TokenManager;
use App\IdeaService;
use App\CommentService;

$databaseManager = new DatabaseManager();
$tokenManager = new TokenManager($databaseManager);
$ideaService = new IdeaService($databaseManager);
$commentService = new CommentService($databaseManager);

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
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid JSON data: " . json_last_error_msg()]);
    exit();
}

if (!isset($data['idea_id']) || !isset($data['content'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid data"]);
    exit();
}

$response = $ideaService->getIdeaById($data['idea_id']);
if ($response['status'] !== 'success') {
    http_response_code(404);
    echo json_encode(["status" => "error", "message" => "Idea not found"]);
    exit();
}

$author_id = $response['message']['user_id'];

$response = $commentService->postComment($data['idea_id'], $user_id, $author_id, $data['content']);
if ($response['status'] !== 'success') {
    http_response_code(500);
}
echo json_encode($response);