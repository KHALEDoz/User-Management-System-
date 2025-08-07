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

try {
    // Get form data
    $name = trim($_POST['name'] ?? '');
    $age = intval($_POST['age'] ?? 0);
    
    // Validate input
    if (empty($name)) {
        echo json_encode(['success' => false, 'message' => 'Name is required']);
        exit;
    }
    
    if ($age <= 0 || $age > 120) {
        echo json_encode(['success' => false, 'message' => 'Age must be between 1 and 120']);
        exit;
    }
    
    // Sanitize input
    $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
    
    // Get database connection
    $pdo = getConnection();
    
    // Prepare and execute insert statement
    $stmt = $pdo->prepare("INSERT INTO users (name, age, status) VALUES (?, ?, 0)");
    $stmt->execute([$name, $age]);
    
    echo json_encode([
        'success' => true, 
        'message' => 'User added successfully',
        'user_id' => $pdo->lastInsertId()
    ]);
    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
} catch (Exception $e) {
    error_log("General error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
}
?> 