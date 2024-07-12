<?php
include 'db_connection.php';

$query = "SELECT c.candidate_id, c.candidate_img, c.candidate_fname, c.candidate_lname, c.position_id, COUNT(v.vote_id) AS vote_count 
          FROM candidate c 
          LEFT JOIN vote v ON c.candidate_id = v.candidate_id 
          GROUP BY c.candidate_id";
$result = $conn->query($query);

$candidates = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Check if candidate image exists
        $image_path = 'C:/xampp/htdocs/adet-voting/Candidate/' . $row['candidate_img'];
        if (!file_exists($image_path) || empty($row['candidate_img'])) {
            $row['candidate_img'] = 'default-ppic.png';
        }
        $candidates[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($candidates);

$conn->close();
?>
