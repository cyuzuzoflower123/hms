<?php
require __DIR__ . '/../includes/db_connect.php';
require_role('staff');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_name = trim($_POST['patient_name'] ?? '');
    $doctor_name  = trim($_POST['doctor_name'] ?? '');
    $appointment_date = $_POST['appointment_date'] ?? '';
    $notes = $_POST['notes'] ?? '';

    if (!$patient_name || !$doctor_name || !$appointment_date) {
        die('Please fill all required fields.');
    }

    // --- Get or create patient ---
    $stmt = $conn->prepare("SELECT id FROM users WHERE username=? AND role='patient' LIMIT 1");
    $stmt->bind_param("s", $patient_name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $patient_id = $row['id'];
    } else {
        $patient_name_safe = $conn->real_escape_string($patient_name);
        $conn->query("INSERT INTO users (username, role) VALUES ('$patient_name_safe','patient')");
        $patient_id = $conn->insert_id;
    }

    // --- Get or create doctor ---
    $stmt = $conn->prepare("SELECT id FROM users WHERE username=? AND role='doctor' LIMIT 1");
    $stmt->bind_param("s", $doctor_name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $doctor_id = $row['id'];
    } else {
        $doctor_name_safe = $conn->real_escape_string($doctor_name);
        $conn->query("INSERT INTO users (username, role) VALUES ('$doctor_name_safe','doctor')");
        $doctor_id = $conn->insert_id;
    }

    // --- Insert appointment ---
    $stmt = $conn->prepare("INSERT INTO appointments (patient_id, doctor_id, appointment_date, notes) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $patient_id, $doctor_id, $appointment_date, $notes);

    if ($stmt->execute()) {
        header("Location: /hms-pro/dashboard.php");
        exit;
    } else {
        die("Failed to add appointment: " . $conn->error);
    }
} else {
    die("Invalid request method.");
}
