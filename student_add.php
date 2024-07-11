<?php
require 'vendor/autoload.php'; // Adjust the path as necessary
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Database connection
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
        $response = ["error" => "Student ID already exists. Please choose a different ID."];
    } else {
        mysqli_stmt_close($stmt_check_id); // Close the statement to free the connection

        // Proceed with inserting the student data
        $sql_insert = "INSERT INTO student (student_id, student_fname, student_mname, student_lname, student_year, student_section, student_program, birth_date, student_email, reference_id) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NULL)";
        $stmt_insert = mysqli_prepare($conn, $sql_insert);
        mysqli_stmt_bind_param($stmt_insert, "sssssssss", $student_id, $first_name, $middle_name, $last_name, $year, $section, $program, $birth_date, $email);

        if (mysqli_stmt_execute($stmt_insert)) {
            // Fetch the auto-generated reference_id
            $reference_id = mysqli_insert_id($conn); // Retrieve last inserted ID

            // Generate a random reference ID (5 characters)
            $random_reference = substr(md5(mt_rand()), 0, 5);

            // Update the newly inserted row with the random reference_id
            $sql_update_reference = "UPDATE student SET reference_id = ? WHERE student_id = ?";
            $stmt_update_reference = mysqli_prepare($conn, $sql_update_reference);
            mysqli_stmt_bind_param($stmt_update_reference, "ss", $random_reference, $student_id);
            mysqli_stmt_execute($stmt_update_reference);

            // Create a PHPMailer instance
            $mail = new PHPMailer(true);
            try {
                //Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Gmail SMTP server
                $mail->SMTPSecure = false;
                $mail->SMTPAuth = true;
                $mail->Username = 'electovote@gmail.com'; // Your Gmail address
                $mail->Password = 'wjur pblg ymbq icja'; // Your Gmail password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                //Recipients
                $mail->setFrom('electovote@gmail.com', 'ElectoVote');
                $mail->addAddress($email, $first_name . ' ' . $last_name);

                // Content
                $mail->isHTML(false);
                $mail->Subject = 'Student Reference ID';
                $mail->Body    = "Dear {$first_name} {$last_name},\r\n\r\nYour student reference ID is: {$random_reference}\r\n\r\nThank you for registering.\r\n";

                $mail->send();
                $response = ["success" => "Student added successfully. Reference ID emailed to {$email}"];
            } catch (Exception $e) {
                $response = ["error" => "Error sending email: {$mail->ErrorInfo}"];
            }
        } else {
            $response = ["error" => "Error adding student: " . mysqli_error($conn)];
        }

        mysqli_stmt_close($stmt_insert); // Close the statement to free the connection
    }

    mysqli_close($conn);
    echo json_encode($response); // Encode response as JSON and echo
} else {
    echo json_encode(["error" => "Invalid request method"]);
}
?>
