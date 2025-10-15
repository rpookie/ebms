<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $boarder_id = $conn->real_escape_string($_POST['boarder_id']);
    $password = $_POST['password'];
    
    // Check if user exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE boarder_id = ? AND status = 'approved'");
    $stmt->bind_param("s", $boarder_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $user['password']) || $password == $user['boarder_id']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['boarder_id'] = $user['boarder_id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['fname'] = $user['fname'];
            $_SESSION['lname'] = $user['lname'];
            
            // Redirect based on role
            if ($user['role'] == 'admin') {
                header("Location: ad-dashboard.php");
            } else {
                header("Location: bd-dashboard.php");
            }
            exit();
        } else {
            $error = "Invalid password. Please try again.";
        }
    } else {
        $error = "Invalid Boarder ID or account not approved yet.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | eBMS</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="main-content" id="mainContent" style="margin-left: 0; display: flex; justify-content: center; align-items: center;">
    <div class="form-container">
      <h2>Login to eBMS</h2>
      
      <?php if (isset($error)): ?>
        <div class="error-message" style="background: #ffebee; color: #c62828; padding: 10px; border-radius: 5px; margin-bottom: 20px; text-align: center;">
          <?php echo $error; ?>
        </div>
      <?php endif; ?>
      
      <form method="POST" action="login.php">
        <div class="form-group">
          <label class="required">Boarder ID:</label>
          <input type="text" name="boarder_id" required placeholder="Enter your Boarder ID">
        </div>
        
        <div class="form-group">
          <label class="required">Password:</label>
          <input type="password" name="password" required placeholder="Enter your password">
        </div>
        
        <div class="form-group" style="text-align: center; margin-top: 20px;">
          <button type="submit" class="btn">Login</button>
        </div>
        
        <div style="text-align: center; margin-top: 15px;color: #333; ">
          <p>Don't have an account? <a href="reserve-room.php">Reserve a room first</a></p>
          <p><small>Use your Boarder ID as temporary password for first login</small></p>
        </div>
      </form>
    </div>
  </div>

  <script src="script.js"></script>
</body>
</html>