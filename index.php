<?php
// --- CONFIGURATION ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require 'db.php'; 

$theme_color = "#006a4e"; 
$theme_light = "#e6f4f1";
$theme_red   = "#ef4444";
$user_id = $_SESSION['user_id'] ?? null;

// =================================================================================
// 🚀 BACKEND LOGIC
// =================================================================================

// 1. LOGIN / REGISTER
if (isset($_POST['auth_action'])) {
    if ($_POST['auth_action'] == 'register') {
        $stmt = $pdo->prepare("INSERT INTO users (student_id, password, full_name) VALUES (?, ?, ?)");
        if ($stmt->execute([$_POST['student_id'], password_hash($_POST['password'], PASSWORD_DEFAULT), $_POST['full_name']])) {
            $success = "Account created. Please login.";
        } else { $error = "User already exists."; }
    } else {
        // THIS IS THE FIX: We select by student_id, NOT username
        $stmt = $pdo->prepare("SELECT * FROM users WHERE student_id = ?");
        $stmt->execute([$_POST['student_id']]);
        $user = $stmt->fetch();
        if ($user && password_verify($_POST['password'], $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            header("Location: index.php"); exit;
        } else { $error = "Invalid Login"; }
    }
}
if (isset($_GET['logout'])) { session_destroy(); header("Location: index.php"); exit; }

// 2. CREATE SEMESTER
if ($user_id && isset($_POST['create_sem'])) {
    $stmt = $pdo->prepare("INSERT INTO semesters (user_id, name, start_date, end_date) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $_POST['name'], $_POST['start'], $_POST['end']]);
    $sem_id = $pdo->lastInsertId();
    foreach(explode(',', $_POST['subjects']) as $sub) {
        if(trim($sub)) {
            $pdo->prepare("INSERT INTO subjects (semester_id, name, total_classes) VALUES (?, ?, 80)")->execute([$sem_id, trim($sub)]);
        }
    }
    header("Location: index.php"); exit;
}

// 3. DELETE SEMESTER
if ($user_id && isset($_POST['delete_sem'])) {
    $sem_id = $_POST['sem_id'];
    $pdo->prepare("DELETE FROM attendance_log WHERE subject_id IN (SELECT id FROM subjects WHERE semester_id=?)")->execute([$sem_id]);
    $pdo->prepare("DELETE FROM schedule WHERE semester_id=?")->execute([$sem_id]);
    $pdo->prepare("DELETE FROM holidays WHERE semester_id=?")->execute([$sem_id]);
    $pdo->prepare("DELETE FROM subjects WHERE semester_id=?")->execute([$sem_id]);
    $pdo->prepare("DELETE FROM semesters WHERE id=? AND user_id=?")->execute([$sem_id, $user_id]);
    header("Location: index.php"); exit;
}

// 4. TIMETABLE
if ($user_id && isset($_POST['update_timetable'])) {
    $sem_id = $_POST['sem_id']; $day = $_POST['day'];
    $pdo->prepare("DELETE FROM schedule WHERE semester_id = ? AND day_name = ?")->execute([$sem_id, $day]);
    if (isset($_POST['subjects'])) {
        foreach($_POST['subjects'] as $sub_id) {
            $pdo->prepare("INSERT INTO schedule (semester_id, day_name, subject_id) VALUES (?, ?, ?)")->execute([$sem_id, $day, $sub_id]);
        }
    }
    header("Location: ?page=view&id=$sem_id&tab=timetable"); exit;
}

// 5. UPDATE PROJECTIONS
if ($user_id && isset($_POST['update_total_classes'])) {
    $pdo->prepare("UPDATE subjects SET total_classes = ? WHERE id = ?")->execute([$_POST['total'], $_POST['sub_id']]);
    header("Location: ?page=view&id=".$_POST['sem_id']."&tab=analysis"); exit;
}

// 6. ADD HOLIDAY
if ($user_id && isset($_POST['add_holiday'])) {
    $pdo->prepare("INSERT INTO holidays (semester_id, holiday_date, name) VALUES (?, ?, ?)")
        ->execute([$_POST['sem_id'], $_POST['date'], $_POST['desc']]);
    header("Location: ?page=view&id=".$_POST['sem_id']."&date=".$_POST['date']); exit;
}

// 7. MARK ATTENDANCE
if ($user_id && isset($_POST['mark_attendance'])) {
    $status = $_POST['mark_attendance']; 
    $pdo->prepare("DELETE FROM attendance_log WHERE subject_id = ? AND date = ?")->execute([$_POST['subject_id'], $_POST['date']]);
    $pdo->prepare("INSERT INTO attendance_log (subject_id, date, status) VALUES (?, ?, ?)")->execute([$_POST['subject_id'], $_POST['date'], $status]);
    header("Location: ?page=view&id=".$_POST['sem_id']."&date=".$_POST['date']); exit;
}

// Helper: Stats
function getAttendanceStats($pdo, $sem_id) {
    $q = $pdo->prepare("SELECT COUNT(*) as total, SUM(CASE WHEN status='Present' THEN 1 ELSE 0 END) as present FROM attendance_log JOIN subjects ON attendance_log.subject_id = subjects.id WHERE subjects.semester_id = ?");
    $q->execute([$sem_id]);
    $data = $q->fetch();
    $perc = ($data['total'] > 0) ? round(($data['present']/$data['total'])*100) : 0;
    return ['perc' => $perc, 'present' => $data['present'], 'total' => $data['total']];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gitam Student Portal</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; margin: 0; color: #1f2937; }
        .btn { background: <?php echo $theme_color; ?>; color: white; border: none; padding: 8px 16px; border-radius: 8px; cursor: pointer; text-decoration:none; display:inline-block; font-weight:600; font-size:0.9rem; transition:0.2s; }
        .btn:hover { opacity: 0.9; transform:translateY(-1px); }
        .btn-outline { background: transparent; border: 1px solid <?php echo $theme_color; ?>; color: <?php echo $theme_color; ?>; }
        .card { background: white; padding: 25px; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 25px; border:1px solid #e5e7eb; }
        .navbar { background: white; padding: 15px 40px; display: flex; justify-content: space-between; border-bottom: 1px solid #eee; align-items: center; box-shadow:0 2px 4px rgba(0,0,0,0.02); }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 25px; }
        .calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 8px; text-align: center; margin-top:15px; }
        .cal-day { aspect-ratio: 1; display:flex; align-items:center; justify-content:center; background: #f9fafb; border-radius: 12px; font-size: 0.95rem; cursor: pointer; text-decoration:none; color:#4b5563; font-weight:500; transition:0.2s; }
        .cal-day:hover { background: #e5e7eb; }
        .is-selected { background: <?php echo $theme_color; ?> !important; color: white !important; font-weight: bold; box-shadow: 0 4px 10px rgba(0,106,78,0.3); }
        .is-holiday-bg { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .donut-chart { position:relative; width: 140px; height: 140px; border-radius: 50%; background: conic-gradient(<?php echo $theme_color; ?> var(--p), #e5e7eb 0); display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; }
        .donut-inner { width: 100px; height: 100px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; font-weight: 800; color: <?php echo $theme_color; ?>; }
        .chart-container { display: flex; align-items: flex-end; justify-content:space-between; height: 180px; padding-top: 30px; border-bottom: 1px solid #e5e7eb; }
        .chart-col { display:flex; flex-direction:column; align-items:center; width:12%; height:100%; justify-content:flex-end; }
        .chart-bar { width: 100%; border-radius: 6px 6px 0 0; background: #3b82f6; transition: height 0.5s ease-out; position:relative; }
        .chart-bar:hover { opacity:0.8; }
        .chart-label { font-size: 0.75rem; margin-top: 8px; color: #6b7280; font-weight:600; }
        .chart-val { position:absolute; top:-20px; width:100%; text-align:center; font-size:0.7em; font-weight:bold; color:#666; }
        .badge { padding: 5px 12px; border-radius: 20px; font-size: 0.75em; font-weight: 700; text-transform:uppercase; letter-spacing:0.5px; }
        .badge-safe { background: #dcfce7; color: #166534; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .tab-nav { display: flex; gap: 30px; border-bottom: 2px solid #e5e7eb; margin-bottom: 25px; }
        .tab-link { padding: 12px 0; cursor: pointer; text-decoration: none; color: #6b7280; font-weight: 600; border-bottom: 3px solid transparent; transition:0.2s; }
        .tab-link:hover { color: <?php echo $theme_color; ?>; }
        .tab-link.active { color: <?php echo $theme_color; ?>; border-color: <?php echo $theme_color; ?>; }
        .nav-arrow { text-decoration: none; color: #4b5563; font-weight: bold; font-size: 1.2em; padding: 5px 12px; border-radius:6px; background:#f3f4f6; }
        .nav-arrow:hover { background:#e5e7eb; }
    </style>
</head>
<body>

<?php if (!$user_id): ?>
    <div style="height:100vh; display:flex; justify-content:center; align-items:center; background:linear-gradient(135deg, <?php echo $theme_color; ?>, #004d38);">
        <div class="card" style="width:100%; max-width:400px; padding:40px;">
            <h2 style="text-align:center; color:<?php echo $theme_color; ?>;">Gitam Portal</h2>
            <?php if(isset($error)) echo "<div style='color:red; text-align:center;'>$error</div>"; ?>
            <?php if(isset($success)) echo "<div style='color:green; text-align:center;'>$success</div>"; ?>
            <form method="POST">
                <input type="hidden" name="auth_action" value="login">
                <label style="font-weight:600; font-size:0.9rem;">Student ID</label>
                <input type="text" name="student_id" required style="width:100%; padding:12px; margin:5px 0 15px; border:1px solid #ddd; border-radius:8px;">
                <label style="font-weight:600; font-size:0.9rem;">Password</label>
                <input type="password" name="password" required style="width:100%; padding:12px; margin:5px 0 20px; border:1px solid #ddd; border-radius:8px;">
                <button class="btn" style="width:100%; padding:12px;">Log In</button>
            </form>
            <p style="text-align:center; margin-top:20px;">New? <a href="#" onclick="document.querySelector('[name=auth_action]').value='register'; this.closest('form').submit();" style="color:<?php echo $theme_color; ?>; font-weight:bold;">Register</a></p>
        </div>
    </div>

<?php else: ?>
    <div class="navbar">
        <div style="font-weight:800; font-size:1.2rem; color:<?php echo $theme_color; ?>">GITAM<span style="color:#333;">PORTAL</span></div>
        <a href="?logout=true" style="color:#ef4444; text-decoration:none; font-weight:600;">Logout ➜</a>
    </div>

    <div style="max-width:1200px; margin:30px auto; padding:0 20px;">
        <?php if (!isset($_GET['page'])): ?>
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:30px;">
                <h1 style="margin:0;">My Semesters</h1>
                <button onclick="document.getElementById('newSemModal').style.display='block'" class="btn">+ Create Semester</button>
            </div>
            <div class="grid">
                <?php 
                $sems = $pdo->prepare("SELECT * FROM semesters WHERE user_id = ? ORDER BY id DESC");
                $sems->execute([$user_id]);
                foreach($sems->fetchAll() as $sem):
                    $stats = getAttendanceStats($pdo, $sem['id']);
                ?>
                <div class="card" style="position:relative; border-left: 6px solid <?php echo $theme_color; ?>;">
                    <form method="POST" onsubmit="return confirm('Delete this semester?');" style="position:absolute; top:15px; right:15px;">
                        <input type="hidden" name="sem_id" value="<?php echo $sem['id']; ?>">
                        <button name="delete_sem" style="background:none; border:none; cursor:pointer; opacity:0.5;">🗑️</button>
                    </form>
                    <div onclick="window.location='?page=view&id=<?php echo $sem['id']; ?>'" style="cursor:pointer;">
                        <h3 style="margin:0 0 5px 0;"><?php echo htmlspecialchars($sem['name']); ?></h3>
                        <p style="color:#666; font-size:0.9rem;"><?php echo date('M Y', strtotime($sem['start_date'])); ?> - <?php echo date('M Y', strtotime($sem['end_date'])); ?></p>
                        <div style="display:flex; justify-content:space-between; margin:15px 0 5px; font-weight:600;"><span>Progress</span><span><?php echo $stats['perc']; ?>%</span></div>
                        <div style="background:#e5e7eb; height:10px; border-radius:5px;"><div style="width:<?php echo $stats['perc']; ?>%; background:<?php echo $theme_color; ?>; height:100%; border-radius:5px;"></div></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div id="newSemModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:100;">
                <div class="card" style="width:400px; margin:100px auto;">
                    <h2>New Semester</h2>
                    <form method="POST">
                        <input type="text" name="name" placeholder="Semester Name" required style="width:100%; margin-bottom:15px; padding:12px; border:1px solid #ddd; border-radius:8px;">
                        <div style="display:flex; gap:10px; margin-bottom:15px;">
                            <input type="date" name="start" required style="flex:1; padding:10px; border:1px solid #ddd; border-radius:8px;">
                            <input type="date" name="end" required style="flex:1; padding:10px; border:1px solid #ddd; border-radius:8px;">
                        </div>
                        <textarea name="subjects" placeholder="Subjects (comma separated)" style="width:100%; height:80px; padding:10px; border:1px solid #ddd; border-radius:8px; margin-bottom:15px;"></textarea>
                        <button name="create_sem" class="btn" style="width:100%;">Create</button>
                        <button type="button" onclick="document.getElementById('newSemModal').style.display='none'" class="btn btn-outline" style="width:100%; margin-top:10px;">Cancel</button>
                    </form>
                </div>
            </div>

        <?php elseif ($_GET['page'] == 'view' && isset($_GET['id'])): 
            $sem_id = $_GET['id'];
            $tab = $_GET['tab'] ?? 'dashboard';
            $selected_date = $_GET['date'] ?? date('Y-m-d');
            
            $sem = $pdo->prepare("SELECT * FROM semesters WHERE id = ?"); $sem->execute([$sem_id]); $sem = $sem->fetch();
            $subjects = $pdo->prepare("SELECT * FROM subjects WHERE semester_id = ?"); $subjects->execute([$sem_id]); $subjects = $subjects->fetchAll();
            $holidays = $pdo->prepare("SELECT * FROM holidays WHERE semester_id = ?"); $holidays->execute([$sem_id]); 
            $holiday_list = []; foreach($holidays->fetchAll() as $h) $holiday_list[$h['holiday_date']] = $h['name'];
        ?>
            
            <a href="index.php" style="text-decoration:none; color:#6b7280; font-weight:600; display:inline-block; margin-bottom:15px;">← Back to Dashboard</a>
            <h1 style="margin:0 0 25px;"><?php echo htmlspecialchars($sem['name']); ?></h1>

            <div class="tab-nav">
                <a href="?page=view&id=<?php echo $sem_id; ?>&tab=dashboard" class="tab-link <?php echo $tab=='dashboard'?'active':''; ?>">Dashboard</a>
                <a href="?page=view&id=<?php echo $sem_id; ?>&tab=timetable" class="tab-link <?php echo $tab=='timetable'?'active':''; ?>">Timetable</a>
                <a href="?page=view&id=<?php echo $sem_id; ?>&tab=analysis" class="tab-link <?php echo $tab=='analysis'?'active':''; ?>">Analytics</a>
            </div>

            <?php if ($tab == 'dashboard'): ?>
                <div style="display:grid; grid-template-columns: 2fr 1fr; gap:25px;">
                    <div>
                        <div class="card">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                                <a href="?page=view&id=<?php echo $sem_id; ?>&date=<?php echo date('Y-m-d', strtotime($selected_date . ' -1 day')); ?>" class="nav-arrow">&lt;</a>
                                <h3 style="margin:0;"><?php echo date('D, d M Y', strtotime($selected_date)); ?></h3>
                                <a href="?page=view&id=<?php echo $sem_id; ?>&date=<?php echo date('Y-m-d', strtotime($selected_date . ' +1 day')); ?>" class="nav-arrow">&gt;</a>
                            </div>

                            <?php if (isset($holiday_list[$selected_date])): ?>
                                <div style='padding:25px; background:#fee2e2; color:#991b1b; border-radius:12px; text-align:center; font-weight:bold;'>
                                    🎉 Holiday: <?php echo $holiday_list[$selected_date]; ?>
                                </div>
                            <?php else: ?>
                                <?php 
                                $day_name = date('l', strtotime($selected_date)); 
                                $sched = $pdo->prepare("SELECT s.name, s.id FROM schedule sc JOIN subjects s ON sc.subject_id = s.id WHERE sc.semester_id = ? AND sc.day_name = ?");
                                $sched->execute([$sem_id, $day_name]);
                                $classes = $sched->fetchAll();
                                if (count($classes) == 0) echo "<p style='color:#666; text-align:center; padding:20px;'>No classes scheduled.</p>";
                                foreach($classes as $cls):
                                    $log = $pdo->prepare("SELECT status FROM attendance_log WHERE subject_id = ? AND date = ?");
                                    $log->execute([$cls['id'], $selected_date]);
                                    $status = $log->fetchColumn();
                                ?>
                                <div style="display:flex; justify-content:space-between; align-items:center; padding:15px; border:1px solid #f3f4f6; border-radius:10px; margin-bottom:10px; background:#f9fafb;">
                                    <strong><?php echo htmlspecialchars($cls['name']); ?></strong>
                                    <form method="POST">
                                        <input type="hidden" name="sem_id" value="<?php echo $sem_id; ?>">
                                        <input type="hidden" name="subject_id" value="<?php echo $cls['id']; ?>">
                                        <input type="hidden" name="date" value="<?php echo $selected_date; ?>">
                                        <button name="mark_attendance" value="Present" class="btn" style="background:<?php echo $status=='Present'?'#dcfce7':'white'; ?>; color:<?php echo $status=='Present'?'#166534':'#4b5563'; ?>; border:1px solid #d1d5db;">Present</button>
                                        <button name="mark_attendance" value="Absent" class="btn" style="background:<?php echo $status=='Absent'?'#fee2e2':'white'; ?>; color:<?php echo $status=='Absent'?'#991b1b':'#4b5563'; ?>; border:1px solid #d1d5db;">Absent</button>
                                    </form>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <?php if (!isset($holiday_list[$selected_date])): ?>
                                <div style="margin-top:20px; text-align:center;">
                                    <form method="POST" style="display:inline-flex; gap:5px;">
                                        <input type="hidden" name="sem_id" value="<?php echo $sem_id; ?>">
                                        <input type="hidden" name="date" value="<?php echo $selected_date; ?>">
                                        <input type="text" name="desc" placeholder="Holiday Name (e.g. Fest)" required style="padding:8px; border:1px solid #ddd; border-radius:6px;">
                                        <button name="add_holiday" class="btn btn-outline">Mark Holiday</button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div>
                        <div class="card">
                            <div style="display:flex; justify-content:space-between; align-items:center;">
                                <a href="?page=view&id=<?php echo $sem_id; ?>&date=<?php echo date('Y-m-01', strtotime($selected_date.' -1 month')); ?>" class="nav-arrow">&lt;</a>
                                <h3 style="margin:0; font-size:1.1rem;"><?php echo date('F Y', strtotime($selected_date)); ?></h3>
                                <a href="?page=view&id=<?php echo $sem_id; ?>&date=<?php echo date('Y-m-01', strtotime($selected_date.' +1 month')); ?>" class="nav-arrow">&gt;</a>
                            </div>
                            <div class="calendar-grid">
                                <div style="color:#9ca3af; font-size:0.8em; font-weight:bold;">S</div><div style="color:#9ca3af; font-size:0.8em; font-weight:bold;">M</div><div style="color:#9ca3af; font-size:0.8em; font-weight:bold;">T</div><div style="color:#9ca3af; font-size:0.8em; font-weight:bold;">W</div><div style="color:#9ca3af; font-size:0.8em; font-weight:bold;">T</div><div style="color:#9ca3af; font-size:0.8em; font-weight:bold;">F</div><div style="color:#9ca3af; font-size:0.8em; font-weight:bold;">S</div>
                                <?php
                                $ym = date('Y-m-', strtotime($selected_date));
                                $daysInMonth = date('t', strtotime($selected_date));
                                $startDay = date('w', strtotime($ym . '01'));
                                for($i=0; $i<$startDay; $i++) echo "<div></div>";
                                for($d=1; $d<=$daysInMonth; $d++) {
                                    $curr = $ym . str_pad($d, 2, '0', STR_PAD_LEFT);
                                    $cls = "cal-day";
                                    if ($curr == $selected_date) $cls .= " is-selected";
                                    if (isset($holiday_list[$curr])) $cls .= " is-holiday-bg";
                                    echo "<a href='?page=view&id=$sem_id&date=$curr' class='$cls'>$d</a>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

            <?php elseif ($tab == 'timetable'): ?>
                <div class="card">
                    <h3>Timetable Setup</h3>
                    <div style="display:flex; gap:15px; flex-wrap:wrap;">
                        <?php 
                        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                        foreach($days as $d): 
                            $ex = $pdo->prepare("SELECT subject_id FROM schedule WHERE semester_id = ? AND day_name = ?");
                            $ex->execute([$sem_id, $d]);
                            $active = $ex->fetchAll(PDO::FETCH_COLUMN);
                        ?>
                        <div style="border:1px solid #e5e7eb; padding:15px; border-radius:12px; width:100%; max-width:180px; background:#f9fafb;">
                            <form method="POST">
                                <input type="hidden" name="sem_id" value="<?php echo $sem_id; ?>">
                                <input type="hidden" name="day" value="<?php echo $d; ?>">
                                <input type="hidden" name="update_timetable" value="true">
                                <strong style="display:block; margin-bottom:10px; color:<?php echo $theme_color; ?>;"><?php echo $d; ?></strong>
                                <?php foreach($subjects as $sub): ?>
                                    <label style="display:block; margin:5px 0; font-size:0.9em; cursor:pointer;">
                                        <input type="checkbox" name="subjects[]" value="<?php echo $sub['id']; ?>" <?php if(in_array($sub['id'], $active)) echo "checked"; ?>> 
                                        <?php echo htmlspecialchars($sub['name']); ?>
                                    </label>
                                <?php endforeach; ?>
                                <button class="btn" style="width:100%; margin-top:8px; font-size:0.8em;">Save</button>
                            </form>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            <?php elseif ($tab == 'analysis'): ?>
                <?php $overall = getAttendanceStats($pdo, $sem_id); ?>
                
                <div style="display:grid; grid-template-columns: 1fr 2fr; gap:25px; margin-bottom:25px;">
                    <div class="card" style="text-align:center; display:flex; flex-direction:column; justify-content:center;">
                        <h3 style="margin-bottom:20px;">Overall</h3>
                        <div class="donut-chart" style="--p: <?php echo $overall['perc']; ?>%;">
                            <div class="donut-inner"><?php echo $overall['perc']; ?>%</div>
                        </div>
                        <p style="color:#6b7280; font-weight:600;">Total: <?php echo $overall['total']; ?> | Present: <?php echo $overall['present']; ?></p>
                    </div>
                    
                    <div class="card">
                        <h3>Weekly Attendance Trend</h3>
                        <div class="chart-container">
                            <?php 
                            for ($i=6; $i>=0; $i--) {
                                $d = date('Y-m-d', strtotime("-$i days"));
                                $label = date('D', strtotime($d));
                                $q = $pdo->prepare("SELECT COUNT(*) as tot, SUM(CASE WHEN status='Present' THEN 1 ELSE 0 END) as pres FROM attendance_log JOIN subjects ON attendance_log.subject_id=subjects.id WHERE subjects.semester_id=? AND attendance_log.date=?");
                                $q->execute([$sem_id, $d]);
                                $day_stat = $q->fetch();
                                $h = ($day_stat['tot'] > 0) ? ($day_stat['pres']/$day_stat['tot'])*100 : 0;
                                echo "
                                <div class='chart-col'>
                                    <div class='chart-val'>".($h>0?round($h).'%':'')."</div>
                                    <div class='chart-bar' style='height:{$h}%; opacity:".($h>0?1:0.1).";'></div>
                                    <div class='chart-label'>$label</div>
                                </div>";
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <h3>Subject-wise Projections</h3>
                    <p style="color:#6b7280; font-size:0.9em; margin-bottom:25px;">
                        Based on target <strong>75%</strong> of <strong>Total Projected Classes</strong>.
                    </p>
                    
                    <div style="display:flex; justify-content:space-between; margin-bottom:15px; font-weight:bold; color:#9ca3af; font-size:0.85em; text-transform:uppercase;">
                        <span style="flex:2;">Subject</span>
                        <span style="flex:1; text-align:center;">Total Plan</span>
                        <span style="flex:1; text-align:center;">Attended</span>
                        <span style="flex:1; text-align:center;">Skipped</span>
                        <span style="flex:2; text-align:right;">Status</span>
                    </div>

                    <?php foreach($subjects as $sub): 
                        $q = $pdo->prepare("SELECT COUNT(*) as total, SUM(CASE WHEN status='Present' THEN 1 ELSE 0 END) as present FROM attendance_log WHERE subject_id = ?");
                        $q->execute([$sub['id']]);
                        $stat = $q->fetch();
                        
                        $attended = intval($stat['present']);
                        $total_so_far = intval($stat['total']);
                        $absent = $total_so_far - $attended;
                        $total_projected = $sub['total_classes'] ?? 80;
                        
                        $max_absences_allowed = floor($total_projected * 0.25);
                        $safe_skips_remaining = $max_absences_allowed - $absent;

                        $badge = ($safe_skips_remaining > 0) ? "badge-safe" : "badge-danger";
                        $msg = ($safe_skips_remaining > 0) ? "Skip $safe_skips_remaining more" : "⚠️ Attend ".abs($safe_skips_remaining);
                    ?>
                    <div style="display:flex; justify-content:space-between; align-items:center; padding:15px 0; border-bottom:1px solid #f3f4f6;">
                        <div style="flex:2;">
                            <strong style="font-size:1.05em;"><?php echo htmlspecialchars($sub['name']); ?></strong>
                            <div style="height:6px; background:#f3f4f6; width:60px; border-radius:3px; margin-top:5px; overflow:hidden;">
                                <div style="height:100%; background:<?php echo $theme_color; ?>; width:<?php echo ($total_so_far>0)?($attended/$total_so_far)*100:0; ?>%;"></div>
                            </div>
                        </div>
                        
                        <div style="flex:1; text-align:center;">
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="sem_id" value="<?php echo $sem_id; ?>">
                                <input type="hidden" name="sub_id" value="<?php echo $sub['id']; ?>">
                                <input type="hidden" name="update_total_classes" value="true">
                                <input type="number" name="total" value="<?php echo $total_projected; ?>" style="width:50px; text-align:center; padding:5px; border:1px solid #ddd; border-radius:6px; font-weight:bold;" onchange="this.form.submit()">
                            </form>
                        </div>

                        <div style="flex:1; text-align:center; font-weight:bold; color:#166534;"><?php echo $attended; ?></div>
                        <div style="flex:1; text-align:center; font-weight:bold; color:#991b1b;"><?php echo $absent; ?></div>
                        
                        <div style="flex:2; text-align:right;">
                            <span class="badge <?php echo $badge; ?>"><?php echo $msg; ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="card">
                    <h3>Sessional Status</h3>
                    <div style="display:flex; gap:20px; margin-top:15px;">
                        <div style="flex:1; background:#f0fdf4; border:1px solid #dcfce7; padding:20px; border-radius:12px; text-align:center;">
                            <h4 style="margin:0 0 10px; color:#166534;">Sessional 1</h4>
                            <div style="font-size:2rem; font-weight:bold; color:#166534;">Safe</div>
                            <p style="margin:5px 0 0; color:#15803d; font-size:0.9em;">If held today</p>
                        </div>
                        <div style="flex:1; background:#fff7ed; border:1px solid #ffedd5; padding:20px; border-radius:12px; text-align:center;">
                            <h4 style="margin:0 0 10px; color:#9a3412;">Sessional 2</h4>
                            <div style="font-size:2rem; font-weight:bold; color:#9a3412;"><?php echo ($overall['perc'] >= 75) ? 'On Track' : 'At Risk'; ?></div>
                            <p style="margin:5px 0 0; color:#c2410c; font-size:0.9em;">Projected Status</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        <?php endif; ?>
    </div>
<?php endif; ?>

</body>
</html>