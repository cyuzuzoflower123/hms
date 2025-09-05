<?php
require __DIR__ . '/../includes/db_connect.php';
require_role('staff');

$err = '';

// Fetch existing patients
$patients = $conn->query("SELECT username FROM users WHERE role='patient' ORDER BY username ASC");

// Fetch existing doctors
$doctors = $conn->query("SELECT id, username FROM users WHERE role='doctor' ORDER BY username ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_name = trim($_POST['patient_name'] ?? '');
    $doctor_id = trim($_POST['doctor_id'] ?? '');
    $status = trim($_POST['status'] ?? '');
    $report_text = trim($_POST['report_text'] ?? '');

    if (!$patient_name || !$doctor_id || !$status || !$report_text) {
        $err = "Please fill all fields.";
    } else {
        // Check if patient exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username=? AND role='patient' LIMIT 1");
        $stmt->bind_param("s", $patient_name);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $patient_id = $row['id'];
        } else {
            // Create new patient in users table
            $patient_safe = $conn->real_escape_string($patient_name);
            $conn->query("INSERT INTO users (username, role) VALUES ('$patient_safe', 'patient')");
            $patient_id = $conn->insert_id;
        }

        // Insert report
        $stmt = $conn->prepare("INSERT INTO reports (patient_id, doctor_id, report_text, status, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("iiss", $patient_id, $doctor_id, $report_text, $status);

        if ($stmt->execute()) {
            header("Location: /hms-pro/dashboard.php");
            exit;
        } else {
            $err = "Failed to add report: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Add Report</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-purple-50 via-pink-50 to-orange-50 p-6">

<div class="max-w-3xl mx-auto bg-white p-8 rounded-2xl shadow-lg">
    <h1 class="text-2xl font-bold mb-6 text-center">➕ Add Report</h1>

    <?php if($err): ?>
        <div class="bg-red-600 text-white p-3 rounded mb-4"><?= e($err) ?></div>
    <?php endif; ?>

    <form method="post" class="space-y-4">
        <!-- Patient field -->
        <div>
            <label class="block mb-1 font-semibold">Patient</label>
            <input list="patients" name="patient_name" class="w-full px-3 py-2 border rounded" placeholder="Type or select patient" required>
            <datalist id="patients">
                <?php while($p = $patients->fetch_assoc()): ?>
                    <option value="<?= e($p['username']) ?>"></option>
                <?php endwhile; ?>
            </datalist>
        </div>

        <!-- Doctor field -->
        <div>
            <label class="block mb-1 font-semibold">Doctor</label>
            <select name="doctor_id" class="w-full px-3 py-2 border rounded" required>
                <option value="">Select doctor</option>
                <?php while($d = $doctors->fetch_assoc()): ?>
                    <option value="<?= e($d['id']) ?>"><?= e($d['username']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <!-- Status field -->
        <div>
            <label class="block mb-1 font-semibold">Status</label>
            <input type="text" name="status" class="w-full px-3 py-2 border rounded" placeholder="e.g., Pending, Completed" required>
        </div>

        <!-- Report Text -->
        <div>
            <label class="block mb-1 font-semibold">Report Text</label>
            <textarea name="report_text" class="w-full px-3 py-2 border rounded" rows="5" placeholder="Enter report details..." required></textarea>
        </div>

        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 w-full">Add Report</button>
    </form>
</div>
</body>
</html>
