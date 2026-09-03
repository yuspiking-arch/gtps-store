<?php
// api.php
require_once 'config.php';
require_once 'db.php';
require_once 'auth.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

// Logout
if ($action === 'logout') {
    Auth::logout();
    header('Location: index.php');
    exit;
}

// Get Items
if ($action === 'get_items') {
    $db = Database::getStoreDb();
    $items = Database::fetchAll($db, "SELECT * FROM store_items ORDER BY price_rp ASC");
    echo json_encode(['success' => true, 'items' => $items]);
    exit;
}

// Create Order
if ($action === 'create_order') {
    Auth::requireLogin();
    $input = json_decode(file_get_contents('php://input'), true);
    $itemId = (int)($input['item_id'] ?? 0);
    $count = (int)($input['count'] ?? 1);

    if ($itemId <= 0 || $count <= 0) {
        echo json_encode(['success' => false, 'message' => 'Parameter tidak valid']);
        exit;
    }

    $db = Database::getStoreDb();
    $item = Database::fetchOne($db, "SELECT * FROM store_items WHERE item_id = :item_id", [':item_id' => $itemId]);
    if (!$item) {
        echo json_encode(['success' => false, 'message' => 'Item tidak ditemukan']);
        exit;
    }

    $username = Auth::getUser()['username'];
    $orderId = 'STORE-' . time() . '-' . rand(1000, 9999);

    $sql = "INSERT INTO orders (username, item_id, count, status, order_id) 
            VALUES (:username, :item_id, :count, 'pending', :order_id)";
    Database::execute($db, $sql, [
        ':username' => $username,
        ':item_id' => $itemId,
        ':count' => $count,
        ':order_id' => $orderId
    ]);

    echo json_encode(['success' => true, 'order_id' => $orderId]);
    exit;
}

// Deliver Order (admin)
if ($action === 'deliver_order') {
    Auth::requireAdmin();
    $orderId = (int)($_POST['order_id'] ?? 0);
    if (!$orderId) {
        die('Invalid order');
    }

    $db = Database::getStoreDb();
    Database::execute($db, "UPDATE orders SET status = 'delivered', updated_at = CURRENT_TIMESTAMP WHERE id = :id", [':id' => $orderId]);
    header('Location: dashboard.php');
    exit;
}

http_response_code(404);
echo json_encode(['success' => false, 'message' => 'Endpoint tidak ditemukan']);
?>
