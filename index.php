<?php
require_once 'auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $result = Auth::login($username, $password);
    if ($result['success']) {
        header('Location: store.php');
        exit;
    } else {
        $error = $result['message'];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - GTPS Store</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="login-container">
        <h1>Login</h1>
        <?php if (isset($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST">
            <label>Username:</label>
            <input type="text" name="username" required>
            <label>Password:</label>
            <input type="password" name="password" required>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
