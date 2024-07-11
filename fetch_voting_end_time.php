<?php
include 'db_connection.php';

$sql = "SELECT voting_deadline FROM election_settings WHERE setting_id = 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $votingDeadline = $row['voting_deadline'];
    echo json_encode(['voting_deadline' => $votingDeadline]);
} else {
    echo json_encode(['error' => 'No voting deadline found']);
}

$conn->close();
?>
