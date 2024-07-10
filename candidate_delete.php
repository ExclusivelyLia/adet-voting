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

    // Fetch the candidate image file name
    $sql = "SELECT candidate_img FROM candidate WHERE candidate_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $candidateId);
    $stmt->execute();
    $result = $stmt->get_result();
    $candidate = $result->fetch_assoc();
    $stmt->close();

    // Check if the candidate exists
    if (!$candidate) {
        echo json_encode(array('success' => false, 'message' => 'Candidate not found'));
        $conn->close();
        exit;
    }

    // Delete the image file if it exists
    if (!empty($candidate['candidate_img'])) {
        $imagePath = 'C:/xampp/htdocs/adet-voting/Candidate/' . $candidate['candidate_img'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
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
