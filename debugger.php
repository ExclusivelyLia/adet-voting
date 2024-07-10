<?php
// Include database connection file
include_once('db_connection.php');
session_start();

// Check if student is logged in
if (!isset($_SESSION['student_id'])) {
    echo "You are not logged in. Please log in to view your vote.";
    exit();
}

// Get student ID from session
$student_id = $_SESSION['student_id'];

// Query to retrieve student information
$query_student = "SELECT * FROM student WHERE student_id = '$student_id'";
$result_student = mysqli_query($conn, $query_student);

if (!$result_student || mysqli_num_rows($result_student) === 0) {
    echo "Error: Student with ID $student_id not found.";
    exit();
}

// Fetch student details
$student = mysqli_fetch_assoc($result_student);

// Query to retrieve voted candidates
$query_votes = "SELECT v.*, c.candidate_fname, c.candidate_mname, c.candidate_lname, c.party_list, p.position_name 
                FROM vote v
                INNER JOIN candidate c ON v.candidate_id = c.candidate_id
                INNER JOIN position p ON c.position_id = p.position_id
                WHERE v.student_id = '$student_id'";
$result_votes = mysqli_query($conn, $query_votes);

if (!$result_votes || mysqli_num_rows($result_votes) === 0) {
    echo "You haven't voted for any candidates yet.";
} else {
    // Display student information
    echo "<h2>Student Information</h2>";
    echo "<p>Student ID: {$student['student_id']}</p>";
    echo "<p>Name: {$student['student_fname']} {$student['student_mname']} {$student['student_lname']}</p>";
    echo "<p>Email: {$student['student_email']}</p>";
    echo "<p>Year: {$student['student_year']}</p>";
    echo "<p>Section: {$student['student_section']}</p>";
    echo "<p>Program: {$student['student_program']}</p>";
    echo "<p>Birth Date: {$student['birth_date']}</p>";

    // Display voted candidates
    echo "<h2>Voted Candidates</h2>";
    echo "<ul>";
    while ($row = mysqli_fetch_assoc($result_votes)) {
        echo "<li>{$row['position_name']} - {$row['candidate_fname']} {$row['candidate_mname']} {$row['candidate_lname']} (Party: {$row['party_list']})</li>";
    }
    echo "</ul>";
}

// Close database connection
mysqli_close($conn);
?>
