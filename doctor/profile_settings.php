<?php 
require __DIR__ . '/../includes/db_connect.php'; 
require_role('doctor'); 

$doctor_user_id = $_SESSION['user_id'];

// Handle profile update
if ($_POST && isset($_POST['update_profile'])) {
    $username = $_POST['username'];
    $new_password = $_POST['new_password'];
    
    if ($new_password) {
        // Update username and password
        $stmt = $conn->prepare("UPDATE users SET username = ?, password = ? WHERE id = ?");
        $stmt->execute([$username, $new_password, $doctor_user_id]);
    } else {
        // Update username only
        $stmt = $conn->prepare("UPDATE users SET username = ? WHERE id = ?");
        $stmt->execute([$username, $doctor_user_id]);
    }
    
    $success = "Profile updated successfully!";
}

// Get current user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$doctor_user_id]);
$user_data = $stmt->fetch();

// Get statistics for dashboard
$stmt = $conn->prepare("SELECT COUNT(*) as total_patients FROM patients");
$stmt->execute();
$total_patients = $stmt->fetch()['total_patients'];

$stmt = $conn->prepare("SELECT COUNT(*) as my_reports FROM reports WHERE doctor_id = ?");
$stmt->execute([$doctor_user_id]);
$my_reports = $stmt->fetch()['my_reports'];

$stmt = $conn->prepare("SELECT COUNT(*) as my_appointments FROM appointments WHERE doctor_id = ?");
$stmt->execute([$doctor_user_id]);
$my_appointments = $stmt->fetch()['my_appointments'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profile & Settings - Doctor Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-purple-50 via-blue-50 to-green-50">

<!-- Navigation -->
<div class="bg-white shadow-md w-full">
    <div class="max-w-7xl mx-auto flex justify-between items-center p-4">
        <h1 class="text-2xl font-bold text-gray-800">⚙️ Profile & Settings</h1>
        <div class="flex space-x-3">
            <a href="../partials/doctor_panel.php" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition">← Dashboard</a>
            <a href="appointments.php" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">Appointments</a>
            <a href="patients.php" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">Patients</a>
            <a href="/central_dashboard.php" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">Logout</a>
        </div>
    </div>
</div>

<div class="max-w-6xl mx-auto p-6">
    <?php if (isset($success)): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="text-center mb-6">
                    <div class="w-24 h-24 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-white text-3xl">👨‍⚕️</span>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800"><?= htmlspecialchars($user_data['username']) ?></h2>
                    <p class="text-gray-600 capitalize"><?= htmlspecialchars($user_data['role']) ?></p>
                    <p class="text-sm text-gray-500 mt-1">
                        Member since <?= date('M Y', strtotime($user_data['created_at'])) ?>
                    </p>
                </div>

                <!-- Quick Stats -->
                <div class="space-y-3">
                    <div class="flex justify-between items-center p-3 bg-blue-50 rounded">
                        <span class="text-blue-700">📅 My Appointments</span>
                        <span class="font-semibold text-blue-800"><?= $my_appointments ?></span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-green-50 rounded">
                        <span class="text-green-700">📋 My Reports</span>
                        <span class="font-semibold text-green-800"><?= $my_reports ?></span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-purple-50 rounded">
                        <span class="text-purple-700">👥 Total Patients</span>
                        <span class="font-semibold text-purple-800"><?= $total_patients ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Settings Forms -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Update Profile Form -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-6">Update Profile</h3>
                
                <form method="POST" class="space-y-6">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                            Username *
                        </label>
                        <input type="text" name="username" id="username" value="<?= htmlspecialchars($user_data['username']) ?>" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">
                            New Password
                        </label>
                        <input type="password" name="new_password" id="new_password" placeholder="Leave blank to keep current password"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="mt-1 text-sm text-gray-500">
                            Leave blank if you don't want to change your password
                        </p>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" name="update_profile" 
                                class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                            💾 Update Profile
                        </button>
                    </div>
                </form>
            </div>

            <!-- Account Information -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-6">Account Information</h3>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-3 border-b border-gray-200">
                        <span class="text-gray-700">Account Type:</span>
                        <span class="font-medium capitalize"><?= htmlspecialchars($user_data['role']) ?></span>
                    </div>
                    <div class="flex justify-between items-center py-3 border-b border-gray-200">
                        <span class="text-gray-700">User ID:</span>
                        <span class="font-medium">#<?= $user_data['id'] ?></span>
                    </div>
                    <div class="flex justify-between items-center py-3 border-b border-gray-200">
                        <span class="text-gray-700">Account Created:</span>
                        <span class="font-medium"><?= date('M j, Y g:i A', strtotime($user_data['created_at'])) ?></span>
                    </div>
                    <div class="flex justify-between items-center py-3">
                        <span class="text-gray-700">Last Login:</span>
                        <span class="font-medium">Current Session</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-6">Quick Actions</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <a href="patients.php" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                        <span class="text-2xl mr-3">👥</span>
                        <div>
                            <h4 class="font-medium text-gray-800">View All Patients</h4>
                            <p class="text-sm text-gray-600">Manage patient records</p>
                        </div>
                    </a>
                    
                    <a href="view_reports.php" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                        <span class="text-2xl mr-3">📋</span>
                        <div>
                            <h4 class="font-medium text-gray-800">My Reports</h4>
                            <p class="text-sm text-gray-600">Review medical reports</p>
                        </div>
                    </a>
                    
                    <a href="appointments.php" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                        <span class="text-2xl mr-3">📅</span>
                        <div>
                            <h4 class="font-medium text-gray-800">My Appointments</h4>
                            <p class="text-sm text-gray-600">Manage appointments</p>
                        </div>
                    </a>
                    
                    <a href="../partials/doctor_panel.php" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                        <span class="text-2xl mr-3">🏠</span>
                        <div>
                            <h4 class="font-medium text-gray-800">Dashboard</h4>
                            <p class="text-sm text-gray-600">Return to main panel</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>