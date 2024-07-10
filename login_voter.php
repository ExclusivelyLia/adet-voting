<?php
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
        // Check if student has already voted
        $stmt_check_vote = $conn->prepare("SELECT * FROM vote WHERE student_id = ?");
        $stmt_check_vote->bind_param("i", $studentID);
        $stmt_check_vote->execute();
        $result_check_vote = $stmt_check_vote->get_result();

        if ($result_check_vote->num_rows > 0) {
            // Redirect with JavaScript alert for already voted
            echo '<script>alert("You have already voted"); window.location.href = "login_voter.html";</script>';
            exit();
        } else {
            // Login successful, start session
            session_start();
            $_SESSION['student_id'] = $studentID;
            header("Location: voter_rules.html");
            exit();
        }
    } else {
        // Login failed, redirect back with error message for invalid credentials
        header("Location: login_voter.html?error=invalid_credentials");
        exit();
    }
}
?>
