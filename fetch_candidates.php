<?php
include 'db_connection.php';

header('Content-Type: application/json');

// Function to fetch candidate details by ID
function fetch_candidate_details($candidate_id, $conn) {
    $sql = "SELECT candidate_id, candidate_fname, candidate_lname, party_list, candidate_img 
            FROM candidate 
            WHERE candidate_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $candidate_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (!empty($row['candidate_img'])) {
            // Adjust path relative to web server's root
            $row['candidate_img'] = 'Candidate/' . basename($row['candidate_img']);
        } else {
            // Default image path if no image is available
            $row['candidate_img'] = 'css/pictures/default-ppic.png';
        }
        $stmt->close();
        return $row;
    } else {
        $stmt->close();
        return null;
    }
}

try {
    // Fetch selected candidate IDs from query parameters
    $selectedPresidentId = isset($_GET['selectedPresidentId']) ? $_GET['selectedPresidentId'] : null;
    $selectedVicePresidentId = isset($_GET['selectedVicePresidentId']) ? $_GET['selectedVicePresidentId'] : null;
    $selectedCouncilorIds = isset($_GET['selectedCouncilorIds']) ? json_decode($_GET['selectedCouncilorIds']) : [];

    // Validate IDs
    $selectedPresidentId = filter_var($selectedPresidentId, FILTER_VALIDATE_INT) ? $selectedPresidentId : null;
    $selectedVicePresidentId = filter_var($selectedVicePresidentId, FILTER_VALIDATE_INT) ? $selectedVicePresidentId : null;
    $selectedCouncilorIds = array_filter($selectedCouncilorIds, 'is_numeric');

    // Initialize candidates array
    $candidates = [
        "president" => null,
        "vice_president" => null,
        "councilors" => []
    ];

    // Fetch President
    if ($selectedPresidentId) {
        $candidates["president"] = fetch_candidate_details($selectedPresidentId, $conn);
    }

    // Fetch Vice President
    if ($selectedVicePresidentId) {
        $candidates["vice_president"] = fetch_candidate_details($selectedVicePresidentId, $conn);
    }

    // Fetch Councilors
    foreach ($selectedCouncilorIds as $councilorId) {
        $councilor = fetch_candidate_details($councilorId, $conn);
        if ($councilor) {
            $candidates["councilors"][] = $councilor;
        }
    }

    // Return candidates as JSON
    echo json_encode($candidates);
} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(array("error" => "Error fetching candidates: " . $e->getMessage()));
}

mysqli_close($conn);
?>
