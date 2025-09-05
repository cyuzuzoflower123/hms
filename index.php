<?php
session_start();
require __DIR__ . '/includes/db_connect.php';

$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = trim($_POST['username']);
    $p = $_POST['password'];

    // Use prepared statements for safety
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $u);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && $p === $user['password']) {
        $_SESSION['user'] = [
            'id'        => $user['id'],
            'username'  => $user['username'],
            'role'      => $user['role'],
            'doctor_id' => $user['doctor_id']
        ];

        // Redirect to central dashboard
        header('Location: dashboard.php');
        exit;
    } else {
        $err = 'Invalid username or password.';
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>HMS Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="/hms-pro/assets/css/styles.css">
</head>
<body class="min-h-screen text-white">
<section class="hero-bg min-h-screen flex items-center justify-center">
  <div class="bg-white/10 backdrop-blur-md p-8 rounded-2xl w-full max-w-md shadow-xl">
    <h1 class="text-2xl font-bold mb-6 text-center">Hospital Management System</h1>
    <?php if ($err): ?>
      <div class="bg-red-600/70 rounded p-3 mb-4 text-sm">
        <?= htmlspecialchars($err) ?>
      </div>
    <?php endif; ?>
    <form method="post" class="space-y-3">
      <div>
        <label class="block text-sm mb-1">Username</label>
        <input name="username" class="w-full rounded-lg px-3 py-2 text-slate-900" required>
      </div>
      <div>
        <label class="block text-sm mb-1">Password</label>
        <input type="password" name="password" class="w-full rounded-lg px-3 py-2 text-slate-900" required>
      </div>
      <button class="btn w-full mt-2" type="submit">Login</button>
    </form>
  </div>
</section>
</body>
</html>
