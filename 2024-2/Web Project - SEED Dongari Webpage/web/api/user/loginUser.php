<?php
header("Content-Type: application/json");
require __DIR__ . '/../../vendor/autoload.php';

use App\DatabaseManager;
use App\UserService;
use App\Role;
use App\TokenManager;

$databaseManager = new DatabaseManager();
$tokenManager = new TokenManager($databaseManager);
$userService = new UserService($databaseManager);

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
if(isset($data['email']) && isset($data['password'])) {
    $email = $data['email'];
    $password = $data['password'];

    $response = $userService->getUserByEmail($email);

    if ($response['status'] !== 'success') {
        http_response_code(401);
        echo json_encode(["status" => "error", "message" => "Invalid credentials1"]);
        exit();
    }
    $user = $response["message"];

    // Check if the user exists
    if ($user['role'] != Role::USER->value && $user['role'] != Role::ADMIN->value) {
        http_response_code(401);
        echo json_encode(["status" => "error", "message" => "Invalid credentials2"]);
        exit();
    }

    // Check if the password is correct
    if (!password_verify($password, $user['password'])) {
        http_response_code(401);
        echo json_encode(["status" => "error", "message" => "Invalid credentials3"]);
        exit();
    }

    // Generate JWT token
    try {
        $jwt = $tokenManager->generateToken($user['user_id'], $user['role']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        exit();
    }
    echo json_encode(["status" => "success", "token" => $jwt]);
} else {
    // Parameter name is not correct
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid data"]);
}