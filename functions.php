<?php
// Helper functions will be added here
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function is_admin() {
    global $conn;
    if (!is_logged_in()) {
        return false;
    }

    $user_id = $_SESSION['user_id'];
    $sql = "SELECT roles.name FROM users JOIN roles ON users.role_id = roles.id WHERE users.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    return $result && $result['name'] === 'admin';
}


?>
