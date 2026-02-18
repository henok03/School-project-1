<?php

session_start();
if(!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}
$current_grade = isset($_GET['grade']) ? $_GET['grade'] : 'all';

$connection = mysqli_connect("127.0.0.1", "root", "", "school_registration", 3309);
if (!$connection) { die("Connection failed: " . mysqli_connect_error()); }

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// Load school statistics
$current_data = array(
    "students" => 0,
    "teachers" => 0,
    "rewards" => 0,
    "subjects" => 0
);

$stats_file = 'stats.json';
if (file_exists($stats_file)) {
    $json = file_get_contents($stats_file);
    $decoded = json_decode($json, true);
    if (is_array($decoded)) {
        $current_data = $decoded;
    }
}

// Update stats if form submitted
if (isset($_POST['update_stats_manual'])) {
    $current_data['students'] = $_POST['stat_students'] ?? $current_data['students'];
    $current_data['teachers'] = $_POST['stat_teachers'] ?? $current_data['teachers'];
    $current_data['rewards'] = $_POST['stat_rewards'] ?? $current_data['rewards'];
    $current_data['subjects'] = $_POST['stat_subjects'] ?? $current_data['subjects'];

    // Save back to JSON
    file_put_contents($stats_file, json_encode($current_data, JSON_PRETTY_PRINT));

    // Refresh to prevent resubmission
    header("Location: admin.php?page=settings&update=success");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

   // Load statistics
$stats_file = 'stats.json';
if (!file_exists($stats_file)) {
    file_put_contents($stats_file, json_encode(["students"=>"0","teachers"=>"0","rewards"=>"0","subjects"=>"0"]));
}
$current_data = json_decode(file_get_contents($stats_file), true);

// Load Faculty Data
if (isset($_POST['update_all_faculty'])) {
    $faculty_file = 'faculty.json';
    $faculty = json_decode(file_get_contents($faculty_file), true);
    $target_dir = "uploads/";

    for ($i = 1; $i <= 3; $i++) {
        $slot = "teacher" . $i;

        // Check each field: If the POST is NOT empty, update it. 
        // Otherwise, do nothing (it remains what it was in the $faculty array).
        if (!empty($_POST["t_name_$i"])) {
            $faculty[$slot]['name']  = htmlspecialchars($_POST["t_name_$i"]);
        }
        
        if (!empty($_POST["t_title_$i"])) {
            $faculty[$slot]['title'] = htmlspecialchars($_POST["t_title_$i"]);
        }

        if (!empty($_POST["t_role_$i"])) {
            $faculty[$slot]['role']  = htmlspecialchars($_POST["t_role_$i"]);
        }

        // Handle Photo Uploads (This already only updates if a file is chosen)
        $file_key = "photo_$i";
        if (isset($_FILES[$file_key]) && $_FILES[$file_key]['error'] == 0) {
            $check = getimagesize($_FILES[$file_key]['tmp_name']);
            if ($check !== false) {
                $target_file = $target_dir . $slot . ".jpg";
                move_uploaded_file($_FILES[$file_key]['tmp_name'], $target_file);
            }
        }
    }

    // Save back to JSON (Existing data is preserved for empty fields)
    file_put_contents($faculty_file, json_encode($faculty, JSON_PRETTY_PRINT));
    
    header("Location: admin.php?page=settings&update=success");
    exit();
}
}



if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    header("Location: login.php");
    exit();
}

if (isset($_GET['action']) && $_GET['action'] == 'export_csv') {

    $grade = isset($_GET['grade']) ? $_GET['grade'] : 'all';

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=student_records_'.$grade.'_2026.csv');

    $output = fopen('php://output', 'w');

    fputcsv($output, array(
        'ID',
        'First Name',
        'Last Name',
        'Email',
        'Phone',
        'Father Name',
        'Father Phone',
        'Mother Name',
        'Mother Phone',
        'School',
        'Result %',
        'Status',
        'Address'
    ));

    /* ⭐ Grade Filter Logic */
    $grade_query = "";

    if($grade !== 'all'){
        $safe_grade = mysqli_real_escape_string($connection, $grade);
        $grade_query = " AND grade='$safe_grade'";
    }

    /* ⭐ Final Query */
$query = "
SELECT id, First_Name, Last_Name, email, phone_number, Father_Name ,Father_Phone_NO, Mother_Name, Mother_Phone_NO, Previous_School, Result, status, address 
FROM school_registration
WHERE status='Approved'
$grade_query
ORDER BY id DESC
";


    $rows = mysqli_query($connection, $query);

    while ($row = mysqli_fetch_assoc($rows)) {
        fputcsv($output, $row);
    }

    fclose($output);
    exit();
}


if (isset($_GET['approve_id'])) {
    $id = (int)$_GET['approve_id'];
    mysqli_query($connection, "UPDATE school_registration SET status='Approved' WHERE id = $id");
    header("Location: admin.php?page=admissions&msg=approved");
    exit();
}

if (isset($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];
    mysqli_query($connection, "DELETE FROM school_registration WHERE id = $id");
    header("Location: admin.php?page=students&status=deleted");
    exit();
}

$pass_msg = "";
if(isset($_POST['update_pass'])) {
    $admin_user = $_SESSION['username'];
    $new_password = mysqli_real_escape_string($connection, $_POST['new_pass']);
    $update_query = "UPDATE admin_users SET password='$new_password' WHERE username='$admin_user'";
    if(mysqli_query($connection, $update_query)) {
        $pass_msg = "<p style='color: #10b981; font-weight: bold;'>Password updated successfully!</p>";
    }
}

$total_q = mysqli_query($connection, "SELECT id FROM school_registration");
$total_count = mysqli_num_rows($total_q);
$top_score_q = mysqli_query($connection, "SELECT MAX(Result) as top FROM school_registration");
$top_score = mysqli_fetch_assoc($top_score_q)['top'] ?? 0;

$search = isset($_GET['search']) ? mysqli_real_escape_string($connection, $_GET['search']) : "";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HMM |Admin <?php echo ucfirst($page); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #6366f1; --sidebar-bg: #0f172a; --body-bg: #f8fafc; --white: #ffffff; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { display: flex; background-color: var(--body-bg); min-height: 100vh; color: #1e293b; }
        .sidebar { width: 260px; background: #053954; color: white; padding: 25px; position: fixed; height: 100vh; display: flex; flex-direction: column; }
        .sidebar-brand { font-size: 19px; font-weight: 800; margin-bottom: 40px; color: #ffffff;; display: flex; align-items: center; gap: 10px; }
        .nav-group { display: flex; flex-direction: column; gap: 8px; flex-grow: 1; }
        .nav-link { display: flex; align-items: center; gap: 12px; padding: 12px 15px; color: #94a3b8; text-decoration: none; border-radius: 12px; font-size: 14px; transition: 0.3s; }
        .nav-link:hover, .nav-link.active { background: rgba(255,255,255,0.1); color: white; }
        .nav-link.active { border-left: 4px solid var(--primary); }
        .logout-btn { margin-top: auto; color: #f87171; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 20px; }
        .main-content { margin-left: 260px; width: calc(100% - 260px); padding: 40px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #f1f5f9; }
        .stat-card i { font-size: 24px; color: var(--primary); margin-bottom: 10px; }
        .stat-card h3 { font-size: 28px; font-weight: 700; }
        .stat-card p { font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase; }
        .content-card { background: white; border-radius: 24px; padding: 30px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { text-align: left; padding: 12px; font-size: 11px; text-transform: uppercase; color: #64748b; border-bottom: 1px solid #f1f5f9; }
        td { padding: 15px 12px; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
        .badge-green { background: #ecfdf5; color: #10b981; padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; }
        .action-icon { color: #94a3b8; cursor: pointer; transition: 0.2s; font-size: 16px; margin-right: 10px; text-decoration: none; border:none; background:none;}
        .action-icon:hover { color: var(--primary); }
        .modal { position: fixed; inset: 0; background: rgba(15, 23, 42, 0.8); display: none; justify-content: center; align-items: center; z-index: 1000; backdrop-filter: blur(4px); padding: 20px; }
        .modal-content { background: white; width: 100%; max-width: 800px; padding: 40px; border-radius: 30px; position: relative; max-height: 90vh; overflow-y: auto; }
        .close-btn { position: absolute; top: 25px; right: 25px; cursor: pointer; font-size: 24px; color: #94a3b8; }
        .info-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-top: 20px; }
        .info-box label { font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase; display: block; margin-bottom: 3px; }
        .info-box p { font-weight: 600; font-size: 15px; color: #1e293b; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-brand"><i class="fas fa-graduation-cap"></i> HMM Admin Page</div>
        <div class="nav-group">
            <a href="admin.php?page=dashboard" class="nav-link <?php echo $page == 'dashboard' ? 'active' : ''; ?>"><i class="fas fa-th-large"></i> Dashboard</a>
            <a href="admin.php?page=students" class="nav-link <?php echo $page == 'students' ? 'active' : ''; ?>"><i class="fas fa-user-graduate"></i> All Students</a>
            <a href="admin.php?page=admissions" class="nav-link <?php echo $page == 'admissions' ? 'active' : ''; ?>"><i class="fas fa-clipboard-list"></i> Admissions</a>
            <a href="admin.php?page=settings" class="nav-link <?php echo $page == 'settings' ? 'active' : ''; ?>"><i class="fas fa-sliders-h"></i> Settings</a>
            <a href="admin.php?action=logout" class="nav-link logout-btn" onclick="return confirm('Logout?')"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <div class="main-content">

    <?php if($page == 'dashboard'): ?>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h1>Performance Overview</h1>
            <p style="color: #64748b;">Welcome back, <?php echo $_SESSION['username']; ?></p>
        </div>
        <a href="admin.php?action=export_csv" class="nav-link" style="background: var(--primary); color: white; border-radius: 12px; padding: 12px 20px; font-weight: 600;">
            <i class="fas fa-file-csv"></i> Export to Excel
        </a>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <i class="fas fa-users"></i>
            <p>Total Registered</p>
            <h3><?php echo $total_count; ?></h3>
        </div>
        <div class="stat-card">
            <i class="fas fa-user-clock" style="color: #f59e0b;"></i>
            <p>Pending Review</p>
            <h3><?php 
                $p_count = mysqli_query($connection, "SELECT id FROM school_registration WHERE status='Pending'");
                echo mysqli_num_rows($p_count);
            ?></h3>
        </div>
        <div class="stat-card">
            <i class="fas fa-award"></i>
            <p>Highest Score</p>
            <h3><?php echo $top_score; ?>%</h3>
        </div>
    </div>

    <div class="content-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h3>Recent Applications</h3>
            <a href="admin.php?page=students" style="font-size: 12px; color: var(--primary); text-decoration: none; font-weight: 700;">View All →</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>School</th>
                    <th>Result</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $recent = mysqli_query($connection, "SELECT * FROM school_registration ORDER BY id DESC LIMIT 5");
                while($r = mysqli_fetch_assoc($recent)): ?>
                <tr>
                    <td><b><?php echo $r['First_Name']; ?></b></td>
                    <td><?php echo $r['Previous_School']; ?></td>
                    <td><b style="color:var(--primary)"><?php echo $r['Result']; ?>%</b></td>
                    <td>
                        <span class="<?php echo ($r['status'] == 'Approved') ? 'badge-green' : ''; ?>" 
                              style="<?php echo ($r['status'] == 'Pending') ? 'background:#fff7ed; color:#c2410c; padding:5px 12px; border-radius:20px; font-size:11px; font-weight:700;' : ''; ?>">
                            <?php echo $r['status']; ?>
                        </span>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <?php elseif($page == 'students'): ?>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1>Official Student Database</h1>
        
        <form method="GET">
            <input type="hidden" name="page" value="students">
            <input type="text" name="search" value="<?php echo $search; ?>" placeholder="Search approved students..." style="padding: 10px; border-radius: 10px; border: 1px solid #ddd; width: 250px;">
        </form>
     <a href="admin.php?action=export_csv&grade=<?php echo urlencode($current_grade); ?>" 
class="nav-link"
style="background: var(--primary); color: white; border-radius: 12px; padding: 12px 20px; font-weight: 600;">
<i class="fas fa-file-csv"></i> Export CSV
</a>


    </div>

    <div style="margin-bottom: 25px; display: flex; gap: 10px;">
        <?php 
        $current_grade = isset($_GET['grade']) ? $_GET['grade'] : 'all';
        // MODIFIED: Grade filters now show ONLY All Students, Grade 9, Grade 10, and Grade 11 (Grade 12 removed)
        $grades = ['all' => 'All Students', 'Grade 9' => 'Grade 9', 'Grade 10' => 'Grade 10', 'Grade 11' => 'Grade 11', 'Grade 12' => 'Grade 12'];
        foreach($grades as $val => $label): 
            $active_style = ($current_grade == $val) ? 'background: var(--primary); color: white;' : 'background: white; color: #64748b;';
        ?>
            <a href="admin.php?page=students&grade=<?php echo $val; ?>&search=<?php echo urlencode($search); ?>" 
               style="text-decoration: none; padding: 10px 20px; border-radius: 12px; font-size: 13px; font-weight: 700; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); transition: 0.3s; <?php echo $active_style; ?>">
               <?php echo $label; ?>
            </a>
            
        <?php endforeach; ?>
        
    </div>

    <div class="content-card">
        <table>
            <thead><tr><th>ID</th><th>Name</th><th>Grade</th><th>Phone</th><th>Status</th><th>Action</th></tr></thead>
            <tbody>
                <?php 
                // FIXED: Using lowercase 'grade' to match database
                $grade_query = ($current_grade !== 'all') ? " AND grade = '" . mysqli_real_escape_string($connection, $current_grade) . "'" : "";
                
                $res = mysqli_query($connection, "SELECT * FROM school_registration WHERE status='Approved' $grade_query AND (First_Name LIKE '%$search%' OR Last_Name LIKE '%$search%') ORDER BY id DESC");
                
                if(mysqli_num_rows($res) == 0) {
                    echo "<tr><td colspan='6' style='text-align:center; padding:30px;'>No students found for this selection.</td></tr>";
                }

                while($row = mysqli_fetch_assoc($res)): 
                    $js = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                ?>
                <tr>
                    <td>#<?php echo $row['id']; ?></td>
                    <td><b><?php echo $row['First_Name']." ".$row['Last_Name']; ?></b></td>
                    <td><span style="background: #f1f5f9; padding: 4px 8px; border-radius: 6px; font-weight: 600;"><?php echo $row['grade']; ?></span></td>
                    <td><?php echo $row['phone_number']; ?></td>
                    <td><span class="badge-green">Enrolled</span></td>
                    <td>
                        <button class="action-icon" onclick='openModal(<?php echo $js; ?>)'><i class="far fa-eye"></i></button>
                        <a href="admin.php?delete_id=<?php echo $row['id']; ?>" class="action-icon" style="color:#ef4444" onclick="return confirm('Remove student?')"><i class="far fa-trash-alt"></i></a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <?php elseif($page == 'admissions'): ?>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <h1>New Admission Requests</h1>
                <div class="badge-green" style="background: #fef3c7; color: #92400e;">Action Required</div>
            </div>

            <div class="content-card">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Grade</th>
                            <th>Score</th>
                            <th>Previous School</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $pend = mysqli_query($connection, "SELECT * FROM school_registration WHERE status='Pending' ORDER BY id DESC");
                        
                        if(mysqli_num_rows($pend) == 0) {
                            echo "<tr><td colspan='5' style='text-align:center; padding: 40px; color: #94a3b8;'>No new requests found.</td></tr>";
                        }

                        while($row = mysqli_fetch_assoc($pend)): 
                            $js = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                        ?>
                        <tr>
                            <td><b><?php echo $row['First_Name']." ".$row['Last_Name']; ?></b></td>
                            <td>Grade <?php echo $row['grade']; ?></td>
                            <td><b style="color: var(--primary)"><?php echo $row['Result']; ?>%</b></td>
                            <td><?php echo $row['Previous_School']; ?></td>
                            <td>
                                <button class="action-icon" onclick='openModal(<?php echo $js; ?>)' title="View"><i class="far fa-eye"></i></button>
                                <a href="admin.php?approve_id=<?php echo $row['id']; ?>" class="action-icon" style="color:#10b981" title="Approve" onclick="return confirm('Approve this student?')">
                                    <i class="fas fa-check-circle"></i>
                                </a>
                                <a href="admin.php?delete_id=<?php echo $row['id']; ?>" class="action-icon" style="color:#ef4444" title="Reject" onclick="return confirm('Reject and Delete?')">
                                    <i class="fas fa-times-circle"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

    <?php elseif($page == 'settings'): ?>
            <h1>Settings</h1>
            <div class="content-card" style="margin-top:20px; max-width: 500px;">
                <?php echo $pass_msg; ?>
                <form method="POST">
                    <label style="font-size: 11px; font-weight: 800; color: #94a3b8;">LOGGED IN AS</label>
                    <p style="font-size: 18px; font-weight: 600; margin-bottom: 20px;"><?php echo $_SESSION['username']; ?></p>
                    <label style="font-size: 11px; font-weight: 800; color: #94a3b8;">NEW PASSWORD</label>
                    <input type="password" name="new_pass" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 10px; margin-bottom: 20px;">
                    <button type="submit" name="update_pass" style="width: 100%; padding: 12px; background: var(--primary); color: white; border: none; border-radius: 10px; font-weight: 600; cursor: pointer;">Update Password</button>
                </form>
            </div>
            
              <?php
// Make sure you have loaded the latest data from the file first!
$faculty_file = 'faculty.json';
$faculty = json_decode(file_get_contents($faculty_file), true);
?>

<form method="POST" enctype="multipart/form-data" >
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 20px;">
        
        <?php for($i=1; $i<=3; $i++): 
            $tid = "teacher".$i;
            // Get current data or set to empty if teacher doesn't exist yet
            $current_name  = $faculty[$tid]['name'] ?? '';
            $current_title = $faculty[$tid]['title'] ?? '';
            $current_role  = $faculty[$tid]['role'] ?? '';
        ?>
        <div class="content-card" style="padding: 20px; border: 1px solid #ddd; border-radius: 12px;">

           <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                    <div style="width: 70px; height: 70px; border-radius: 50%; overflow: hidden; border: 2px solid var(--primary);">
                        <img src="uploads/<?php echo $tid; ?>.jpg?t=<?php echo time(); ?>" 
                             style="width: 100%; height: 100%; object-fit: cover;" 
                             onerror="this.src='https://via.placeholder.com/70?text=Staff'">
                    </div>
                    <div>
                        <h4 style="color: #1e293b;">Member #<?php echo $i; ?></h4>
                        <input type="file" name="photo_<?php echo $i; ?>" accept="image/*" style="font-size: 11px;">
                    </div>
                </div>
            
            <label style="font-size: 11px; font-weight: bold; color: #666;">NAME</label>
            <input type="text" name="t_name_<?php echo $i; ?>" 
                   value="<?php echo htmlspecialchars($current_name); ?>" 
                   style="width: 100%; padding: 8px; margin-bottom: 15px; border-radius: 5px; border: 1px solid #ccc;">
            
            <label style="font-size: 11px; font-weight: bold; color: #666;">TITLE</label>
            <input type="text" name="t_title_<?php echo $i; ?>" 
                   value="<?php echo htmlspecialchars($current_title); ?>" 
                   style="width: 100%; padding: 8px; margin-bottom: 15px; border-radius: 5px; border: 1px solid #ccc;">
            
            <label style="font-size: 11px; font-weight: bold; color: #666;">ROLE</label>
            <input type="text" name="t_role_<?php echo $i; ?>" 
                   value="<?php echo htmlspecialchars($current_role); ?>" 
                   style="width: 100%; padding: 8px; margin-bottom: 15px; border-radius: 5px; border: 1px solid #ccc;">

            
        </div>
        <?php endfor; ?>

    </div>

    <button type="submit" name="update_all_faculty" style="margin-top: 30px; padding: 12px 25px; background: #6366f1; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold;">
        Save All Changes
    </button>
</form>


            <div class="content-card" style="margin-top:20px; max-width: 500px;">
                <h3>School Statistics</h3>
                <form method="POST" style="margin-top: 15px;">
                    <label>Students Count:</label>
                    <input type="text" name="stat_students" value="<?php echo $current_data['students']; ?>" style="width:100%; padding:10px; margin-bottom:10px; border-radius:5px; border:1px solid #ccc;">
                    <label>Teachers Count:</label>
                    <input type="text" name="stat_teachers" value="<?php echo $current_data['teachers']; ?>" style="width:100%; padding:10px; margin-bottom:10px; border-radius:5px; border:1px solid #ccc;">
                    <label>Rewards Count:</label>
                    <input type="text" name="stat_rewards" value="<?php echo $current_data['rewards']; ?>" style="width:100%; padding:10px; margin-bottom:10px; border-radius:5px; border:1px solid #ccc;">
                    <label>Subjects Count:</label>
                    <input type="text" name="stat_subjects" value="<?php echo $current_data['subjects']; ?>" style="width:100%; padding:10px; margin-bottom:15px; border-radius:5px; border:1px solid #ccc;">
                    <button type="submit" name="update_stats_manual" style="background: #007bff; color: white; border: none; padding: 12px; border-radius: 5px; width: 100%; cursor: pointer; font-weight: bold;">Apply Changes</button>
                </form>
            </div>
      <?php endif; ?>

    <div id="stuModal" class="modal" onclick="this.style.display='none'">
        <div class="modal-content" onclick="event.stopPropagation()">
            <span class="close-btn" onclick="document.getElementById('stuModal').style.display='none'">&times;</span>
            <h2 id="m_name" style="margin-bottom: 5px;"></h2>
            <p id="m_email" style="color: var(--primary); font-weight: 600; margin-bottom: 25px;"></p>
            <div class="info-grid">
                <div class="info-box"><label>Phone Number</label><p id="m_phone"></p></div>
                <div class="info-box"><label>Grade</label><p id="m_grade"></p></div>
                <div class="info-box"><label> optional-Email</label><p id="m_optional_email"></p></div>
                <div class="info-box"><label>Date of Birth</label><p id="m_dob"></p></div>
                <div class="info-box"><label>Gender</label><p id="m_gender"></p></div>

                <div class="info-box"><label>Father's Name</label><p id="m_f_name"></p></div>
                <div class="info-box"><label>Father's Phone</label><p id="m_f_phone"></p></div>
                <div class="info-box"><label>Mother's Name</label><p id="m_m_name"></p></div>
                <div class="info-box"><label>Mother's Phone</label><p id="m_m_phone"></p></div>

                <div class="info-box"><label>Previous School</label><p id="m_school"></p></div>

                <div class="info-box"><label>Exam Result</label><p id="m_result"></p></div>
                <div class="info-box" style="grid-column: span 2;"><label>Home Address</label><p id="m_address"></p></div>
            </div>
            <div style="margin-top: 30px;">
                <label style="font-size: 10px; font-weight: 800; color: #94a3b8;">RESULT CARD DOCUMENT</label>
                <img id="m_img" src="" style="width: 100%; border-radius: 15px; margin-top: 10px; border: 1px solid #eee; max-height: 400px; object-fit: contain;">
            </div>
        </div>
    </div>

    <script>
        function openModal(data) {
            document.getElementById('m_name').innerText = data.First_Name + " " + data.Last_Name;
            document.getElementById('m_email').innerText = data.email;
            document.getElementById('m_phone').innerText = data.phone_number;
            document.getElementById('m_grade').innerText =  data.grade;
            document.getElementById('m_dob').innerText = data.Date_of_birth;
            document.getElementById('m_gender').innerText = data.Gender;
            document.getElementById('m_f_name').innerText = data.Father_Name;
            document.getElementById('m_f_phone').innerText = data.Father_Phone_NO;
            document.getElementById('m_m_name').innerText = data.Mother_Name;
                document.getElementById('m_m_phone').innerText = data.Mother_Phone_NO;
                document.getElementById('m_optional_email').innerText = data.optional_email;
                
            document.getElementById('m_school').innerText = data.Previous_School;
            document.getElementById('m_result').innerText = data.Result + "%";
            document.getElementById('m_address').innerText = data.address;
            document.getElementById('m_img').src = data.Result_Card;
            document.getElementById('stuModal').style.display = 'flex';
        }
    </script>
</body>
</html>
