<?php
header("Content-Type: application/json");
require __DIR__ . '/../../vendor/autoload.php';

use App\DatabaseManager;
use App\TokenManager;
use App\UserService;
use App\Role;

$databaseManager = new DatabaseManager();
$tokenManager = new TokenManager($databaseManager);
$userService = new UserService($databaseManager);

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
    echo json_encode(["status" => "error", "message" => "Not authorized to approve user registration"]);
    exit();
}

// Respond with an error if the request is not a POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Invalid request method: " . $_SERVER['REQUEST_METHOD']]);
    exit();
}

// Retrieve JSON data
$json = file_get_contents("php://input");
$data = json_decode($json, true);

// Check if JSON data is parsed correctly
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid JSON data: " . json_last_error_msg()]);
    exit();
}

// If data is received correctly, process it in the database
if(isset($data['user_id'])) {
    $user_id = $data['user_id'];
    $result = $userService->changeUserRole($user_id, Role::USER->value);

    // Success response or Database error
    if($result['status'] !== 'success') {
        http_response_code(500);
    }
    echo json_encode($result);
} else {
    // Parameter name is not correct
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid data"]);
}