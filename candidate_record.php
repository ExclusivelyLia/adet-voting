<?php

include 'db_connection.php';

// Retrieve candidates from database
$sql = "SELECT * FROM candidate";
$result = mysqli_query($conn, $sql);

if ($result) {
    // Fetch all rows from the result set
    $candidates = mysqli_fetch_all($result, MYSQLI_ASSOC);
    echo json_encode($candidates);
} else {
    echo json_encode(["error" => "Error retrieving candidates: " . mysqli_error($conn)]);
}

// Close connection
mysqli_close($conn);

// Get the contents of the output buffer and clear it
$output = ob_get_clean();

// Remove any whitespace before the JSON data
echo trim($output);
?>