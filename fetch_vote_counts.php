<?php
include 'db_connection.php';

// Query to get the vote counts for each student program
$query = "
    SELECT 
        student_program, 
        COUNT(DISTINCT v.student_id) AS vote_count
    FROM vote v
    JOIN student s ON v.student_id = s.student_id
    GROUP BY student_program
";

$result = $conn->query($query);

$votes = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $votes[] = $row;
    }
}

// Return the data as JSON
header('Content-Type: application/json');
echo json_encode($votes);

$conn->close();
?>
