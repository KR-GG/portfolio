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

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') { // DELETE요청이 아닌 경우 에러
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
    exit();
}

if (!isset($_GET['id'])) {  // id를 받아왔는지 확인
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "ID is required"]);
    exit();  //Id없으면 에러메세지를 반환
}

$id = intval($_GET['id']);
$response = $commentService->getCommentById($id); //id로 댓글을 찾아옴

if ($response['status'] !== 'success') { //댓글이 없는 경우 에러
    http_response_code(404);
    echo json_encode(["status" => "error", "message" => "Comment not found"]);
    exit();
}

$comment = $response['message'];

if ($comment['user_id'] !== $user_id && $user_role !== 'admin') { //댓글을 쓴 유저가 아닌경우 에러
    http_response_code(403);
    echo json_encode(["status" => "error", "message" => "Not authorized to delete this comment"]);
    exit();
}

$result = $commentService->deleteComment($id); //댓글 삭제
if ($result['status'] === 'success') {
    echo json_encode(["status" => "success", "message" => "Comment deleted successfully"]);
} else {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Failed to delete comment"]);
}