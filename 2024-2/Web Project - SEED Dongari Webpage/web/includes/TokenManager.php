<?php
namespace App;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

use mysqli;
use mysqli_sql_exception;

class TokenManager {
    private $secretKey;
    private $connection;

    public function __construct(DatabaseManager $db) {
        $this->secretKey = getenv('JWT_SECRET_KEY');
        $this->connection = $db->get_connection();
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    }

    public function generateToken($user_id, $role) {
        $issuedAt = time();
        $expirationTime = $issuedAt + 1800;  // jwt valid for 30 minutes from the issued time
        $payload = array(
            'iat' => $issuedAt,
            'user_id' => $user_id,
            'role' => $role,
            'exp' => $expirationTime
        );
        return JWT::encode($payload, $this->secretKey, 'HS256');
    }

    public function validateToken() {
        $headers = getallheaders();

        if (!isset($headers['Authorization'])) {
            throw new Exception("No token provided", 401);
        }

        $parts = explode(" ", $headers['Authorization']);

        if (count($parts) !== 2 || $parts[0] !== "Bearer") {
            throw new Exception("Invalid token type", 401);
        }

        $token = $parts[1];

        if ($this->isTokenBlacklisted($token)) {
            throw new Exception("Token is blacklisted", 401);
        }

        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
            return $decoded;
        } catch (Exception $e) {
            throw new Exception("Invalid token", 401);
        }
    }

    public function blacklistToken($token) {
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
            $stmt = $this->connection->prepare("INSERT INTO Token_blacklist (token, expiration_time) VALUES (?, ?)");
            $stmt->bind_param("ss", $token, date('Y-m-d H:i:s', $decoded->exp));
            $stmt->execute();
            return ["status" => "success", "message" => "Token blacklisted"];
        } catch (mysqli_sql_exception $e) {
            switch ($e->getCode()) {
                case 1062:
                    // Duplicate entry error code
                    return ["status" => $e->getCode(), "message" => "Duplicate entry: " . $e->getMessage()];
                case 1451:
                    // Cannot delete or update a parent row: a foreign key constraint fails
                    return ["status" => $e->getCode(), "message" => "Cannot delete or update: " . $e->getMessage()];
                case 1452:
                    // Cannot add or update a child row: a foreign key constraint fails
                    return ["status" => $e->getCode(), "message" => "Cannot add or update: " . $e->getMessage()];
                case 1048:
                    // Column cannot be null
                    return ["status" => $e->getCode(), "message" => "Column cannot be null: " . $e->getMessage()];
                default:
                    // Other database errors
                    return ["status" => $e->getCode(), "message" => "Database error: " . $e->getMessage()];
            }
        } finally {
            $stmt->close();
        }
    }

    public function isTokenBlacklisted($token) {
        $stmt = $this->connection->prepare("SELECT COUNT(*) as count FROM Token_blacklist WHERE token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['count'] > 0;
    }
}