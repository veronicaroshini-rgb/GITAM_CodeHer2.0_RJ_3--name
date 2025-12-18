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
