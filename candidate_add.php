<?php
include 'db_connection.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Initialize variables to store form data
    $candidate_id = '';
    $candidate_fname = '';
    $candidate_mname = null;
    $candidate_lname = '';
    $party_list = '';
    $position_id = '';
    $targetFile = null;

    // Check and assign form data
    if (isset($_POST['candidate-id'])) {
        $candidate_id = $_POST['candidate-id'];
    }
    if (isset($_POST['first-name'])) {
        $candidate_fname = $_POST['first-name'];
    }
    if (isset($_POST['middle-name'])) {
        $candidate_mname = $_POST['middle-name'];
    }
    if (isset($_POST['last-name'])) {
        $candidate_lname = $_POST['last-name'];
    }
    if (isset($_POST['party-list'])) {
        $party_list = $_POST['party-list'];
    }
    if (isset($_POST['position-id'])) {
        $position_id = $_POST['position-id'];
    }

    // File upload handling
    $uploadOk = 1;
    $error_message = '';

    if (!empty($_FILES["candidate_img"]["name"])) {
        $uploadDirectory = "C:/xampp/htdocs/adet-voting/Candidate/";
        $targetFile = $uploadDirectory . basename($_FILES["candidate_img"]["name"]);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Check if image file is a valid image
        $check = getimagesize($_FILES["candidate_img"]["tmp_name"]);
        if ($check === false) {
            $uploadOk = 0;
            $error_message = "File is not a valid image.";
        }

        // Check file size (adjust as needed)
        if ($_FILES["candidate_img"]["size"] > 5242880) {
            $uploadOk = 0;
            $error_message = "Sorry, your file is too large.";
        }

        // Allow certain file formats
        $allowedFormats = ["jpg", "jpeg", "png"];
        if (!in_array($imageFileType, $allowedFormats)) {
            $uploadOk = 0;
            $error_message = "Sorry, only JPG, JPEG, & PNG files are allowed.";
        }

        // Attempt to move the uploaded file to the target directory
        if ($uploadOk == 1) {
            if (!move_uploaded_file($_FILES["candidate_img"]["tmp_name"], $targetFile)) {
                $uploadOk = 0;
                $error_message = "Sorry, there was an error uploading your file.";
            }
        }
    }

    // Validate form data before proceeding
    if ($uploadOk == 0) {
        // Display error message and retain form data
        echo '<script>alert("' . $error_message . '"); window.history.back();</script>';
    } elseif (empty($candidate_id) || empty($candidate_fname) || empty($candidate_lname) || empty($party_list) || empty($position_id)) {
        // Display error message if required fields are empty
        echo '<script>alert("Please fill in all required fields."); window.history.back();</script>';
    } else {
        // Validate candidate ID duplication
        $sql_check_id = "SELECT * FROM candidate WHERE candidate_id = ?";
        $stmt_check_id = mysqli_prepare($conn, $sql_check_id);
        mysqli_stmt_bind_param($stmt_check_id, "s", $candidate_id);
        mysqli_stmt_execute($stmt_check_id);
        mysqli_stmt_store_result($stmt_check_id);

        if (mysqli_stmt_num_rows($stmt_check_id) > 0) {
            // Display error message and retain form data
            echo '<script>alert("Candidate ID already exists. Please choose a different ID."); window.history.back();</script>';
        } else {
            mysqli_stmt_close($stmt_check_id); // Close the statement to free the connection

            // Validate position ID existence
            $sql_check_position = "SELECT * FROM position WHERE position_id = ?";
            $stmt_check_position = mysqli_prepare($conn, $sql_check_position);
            mysqli_stmt_bind_param($stmt_check_position, "s", $position_id);
            mysqli_stmt_execute($stmt_check_position);
            mysqli_stmt_store_result($stmt_check_position);

            if (mysqli_stmt_num_rows($stmt_check_position) == 0) {
                // Display error message and retain form data
                echo '<script>alert("Position ID does not exist. Please enter a valid position ID."); window.history.back();</script>';
            } else {
                mysqli_stmt_close($stmt_check_position); // Close the statement to free the connection

                // Validate position limits within the same party list
                $sql_check_position_limit = "SELECT COUNT(*) FROM candidate WHERE position_id = ? AND party_list = ?";
                $stmt_check_position_limit = mysqli_prepare($conn, $sql_check_position_limit);
                mysqli_stmt_bind_param($stmt_check_position_limit, "ss", $position_id, $party_list);
                mysqli_stmt_execute($stmt_check_position_limit);
                mysqli_stmt_bind_result($stmt_check_position_limit, $position_count);
                mysqli_stmt_fetch($stmt_check_position_limit);
                mysqli_stmt_close($stmt_check_position_limit); // Close the statement to free the connection

                // Define position limits
                $positionLimits = [
                    '1' => 1, // 1 is the position_id for President
                    '2' => 1, // 2 is the position_id for Vice President
                    '3' => 6  // 3 is the position_id for Councilor
                ];

                if (isset($positionLimits[$position_id]) && $position_count >= $positionLimits[$position_id]) {
                    $positionName = ($position_id == '1') ? "President" : (($position_id == '2') ? "Vice President" : "Councilor");
                    // Display error message if position limit exceeded
                    echo '<script>alert("Only ' . $positionLimits[$position_id] . ' ' . $positionName . ' allowed per party list."); window.history.back();</script>';
                } else {
                    // Proceed with insertion
                    $sql = "INSERT INTO candidate (candidate_id, candidate_fname, candidate_mname, candidate_lname, party_list, position_id, candidate_img)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";

                    $stmt = mysqli_prepare($conn, $sql);
                    mysqli_stmt_bind_param($stmt, "sssssss", $candidate_id, $candidate_fname, $candidate_mname, $candidate_lname, $party_list, $position_id, $targetFile);

                    if (mysqli_stmt_execute($stmt)) {
                        // Display success message and retain form data
                        echo '<script>alert("Record added successfully!"); window.location.href = "candidate_add.html";</script>';
                    } else {
                        // Display error message and retain form data
                        echo '<script>alert("Error: ' . mysqli_error($conn) . '"); window.history.back();</script>';
                    }
                }
            }
        }
    }
}

// Close the database connection
mysqli_close($conn);
?>
