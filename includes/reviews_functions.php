<?php
require_once 'db_connect.php';

function add_review($product_id, $user_id, $rating, $review_text) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO reviews (product_id, user_id, rating, review_text) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $product_id, $user_id, $rating, $review_text);
    $stmt->execute();
    $stmt->close();
}

function get_reviews_for_product($product_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT r.*, u.username FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.product_id = ? ORDER BY r.created_at DESC");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $reviews = [];
    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }
    $stmt->close();
    return $reviews;
}

function get_average_rating($product_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT AVG(rating) AS avg_rating FROM reviews WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    return round($row['avg_rating'], 1);
}
?>
