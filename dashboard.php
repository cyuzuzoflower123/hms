<?php
require __DIR__ . '/includes/db_connect.php';
require_login(); // ensure user is logged in

$role = $_SESSION['user']['role'];
$username = $_SESSION['user']['username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= ucfirst($role) ?> Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
  <!-- Header -->
  <header class="bg-blue-700 text-white p-4 flex justify-between items-center">
    <h1 class="text-xl font-bold">FloHospital🏥</h1>
    <div>
      <span class="mr-4"><?= htmlspecialchars($username) ?> (<?= htmlspecialchars($role) ?>)</span>
      <a href="logout.php" class="bg-red-600 px-3 py-1 rounded hover:bg-red-700">Logout</a>
    </div>
  </header>

  <!-- Main Content -->
  <main class="flex-grow p-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

      <?php
      // Include role-specific panel
      switch($role){
          case 'admin':
          case 'admin2':  // treat admin2 same as admin
              include __DIR__.'/partials/admin_panel.php';
              break;
          case 'doctor':
              include __DIR__.'/partials/doctor_panel.php';
              break;
          case 'staff':
              include __DIR__.'/partials/staff_panel.php';
              break;
          default:
              echo '<div class="col-span-full bg-gray-200 p-4 rounded">Custom role dashboard for '.htmlspecialchars($role).'</div>';
      }
      ?>

    </div>
  </main>
</body>
</html>
