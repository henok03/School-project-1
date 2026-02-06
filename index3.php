<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfect Student Enrollment | 2026</title>
    <link rel="stylesheet" href="styleR.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    
    
</head>
<body>

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
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <nav class="nav-menu ms-auto">
                <ul class="navbar-nav">
                    <li><a href="index.html">Home</a></li>
                    <li><a href="index2.html">About</a></li>
                    <li><a href="#" class="active">Register</a></li>
                    <li><a href="contact us.html">Contact</a></li>
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

    <form class="form-body" method="POST" action="" enctype="multipart/form-data" id="enrollmentForm">
        
        <div class="section-title"><span>1</span> Student Identity</div>
        <div class="grid">
            <div class="input-group">
                <label>First Name</label>
                <input type="text" placeholder="Enter first name" name="first_name" required>
            </div>
            <div class="input-group">
                <label>Last Name</label>
                <input type="text" placeholder="Enter last name" name="last_name" required>
            </div>
             <div class="input-group">
                <label>Email</label>
                <input type="email" placeholder="Enter email address" name="email" required>
            </div>
            <div class="input-group">
                <label>Date of Birth</label>
                <input type="date" name="date_of_birth" required>
            </div>
            <div class="input-group">
                <label>Optional-Email</label>
                <input type="email" placeholder="Enter optional email address" name="optional_email">
            </div>
            <div class="input-group">
                <label>Address</label>
                <input type="text" placeholder="Enter address" name="address" required>
            </div>
            <div class="input-group">
                <label>Phone Number</label>
                <input type="tel" placeholder="+123 456 7890" name="phone_number">
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

        <div class="section-title"><span>2</span> Parent / Guardian Information</div>
        <div class="grid">
            <div class="input-group">
                <label>Father's Name</label>
                <input type="text" placeholder="Father's Full Name" name="father_name" required>
            </div>
            <div class="input-group">
                <label>Father's Phone Number</label>
                <input type="tel" placeholder="+123 456 7890" name="father_phone_number" required>
            </div>
            <div class="input-group">
                <label>Mother's Name</label>
                <input type="text" placeholder="Mother's Full Name" name="mother_name" required>
            </div>
            <div class="input-group">
                <label>Mother's Phone Number</label>
                <input type="tel" placeholder="+123 456 7890" name="mother_phone_number" required>
            </div>
        </div>

        <div class="section-title"><span>3</span> Previous Academic Records</div>
        <div class="grid">
            <div class="input-group full">
                <label>Previous School Name</label>
                <input type="text" placeholder="Name of previous school" name="previous_school" required>
            </div>
            <div class="input-group full">
                <label>Final Result / Percentage (%)</label>
                <input type="number" min="0" max="100" placeholder="e.g. 85" name="result" required>
            </div>
        </div>

        <div class="section-title"><span>4</span> Document Verification</div>
        <div class="input-group full">
            <label>Upload Result Card / Photo</label>
            <div class="upload-area" id="uploadBox">
                <p id="uploadMsg">Click to Select or Drag Photo Here</p>
                <img id="preview-img" src="#" alt="Preview">
                <input type="file" id="fileInput" name="result_card_photo" accept="image/*" required style="position: absolute; top:0; left:0; width:100%; height:100%; opacity:0; cursor:pointer;">
            </div>
        </div>

        <button type="submit" class="submit-btn">SUBMIT ADMISSION APPLICATION</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // File Upload Preview
    const fileInput = document.getElementById('fileInput');
    const previewImg = document.getElementById('preview-img');
    const uploadMsg = document.getElementById('uploadMsg');

    fileInput.onchange = evt => {
        const [file] = fileInput.files;
        if (file) {
            previewImg.src = URL.createObjectURL(file);
            previewImg.style.display = 'inline-block';
            uploadMsg.innerHTML = "✅ Photo Added: " + file.name;
        }
    }

    // Navbar scroll effect
    window.addEventListener('scroll', function() {
        const navbar = document.getElementById('navbar');
        if (window.scrollY > 60) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });
</script>

<?php
// DATABASE CONNECTION
$connection = mysqli_connect("127.0.0.1", "root", "", "school_registration", 3309);

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

if(isset($_POST['first_name'])){
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $date_of_birth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $father_name = $_POST['father_name'];
    $father_phone = $_POST['father_phone_number'];
    $mother_name = $_POST['mother_name'];
    $mother_phone = $_POST['mother_phone_number'];
    $address = $_POST['address'];
    $phone_number = $_POST['phone_number'];
    $optional_email = $_POST['optional_email'];
    $email= $_POST['email'];
    $previous_school = $_POST['previous_school'];
    $result = $_POST['result'];

    // Image Upload
    $file_name = $_FILES['result_card_photo']['name'];
    $file_tmp = $_FILES['result_card_photo']['tmp_name'];
    $upload_folder = "uploads/";

    if(!is_dir($upload_folder)) { mkdir($upload_folder); }
    $file_path = $upload_folder . time() . "_" . $file_name;
    move_uploaded_file($file_tmp, $file_path);

    $query = "INSERT INTO school_registration 
              (First_Name, Last_Name, Date_of_birth, Gender, Father_Name, Father_Phone_NO, address, phone_number, optional_email, email, Mother_Name, Mother_Phone_NO, Previous_School, Result, Result_Card) 
              VALUES 
              ('$first_name','$last_name','$date_of_birth','$gender','$father_name','$father_phone', '$address', '$phone_number', '$optional_email', '$email', '$mother_name','$mother_phone', '$previous_school','$result','$file_path')";

    if(mysqli_query($connection, $query)){
        echo "<script>alert('Student Registered Successfully');</script>";
    } else {
        echo "Error: " . mysqli_error($connection);
    }
}
mysqli_close($connection);
?>

</body>
</html>