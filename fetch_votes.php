<?php
include 'db_connection.php';

// Query to get the vote counts by position and student program
$query = "
    SELECT
        p.position_name AS category,
        s.student_program,
        COUNT(DISTINCT v.student_id) AS vote_count
    FROM vote v
    JOIN candidate c ON v.candidate_id = c.candidate_id
    JOIN position p ON c.position_id = p.position_id
    JOIN student s ON v.student_id = s.student_id
    GROUP BY p.position_name, s.student_program
    ORDER BY p.position_name, s.student_program
";

$result = $conn->query($query);

$votes = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $votes[] = $row;
    }
}

// Query to get the total distinct student votes
$totalQuery = "
    SELECT
        COUNT(DISTINCT student_id) AS total_votes
    FROM vote
";

$totalResult = $conn->query($totalQuery);
$totalVotes = 0;

if ($totalResult->num_rows > 0) {
    $totalRow = $totalResult->fetch_assoc();
    $totalVotes = $totalRow['total_votes'];
}

// Combine the results
$response = [
    'votes' => $votes,
    'total_votes' => $totalVotes
];

// Return the data as JSON
header('Content-Type: application/json');
echo json_encode($response);

$conn->close();
?>
