<?php
require __DIR__ . '/../includes/db_connect.php';
require_role('admin'); // only admin can access

$err = '';
$success = '';

// Handle adding a new user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role     = $_POST['role'];

    if ($username && $password && $role) {
        $pass_plain = $password; // store plain password
        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $pass_plain, $role);
        if ($stmt->execute()) {
            $success = "User '$username' added successfully!";
        } else {
            $err = "Error: " . $conn->error;
        }
    } else {
        $err = "All fields are required!";
    }
}

// Handle deleting a user
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM users WHERE id = $id");
    header("Location: manage_users.php");
    exit;
}

// Fetch all users
$result = $conn->query("SELECT id, username, role FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Manage Users | Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-green-50 via-blue-50 to-purple-100 p-6 text-gray-800">

<div class="max-w-6xl mx-auto">

  <div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold">👥 Manage Users</h1>
    <a href="/hms-pro/dashboard.php" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">← Back</a>
  </div>

  <!-- Messages -->
  <?php if($err): ?>
    <div class="bg-red-500 text-white p-3 rounded mb-4"><?= e($err) ?></div>
  <?php endif; ?>
  <?php if($success): ?>
    <div class="bg-green-500 text-white p-3 rounded mb-4"><?= e($success) ?></div>
  <?php endif; ?>

  <!-- Add User Form -->
  <div class="bg-white p-6 rounded-2xl shadow-xl mb-8">
    <h2 class="text-xl font-bold mb-4">Add New User</h2>
    <form method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
      <div>
        <label class="block mb-1">Username</label>
        <input type="text" name="username" class="w-full p-2 border rounded" required>
      </div>
      <div>
        <label class="block mb-1">Password</label>
        <input type="text" name="password" class="w-full p-2 border rounded" required>
      </div>
      <div>
        <label class="block mb-1">Role</label>
        <select name="role" class="w-full p-2 border rounded" required>
          <option value="admin">Admin</option>
          <option value="admin2">Admin2</option>
          <option value="doctor">Doctor</option>
          <option value="staff">Staff</option>
        </select>
      </div>
      <div class="md:col-span-3">
        <button type="submit" name="add_user" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition mt-2">Add User</button>
      </div>
    </form>
  </div>

  <!-- Users Table -->
  <div class="bg-white p-6 rounded-2xl shadow-xl overflow-x-auto">
    <table class="min-w-full text-left text-sm">
      <thead class="bg-gradient-to-r from-green-200 to-blue-200 uppercase text-xs font-bold">
        <tr>
          <th class="px-6 py-3">ID</th>
          <th class="px-6 py-3">Username</th>
          <th class="px-6 py-3">Role</th>
          <th class="px-6 py-3">Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr class="border-b hover:bg-blue-50 transition">
          <td class="px-6 py-4"><?= e($row['id']) ?></td>
          <td class="px-6 py-4 font-medium"><?= e($row['username']) ?></td>
          <td class="px-6 py-4"><?= ucfirst(e($row['role'])) ?></td>
          <td class="px-6 py-4">
            <a href="?delete=<?= $row['id'] ?>" class="text-red-600 hover:underline" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
          </td>
        </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr>
          <td colspan="4" class="px-6 py-6 text-center text-gray-500 italic">No users found.</td>
        </tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>

</div>
</body>
</html>
