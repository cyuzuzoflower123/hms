<?php
require __DIR__ . '/../includes/db_connect.php';
require_role('staff');

// Fetch all reports
$reports = mysqli_query($conn, "
    SELECT r.id, p.username AS patient_name, d.username AS doctor_name, r.report_text, r.status, r.created_at
    FROM reports r
    LEFT JOIN users p ON r.patient_id = p.id
    LEFT JOIN users d ON r.doctor_id = d.id
    ORDER BY r.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>View Reports | Staff</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-purple-50 via-blue-50 to-green-50 p-6">

<div class="max-w-7xl mx-auto">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">📑 All Reports</h1>
        <a href="/hms-pro/dashboard.php" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">← Back</a>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-xl overflow-x-auto">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-gradient-to-r from-purple-200 to-blue-200 uppercase text-xs font-bold">
                <tr>
                    <th class="px-6 py-3">ID</th>
                    <th class="px-6 py-3">Patient</th>
                    <th class="px-6 py-3">Doctor</th>
                    <th class="px-6 py-3">Report</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($reports && mysqli_num_rows($reports) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($reports)): ?>
                    <tr class="border-b hover:bg-blue-50 transition">
                        <td class="px-6 py-4"><?= e($row['id']) ?></td>
                        <td class="px-6 py-4"><?= e($row['patient_name']) ?></td>
                        <td class="px-6 py-4"><?= e($row['doctor_name']) ?></td>
                        <td class="px-6 py-4"><?= e($row['report_text']) ?></td>
                        <td class="px-6 py-4"><?= e($row['status']) ?></td>
                        <td class="px-6 py-4"><?= e($row['created_at']) ?></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="px-6 py-6 text-center text-gray-500 italic">No reports found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>
</body>
</html>
