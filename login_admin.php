<?php
include 'db_connection.php';

// Function to check login credentials
function adminLogin($conn, $username, $password) {
    $stmt = $conn->prepare("SELECT * FROM admin WHERE admin_username = ? AND admin_password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    
    if ($result->num_rows > 0) {
        // Login successful
        return true;
    } else {
        // Login failed
        return false;
    }
}

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (adminLogin($conn, $username, $password)) {
        // Start a session and redirect to the admin dashboard
        session_start();
        $_SESSION['admin_username'] = $username;
        header("Location: test_admindashboard.html");  
        exit();
    } else {
        // Login failed, redirect back
        header("Location: login_admin.html?error=invalid_credentials");
        exit();
    }
}
?>
