<?php
session_start();
include 'db.php';

// Check if user is logged in and is a boarder
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'boarder') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_query = $conn->prepare("SELECT * FROM users WHERE id = ?");
$user_query->bind_param("i", $user_id);
$user_query->execute();
$user_result = $user_query->get_result();
$user = $user_result->fetch_assoc();

// Get payment history
$payments_query = $conn->prepare("SELECT * FROM payments WHERE boarder_id = ? ORDER BY payment_date DESC");
$payments_query->bind_param("s", $user['boarder_id']);
$payments_query->execute();
$payments_result = $payments_query->get_result();

// Calculate totals
$total_paid = 0;
$total_rent = 0; // This would be calculated based on stay duration
while ($payment = $payments_result->fetch_assoc()) {
    if ($payment['status'] == 'approved') {
        $total_paid += $payment['amount'];
    }
}
// Reset pointer for later use
$payments_result->data_seek(0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payment History | eBMS</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <?php include 'bd-header.php'; ?>

  <div class="main-container">
    <?php include 'bd-sidebar.php'; ?>

    <main class="main-content" id="mainContent">
      <div class="content-header">
        <h1>Payment History</h1>
        <p>View your payment records and status</p>
      </div>

      <!-- Summary Cards -->
      <div class="info-boxes">
        <div class="info-box">
          <strong><i class="fa-solid fa-money-bill-wave"></i> Total Paid:</strong>
          <span>₱<?php echo number_format($total_paid, 2); ?></span>
        </div>
        <div class="info-box">
          <strong><i class="fa-solid fa-calendar-check"></i> Approved Payments:</strong>
          <span>
            <?php
            $approved_count = $conn->query("SELECT COUNT(*) as count FROM payments WHERE boarder_id = '{$user['boarder_id']}' AND status = 'approved'")->fetch_assoc()['count'];
            echo $approved_count;
            ?>
          </span>
        </div>
        <div class="info-box">
          <strong><i class="fa-solid fa-clock"></i> Pending Payments:</strong>
          <span>
            <?php
            $pending_count = $conn->query("SELECT COUNT(*) as count FROM payments WHERE boarder_id = '{$user['boarder_id']}' AND status = 'pending'")->fetch_assoc()['count'];
            echo $pending_count;
            ?>
          </span>
        </div>
      </div>

      <!-- Payment History Table -->
      <div class="table-container" style="margin-top: 30px;">
        <h3 style="color: #4263eb; margin-bottom: 20px;">Payment Records</h3>
        
        <?php if ($payments_result->num_rows > 0): ?>
          <table class="data-table">
            <thead>
              <tr>
                <th>Date</th>
                <th>Month Covered</th>
                <th>Amount</th>
                <th>Mode of Payment</th>
                <th>Reference</th>
                <th>Status</th>
                <th>Receipt</th>
                <th>Admin Notes</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($payment = $payments_result->fetch_assoc()): ?>
                <tr>
                  <td><?php echo date('M j, Y', strtotime($payment['payment_date'])); ?></td>
                  <td><?php echo $payment['month_covered']; ?></td>
                  <td>₱<?php echo number_format($payment['amount'], 2); ?></td>
                  <td><?php echo $payment['mode_of_payment']; ?></td>
                  <td><?php echo $payment['reference_number']; ?></td>
                  <td>
                    <span class="status-<?php echo $payment['status']; ?>">
                      <?php echo ucfirst($payment['status']); ?>
                    </span>
                  </td>
                  <td>
                    <?php if ($payment['receipt_image']): ?>
                      <a href="uploads/receipts/<?php echo $payment['receipt_image']; ?>" target="_blank" class="view-btn" style="padding: 5px 10px; font-size: 0.8em;">View</a>
                    <?php else: ?>
                      N/A
                    <?php endif; ?>
                  </td>
                  <td><?php echo $payment['admin_notes'] ?: '—'; ?></td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        <?php else: ?>
          <div style="text-align: center; padding: 40px; color: #666;">
            <i class="fa-solid fa-receipt" style="font-size: 48px; color: #ccc; margin-bottom: 15px;"></i>
            <p>No payment records found.</p>
            <a href="payment.php" class="btn" style="margin-top: 15px;">Make Your First Payment</a>
          </div>
        <?php endif; ?>
      </div>
    </main>
  </div>

  <?php include 'bd-footer.php'; ?>
</body>
</html>