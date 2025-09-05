<?php
require __DIR__ . '/../includes/db_connect.php';
require_role('staff');

// Fetch appointments
$appointments = mysqli_query($conn, "
    SELECT a.id, u.username AS patient_name, d.username AS doctor_name, a.appointment_date, a.notes
    FROM appointments a
    LEFT JOIN users u ON a.patient_id = u.id
    LEFT JOIN users d ON a.doctor_id = d.id
    ORDER BY a.appointment_date DESC
");

// Fetch reports
$reports = mysqli_query($conn, "
    SELECT r.id, p.username AS patient_name, d.username AS doctor_name, r.report_text, r.status, r.created_at
    FROM reports r
    LEFT JOIN users p ON r.patient_id = p.id
    LEFT JOIN users d ON r.doctor_id = d.id
    ORDER BY r.created_at DESC
");

// Fetch patients and doctors for datalist
$patients = mysqli_query($conn, "SELECT username FROM users WHERE role='patient'");
$doctors  = mysqli_query($conn, "SELECT username FROM users WHERE role='doctor'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Staff Dashboard</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-purple-50 via-blue-50 to-green-50 p-6">

<div class="max-w-7xl mx-auto">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">👨‍⚕️ Staff Dashboard</h1>
    </div>

    <!-- Appointments Section -->
    <div class="mb-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-semibold">📅 Appointments</h2>
            <button id="addAppointmentBtn" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add Appointment</button>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-xl overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-gradient-to-r from-purple-200 to-blue-200 uppercase text-xs font-bold">
                    <tr>
                        <th class="px-6 py-3">ID</th>
                        <th class="px-6 py-3">Patient</th>
                        <th class="px-6 py-3">Doctor</th>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($appointments && mysqli_num_rows($appointments) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($appointments)): ?>
                        <tr class="border-b hover:bg-blue-50 transition">
                            <td class="px-6 py-4"><?= e($row['id']) ?></td>
                            <td class="px-6 py-4"><?= e($row['patient_name']) ?></td>
                            <td class="px-6 py-4"><?= e($row['doctor_name']) ?></td>
                            <td class="px-6 py-4"><?= e($row['appointment_date']) ?></td>
                            <td class="px-6 py-4"><?= e($row['notes']) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="px-6 py-6 text-center text-gray-500 italic">No appointments found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Reports Section -->
    <div>
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-semibold">📄 Reports</h2>
            <a href="/hms-pro/staff/add_report.php" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Add Report</a>
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

</div>

<!-- Add Appointment Modal -->
<div id="appointmentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full shadow-xl">
        <h3 class="text-xl font-bold mb-4">Add New Appointment</h3>
        <form method="post" action="/hms-pro/staff/add_appointment.php" class="space-y-3">
            <div>
                <label class="block mb-1">Patient</label>
                <input list="patientsList" name="patient_name" class="w-full border px-3 py-2 rounded" required>
                <datalist id="patientsList">
                    <?php
                    mysqli_data_seek($patients, 0);
                    while($p = mysqli_fetch_assoc($patients)) {
                        echo "<option value='".e($p['username'])."'>";
                    }
                    ?>
                </datalist>
            </div>
            <div>
                <label class="block mb-1">Doctor</label>
                <input list="doctorsList" name="doctor_name" class="w-full border px-3 py-2 rounded" required>
                <datalist id="doctorsList">
                    <?php
                    mysqli_data_seek($doctors, 0);
                    while($d = mysqli_fetch_assoc($doctors)) {
                        echo "<option value='".e($d['username'])."'>";
                    }
                    ?>
                </datalist>
            </div>
            <div>
                <label class="block mb-1">Appointment Date</label>
                <input type="date" name="appointment_date" class="w-full border px-3 py-2 rounded" required>
            </div>
            <div>
                <label class="block mb-1">Notes</label>
                <textarea name="notes" class="w-full border px-3 py-2 rounded" rows="3"></textarea>
            </div>
            <div class="flex justify-end mt-4">
                <button type="button" id="closeModal" class="mr-2 px-4 py-2 rounded border hover:bg-gray-100">Cancel</button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add</button>
            </div>
        </form>
    </div>
</div>

<script>
const modal = document.getElementById('appointmentModal');
document.getElementById('addAppointmentBtn').addEventListener('click', () => modal.classList.remove('hidden'));
document.getElementById('closeModal').addEventListener('click', () => modal.classList.add('hidden'));
</script>

</body>
</html>
