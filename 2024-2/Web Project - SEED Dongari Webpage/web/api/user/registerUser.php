<?php
header("Content-Type: application/json");
require __DIR__ . '/../../vendor/autoload.php';

use App\DatabaseManager;
use App\UserService;
use App\Role;

$databaseManager = new DatabaseManager();
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
if(isset($data['username']) && isset($data['password']) && isset($data['email'])) {
    $username = $data['username'];
    $password = $data['password'];
    $email = $data['email'];

    // Password hashing
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $role = Role::PENDING;

    // Save user data
    $result = $userService->saveUserApply($username, $hashedPassword, $email, $role->value);

    // Success response
    if ($result['status'] !== 'success') {
        // Database error
        http_response_code(500);
    }
    echo json_encode($result);
} else {
    // Parameter name is not correct
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid data"]);
}