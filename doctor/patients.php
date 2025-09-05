<?php 
require __DIR__ . '/../includes/db_connect.php'; 
require_role('doctor'); 

$doctor_user_id = $_SESSION['user_id'];

// Search functionality
$search = $_GET['search'] ?? '';
$search_condition = '';
$params = [];

if ($search) {
    $search_condition = "AND (p.name LIKE ? OR p.contact LIKE ?)";
    $params = ["%$search%", "%$search%"];
}

// Fetch patients
$stmt = $conn->prepare("
    SELECT p.*, COUNT(r.id) as report_count,
           MAX(r.created_at) as last_report_date
    FROM patients p 
    LEFT JOIN reports r ON p.id = r.patient_id AND r.doctor_id = ?
    WHERE 1=1 $search_condition
    GROUP BY p.id
    ORDER BY p.created_at DESC
");
$stmt->execute(array_merge([$doctor_user_id], $params));
$patients = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Patients - Doctor Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-purple-50 via-blue-50 to-green-50">

<!-- Navigation -->
<div class="bg-white shadow-md w-full">
    <div class="max-w-7xl mx-auto flex justify-between items-center p-4">
        <h1 class="text-2xl font-bold text-gray-800">👥 My Patients</h1>
        <div class="flex space-x-3">
            <a href="../partials/doctor_panel.php" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition">← Dashboard</a>
            <a href="appointments.php" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">Appointments</a>
            <a href="view_reports.php" class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 transition">Reports</a>
            <a href="/central_dashboard.php" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">Logout</a>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto p-6">
    <!-- Search Bar -->
    <div class="bg-white rounded-lg shadow-lg p-4 mb-6">
        <form method="GET" class="flex items-center space-x-4">
            <div class="flex-1">
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                       placeholder="Search patients by name or contact..." 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                🔍 Search
            </button>
            <a href="patients.php" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                Clear
            </a>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Patient List</h2>
            <div class="text-sm text-gray-500">
                Total: <?= count($patients) ?> patients
            </div>
        </div>
        
        <?php if (empty($patients)): ?>
            <div class="text-center py-8">
                <p class="text-gray-500 text-lg">No patients found</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($patients as $patient): ?>
                    <div class="bg-gray-50 rounded-lg p-6 border border-gray-200 hover:shadow-md transition">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800"><?= htmlspecialchars($patient['name']) ?></h3>
                                <p class="text-sm text-gray-600">
                                    <?= $patient['age'] ? htmlspecialchars($patient['age']) . ' years old' : 'Age not specified' ?> • 
                                    <?= htmlspecialchars($patient['gender']) ?>
                                </p>
                            </div>
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                                <?= $patient['report_count'] ?> reports
                            </span>
                        </div>
                        
                        <div class="space-y-2 mb-4">
                            <p class="text-sm text-gray-600">
                                📞 <?= htmlspecialchars($patient['contact']) ?: 'No contact' ?>
                            </p>
                            <p class="text-sm text-gray-600">
                                📅 Registered: <?= date('M j, Y', strtotime($patient['created_at'])) ?>
                            </p>
                            <?php if ($patient['last_report_date']): ?>
                                <p class="text-sm text-gray-600">
                                    📋 Last report: <?= date('M j, Y', strtotime($patient['last_report_date'])) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="flex space-x-2">
                            <a href="add_report.php?patient_id=<?= $patient['id'] ?>" 
                               class="flex-1 px-3 py-2 bg-green-600 text-white text-sm rounded text-center hover:bg-green-700 transition">
                                Add Report
                            </a>
                            <a href="view_reports.php?patient_id=<?= $patient['id'] ?>" 
                               class="flex-1 px-3 py-2 bg-blue-600 text-white text-sm rounded text-center hover:bg-blue-700 transition">
                                View Reports
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>