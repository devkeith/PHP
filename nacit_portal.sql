-- NACIT PORTAL DATABASE
-- Run this in phpMyAdmin -> nacit_portal -> SQL

CREATE DATABASE IF NOT EXISTS nacit_portal
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE nacit_portal;

CREATE TABLE IF NOT EXISTS students (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) NOT NULL UNIQUE,
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    program VARCHAR(150) NOT NULL DEFAULT 'Advanced Diploma in Software Engineering',
    year_level TINYINT UNSIGNED NOT NULL DEFAULT 1,
    semester TINYINT UNSIGNED NOT NULL DEFAULT 1,
    status ENUM('Active', 'Inactive', 'Suspended') NOT NULL DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS student_modules (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id INT UNSIGNED NOT NULL,
    module_code VARCHAR(20) NOT NULL,
    module_name VARCHAR(150) NOT NULL,
    academic_year YEAR NOT NULL,
    year_level TINYINT UNSIGNED NOT NULL,
    semester TINYINT UNSIGNED NOT NULL,
    credits TINYINT UNSIGNED NOT NULL DEFAULT 3,
    lecturer_name VARCHAR(150) DEFAULT NULL,
    class_schedule VARCHAR(150) DEFAULT NULL,
    venue VARCHAR(100) DEFAULT NULL,
    assignment_name VARCHAR(255) DEFAULT NULL,
    assignment_due_date DATE DEFAULT NULL,
    assignment_status ENUM('Not Started','Pending','Submitted','Graded') NOT NULL DEFAULT 'Not Started',
    grade VARCHAR(10) DEFAULT NULL,
    module_status ENUM('Active','Completed','Dropped') NOT NULL DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_student_modules_student
        FOREIGN KEY (student_id)
        REFERENCES students(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);
