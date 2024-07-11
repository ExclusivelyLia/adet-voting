<?php
include 'db_connection.php';

// Define position limits
$positionLimits = array(
    '1' => 1,  // President
    '2' => 1,  // Vice President
    '3' => 6   // Councilor
);

// Check if form data is submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $candidateId = $_POST['candidate_id'];
    $positionId = $_POST['position_id'];
    $candidateFname = $_POST['candidate_fname'];
    $candidateMname = $_POST['candidate_mname'];
    $candidateLname = $_POST['candidate_lname'];
    $partyList = $_POST['party_list'];

    // Check if candidate ID already exists
    $checkQuery = "SELECT * FROM candidate WHERE candidate_id = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("s", $candidateId); // Bind as string
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $response = array('error' => 'Candidate ID already exists.');
        echo json_encode($response);
        exit; // Exit PHP script
    }

    // Validate party list constraints
    $partyListArray = explode(',', $partyList);
    $positionCountQuery = "SELECT COUNT(*) AS position_count FROM candidate WHERE party_list = ? AND position_id = ?";
    $stmt = $conn->prepare($positionCountQuery);
    $stmt->bind_param("si", $partyListItem, $positionId);

    foreach ($partyListArray as $partyListItem) {
        $trimmedPartyListItem = trim($partyListItem);

        // Check if position limit is exceeded for this position
        if (isset($positionLimits[$positionId])) {
            $stmt->bind_param("si", $trimmedPartyListItem, $positionId);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $position_count = $row['position_count'];

            if ($position_count >= $positionLimits[$positionId]) {
                $positionName = ($positionId == '1') ? "President" : (($positionId == '2') ? "Vice President" : "Councilor");
                // Display error message if position limit exceeded
                $response = array('error' => 'Only ' . $positionLimits[$positionId] . ' ' . $positionName . ' allowed per party list.');
                echo json_encode($response);
                exit; // Exit PHP script
            }
        }
    }

    // Optional: Check if candidate_img is uploaded
    if (isset($_FILES['candidate_img']) && $_FILES['candidate_img']['error'] === UPLOAD_ERR_OK) {
        // Handle file upload and move file to directory
        $fileName = $_FILES['candidate_img']['name'];
        $tempFilePath = $_FILES['candidate_img']['tmp_name'];
        $targetDirectory = "C:/xampp/htdocs/adet-voting/Candidate/";
        $targetFilePath = $targetDirectory . $fileName;

        // Move uploaded file to specified directory
        if (move_uploaded_file($tempFilePath, $targetFilePath)) {
            // File moved successfully, proceed with database insertion
            $sql = "INSERT INTO candidate (candidate_id, candidate_fname, candidate_mname, candidate_lname, party_list, position_id, candidate_img) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssis", $candidateId, $candidateFname, $candidateMname, $candidateLname, $partyListItem, $positionId, $fileName);
            $stmt->execute();
            
            // Echo success response
            $response = array('success' => 'New candidate added successfully');
            echo json_encode($response);
        } else {
            $response = array('error' => 'Sorry, there was an error uploading your file.');
            echo json_encode($response);
        }
    } else {
        // If candidate_img is not uploaded, proceed with database insertion without candidate_img
        $sql = "INSERT INTO candidate (candidate_id, candidate_fname, candidate_mname, candidate_lname, party_list, position_id) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $candidateId, $candidateFname, $candidateMname, $candidateLname, $partyListItem, $positionId);
        $stmt->execute();

        // Echo success response
        $response = array('success' => 'New candidate added successfully');
        echo json_encode($response);
    }

    // Close prepared statement and database connection
    $stmt->close();
    $conn->close();
}
?>
