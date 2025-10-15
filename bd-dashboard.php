<?php
session_start();
include 'db.php';

// Check if user is logged in and is a boarder
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'boarder') {
    header("Location: login.php");
    exit();
}

// Get boarder information
$user_id = $_SESSION['user_id'];
$user_query = $conn->prepare("SELECT * FROM users WHERE id = ?");
$user_query->bind_param("i", $user_id);
$user_query->execute();
$user_result = $user_query->get_result();
$user = $user_result->fetch_assoc();

// Get payment due date (example: 7th of next month)
$next_month = date('Y-m-07', strtotime('+1 month'));
$due_date = date('M j, Y', strtotime($next_month));

// Get stay duration
$stay_since = "Jan 25, 2025"; // This would come from database in real implementation
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Boarder Dashboard - eBMS</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <?php include 'bd-header.php'; ?>

  <div class="main-container">
    <?php include 'bd-sidebar.php'; ?>

    <main class="main-content" id="mainContent">
      <h2>ROOM <?php echo $user['room_number']; ?></h2>
      <p>Welcome back, <?php echo $user['fname'] . ' ' . $user['lname']; ?>!</p>

      <div class="info-boxes">
        <div class="info-box">
          <strong><i class="fa-regular fa-calendar-xmark"></i> Payment Due Date:</strong>
          <span><?php echo $due_date; ?></span>
        </div>

        <div class="info-box">
          <strong><i class="fa-regular fa-calendar"></i> Stay Duration:</strong>
          <span>Since <?php echo $stay_since; ?></span>
        </div>

        <div class="info-box">
          <strong><i class="fa-solid fa-bed"></i> Room Status:</strong>
          <span><?php echo $user['room_number'] ? 'Occupied' : 'No Room Assigned'; ?></span>
        </div>
      </div>

      <div class="buttons">
        <a href="payment.php" class="btn">
          <i class="fa-solid fa-money-bill-wave"></i> Pay Now
        </a>
        <a href="payment-history.php" class="btn">
          <i class="fa-solid fa-clock-rotate-left"></i> View History
        </a>
        <a href="maintenance.php" class="btn">
          <i class="fa-solid fa-tools"></i> Maintenance
        </a>
      </div>

      <!-- Quick Announcements -->
      <div style="margin-top: 40px; background: white; padding: 20px; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); max-width: 600px;">
        <h3 style="color: #4263eb; margin-bottom: 15px;">Latest Announcements</h3>
        <?php
        $announcements = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC LIMIT 3");
        if ($announcements->num_rows > 0) {
            while ($announcement = $announcements->fetch_assoc()) {
                echo "<div style='border-left: 3px solid #4263eb; padding-left: 15px; margin-bottom: 15px;'>";
                echo "<h4 style='margin: 0 0 5px 0;'>{$announcement['title']}</h4>";
                echo "<p style='margin: 0; color: #666; font-size: 0.9em;'>{$announcement['content']}</p>";
                echo "<small style='color: #999;'>" . date('M j, Y', strtotime($announcement['created_at'])) . "</small>";
                echo "</div>";
            }
        } else {
            echo "<p>No announcements at the moment.</p>";
        }
        ?>
        <a href="announcements.php" style="display: inline-block; margin-top: 10px; color: #4263eb; text-decoration: none;">View All Announcements →</a>
      </div>
    </main>
  </div>

  <?php include 'bd-footer.php'; ?>
</body>
</html>