<?php
include('../database/db_connection.php');

header('Content-Type: application/json');

if (isset($_GET['country_id'])) {
    // Fetch distinct states for a given country from the branches table
    $countryName = $_GET['country_id']; // Expecting country name from JS
    $sql = "SELECT DISTINCT state AS name FROM branches WHERE country = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $countryName); // Bind as string
    $stmt->execute();
    $result = $stmt->get_result();
    $states = $result->fetch_all(MYSQLI_ASSOC);

    // Map to include 'id' field, using state name as id for simplicity
    $state_options = [];
    foreach ($states as $state) {
        $state_options[] = ['id' => $state['name'], 'name' => $state['name']];
    }
    echo json_encode($state_options);

} elseif (isset($_GET['state_id'])) {
    // Fetch branches for a given state from the branches table
    $stateName = $_GET['state_id']; // Expecting state name from JS
    $sql = "SELECT branch_id AS id, branch_name AS name FROM branches WHERE state = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $stateName); // Bind as string
    $stmt->execute();
    $result = $stmt->get_result();
    $branches = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($branches);

} else {
    // Fetch distinct countries from the branches table
    $sql = "SELECT DISTINCT country AS name FROM branches";
    $result = $conn->query($sql);
    $countries = $result->fetch_all(MYSQLI_ASSOC);

    // Map to include 'id' field, using country name as id for simplicity
    $country_options = [];
    foreach ($countries as $country) {
        $country_options[] = ['id' => $country['name'], 'name' => $country['name']];
    }
    echo json_encode($country_options);
}
?>
