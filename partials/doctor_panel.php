<?php
require __DIR__ . '/../includes/db_connect.php';
require_role('doctor');

$doctor_name = $_SESSION['user']['name'] ?? "Doctor";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Doctor Panel</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">

  <header class="bg-white shadow-md p-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-indigo-700">👨‍⚕️ Welcome, Dr. <?= htmlspecialchars($doctor_name) ?></h1>
    <a href="../dashboard.php" class="text-indigo-600 hover:underline">⬅ Back to Central Dashboard</a>
  </header>

  <main class="max-w-6xl mx-auto py-10 px-4 grid md:grid-cols-2 lg:grid-cols-3 gap-6">

    <a href="../doctor/appointments.php" class="bg-white p-6 rounded-xl shadow-lg hover:shadow-2xl transition">
      <h2 class="text-xl font-semibold text-indigo-600">📅 Appointments</h2>
      <p class="text-gray-600 mt-2">View and manage your appointments.</p>
    </a>

    <a href="../doctor/patients.php" class="bg-white p-6 rounded-xl shadow-lg hover:shadow-2xl transition">
      <h2 class="text-xl font-semibold text-indigo-600">👥 Patients</h2>
      <p class="text-gray-600 mt-2">See a list of your patients.</p>
    </a>

    <a href="../doctor/add_report.php" class="bg-white p-6 rounded-xl shadow-lg hover:shadow-2xl transition">
      <h2 class="text-xl font-semibold text-indigo-600">📝 Add Report</h2>
      <p class="text-gray-600 mt-2">Submit new patient reports.</p>
    </a>

    <a href="../doctor/view_reports.php" class="bg-white p-6 rounded-xl shadow-lg hover:shadow-2xl transition">
      <h2 class="text-xl font-semibold text-indigo-600">📑 View Reports</h2>
      <p class="text-gray-600 mt-2">Check submitted reports.</p>
    </a>

    <a href="../doctor/profile.php" class="bg-white p-6 rounded-xl shadow-lg hover:shadow-2xl transition">
      <h2 class="text-xl font-semibold text-indigo-600">⚙️ Profile Settings</h2>
      <p class="text-gray-600 mt-2">Manage your profile and settings.</p>
    </a>

  </main>
</body>
</html>
