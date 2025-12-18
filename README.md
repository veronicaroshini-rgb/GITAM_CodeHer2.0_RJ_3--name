Abstract:
Submission for Codeher 2.0 Hackathon(Gitam, vskp). A PHP-based approach for a digital solution that simplifies attendance management by allowing students to easily track subject-wise attendance, and view analytics. The system also helps students to have calculate how many classes they can safely skip while still maintaining the minimum required attendance percentage.
SOFTWARE REQUIREMENTS:
Backend: PHP (Native) 
Database: MySQL
Frontend: HTML5, CSS3 (Custom styling) 
Server: XAMPP / Apache 
KEY FEATURES 
1.   Smart Dashboard(Analytics):   Visual donut charts – to present overall percentage and bar graphs- showing attendance trends. 
2.   "Safe Skip" Calculator:   Automatically tells you how many you can skip while meeting the attendance criteria 75%. Example “safe to skip 2 classes”   or    "Attend next 2 classes" 
3.   Timetable Management:   Students have to manually enter their classes separated by commas
 4.   Holiday Tracking:   Mark holidays so they don't count as "Absent".          
INSTALLATION INSTRUCTIONS: 
1.   Database Setup:  Open phpMyAdmin (`localhost/phpmyadmin`).     
Create a database named `gitam_attendance`.   
Import the `database.sql` file provided in this repository. 
2. Project Setup: Download this repository and place the folder in `C:\xampp\htdocs\`. Open `db.php` and ensure the username/password matches your XAMPP settings (default is usually `root` and empty). 3. Run: Open browser and go to: `http://localhost/GITAM_CodeHer2.0_Jini_3/index.php`         
USAGE     
•	Register: Create an account with your Student ID.      
•	Create Semester: Enter start/end dates and subjects.       
•	Set Timetable: Go to the "Timetable" tab to assign subjects to days.       
•	Mark Attendance: Use the Dashboard calendar to mark Present/Absent.

CREATE DATABASE IF NOT EXISTS gitam_attendance;
USE gitam_attendance;

-- Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL
);

-- Semesters Table
CREATE TABLE semesters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Subjects Table
CREATE TABLE subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    semester_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    total_classes INT DEFAULT 80,
    FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE CASCADE
);

-- Schedule Table (Timetable)
CREATE TABLE schedule (
    id INT AUTO_INCREMENT PRIMARY KEY,
    semester_id INT NOT NULL,
    day_name VARCHAR(20) NOT NULL,
    subject_id INT NOT NULL,
    FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE
);

-- Attendance Log
CREATE TABLE attendance_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_id INT NOT NULL,
    date DATE NOT NULL,
    status ENUM('Present', 'Absent') NOT NULL,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE
);

-- Holidays
CREATE TABLE holidays (
    id INT AUTO_INCREMENT PRIMARY KEY,
    semester_id INT NOT NULL,
    holiday_date DATE NOT NULL,
    name VARCHAR(100) NOT NULL,
    FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE CASCADE
);
