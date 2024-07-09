<?php
include 'db_connection.php'; 

// Check if the request method is DELETE
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    
    // Parse the raw HTTP request body
    parse_str(file_get_contents("php://input"), $_DELETE);

    // Get student ID from parsed input
    $studentId = $_DELETE['id'] ?? null;

    // Validate student ID
    if (!isset($studentId)) {
        echo json_encode(array('success' => false, 'message' => 'Student ID not provided'));
        exit;
    }

    // Prepare and execute the DELETE statement
    $sql = "DELETE FROM student WHERE student_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $studentId);

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
