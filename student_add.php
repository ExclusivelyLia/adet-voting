<?php
include 'db_connection.php';

header('Content-Type: application/json'); // Set JSON header

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST['student_id'];
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'] ?: null; // Middle name can be null
    $last_name = $_POST['last_name'];
    $year = $_POST['student_year'];
    $section = $_POST['student_section'];
    $program = $_POST['student_program'];
    $birth_date = $_POST['date'];
    $email = $_POST['email'];

    // Check if student ID already exists
    $sql_check_id = "SELECT * FROM student WHERE student_id = ?";
    $stmt_check_id = mysqli_prepare($conn, $sql_check_id);
    mysqli_stmt_bind_param($stmt_check_id, "s", $student_id);
    mysqli_stmt_execute($stmt_check_id);
    mysqli_stmt_store_result($stmt_check_id);

    if (mysqli_stmt_num_rows($stmt_check_id) > 0) {
        echo json_encode(["error" => "Student ID already exists. Please choose a different ID."]);
    } else {
        mysqli_stmt_close($stmt_check_id); // Close the statement to free the connection

        // Proceed with inserting the student data
        $sql_insert = "INSERT INTO student (student_id, student_fname, student_mname, student_lname, student_year, student_section, student_program, birth_date, student_email) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_insert = mysqli_prepare($conn, $sql_insert);
        mysqli_stmt_bind_param($stmt_insert, "sssssssss", $student_id, $first_name, $middle_name, $last_name, $year, $section, $program, $birth_date, $email);

        if (mysqli_stmt_execute($stmt_insert)) {
            echo json_encode(["success" => "Student added successfully"]);
        } else {
            echo json_encode(["error" => "Error adding student: " . mysqli_error($conn)]);
        }

        mysqli_stmt_close($stmt_insert); // Close the statement to free the connection
    }

    mysqli_close($conn);
} else {
    echo json_encode(["error" => "Invalid request method"]);
}
?>
