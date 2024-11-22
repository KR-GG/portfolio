<?php
header("Content-Type: application/json");
require __DIR__ . '/../../vendor/autoload.php';

use App\DatabaseManager;
use App\TokenManager;
use App\UserService;

$databaseManager = new DatabaseManager();
$tokenManager = new TokenManager($databaseManager);
$userService = new UserService($databaseManager);

// Respond with an error if the request is not a GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Invalid request method: " . $_SERVER['REQUEST_METHOD']]);
    exit();
}

// Get all pending users
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
    echo json_encode(["status" => "error", "message" => "Not authorized to view pending users"]);
    exit();
}

$result = $userService->getPendingUsers();

// Respond with the result
if ($result['status'] !== 'success') {
    // Database error
    http_response_code(500);
}
echo json_encode($result);