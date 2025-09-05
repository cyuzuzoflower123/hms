<?php
require __DIR__ . '/../includes/db_connect.php';
require_role('admin');

$err = $success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hospital_name = trim($_POST['hospital_name'] ?? '');
    $address       = trim($_POST['address'] ?? '');
    if ($hospital_name && $address) {
        // Save settings (example: into a table `hospital_settings`)
        $conn->query("UPDATE hospital_settings SET hospital_name='$hospital_name', address='$address' WHERE id=1");
        $success = "Hospital settings updated successfully!";
    } else {
        $err = "All fields are required!";
    }
}

// Fetch existing settings
$settings = $conn->query("SELECT * FROM hospital_settings WHERE id=1")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Hospital Settings | Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-50 via-green-50 to-purple-50 p-6 text-gray-800">

<div class="max-w-3xl mx-auto">

  <div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold">⚙️ Hospital Settings</h1>
    <a href="/hms-pro/dashboard.php" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">← Back</a>
  </div>

  <!-- Messages -->
  <?php if($err): ?><div class="bg-red-500 text-white p-3 rounded mb-4"><?= e($err) ?></div><?php endif; ?>
  <?php if($success): ?><div class="bg-green-500 text-white p-3 rounded mb-4"><?= e($success) ?></div><?php endif; ?>

  <form method="POST" class="bg-white p-6 rounded-2xl shadow-xl space-y-4">
    <div>
      <label class="block mb-1 font-medium">Hospital Name</label>
      <input type="text" name="hospital_name" class="w-full p-2 border rounded" value="<?= e($settings['hospital_name'] ?? '') ?>" required>
    </div>
    <div>
      <label class="block mb-1 font-medium">Address</label>
      <input type="text" name="address" class="w-full p-2 border rounded" value="<?= e($settings['address'] ?? '') ?>" required>
    </div>
    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">Save Settings</button>
  </form>

</div>
</body>
</html>
