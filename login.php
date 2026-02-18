<?php
session_start();
$connection = mysqli_connect("127.0.0.1", "root", "", "school_registration", 3309);

if(isset($_POST['login'])) {
    $user = mysqli_real_escape_string($connection, $_POST['username']);
    $pass = $_POST['password']; // In a real app, use password_verify()

    $query = "SELECT * FROM admin_users WHERE username='$user' AND password='$pass'";
    $result = mysqli_query($connection, $query);

    if(mysqli_num_rows($result) == 1) {
        // --- THIS RELATES IT TO ADMIN.PHP ---
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['username'] = $user;
        header("Location: admin.php?page=dashboard"); 
        exit();
    } else {
        $error = "Access Denied: Invalid Credentials";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>EduAdmin | Secure Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        /* Update your existing .login-card class with these two changes */
.login-card {
    background: white;
    padding: 40px;
    border-radius: 24px;
    box-shadow: 0 20px 50px rgba(0,0,0,0.05);
    
    /* CHANGE THESE TWO LINES */
    width: 90%;          /* Uses 90% of the screen on small devices */
    max-width: 500px;    /* Prevents it from getting too wide on desktop */
    
    text-align: center;
    border: 1px solid #f1f5f9;
    box-sizing: border-box; /* Ensures padding doesn't increase width */
}

/* ADD THIS MEDIA QUERY at the bottom of your style section */
@media (max-width: 480px) {
    .login-card {
        padding: 30px 20px; /* Reduce padding on mobile to save space */
    }
    
    h2 {
        font-size: 24px; /* Slightly smaller title for small screens */
    }
}
        p.subtitle { color: #64748b; font-size: 14px; margin-bottom: 30px; }
        input { width: 100%; padding: 14px; margin: 10px 0; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 14px; box-sizing: border-box; outline: none; transition: 0.3s; }
        input:focus { border-color: #6366f1; box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1); }
        button { width: 100%; padding: 14px; background: #6366f1; color: white; border: none; border-radius: 12px; font-size: 16px; font-weight: 600; cursor: pointer; margin-top: 15px; transition: 0.3s; }
        button:hover { background: #4f46e5; transform: translateY(-2px); }
        .error { color: #ef4444; background: #fef2f2; padding: 10px; border-radius: 8px; font-size: 13px; margin-bottom: 20px; font-weight: 600; }
       
    </style>
</head>
<body>
    <div class="login-card">
        <h2>Welcome Back</h2>
        <p class="subtitle">Enter your admin credentials to continue</p>
        
        <?php if(isset($error)) echo "<div class='error'>$error</div>"; ?>
        
        <form method="POST" id="loginForm">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Login to Dashboard</button>
        </form>
    </div>
    
</body>
</html>