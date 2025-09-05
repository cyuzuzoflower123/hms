CREATE DATABASE IF NOT EXISTS hms;
USE hms;
DROP TABLE IF EXISTS appointments; DROP TABLE IF EXISTS users; DROP TABLE IF EXISTS patients; DROP TABLE IF EXISTS doctors;
CREATE TABLE doctors (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100) NOT NULL, specialization VARCHAR(100) NOT NULL, contact VARCHAR(50));
CREATE TABLE patients (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100) NOT NULL, age INT, gender ENUM('Male','Female') DEFAULT 'Male', contact VARCHAR(50), created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
CREATE TABLE users (id INT AUTO_INCREMENT PRIMARY KEY, username VARCHAR(100) NOT NULL UNIQUE, password VARCHAR(255) NOT NULL, role ENUM('admin','doctor','staff') NOT NULL DEFAULT 'staff', doctor_id INT NULL, FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE SET NULL);
CREATE TABLE appointments (id INT AUTO_INCREMENT PRIMARY KEY, patient_id INT NOT NULL, doctor_id INT NOT NULL, appointment_date DATETIME NOT NULL, notes TEXT, FOREIGN KEY(patient_id) REFERENCES patients(id) ON DELETE CASCADE, FOREIGN KEY(doctor_id) REFERENCES doctors(id) ON DELETE CASCADE);
INSERT INTO doctors(name,specialization,contact) VALUES ('Dr. Alice Karema','Cardiologist','+250 780 000 111'),('Dr. Ben Nkurunziza','Pediatrician','+250 780 000 222');
-- bcrypt of 'admin123'
INSERT INTO users(username,password,role,doctor_id) VALUES ('admin','$2y$10$yD5Z..f0qzL7Htkj6dKq..7YtlRrihxfyNlq9rgZ3qyk9mP5dcFPq','admin',NULL),('doctor','$2y$10$yD5Z..f0qzL7Htkj6dKq..7YtlRrihxfyNlq9rgZ3qyk9mP5dcFPq','doctor',1),('staff','$2y$10$yD5Z..f0qzL7Htkj6dKq..7YtlRrihxfyNlq9rgZ3qyk9mP5dcFPq','staff',NULL);
INSERT INTO patients(name,age,gender,contact) VALUES ('Gakwaya David',35,'Male','+250 780 123 456'),('Jane Uwase',28,'Female','+250 780 654 321');
INSERT INTO appointments(patient_id,doctor_id,appointment_date,notes) VALUES (1,1,DATE_ADD(NOW(), INTERVAL 1 DAY),'Initial consultation'),(2,2,DATE_ADD(NOW(), INTERVAL 2 DAY),'Follow-up check');