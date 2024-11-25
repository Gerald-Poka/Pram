<?php
header('Content-Type: application/json');
session_start();
include_once 'layouts/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_items'])) {
    $order_items = json_decode($_POST['order_items'], true);
    $user_id = $_SESSION['id'];
    $sql = "SELECT useremail FROM users WHERE id = ?";
    $stmt = $link->prepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }

    $order_date = date('Y-m-d H:i:s');
    $order_id = 'ORD-' . date('Ymd') . '-' . rand(1000, 9999);

    $link->begin_transaction();
    
    try {
        $stmt = $link->prepare("INSERT INTO orders (order_id, customer_name, medicine_name, quantity, price, total, status, order_date) VALUES (?, ?, ?, ?, ?, ?, 'Processing', ?)");
        
        foreach ($order_items as $item) {
            $stmt->bind_param('sssdids', 
                $order_id,
                $user['useremail'],
                $item['name'],
                $item['quantity'],
                $item['price'],
                $item['total'],
                $order_date
            );
            $stmt->execute();
        }
        
        $link->commit();
        echo json_encode(['success' => true, 'order_id' => $order_id]);
        
    } catch (Exception $e) {
        $link->rollback();
        echo json_encode(['success' => false, 'message' => 'Transaction failed']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
