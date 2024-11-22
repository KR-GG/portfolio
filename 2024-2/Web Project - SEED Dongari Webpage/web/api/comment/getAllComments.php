<?php
//모든 댓글을 조회하는 ? maybe 관리자용 ?
header("Content-Type: application/json");
require __DIR__ . '/../../vendor/autoload.php';

use \App\DatabaseManager;
use \App\TokenManager;
use \App\CommentService;

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

if ($user_role !== 'admin') {
    http_response_code(403);
    echo json_encode(["status" => "error", "message" => "Not authorized to view all comments"]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
    exit();
}

$result = $commentService->getAllComments();

if ($result['status'] !== 'success') {
    http_response_code(500);
}
echo json_encode($result);


