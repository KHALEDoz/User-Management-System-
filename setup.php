<?php
/**
 * Setup Script for User Management System
 * Run this file once to initialize the database and test the connection
 */

echo "<h1>User Management System - Setup</h1>";

// Include configuration
require_once 'config.php';

echo "<h2>Database Connection Test</h2>";

try {
    // Test database connection
    $pdo = getConnection();
    echo "<p style='color: green;'>✅ Database connection successful!</p>";
    
    // Check if users table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: green;'>✅ Users table exists!</p>";
        
        // Count existing records
        $countStmt = $pdo->query("SELECT COUNT(*) as count FROM users");
        $count = $countStmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "<p>📊 Current users in database: <strong>$count</strong></p>";
    } else {
        echo "<p style='color: orange;'>⚠️ Users table not found. Creating...</p>";
        initializeDatabase();
        echo "<p style='color: green;'>✅ Users table created successfully!</p>";
    }
    
    echo "<h2>System Status</h2>";
    echo "<p>✅ PHP Version: " . PHP_VERSION . "</p>";
    echo "<p>✅ PDO Extension: " . (extension_loaded('pdo') ? 'Loaded' : 'Not Loaded') . "</p>";
    echo "<p>✅ PDO MySQL Extension: " . (extension_loaded('pdo_mysql') ? 'Loaded' : 'Not Loaded') . "</p>";
    
    echo "<h2>Next Steps</h2>";
    echo "<p>🎉 Setup completed successfully! You can now:</p>";
    echo "<ul>";
    echo "<li><a href='index.html'>Open the main application</a></li>";
    echo "<li>Start adding users through the web interface</li>";
    echo "<li>Test the status toggle functionality</li>";
    echo "</ul>";
    
    echo "<p><strong>Note:</strong> You can delete this setup.php file after confirming everything works.</p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Database connection failed!</p>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    
    echo "<h2>Troubleshooting</h2>";
    echo "<ul>";
    echo "<li>Check your MySQL credentials in config.php</li>";
    echo "<li>Ensure MySQL service is running</li>";
    echo "<li>Verify PHP PDO extension is installed</li>";
    echo "<li>Check if the database exists and is accessible</li>";
    echo "</ul>";
    
    echo "<h2>Common Solutions</h2>";
    echo "<ol>";
    echo "<li>Update config.php with correct database credentials</li>";
    echo "<li>Create the database manually: <code>CREATE DATABASE user_management;</code></li>";
    echo "<li>Ensure MySQL user has proper permissions</li>";
    echo "<li>Check if MySQL is running on the correct port</li>";
    echo "</ol>";
}

echo "<hr>";
echo "<p><small>Setup completed at: " . date('Y-m-d H:i:s') . "</small></p>";
?> 