<?php 
require __DIR__ . '/../includes/db_connect.php'; 
require_role('doctor'); 

$doctor_user_id = $_SESSION['user_id'];
$patient_id = $_GET['patient_id'] ?? null;

// Handle form submission
if ($_POST && isset($_POST['add_report'])) {
    $patient_id = $_POST['patient_id'];
    $report_text = $_POST['report_text'];
    $status = $_POST['status'];
    
    $stmt = $conn->prepare("INSERT INTO reports (patient_id, doctor_id, report_text, status) VALUES (?, ?, ?, ?)");
    $stmt->execute([$patient_id, $doctor_user_id, $report_text, $status]);
    
    $success = "Report added successfully!";
}

// Get patient info
$patient_info = null;
if ($patient_id) {
    $stmt = $conn->prepare("SELECT * FROM patients WHERE id = ?");
    $stmt->execute([$patient_id]);
    $patient_info = $stmt->fetch();
}

// Get all patients for dropdown (if no patient specified)
if (!$patient_id) {
    $stmt = $conn->prepare("SELECT id, name FROM patients ORDER BY name");
    $stmt->execute();
    $all_patients = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Medical Report - Doctor Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-purple-50 via-blue-50 to-green-50">

<!-- Navigation -->
<div class="bg-white shadow-md w-full">
    <div class="max-w-7xl mx-auto flex justify-between items-center p-4">
        <h1 class="text-2xl font-bold text-gray-800">📝 Add Medical Report</h1>
        <div class="flex space-x-3">
            <a href="../partials/doctor_panel.php" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition">← Dashboard</a>
            <a href="view_reports.php" class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 transition">View Reports</a>
            <a href="patients.php" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">Patients</a>
            <a href="/central_dashboard.php" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">Logout</a>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto p-6">
    <?php if (isset($success)): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            <?= htmlspecialchars($success) ?>
            <div class="mt-2">
                <a href="view_reports.php?patient_id=<?= $patient_id ?>" class="text-green-800 underline">View all reports for this patient</a>
            </div>
        </div>
    <?php endif; ?>

    <!-- Patient Info Display -->
    <?php if ($patient_info): ?>
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <h3 class="text-lg font-semibold text-blue-800 mb-2">Patient Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-blue-700">
                <div><strong>Name:</strong> <?= htmlspecialchars($patient_info['name']) ?></div>
                <div><strong>Age:</strong> <?= $patient_info['age'] ? htmlspecialchars($patient_info['age']) . ' years' : 'Not specified' ?></div>
                <div><strong>Gender:</strong> <?= htmlspecialchars($patient_info['gender']) ?></div>
                <div><strong>Contact:</strong> <?= htmlspecialchars($patient_info['contact']) ?: 'Not provided' ?></div>
            </div>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow-lg p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Create New Medical Report</h2>
        
        <form method="POST" class="space-y-6">
            <!-- Patient Selection -->
            <div>
                <label for="patient_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Select Patient *
                </label>
                <?php if ($patient_info): ?>
                    <input type="hidden" name="patient_id" value="<?= $patient_id ?>">
                    <div class="px-4 py-2 bg-gray-100 border border-gray-300 rounded-md">
                        <?= htmlspecialchars($patient_info['name']) ?>
                    </div>
                <?php else: ?>
                    <select name="patient_id" id="patient_id" required 
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Choose a patient...</option>
                        <?php foreach ($all_patients as $patient): ?>
                            <option value="<?= $patient['id'] ?>"><?= htmlspecialchars($patient['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>
            </div>

            <!-- Report Status -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                    Report Status *
                </label>
                <select name="status" id="status" required 
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="pending">Pending</option>
                    <option value="completed">Completed</option>
                </select>
            </div>

            <!-- Report Text -->
            <div>
                <label for="report_text" class="block text-sm font-medium text-gray-700 mb-2">
                    Medical Report *
                </label>
                <textarea name="report_text" id="report_text" rows="12" required placeholder="Enter detailed medical report, findings, diagnosis, treatment plan, etc..."
                          class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                <p class="mt-1 text-sm text-gray-500">
                    Include patient symptoms, examination findings, diagnosis, treatment recommendations, and follow-up instructions.
                </p>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-between items-center">
                <a href="<?= $patient_id ? "patients.php" : "view_reports.php" ?>" 
                   class="px-6 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition">
                    Cancel
                </a>
                <button type="submit" name="add_report" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                    💾 Save Report
                </button>
            </div>
        </form>
    </div>
</div>

</body>
</html>