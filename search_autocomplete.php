<?php
include 'db.php';

// Check if query parameter exists
if(isset($_GET['query']) && !empty($_GET['query'])) {
    $query = mysqli_real_escape_string($conn, $_GET['query']);
    
    // Search for beverages that start with the query
    $sql = "SELECT id, name FROM beverages WHERE name LIKE '$query%' ORDER BY name LIMIT 10";
    $result = mysqli_query($conn, $sql);
    
    $suggestions = array();
    
    if($result && mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            $suggestions[] = array(
                'id' => $row['id'],
                'name' => $row['name']
            );
        }
    }
    
    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($suggestions);
} else {
    // Return empty array if no query
    header('Content-Type: application/json');
    echo json_encode([]);
}
?>