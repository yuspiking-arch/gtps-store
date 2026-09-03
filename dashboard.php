<?php
require_once 'auth.php';
Auth::requireAdmin();
require_once 'db.php';

$db = Database::getStoreDb();
$orders = Database::fetchAll($db, "SELECT * FROM orders ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="header">
        <span>Admin: <?= htmlspecialchars(Auth::getUser()['username']) ?></span>
        <a href="store.php">Kembali ke Store</a>
        <a href="api.php?action=logout">Logout</a>
    </div>

    <h1>Manajemen Pesanan</h1>
    <table border="1" cellpadding="5">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Item ID</th>
                <th>Jumlah</th>
                <th>Status</th>
                <th>Order ID</th>
                <th>Dibuat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($orders as $order): ?>
            <tr>
                <td><?= $order['id'] ?></td>
                <td><?= htmlspecialchars($order['username']) ?></td>
                <td><?= $order['item_id'] ?></td>
                <td><?= $order['count'] ?></td>
                <td><span class="status-<?= $order['status'] ?>"><?= $order['status'] ?></span></td>
                <td><?= htmlspecialchars($order['order_id'] ?? '-') ?></td>
                <td><?= $order['created_at'] ?></td>
                <td>
                    <?php if ($order['status'] === 'paid'): ?>
                        <form action="api.php?action=deliver_order" method="POST">
                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                            <button type="submit">Kirim Item</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
