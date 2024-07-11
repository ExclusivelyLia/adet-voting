<?php
session_start(); // Start session at the beginning of the script
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentID = $_POST['studentID'];
    $referenceID = $_POST['referenceID'];

    // Check student credentials for login
    $stmt_login = $conn->prepare("SELECT * FROM student WHERE student_id = ? AND reference_id = ?");
    $stmt_login->bind_param("ss", $studentID, $referenceID);
    $stmt_login->execute();
    $result_login = $stmt_login->get_result();

    if ($result_login->num_rows > 0) {
        // Set the session for dashboard access
        $_SESSION['student_id'] = $studentID;
        header("Location: student_dashboard.html");
        exit();
    } else {
        // Login failed, redirect back with error message for invalid credentials
        header("Location: login_voter.html?error=invalid_credentials");
        exit();
    }
}
?>
