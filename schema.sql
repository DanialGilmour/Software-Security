CREATE DATABASE IF NOT EXISTS student_portal;
USE student_portal;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('registrar', 'lecturer', 'clerk', 'maintenance', 'student') DEFAULT 'student',
    mfa_secret VARCHAR(32) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    credit_hours INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    subject_id INT,
    grade VARCHAR(10) DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(255),
    description TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Seed Initial Data
-- Default passwords are 'Admin@123!' and 'Student@123!' (Hashed with BCrypt)
INSERT INTO users (id, name, email, password, role) VALUES 
(2, 'Assoc. Prof. Ts. Dr. Danial Rosli', 'registrar@portal.edu', '$2y$10$39LZeBlr4Pr4zEpT5Am6HexXZTLTJe/xcCovRn7PwL8Ywa35soeaW', 'registrar'),
(12, 'Dr. Hani Ramadhani', 'registrar2@portal.edu', '$2y$10$39LZeBlr4Pr4zEpT5Am6HexXZTLTJe/xcCovRn7PwL8Ywa35soeaW', 'registrar'),
(4, 'Encik Adam Smith', 'clerk@portal.edu', '$2y$10$sWoBg2vWUlKHWl63SFIgauem0luJ8SpZJVmKzJhfUXtaf/Egujat.', 'clerk'),
(14, 'Puan Siti Aminah', 'clerk2@portal.edu', '$2y$10$sWoBg2vWUlKHWl63SFIgauem0luJ8SpZJVmKzJhfUXtaf/Egujat.', 'clerk'),
(6, 'Prof. Lee Wei', 'lecturer@portal.edu', '$2y$10$sWoBg2vWUlKHWl63SFIgauem0luJ8SpZJVmKzJhfUXtaf/Egujat.', 'lecturer'),
(16, 'Dr. Tan Kian', 'lecturer2@portal.edu', '$2y$10$sWoBg2vWUlKHWl63SFIgauem0luJ8SpZJVmKzJhfUXtaf/Egujat.', 'lecturer'),
(8, 'Maintenance Support', 'tech@portal.edu', '$2y$10$sWoBg2vWUlKHWl63SFIgauem0luJ8SpZJVmKzJhfUXtaf/Egujat.', 'maintenance'),
(1, 'Danial Hensem', 'student@portal.edu', '$2y$10$sWoBg2vWUlKHWl63SFIgauem0luJ8SpZJVmKzJhfUXtaf/Egujat.', 'student'),
(11, 'Ali Bin Abu', 'student2@portal.edu', '$2y$10$sWoBg2vWUlKHWl63SFIgauem0luJ8SpZJVmKzJhfUXtaf/Egujat.', 'student');

INSERT INTO subjects (code, name, credit_hours) VALUES 
('CCSB5113', 'Software Security', 3),
('CSEB4113', 'Software Quality', 3),
('CSEB5133', 'Data Visualization', 3),
('CSEB5143', 'DevOps: Tools & Technologies', 3),
('CSEB5223', 'Software Construction & Methods', 3),
('CSNB4133', 'Artificial Intelligence', 3),
('MPU3372', 'Kursus Integriti dan Anti-Rasuah', 2);
 