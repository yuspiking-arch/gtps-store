<?php
require_once 'auth.php';
Auth::requireLogin();
$user = Auth::getUser();
?>
<!DOCTYPE html>
<html>
<head>
    <title>GTPS Store</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="header">
        <span>Halo, <?= htmlspecialchars($user['username']) ?></span>
        <?php if (Auth::isAdmin()): ?>
            <a href="dashboard.php">Dashboard Admin</a>
        <?php endif; ?>
        <a href="api.php?action=logout">Logout</a>
    </div>

    <h1>Daftar Item</h1>
    <div id="items-container"></div>

    <script>
        // Muat daftar item
        fetch('api.php?action=get_items')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const container = document.getElementById('items-container');
                    data.items.forEach(item => {
                        const div = document.createElement('div');
                        div.className = 'item-card';
                        div.innerHTML = `
                            <h3>${item.name}</h3>
                            <p>Harga: Rp ${item.price_rp.toLocaleString()}</p>
                            <p>${item.description || ''}</p>
                            <button onclick="buyItem(${item.item_id})">Beli</button>
                        `;
                        container.appendChild(div);
                    });
                }
            });

        function buyItem(itemId) {
            fetch('api.php?action=create_order', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ item_id: itemId, count: 1 })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Pesanan berhasil dibuat!');
                } else {
                    alert('Gagal: ' + data.message);
                }
            });
        }
    </script>
</body>
</html>
