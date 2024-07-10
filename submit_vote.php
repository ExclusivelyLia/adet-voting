<?php
session_start(); // Ensure session is started to access $_SESSION variables

include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Decode JSON data from the request body
    $data = json_decode(file_get_contents("php://input"));

    // Extract data from JSON
    $presidentVote = $data->presidentVote ?? null;
    $vicePresidentVote = $data->vicePresidentVote ?? null;
    $councilorVotes = $data->councilorVotes ?? [];

    // Example of inserting data into the 'vote' table
    // Ensure to use prepared statements to prevent SQL injection

    // Insert President vote
    if ($presidentVote) {
        $stmt = $conn->prepare("INSERT INTO vote (student_id, candidate_id, date_voted) VALUES (?, ?, NOW())");
        $stmt->bind_param("ii", $_SESSION['student_id'], $presidentVote);
        $stmt->execute();
        $stmt->close();
    }

    // Insert Vice President vote
    if ($vicePresidentVote) {
        $stmt = $conn->prepare("INSERT INTO vote (student_id, candidate_id, date_voted) VALUES (?, ?, NOW())");
        $stmt->bind_param("ii", $_SESSION['student_id'], $vicePresidentVote);
        $stmt->execute();
        $stmt->close();
    }

    // Insert Councilor votes
    foreach ($councilorVotes as $councilorVote) {
        $stmt = $conn->prepare("INSERT INTO vote (student_id, candidate_id, date_voted) VALUES (?, ?, NOW())");
        $stmt->bind_param("ii", $_SESSION['student_id'], $councilorVote);
        $stmt->execute();
        $stmt->close();
    }

    // Fetch details of selected candidates after voting
    $selectedCandidates = [];

    // Fetch President candidate details
    if ($presidentVote) {
        $stmt = $conn->prepare("SELECT candidate_id, candidate_fname, candidate_lname, party_list, candidate_img FROM candidate WHERE candidate_id = ?");
        $stmt->bind_param("i", $presidentVote);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $selectedCandidates['president'] = $result->fetch_assoc();
        }
        $stmt->close();
    }

    // Fetch Vice President candidate details
    if ($vicePresidentVote) {
        $stmt = $conn->prepare("SELECT candidate_id, candidate_fname, candidate_lname, party_list, candidate_img FROM candidate WHERE candidate_id = ?");
        $stmt->bind_param("i", $vicePresidentVote);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $selectedCandidates['vicePresident'] = $result->fetch_assoc();
        }
        $stmt->close();
    }

    // Fetch Councilors candidate details
    if (!empty($councilorVotes)) {
        $councilorDetails = [];
        foreach ($councilorVotes as $councilorVote) {
            $stmt = $conn->prepare("SELECT candidate_id, candidate_fname, candidate_lname, party_list, candidate_img FROM candidate WHERE candidate_id = ?");
            $stmt->bind_param("i", $councilorVote);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $councilorDetails[] = $result->fetch_assoc();
            }
            $stmt->close();
        }
        $selectedCandidates['councilors'] = $councilorDetails;
    }

    // Close database connection
    mysqli_close($conn);

    // Example response
    echo json_encode(["success" => true, "selectedCandidates" => $selectedCandidates]);
    exit();
} else {
    // Handle invalid request method
    http_response_code(405);
    echo json_encode(["error" => "Method Not Allowed"]);
    exit();
}
?>
