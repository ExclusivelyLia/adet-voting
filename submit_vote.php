<?php
session_start();
include 'db_connection.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

error_log('Session data: ' . print_r($_SESSION, true));

if (!isset($_SESSION['student_id'])) {
    echo json_encode(["success" => false, "message" => "User not logged in."]);
    exit();
}

if (empty($_SESSION['student_id'])) {
    echo json_encode(["success" => false, "message" => "Student ID is empty. Please log in again."]);
    exit();
}

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]);
    exit();
}

$conn->set_charset("utf8mb4");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ["success" => false, "message" => "An error occurred while processing your vote."];

    try {
        // Fetch the voting deadline from the database
        $stmt = $conn->prepare("SELECT voting_deadline FROM election_settings WHERE setting_id = 1");
        $stmt->execute();
        $stmt->bind_result($votingDeadline);
        $stmt->fetch();
        $stmt->close();

        // Check if the voting deadline has passed
        $currentDateTime = new DateTime();
        $deadlineDateTime = new DateTime($votingDeadline);

        if ($currentDateTime >= $deadlineDateTime) {
            throw new Exception("Voting deadline has passed. You cannot submit your vote.");
        }

        // Continue with the vote submission process
        $data = json_decode(file_get_contents("php://input"));

        if (!isset($data->presidentVote) || !isset($data->vicePresidentVote) || !isset($data->councilorVotes)) {
            throw new Exception("Invalid JSON format or missing required fields.");
        }

        // Verify Student ID exists in the student table
        $stmt = $conn->prepare("SELECT COUNT(*) FROM student WHERE student_id = ?");
        $stmt->bind_param("s", $_SESSION['student_id']);  // Note the "s" for string parameter
        $stmt->execute();
        $stmt->bind_result($studentExists);
        $stmt->fetch();
        $stmt->close();

        if (!$studentExists) {
            throw new Exception("Invalid student ID. Please log in again.");
        }

        // Check for duplicate votes
        $stmt = $conn->prepare("SELECT COUNT(*) FROM vote WHERE student_id = ?");
        $stmt->bind_param("s", $_SESSION['student_id']);  // Note the "s" for string parameter
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            throw new Exception("You have already voted.");
        }

        $conn->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

        // Insert President vote
        if (!is_null($data->presidentVote)) {
            $stmt = $conn->prepare("INSERT INTO vote (student_id, candidate_id, date_voted) VALUES (?, ?, NOW())");
            $stmt->bind_param("si", $_SESSION['student_id'], $data->presidentVote);  // Note the "s" for string student_id
            if (!$stmt->execute()) {
                throw new Exception("Failed to insert President vote: " . $stmt->error);
            }
            $stmt->close();
        }

        // Insert Vice President vote
        if (!is_null($data->vicePresidentVote)) {
            $stmt = $conn->prepare("INSERT INTO vote (student_id, candidate_id, date_voted) VALUES (?, ?, NOW())");
            $stmt->bind_param("si", $_SESSION['student_id'], $data->vicePresidentVote);  // Note the "s" for string student_id
            if (!$stmt->execute()) {
                throw new Exception("Failed to insert Vice President vote: " . $stmt->error);
            }
            $stmt->close();
        }

        // Insert Councilor votes
        foreach ($data->councilorVotes as $councilorVote) {
            $stmt = $conn->prepare("INSERT INTO vote (student_id, candidate_id, date_voted) VALUES (?, ?, NOW())");
            $stmt->bind_param("si", $_SESSION['student_id'], $councilorVote);  // Note the "s" for string student_id
            if (!$stmt->execute()) {
                throw new Exception("Failed to insert Councilor vote: " . $stmt->error);
            }
            $stmt->close();
        }

        $conn->commit();

        $response = ["success" => true, "message" => "Vote submitted successfully."];
    } catch (Exception $e) {
        $conn->rollback();
        error_log('Error in submit_vote.php for student_id ' . $_SESSION['student_id'] . ': ' . $e->getMessage() . ' | Stack trace: ' . $e->getTraceAsString());
        $response = [
            "success" => false, 
            "message" => "An error occurred: " . $e->getMessage(),
            "student_id" => $_SESSION['student_id']
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
} else {
    http_response_code(405);
    echo json_encode(["error" => "Method Not Allowed"]);
    exit();
}
?>
