<?php
require __DIR__ . '/../includes/db_connect.php';
require_login();

$result = @mysqli_query($conn, "SELECT * FROM audit_logs ORDER BY created_at DESC");
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Audit Logs | Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50 to-purple-50">

  <div class="max-w-6xl mx-auto p-20">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-3xl font-bold">🔍 Audit Logs</h1>
      <a href="/hms-pro/dashboard.php" class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700">← Back</a>
    </div>

    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
      <table class="min-w-full text-left text-sm">
        <thead class="bg-gradient-to-r from-gray-200 to-blue-200 uppercase text-xs font-bold">
          <tr>
            <th class="px-6 py-3">ID</th>
            <th class="px-6 py-3">Action</th>
            <th class="px-6 py-3">User</th>
            <th class="px-6 py-3">Date</th>
          </tr>
        </thead>
        <tbody>
        <?php if ($result && mysqli_num_rows($result) > 0): ?>
          <?php while ($row = mysqli_fetch_assoc($result)): ?>
          <tr class="border-b hover:bg-blue-50 transition">
            <td class="px-6 py-4"><?= e($row['id']) ?></td>
            <td class="px-6 py-4"><?= e($row['action']) ?></td>
            <td class="px-6 py-4"><?= e($row['username']) ?></td>
            <td class="px-6 py-4"><?= e($row['created_at']) ?></td>
          </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="4" class="px-6 py-6 text-center text-gray-500 italic">No logs found.</td>
          </tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
