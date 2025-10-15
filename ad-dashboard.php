<?php
session_start();
include 'db.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Get dashboard statistics
$total_boarders = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'boarder' AND status = 'approved'")->fetch_assoc()['count'];
$pending_reservations = $conn->query("SELECT COUNT(*) as count FROM users WHERE status = 'pending'")->fetch_assoc()['count'];
$pending_payments = $conn->query("SELECT COUNT(*) as count FROM payments WHERE status = 'pending'")->fetch_assoc()['count'];
$active_maintenance = $conn->query("SELECT COUNT(*) as count FROM maintenance WHERE status IN ('not started', 'ongoing')")->fetch_assoc()['count'];
$total_rooms = $conn->query("SELECT COUNT(*) as count FROM rooms")->fetch_assoc()['count'];
$occupied_rooms = $conn->query("SELECT COUNT(*) as count FROM rooms WHERE status = 'Occupied'")->fetch_assoc()['count'];

// Get recent activities
$recent_payments = $conn->query("SELECT p.*, u.fname, u.lname FROM payments p JOIN users u ON p.boarder_id = u.boarder_id ORDER BY p.payment_date DESC LIMIT 5");
$recent_reservations = $conn->query("SELECT * FROM users WHERE status = 'pending' ORDER BY created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - eBMS</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <?php include 'ad-header.php'; ?>

  <div class="main-container">
    <?php include 'ad-sidebar.php'; ?>

    <main class="main-content" id="mainContent">
      <div class="content-header">
        <h1>Admin Dashboard</h1>
        <p>Welcome back, Administrator! Here's an overview of your boarding house.</p>
      </div>

      <!-- Statistics Cards -->
      <div class="info-boxes">
        <div class="info-box">
          <strong><i class="fa-solid fa-users"></i> Total Boarders:</strong>
          <span><?php echo $total_boarders; ?></span>
        </div>
        <div class="info-box">
          <strong><i class="fa-solid fa-bed"></i> Rooms:</strong>
          <span><?php echo $occupied_rooms; ?>/<?php echo $total_rooms; ?> Occupied</span>
        </div>
        <div class="info-box">
          <strong><i class="fa-solid fa-clock"></i> Pending Reservations:</strong>
          <span><?php echo $pending_reservations; ?></span>
        </div>
        <div class="info-box">
          <strong><i class="fa-solid fa-money-bill-wave"></i> Pending Payments:</strong>
          <span><?php echo $pending_payments; ?></span>
        </div>
        <div class="info-box">
          <strong><i class="fa-solid fa-tools"></i> Active Maintenance:</strong>
          <span><?php echo $active_maintenance; ?></span>
        </div>
      </div>

      <!-- Quick Actions -->
      <div style="display: flex; gap: 20px; margin: 30px 0; flex-wrap: wrap;">
        <a href="ad-reservations.php" class="btn" style="flex: 1; min-width: 200px;">
          <i class="fa-solid fa-user-check"></i> Manage Reservations
        </a>
        <a href="ad-payments.php" class="btn" style="flex: 1; min-width: 200px;">
          <i class="fa-solid fa-credit-card"></i> Approve Payments
        </a>
        <a href="ad-boarders.php" class="btn" style="flex: 1; min-width: 200px;">
          <i class="fa-solid fa-list"></i> View Boarders
        </a>
        <a href="announcements.php" class="btn" style="flex: 1; min-width: 200px;">
          <i class="fa-solid fa-bullhorn"></i> Post Announcement
        </a>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-top: 30px;">
        <!-- Recent Reservations -->
        <div style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
          <h3 style="color: #4263eb; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
            <span>Pending Reservations</span>
            <a href="ad-reservations.php" style="font-size: 0.9em; color: #666; text-decoration: none;">View All →</a>
          </h3>
          
          <?php if ($recent_reservations->num_rows > 0): ?>
            <div style="max-height: 300px; overflow-y: auto;">
              <?php while ($reservation = $recent_reservations->fetch_assoc()): ?>
                <div style="border-left: 3px solid #4263eb; padding: 15px; margin-bottom: 15px; background: #f8f9fa;">
                  <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px;">
                    <strong><?php echo $reservation['fname'] . ' ' . $reservation['lname']; ?></strong>
                    <small style="color: #666;"><?php echo date('M j', strtotime($reservation['created_at'])); ?></small>
                  </div>
                  <div style="color: #666; font-size: 0.9em;">
                    Room: <?php echo $reservation['room_number']; ?> | 
                    Contact: <?php echo $reservation['contact']; ?>
                  </div>
                  <div style="margin-top: 8px;">
                    <a href="ad-reservations.php?action=view&id=<?php echo $reservation['id']; ?>" class="view-btn" style="padding: 5px 10px; font-size: 0.8em;">Review</a>
                  </div>
                </div>
              <?php endwhile; ?>
            </div>
          <?php else: ?>
            <div style="text-align: center; padding: 20px; color: #666;">
              <i class="fa-solid fa-check" style="font-size: 24px; color: #ccc; margin-bottom: 10px;"></i>
              <p>No pending reservations</p>
            </div>
          <?php endif; ?>
        </div>

        <!-- Recent Payments -->
        <div style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
          <h3 style="color: #4263eb; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
            <span>Recent Payments</span>
            <a href="ad-payments.php" style="font-size: 0.9em; color: #666; text-decoration: none;">View All →</a>
          </h3>
          
          <?php if ($recent_payments->num_rows > 0): ?>
            <div style="max-height: 300px; overflow-y: auto;">
              <?php while ($payment = $recent_payments->fetch_assoc()): ?>
                <div style="border-left: 3px solid #28a745; padding: 15px; margin-bottom: 15px; background: #f8f9fa;">
                  <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px;">
                    <strong><?php echo $payment['fname'] . ' ' . $payment['lname']; ?></strong>
                    <span class="status-<?php echo $payment['status']; ?>" style="padding: 2px 8px; border-radius: 10px; font-size: 0.8em;">
                      <?php echo ucfirst($payment['status']); ?>
                    </span>
                  </div>
                  <div style="color: #666; font-size: 0.9em;">
                    ₱<?php echo number_format($payment['amount'], 2); ?> | <?php echo $payment['month_covered']; ?>
                  </div>
                  <div style="color: #666; font-size: 0.9em;">
                    <?php echo $payment['mode_of_payment']; ?>: <?php echo $payment['reference_number']; ?>
                  </div>
                  <?php if ($payment['status'] == 'pending'): ?>
                    <div style="margin-top: 8px;">
                      <a href="ad-payments.php?action=view&id=<?php echo $payment['id']; ?>" class="view-btn" style="padding: 5px 10px; font-size: 0.8em;">Review</a>
                    </div>
                  <?php endif; ?>
                </div>
              <?php endwhile; ?>
            </div>
          <?php else: ?>
            <div style="text-align: center; padding: 20px; color: #666;">
              <i class="fa-solid fa-receipt" style="font-size: 24px; color: #ccc; margin-bottom: 10px;"></i>
              <p>No recent payments</p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </main>
  </div>

  <?php include 'ad-footer.php'; ?>
</body>
</html>