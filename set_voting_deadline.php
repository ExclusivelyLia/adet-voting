<?php
include 'db_connection.php';

// Assuming POST method is used to update the voting deadline
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (isset($data['deadline'])) {
        $newDeadline = $data['deadline'];
        
        // Log the received deadline
        error_log("Received new deadline: " . $newDeadline);
        
        // Check the current value of voting_deadline
        $sql = "SELECT voting_deadline FROM election_settings WHERE setting_id = 1";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $currentDeadline = $row['voting_deadline'];
            error_log("Current deadline: " . $currentDeadline);
        } else {
            error_log("No record found with setting_id = 1");
            echo json_encode(["success" => false, "message" => "No record found with setting_id = 1"]);
            exit;
        }
        
        // Update the voting deadline in the database
        $sql = "UPDATE election_settings SET voting_deadline = ? WHERE setting_id = 1"; // Assuming setting_id = 1 is your unique identifier
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            error_log("Prepare failed: " . htmlspecialchars($conn->error));
            echo json_encode(["success" => false, "message" => "Error preparing statement"]);
            exit;
        }

        $stmt->bind_param("s", $newDeadline);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode(["success" => true, "message" => "Voting deadline updated successfully"]);
            } else {
                echo json_encode(["success" => false, "message" => "No rows updated"]);
            }
        } else {
            error_log("Execute failed: " . htmlspecialchars($stmt->error));
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
