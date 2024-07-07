<?php
include 'db_connection.php';

// Retrieve position names from the database
$sql = "SELECT * FROM position";
$result = mysqli_query($conn, $sql);

if ($result) {
    // Fetch all rows from the result set
    $positions = mysqli_fetch_all($result, MYSQLI_ASSOC);
    echo json_encode($positions);
} else {
    echo json_encode(["error" => "Error retrieving positions: " . mysqli_error($conn)]);
}

// Close connection
mysqli_close($conn);
?>
