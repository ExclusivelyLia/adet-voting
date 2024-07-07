<?php
include 'db_connection.php'; 

header('Content-Type: application/json');

// Fetch all candidates for Councilor (position_id = 3)
$sql = "SELECT candidate_id, candidate_fname, candidate_lname, party_list, candidate_img FROM candidate WHERE position_id = 3";
$result = mysqli_query($conn, $sql);

$candidates = array();

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Set the correct path for candidate images
        if (!empty($row['candidate_img'])) {
            $row['candidate_img'] = 'Candidate/' . basename($row['candidate_img']);
        }
        $candidates[] = $row;
    }
    echo json_encode($candidates);
} else {
    echo json_encode(["error" => "Error fetching candidates: " . mysqli_error($conn)]);
}

mysqli_close($conn);
?>
