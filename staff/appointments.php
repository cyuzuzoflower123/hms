<?php
require __DIR__ . '/../includes/db_connect.php';
require_role('staff'); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Patient Records</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-6">
<?php include __DIR__ . '/../includes/navbar.php'; ?>
<h1 class="text-2xl font-bold mb-4">Patient Records</h1>
<p>Staff can view and manage patient records here.</p>
</body>
</html>
