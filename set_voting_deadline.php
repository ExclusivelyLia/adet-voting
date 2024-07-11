<?php
include 'db_connection.php';

// Assuming POST method is used to update the voting deadline
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (isset($data['deadline'])) {
        $newDeadline = $data['deadline'];
        
        // Update the voting deadline in the database
        $sql = "UPDATE election_settings SET voting_deadline = ? WHERE setting_id = 1"; // Assuming setting_id = 1 is your unique identifier
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $newDeadline);
        
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Voting deadline updated successfully"]);
        } else {
            echo json_encode(["success" => false, "message" => "Error updating voting deadline"]);
        }
        
        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "Invalid data received"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Method Not Allowed"]);
}
?>
