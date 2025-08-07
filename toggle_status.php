<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
    exit;
}

$userId = intval($input['user_id'] ?? 0);
$status = intval($input['status'] ?? -1);

// Validate input
if ($userId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
    exit;
}

if ($status !== 0 && $status !== 1) {
    echo json_encode(['success' => false, 'message' => 'Invalid status value']);
    exit;
}

try {
    // Get database connection
    $pdo = getConnection();
    
    // First check if user exists
    $checkStmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
    $checkStmt->execute([$userId]);
    
    if (!$checkStmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }
    
    // Update user status
    $stmt = $pdo->prepare("UPDATE users SET status = ? WHERE id = ?");
    $stmt->execute([$status, $userId]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Status updated successfully',
            'user_id' => $userId,
            'new_status' => $status
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No changes made']);
    }
    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
} catch (Exception $e) {
    error_log("General error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
}
?> 