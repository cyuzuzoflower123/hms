<?php
// FILE 1: doctor/appointments.php
?>
<?php 
require __DIR__ . '/../includes/db_connect.php'; 
require_role('doctor'); 

// Get current doctor's ID from session
$doctor_user_id = $_SESSION['user_id'];

// Handle appointment status updates
if ($_POST && isset($_POST['update_appointment'])) {
    $appointment_id = $_POST['appointment_id'];
    $notes = $_POST['notes'];
    
    $stmt = $conn->prepare("UPDATE appointments SET notes = ? WHERE id = ? AND doctor_id = ?");
    $stmt->execute([$notes, $appointment_id, $doctor_user_id]);
    
    $success = "Appointment updated successfully!";
}

// Fetch appointments for this doctor
$stmt = $conn->prepare("
    SELECT a.*, p.name as patient_name, p.contact, p.age, p.gender 
    FROM appointments a 
    JOIN patients p ON a.patient_id = p.id 
    WHERE a.doctor_id = ? 
    ORDER BY a.appointment_date DESC
");
$stmt->execute([$doctor_user_id]);
$appointments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Appointments - Doctor Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-purple-50 via-blue-50 to-green-50">

<!-- Navigation -->
<div class="bg-white shadow-md w-full">
    <div class="max-w-7xl mx-auto flex justify-between items-center p-4">
        <h1 class="text-2xl font-bold text-gray-800">📅 My Appointments</h1>
        <div class="flex space-x-3">
            <a href="../partials/doctor_panel.php" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition">← Dashboard</a>
            <a href="patients.php" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">Patients</a>
            <a href="view_reports.php" class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 transition">Reports</a>
            <a href="/central_dashboard.php" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">Logout</a>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto p-6">
    <?php if (isset($success)): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Scheduled Appointments</h2>
        
        <?php if (empty($appointments)): ?>
            <div class="text-center py-8">
                <p class="text-gray-500 text-lg">No appointments scheduled</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($appointments as $appointment): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($appointment['patient_name']) ?></div>
                                    <div class="text-sm text-gray-500"><?= htmlspecialchars($appointment['age']) ?>y, <?= htmlspecialchars($appointment['gender']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?= date('M j, Y', strtotime($appointment['appointment_date'])) ?></div>
                                    <div class="text-sm text-gray-500"><?= date('g:i A', strtotime($appointment['appointment_date'])) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= htmlspecialchars($appointment['contact']) ?>
                                </td>
                                <td class="px-6 py-4">
                                    <form method="POST" class="flex items-center space-x-2">
                                        <input type="hidden" name="appointment_id" value="<?= $appointment['id'] ?>">
                                        <textarea name="notes" class="w-64 p-2 border border-gray-300 rounded text-sm" rows="2" placeholder="Add notes..."><?= htmlspecialchars($appointment['notes']) ?></textarea>
                                        <button type="submit" name="update_appointment" class="px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">Update</button>
                                    </form>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="add_report.php?patient_id=<?= $appointment['patient_id'] ?>" class="text-green-600 hover:text-green-900 mr-3">Add Report</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>

<?php