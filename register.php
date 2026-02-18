<?php
// 1. DATABASE CONNECTION
$connection = mysqli_connect("127.0.0.1", "root", "", "school_registration", 3309);

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// 2. PROCESS SUBMISSION & REDIRECT
if (isset($_POST['first_name'])) {
    // Sanitize inputs
    $first_name = mysqli_real_escape_string($connection, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($connection, $_POST['last_name']);
    $date_of_birth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $apply_to = $_POST['apply_to'];
    $father_name = mysqli_real_escape_string($connection, $_POST['father_name']);
    $father_phone = $_POST['father_phone_number'];
    $mother_name = mysqli_real_escape_string($connection, $_POST['mother_name']);
    $mother_phone = $_POST['mother_phone_number'];
    $address = mysqli_real_escape_string($connection, $_POST['address']);
    $phone_number = $_POST['phone_number'];
    $optional_email = $_POST['optional_email'];
    $email = $_POST['email'];
    $previous_school = mysqli_real_escape_string($connection, $_POST['previous_school']);
    $result = $_POST['result'];

    // Image Upload
    $file_name = $_FILES['result_card_photo']['name'];
    $file_tmp = $_FILES['result_card_photo']['tmp_name'];
    $upload_folder = "uploads/";
    if(!is_dir($upload_folder)) { mkdir($upload_folder); }
    $file_path = $upload_folder . time() . "_" . $file_name;
    move_uploaded_file($file_tmp, $file_path);

    $query = "INSERT INTO school_registration 
              (First_Name, Last_Name, Date_of_birth, Gender, grade, Father_Name, Father_Phone_NO, address, phone_number, optional_email, email, Mother_Name, Mother_Phone_NO, Previous_School, Result, Result_Card) 
              VALUES 
              ('$first_name','$last_name','$date_of_birth','$gender','$apply_to','$father_name','$father_phone', '$address', '$phone_number', '$optional_email', '$email', '$mother_name','$mother_phone', '$previous_school','$result','$file_path')";

    if (mysqli_query($connection, $query)) {
        // Redirect to self with success parameters to prevent resubmission on refresh
        header("Location: " . $_SERVER['PHP_SELF'] . "?status=success&user=" . urlencode($first_name));
        mysqli_close($connection);
        exit; 
    } else {
        $db_error = mysqli_error($connection);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration Form</title>
    <link rel="stylesheet" href="styleR.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
<style>
    :root {
    --primary: #1a5f7a;
    --secondary: #159895;
    --success: #10b981;
    --light-bg: #f0f4f8;
    --border-color: #d1d9e6;
    --navbar-bg: #053954;
}

body {
    background-color: #f4f7f9;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    padding-top: 110px;
    /* FIX: Prevents form from being hidden under navbar */
}
  @media (max-width: 576px) {
            #navbarNav {
                padding: 3px;
            }

            .navbar-brand img {
                width: 70px !important;
                /* Smaller logo for mobile */
                height: auto !important;
            }

          .school-name h4 {
                padding: 1px;
                font-size: 21px;
                /* Smaller font for mobile */
            }

            .school-name small {
                font-size: 17px;
                display: block;

                /* Ensures the subtitle stays underneath */
            }
        }

/* --- 2. NAVBAR & MOBILE FIXES --- */
#navbar {
    position: fixed;
    top: 0;
    width: 100%;
    z-index: 1000;
    padding: 10px 0;
    transition: all 0.4s ease-in-out;
    background: var(--navbar-bg);
}
.nav-menu li a {
            text-decoration: none;
            color: #f6f7f8 !important;
            font-weight: 600;
            padding: 0.5rem 1rem;
            display: block;
            transition: 0.3s;
        }
.nav-menu li a:hover {
            color: #25bdeb !important;
            background-color: rgba(2, 9, 25, 0.1);
            border-radius: 8px;
        }
        .nav-menu li a.active {
            color: #25caeb !important;
            background-color: rgba(2, 9, 25, 0.15);
            font-weight: 700;
            border-radius: 8px;
        }
.nav-link {
   
    margin-right: 20px;
    transition: color 0.3s ease;
}

.nav-menu ul {
    display: flex;
    list-style: none;
    margin: 0;
    padding: 0;
}

/* --- FIXED NAVBAR TOGGLER --- */
/* --- FIXED BLACK NAVBAR TOGGLER --- */
.navbar-toggler {
    /* The outer border */
    border: 3px solid #000000 !important;
    border-radius: 8px;
    padding: 0.4rem;
    background: transparent;
    transition: all 0.3s ease;
}

.navbar-toggler-icon {
    /* Change #000 to transparent to remove the solid black block */
    background: transparent !important;
    position: relative;
    display: inline-block;
    width: 24px;
    height: 18px;
    vertical-align: middle;
}

.navbar-toggler:hover {
    background-color: rgba(37, 99, 235, 0.1);
    background-color: rgba(37, 99, 235, 0.1);
    
}

/* This styles the actual white lines inside the button */
.navbar-toggler-icon::before,
.navbar-toggler-icon::after,
.navbar-toggler-icon span {
    position: absolute;
    left: 0;
    width: 100%;
    height: 2px;
    background-color: #000000 !important;
    /* Keep this white for the blue navbar */
    transition: all 0.3s ease;
    content: '';
}

/* Line spacing */
.navbar-toggler-icon::before {
    top: 0;
}

.navbar-toggler-icon span {
    top: 8px;
}

.navbar-toggler-icon::after {
    bottom: 0;
}

/* Mobile Menu Background */
@media (max-width: 991px) {
    .navbar-collapse {
        background: var(--navbar-bg);
        margin-top: 15px;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }

    .nav-menu ul {
        flex-direction: column;
        text-align: center;
        gap: 10px;
    }
}


/* --- 3. FORM STYLING --- */
.form-card {
    background: #ffffff;
    max-width: 850px;
    margin: 0 auto 50px auto;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}

.form-header {
    background: var(--primary);
    color: white;
    padding: 30px;
    text-align: center;
}

.form-body {
    padding: 40px;
}

.section-title {
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--primary);
    border-bottom: 2px solid var(--light-bg);
    padding-bottom: 10px;
    margin: 30px 0 20px 0;
    display: flex;
    align-items: center;
}

.section-title span {
    background: var(--primary);
    color: white;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
    font-size: 0.9rem;
}

.grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.input-group {
    margin-bottom: 15px;
    flex-direction: column;
    display: flex;
}

.full {
    grid-column: span 2;
}

label {
    font-weight: 600;
    margin-bottom: 8px;
    font-size: 0.9rem;
}

input,
select {
    width: 100%;
    padding: 12px;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    transition: 0.3s;
}

.upload-area {
    border: 2px dashed #94a3b8;
    border-radius: 12px;
    padding: 30px;
    text-align: center;
    background: #f8fafc;
    position: relative;
    cursor: pointer;
}

#preview-img {
    max-width: 150px;
    margin-top: 15px;
    display: none;
    border-radius: 8px;
}

.submit-btn {
    width: 100%;
    background: var(--primary);
    color: white;
    padding: 18px;
    border: none;
    border-radius: 8px;
    font-weight: bold;
    margin-top: 30px;
    transition: 0.3s;
}

.submit-btn:hover {
    background: var(--secondary);
}

/* --- 📱 MOBILE OPTIMIZATION (Phone & Tablet) --- */
@media (max-width: 768px) {

    /* 1. Remove side margins so the card fits the screen width */
    .form-card {
        margin: 10px;
        border-radius: 8px;
        /* Slightly sharper corners look better on small screens */
    }

    /* 2. Reduce padding so we don't waste precious screen space */
    .form-header {
        padding: 20px 15px;
    }

    .form-body {
        padding: 20px;
    }

    /* 3. FORCE GRID TO SINGLE COLUMN */
    /* This stops "First Name" and "Last Name" from being side-by-side */
    .grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }

    .full {
        grid-column: span 1;
    }

    /* 4. Larger Inputs for easier tapping */
    input,
    select {
        padding: 14px;
        /* Bigger tap area for thumbs */
        font-size: 16px;
        /* Prevents iOS from auto-zooming on focus */
    }

    /* 5. Adjust Section Titles */
    .section-title {
        font-size: 1.1rem;
        margin: 20px 0 15px 0;
    }

    /* 6. Make the Submit Button "Sticky" or more prominent */
    .submit-btn {
        padding: 20px;
        font-size: 1.1rem;
    }
}        /* --- 1. CORE STYLES & VARIABLES --- */
        
</style>

</head>


<body>

<?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
    <div style="display: flex; justify-content: center; align-items: center; height: 100vh; background: #f0f2f5; position: fixed; width: 100%; top: 0; left: 0; z-index: 10000;">
        <div style="background: white; padding: 60px; border-radius: 25px; box-shadow: 0 20px 50px rgba(0,0,0,0.1); text-align: center; max-width: 550px; width: 90%;">
     
            <h1 style="font-weight: 800; margin-top: 20px;">Registration Successful!</h1>
            <p style="font-size: 1.2rem; color: #444;">Thank you, <b><?php echo htmlspecialchars($_GET['user']); ?></b>. Your application is submitted.</p>

            <div style="margin-top: 30px; display: flex; gap: 15px; justify-content: center;">
                <a href="index.html" class="btn btn-primary btn-lg" style="border-radius: 50px; padding: 12px 35px;">GO TO HOME PAGE</a>
                <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-outline-secondary btn-lg" style="border-radius: 50px;">New Register</a>
            </div>
        </div>
    </div>
    <?php exit; ?>
<?php endif; ?>


<header id="navbar" class="navbar navbar-expand-lg">

    <div class="container">

        <a class="navbar-brand d-flex align-items-center" href="index.html">

            <img src="logo.png" alt="logo" width="80" height="70" class="me-2">

            <div class="school-name text-white">

                <h4 class="m-0">Haile Mariam</h4>

                <small>Mamo School</small>

            </div>

        </a>

       <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
    <span class="navbar-toggler-icon">
        <span></span> </span>
</button>



        <div class="collapse navbar-collapse" id="navbarNav">

            <nav class="nav-menu ms-auto">

                <ul class="navbar-nav">

                    <li class="nav-item"><a class="nav-link" href="index.html">Home</a></li>

                    <li class="nav-item"><a class="nav-link" href="index2.html">About</a></li>

                    <li class="nav-item"><a class="nav-link active" href="#">Register</a></li>

                    <li class="nav-item"><a class="nav-link" href="contact us.html">Contact</a></li>

                </ul>

            </nav>

        </div>

    </div>

</header>

<div class="form-card">
    <div class="form-header">
        <h2>School Admission Portal</h2>
        <p>Official Enrollment Form 2026</p>
    </div>

    <?php if(isset($db_error)): ?>
        <div class="alert alert-danger"><?php echo $db_error; ?></div>
    <?php endif; ?>

    <form class="form-body" method="POST" action="" enctype="multipart/form-data">
        
        <div class="section-title"><span>1</span> Student Identity</div>
        <div class="grid">
            <div class="input-group">
                <label>First Name</label>
                <input type="text" name="first_name" required>
            </div>
            <div class="input-group">
                <label>Last Name</label>
                <input type="text" name="last_name" required>
            </div>
             <div class="input-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="input-group">
                <label>Date of Birth</label>
                <input type="date" name="date_of_birth" required>
            </div>
            <div class="input-group">
                <label>Optional-Email</label>
                <input type="email" name="optional_email">
            </div>
            <div class="input-group">
                <label>Address</label>
                <input type="text" name="address" required>
            </div>
            <div class="input-group">
                <label>Phone Number</label>
                <input type="tel" name="phone_number">
            </div>
            <div class="input-group">
                <label>Gender</label>
                <select name="gender" required>
                    <option value="">Select Gender</option>
                    <option>Male</option>
                    <option>Female</option>
                </select>
            </div>
        </div>

        <div class="input-group">
            <label>Apply to</label>
            <select name="apply_to" required>
                <option value="">Select Grade</option>
                <option>Grade 9</option>
                <option>Grade 10</option>
                <option>Grade 11</option>
                <option>Grade 12</option>
            </select>
        </div>
        
        <div class="section-title"><span>2</span> Parent / Guardian Information</div>
        <div class="grid">
            <div class="input-group">
                <label>Father's Name</label>
                <input type="text" name="father_name" required>
            </div>
            <div class="input-group">
                <label>Father's Phone NO</label>
                <input type="tel" name="father_phone_number" required>
            </div>
            <div class="input-group">
                <label>Mother's Name</label>
                <input type="text" name="mother_name" required>
            </div>
            <div class="input-group">
                <label>Mother's Phone NO</label>
                <input type="tel" name="mother_phone_number" required>
            </div>
        </div>

        <div class="section-title"><span>3</span> Previous Academic Records</div>
        <div class="grid">
            <div class="input-group full">
                <label>Previous School Name</label>
                <input type="text" name="previous_school" required>
            </div>
            <div class="input-group full">
                <label>Final Result / Percentage (%)</label>
                <input type="number" min="0" max="100" name="result" required>
            </div>
        </div>

        <div class="section-title"><span>4</span> Document Verification</div>
<div class="input-group full">
    <label>Upload Result Card / Photo</label>
    <div class="upload-area" style="position: relative; border: 2px dashed #ccc; padding: 20px; text-align: center; border-radius: 10px;">
        
        <p id="uploadMsg" style="margin-bottom: 10px;">Click to Select Photo</p>
        
        <img id="imagePreview" src="#" alt="Preview" style="display: none; max-width: 150px; border-radius: 8px; margin: 0 auto;">

        <input type="file" id="fileInput" name="result_card_photo" accept="image/*" required 
               style="position: absolute; opacity: 0; width: 100%; height: 100%; top: 0; left: 0; cursor: pointer;">
    </div>
</div>
        <button type="submit" class="submit-btn">SUBMIT ADMISSION APPLICATION</button>
    </form>
</div>
<script>
    const fileInput = document.getElementById('fileInput');
    const uploadMsg = document.getElementById('uploadMsg');
    const imagePreview = document.getElementById('imagePreview');

    fileInput.onchange = evt => {
        const [file] = fileInput.files;
        if (file) {
            // 1. Create a URL for the selected image
            imagePreview.src = URL.createObjectURL(file);
            
            // 2. Show the image and change the text
            imagePreview.style.display = 'block';
            uploadMsg.innerHTML = "✅ <span style='color: #28a745; font-weight: bold;'>Photo Selected:</span> " + file.name;
            
            // 3. Add a success border to the box
            fileInput.parentElement.style.borderColor = "#28a745";
            fileInput.parentElement.style.backgroundColor = "#f8fff9";
        }
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>