<?php
require __DIR__ . '/../includes/db_connect.php';
require_role('admin');

$result = @mysqli_query($conn, "SELECT a.id, u.username, a.action, a.created_at
                                FROM audit_logs a
                                LEFT JOIN users u ON a.user_id = u.id
                                ORDER BY a.created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Audit Logs | Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-yellow-50 via-orange-50 to-red-50 p-6 text-gray-800">

<div class="max-w-6xl mx-auto">

  <div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold">🔍 Audit Logs</h1>
    <a href="/hms-pro/dashboard.php" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">← Back</a>
  </div>

  <div class="bg-white p-6 rounded-2xl shadow-xl overflow-x-auto">
    <table class="min-w-full text-left text-sm">
      <thead class="bg-gradient-to-r from-yellow-200 to-orange-200 uppercase text-xs font-bold">
        <tr>
          <th class="px-6 py-3">ID</th>
          <th class="px-6 py-3">User</th>
          <th class="px-6 py-3">Action</th>
          <th class="px-6 py-3">Timestamp</th>
        </tr>
      </thead>
      <tbody>
      <?php if ($result && mysqli_num_rows($result) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr class="border-b hover:bg-orange-50 transition">
          <td class="px-6 py-4"><?= e($row['id']) ?></td>
          <td class="px-6 py-4"><?= e($row['username']) ?></td>
          <td class="px-6 py-4"><?= e($row['action']) ?></td>
          <td class="px-6 py-4"><?= e($row['created_at']) ?></td>
        </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr>
          <td colspan="4" class="px-6 py-6 text-center text-gray-500 italic">No audit logs found.</td>
        </tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>

</div>
</body>
</html>
