<?php
include 'db_connection.php';

// Fetch candidates with their vote counts for each position
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

// Determine winners and potential ties
$winners = [
    'president' => null,
    'vice_president' => null,
    'councilors' => []
];

// Sort candidates by position and vote count descending
usort($candidates, function($a, $b) {
    if ($a['vote_count'] == $b['vote_count']) {
        return 0;
    }
    return ($a['vote_count'] > $b['vote_count']) ? -1 : 1;
});

// Extract winners and check for ties
foreach ($candidates as $candidate) {
    switch ($candidate['position_id']) {
        case 1: // President
            if (!$winners['president']) {
                $winners['president'] = $candidate;
            } elseif ($winners['president']['vote_count'] == $candidate['vote_count']) {
                // Tie for President
                if (!isset($winners['president_tie'])) {
                    $winners['president_tie'] = [];
                    $winners['president_tie'][] = $winners['president'];
                }
                $winners['president_tie'][] = $candidate;
            }
            break;
        case 2: // Vice President
            if (!$winners['vice_president']) {
                $winners['vice_president'] = $candidate;
            } elseif ($winners['vice_president']['vote_count'] == $candidate['vote_count']) {
                // Tie for Vice President
                if (!isset($winners['vice_president_tie'])) {
                    $winners['vice_president_tie'] = [];
                    $winners['vice_president_tie'][] = $winners['vice_president'];
                }
                $winners['vice_president_tie'][] = $candidate;
            }
            break;
        case 3: // Councilor (top 6)
            if (count($winners['councilors']) < 6) {
                $winners['councilors'][] = $candidate;
            } elseif (count($winners['councilors']) == 6 && $winners['councilors'][5]['vote_count'] == $candidate['vote_count']) {
                // Tie for Councilor
                if (!isset($winners['councilors_tie'])) {
                    $winners['councilors_tie'] = [];
                    $winners['councilors_tie'] = array_slice($winners['councilors'], 0, 6); // Store top 6 winners
                }
                $winners['councilors_tie'][] = $candidate;
            }
            break;
        default:
            break;
    }
}

// Return winners and ties in JSON format
header('Content-Type: application/json');
echo json_encode($winners);

$conn->close();
?>
