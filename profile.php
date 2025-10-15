<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_query = $conn->prepare("SELECT * FROM users WHERE id = ?");
$user_query->bind_param("i", $user_id);
$user_query->execute();
$user_result = $user_query->get_result();
$user = $user_result->fetch_assoc();

// Handle profile picture upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['profile_picture'])) {
    $target_dir = "uploads/profiles/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
    // Check if image file is actual image
    $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
    if ($check !== false) {
        // Generate unique filename
        $new_filename = "profile_" . $user_id . "." . $imageFileType;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
            // Update database
            $update_stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
            $update_stmt->bind_param("si", $new_filename, $user_id);
            if ($update_stmt->execute()) {
                $success = "Profile picture updated successfully!";
                $user['profile_picture'] = $new_filename;
            }
        } else {
            $error = "Sorry, there was an error uploading your file.";
        }
    } else {
        $error = "File is not an image.";
    }
}

// Handle password change
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (password_verify($current_password, $user['password'])) {
        if ($new_password === $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $update_stmt->bind_param("si", $hashed_password, $user_id);
            if ($update_stmt->execute()) {
                $password_success = "Password changed successfully!";
            }
        } else {
            $password_error = "New passwords do not match.";
        }
    } else {
        $password_error = "Current password is incorrect.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profile | eBMS</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <?php 
  if ($_SESSION['role'] == 'boarder') {
      include 'bd-header.php';
  } else {
      include 'ad-header.php';
  }
  ?>

  <div class="main-container">
    <?php 
    if ($_SESSION['role'] == 'boarder') {
        include 'bd-sidebar.php';
    } else {
        include 'ad-sidebar.php';
    }
    ?>

    <main class="main-content" id="mainContent">
      <div class="content-header">
        <h1>My Profile</h1>
        <p>Manage your personal information and account settings</p>
      </div>

      <div class="profile-container" style="display: flex; gap: 30px; flex-wrap: wrap;">
        <!-- Profile Picture Section -->
        <div class="profile-picture-section" style="flex: 1; min-width: 300px;">
          <div style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); text-align: center;">
            <div style="width: 150px; height: 150px; border-radius: 50%; overflow: hidden; margin: 0 auto 20px; border: 3px solid #4263eb;">
              <img src="<?php echo $user['profile_picture'] ? 'uploads/profiles/' . $user['profile_picture'] : 'img/default-avatar.png'; ?>" 
                   alt="Profile Picture" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            <h3><?php echo $user['fname'] . ' ' . $user['lname']; ?></h3>
            <p style="color: #666; margin-bottom: 20px;">Boarder ID: <?php echo $user['boarder_id']; ?></p>
            
            <form method="POST" enctype="multipart/form-data" style="margin-top: 20px;">
              <div class="form-group">
                <label>Update Profile Picture:</label>
                <input type="file" name="profile_picture" accept="image/*" required>
              </div>
              <button type="submit" class="btn" style="margin-top: 10px;">Upload Picture</button>
            </form>
            
            <?php if (isset($success)): ?>
              <div style="color: green; margin-top: 10px;"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
              <div style="color: red; margin-top: 10px;"><?php echo $error; ?></div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Personal Information Section -->
        <div class="personal-info-section" style="flex: 2; min-width: 300px;">
          <div style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); margin-bottom: 20px;">
            <h3 style="color: #4263eb; margin-bottom: 20px;">Personal Information</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
              <div>
                <label><strong>First Name:</strong></label>
                <p><?php echo $user['fname']; ?></p>
              </div>
              <div>
                <label><strong>Middle Name:</strong></label>
                <p><?php echo $user['mname'] ?: 'N/A'; ?></p>
              </div>
              <div>
                <label><strong>Last Name:</strong></label>
                <p><?php echo $user['lname']; ?></p>
              </div>
              <div>
                <label><strong>Age:</strong></label>
                <p><?php echo $user['age']; ?></p>
              </div>
              <div class="full-width">
                <label><strong>Address:</strong></label>
                <p><?php echo $user['address']; ?></p>
              </div>
              <div>
                <label><strong>Email:</strong></label>
                <p><?php echo $user['email']; ?></p>
              </div>
              <div>
                <label><strong>Contact No.:</strong></label>
                <p><?php echo $user['contact']; ?></p>
              </div>
              <div>
                <label><strong>Room Number:</strong></label>
                <p><?php echo $user['room_number'] ?: 'Not assigned'; ?></p>
              </div>
            </div>
          </div>

          <!-- Guardian Information -->
          <div style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); margin-bottom: 20px;">
            <h3 style="color: #4263eb; margin-bottom: 20px;">Guardian Information</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
              <div>
                <label><strong>Full Name:</strong></label>
                <p><?php echo $user['guardian_fullname']; ?></p>
              </div>
              <div>
                <label><strong>Relationship:</strong></label>
                <p><?php echo $user['guardian_relationship']; ?></p>
              </div>
              <div>
                <label><strong>Contact No.:</strong></label>
                <p><?php echo $user['guardian_contact']; ?></p>
              </div>
              <div>
                <label><strong>Email:</strong></label>
                <p><?php echo $user['guardian_email'] ?: 'N/A'; ?></p>
              </div>
            </div>
          </div>

          <!-- Change Password -->
          <div style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
            <h3 style="color: #4263eb; margin-bottom: 20px;">Change Password</h3>
            <form method="POST">
              <input type="hidden" name="change_password" value="1">
              <div class="form-group">
                <label class="required">Current Password:</label>
                <input type="password" name="current_password" required>
              </div>
              <div class="form-group">
                <label class="required">New Password:</label>
                <input type="password" name="new_password" required>
              </div>
              <div class="form-group">
                <label class="required">Confirm New Password:</label>
                <input type="password" name="confirm_password" required>
              </div>
              <button type="submit" class="btn">Change Password</button>
              
              <?php if (isset($password_success)): ?>
                <div style="color: green; margin-top: 10px;"><?php echo $password_success; ?></div>
              <?php endif; ?>
              <?php if (isset($password_error)): ?>
                <div style="color: red; margin-top: 10px;"><?php echo $password_error; ?></div>
              <?php endif; ?>
            </form>
          </div>
        </div>
      </div>
    </main>
  </div>

  <?php 
  if ($_SESSION['role'] == 'boarder') {
      include 'bd-footer.php';
  } else {
      include 'ad-footer.php';
  }
  ?>
</body>
</html>