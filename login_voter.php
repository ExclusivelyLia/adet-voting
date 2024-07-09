<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentID = $_POST['studentID'];
    $referenceID = $_POST['referenceID'];

    // Perform database query to check credentials
    $stmt = $conn->prepare("SELECT * FROM student WHERE student_id = ? AND reference_id = ?");
    $stmt->bind_param("ss", $studentID, $referenceID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Login successful, redirect to student dashboard
        session_start();
        $_SESSION['student_id'] = $studentID;
        header("Location: voter_rules.html");
        exit();
    } else {
        // Login failed, redirect back with error message
        header("Location: login_voter.html?error=invalid_credentials");
        exit();
    }
}
?>
