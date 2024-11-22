<?php
header("Content-Type: application/json");
require __DIR__ . '/../../vendor/autoload.php';

use App\DatabaseManager;
use App\TokenManager;
use App\CommentService;

$databaseManager = new DatabaseManager();
$tokenManager = new TokenManager($databaseManager);
$commentService = new CommentService($databaseManager);

try {
    $tokenData = $tokenManager->validateToken();
    $user_id = $tokenData->user_id;
    $user_role = $tokenData->role;
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

if (!isset($data['id']) || !isset($data['content'])) {
    echo json_encode(["status" => "error", "message" => "Invalid data"]);
    exit();
}

$id = intval($data['id']);
$result = $commentService->updateComment($id, $data['content']);
if ($result['status'] !== 'success') {
    http_response_code(500);
}
echo json_encode($result);