<?php
include 'layouts/session.php';
include 'layouts/config.php';

if (isset($_GET['order_id'])) {
    $order_id = $link->real_escape_string($_GET['order_id']);
    
    // Get order details
    $sql = "SELECT 
            o.*,
            u.username,
            u.useremail,
            u.phone,
            u.address
            FROM orders o
            JOIN users u ON o.customer_name = u.useremail
            WHERE o.order_id = ?";
            
    $stmt = $link->prepare($sql);
    $stmt->bind_param('s', $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();

    // Get order items
    $items_sql = "SELECT * FROM orders WHERE order_id = ?";
    $items_stmt = $link->prepare($items_sql);
    $items_stmt->bind_param('s', $order_id);
    $items_stmt->execute();
    $items = $items_stmt->get_result();
    
    include 'order-detail.php';
}
