<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle new announcement creation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_announcement'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);
    $type = $conn->real_escape_string($_POST['type']);
    $created_by = $_SESSION['user_id'];
    
    $stmt = $conn->prepare("INSERT INTO announcements (title, content, type, created_by) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $title, $content, $type, $created_by);
    
    if ($stmt->execute()) {
        $success = "Announcement posted successfully!";
    } else {
        $error = "Error posting announcement: " . $conn->error;
    }
}

// Get all announcements
$announcements_query = $conn->query("SELECT a.*, u.fname, u.lname FROM announcements a LEFT JOIN users u ON a.created_by = u.boarder_id ORDER BY a.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Announcements | eBMS</title>
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
        <h1>Announcements</h1>
        <p>Stay updated with the latest news and information</p>
      </div>

      <?php if (isset($success)): ?>
        <div class="success-message">
          <?php echo $success; ?>
        </div>
      <?php endif; ?>

      <?php if (isset($error)): ?>
        <div class="error-message">
          <?php echo $error; ?>
        </div>
      <?php endif; ?>

      <!-- Payment Information Card -->
      <div class="payment-info-card">
        <h3><i class="fa-solid fa-qrcode"></i> Payment Information</h3>
        <div class="payment-grid">
          <div class="payment-method">
            <h4>GCash</h4>
            <p class="payment-details">0917-XXX-XXXX</p>
            <p class="payment-name">John Doe</p>
          </div>
          <div class="payment-method">
            <h4>Bank Transfer</h4>
            <p class="payment-details">BPI: XXXX-XXXX-XXXX</p>
            <p class="payment-name">Jane Smith</p>
          </div>
          <div class="qr-code">
            <h4>QR Code</h4>
            <div class="qr-placeholder">
              <i class="fa-solid fa-qrcode"></i>
              <span>QR Code</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Announcements List -->
      <div class="announcements-list">
        <?php if ($announcements_query->num_rows > 0): ?>
          <?php while ($announcement = $announcements_query->fetch_assoc()): ?>
            <div class="announcement-card">
              <div class="announcement-header">
                <h3><?php echo $announcement['title']; ?></h3>
                <span class="announcement-date">
                  <?php echo date('M j, Y g:i A', strtotime($announcement['created_at'])); ?>
                </span>
              </div>
              
              <div class="announcement-content">
                <?php echo nl2br(htmlspecialchars($announcement['content'])); ?>
              </div>
              
              <div class="announcement-footer">
                <span class="announcement-author">
                  <i class="fa-solid fa-user"></i>
                  Posted by: <?php echo $announcement['fname'] ? $announcement['fname'] . ' ' . $announcement['lname'] : 'Administrator'; ?>
                </span>
                <span class="announcement-type">
                  <?php echo ucfirst($announcement['type']); ?>
                </span>
              </div>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <div class="empty-state">
            <i class="fa-solid fa-bullhorn"></i>
            <p>No announcements at the moment.</p>
          </div>
        <?php endif; ?>
      </div>

      <!-- Admin Only: Add Announcement Form -->
      <?php if ($_SESSION['role'] == 'admin'): ?>
        <div class="form-section">
          <h3>Add New Announcement</h3>
          <form method="POST">
            <div class="form-grid">
              <div class="form-group">
                <label class="required">Title:</label>
                <input type="text" name="title" required>
              </div>
              <div class="form-group">
                <label class="required">Type:</label>
                <select name="type" required>
                  <option value="general">General</option>
                  <option value="payment">Payment</option>
                  <option value="maintenance">Maintenance</option>
                  <option value="urgent">Urgent</option>
                </select>
              </div>
              <div class="form-group full-width">
                <label class="required">Content:</label>
                <textarea name="content" rows="4" required></textarea>
              </div>
              <div class="form-group full-width">
                <button type="submit" name="add_announcement" class="btn">Post Announcement</button>
              </div>
            </div>
          </form>
        </div>
      <?php endif; ?>
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