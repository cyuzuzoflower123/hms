<?php 
require __DIR__ . '/../includes/db_connect.php'; 
require_role('doctor'); 

$doctor_user_id = $_SESSION['user_id'];
$patient_id = $_GET['patient_id'] ?? null;

// Build query based on patient filter
$where_condition = "r.doctor_id = ?";
$params = [$doctor_user_id];

if ($patient_id) {
    $where_condition .= " AND r.patient_id = ?";
    $params[] = $patient_id;
}

// Fetch reports
$stmt = $conn->prepare("
    SELECT r.*, p.name as patient_name, p.age, p.gender, p.contact
    FROM reports r 
    JOIN patients p ON r.patient_id = p.id 
    WHERE $where_condition
    ORDER BY r.created_at DESC
");
$stmt->execute($params);
$reports = $stmt->fetchAll();

// Get patient info if filtering by patient
$patient_info = null;
if ($patient_id) {
    $stmt = $conn->prepare("SELECT * FROM patients WHERE id = ?");
    $stmt->execute([$patient_id]);
    $patient_info = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Medical Reports - Doctor Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-purple-50 via-blue-50 to-green-50">

<!-- Navigation -->
<div class="bg-white shadow-md w-full">
    <div class="max-w-7xl mx-auto flex justify-between items-center p-4">
        <h1 class="text-2xl font-bold text-gray-800">📋 Medical Reports</h1>
        <div class="flex space-x-3">
            <a href="../partials/doctor_panel.php" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition">← Dashboard</a>
            <a href="appointments.php" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">Appointments</a>
            <a href="patients.php" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">Patients</a>
            <a href="/central_dashboard.php" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">Logout</a>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto p-6">
    <!-- Patient Filter Info -->
    <?php if ($patient_info): ?>
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold text-blue-800">
                        Reports for: <?= htmlspecialchars($patient_info['name']) ?>
                    </h3>
                    <p class="text-blue-600">
                        <?= $patient_info['age'] ?>y • <?= htmlspecialchars($patient_info['gender']) ?> • 
                        📞 <?= htmlspecialchars($patient_info['contact']) ?>
                    </p>
                </div>
                <div class="space-x-2">
                    <a href="add_report.php?patient_id=<?= $patient_id ?>" 
                       class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                        Add New Report
                    </a>
                    <a href="view_reports.php" 
                       class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition">
                        View All Reports
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">
                <?= $patient_info ? 'Patient Reports' : 'All My Reports' ?>
            </h2>
            <div class="text-sm text-gray-500">
                Total: <?= count($reports) ?> reports
            </div>
        </div>
        
        <?php if (empty($reports)): ?>
            <div class="text-center py-8">
                <p class="text-gray-500 text-lg">No reports found</p>
                <?php if ($patient_id): ?>
                    <a href="add_report.php?patient_id=<?= $patient_id ?>" 
                       class="mt-4 inline-block px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                        Create First Report
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="space-y-6">
                <?php foreach ($reports as $report): ?>
                    <div class="border border-gray-200 rounded-lg p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-800">
                                    <?= htmlspecialchars($report['patient_name']) ?>
                                </h3>
                                <div class="flex items-center space-x-4 text-sm text-gray-600 mt-1">
                                    <span><?= $report['age'] ?>y • <?= htmlspecialchars($report['gender']) ?></span>
                                    <span>📞 <?= htmlspecialchars($report['contact']) ?></span>
                                    <span>📅 <?= date('M j, Y g:i A', strtotime($report['created_at'])) ?></span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="px-3 py-1 text-xs rounded-full <?= 
                                    $report['status'] === 'completed' 
                                        ? 'bg-green-100 text-green-800' 
                                        : 'bg-yellow-100 text-yellow-800' 
                                ?>">
                                    <?= ucfirst($report['status']) ?>
                                </span>
                                <a href="add_report.php?patient_id=<?= $report['patient_id'] ?>" 
                                   class="text-blue-600 hover:text-blue-800 text-sm">
                                    Add New Report
                                </a>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-800 mb-2">Medical Report:</h4>
                            <div class="text-gray-700 whitespace-pre-wrap">
                                <?= htmlspecialchars($report['report_text']) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>