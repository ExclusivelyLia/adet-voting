<?php
include 'db_connection.php'; 

// Check if the request method is DELETE
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    
    // Get candidate ID from query parameter
    $candidateId = $_GET['id'] ?? null;

    // Validate candidate ID
    if (!isset($candidateId)) {
        echo json_encode(array('success' => false, 'message' => 'Candidate ID not provided'));
        exit;
    }

    // Prepare and execute the DELETE statement
    $sql = "DELETE FROM candidate WHERE candidate_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $candidateId);

    if ($stmt->execute()) {
        echo json_encode(array('success' => true));
    } else {
        echo json_encode(array('success' => false, 'message' => 'Error deleting record: ' . $stmt->error));
    }

    $stmt->close();
    $conn->close();

} else {
    echo json_encode(array('success' => false, 'message' => 'Invalid request method'));
}
?>