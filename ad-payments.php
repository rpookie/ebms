<?php
session_start();
include 'db.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Handle payment approval/rejection
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $payment_id = intval($_POST['payment_id']);
    $action = $_POST['action'];
    $admin_notes = $conn->real_escape_string($_POST['admin_notes'] ?? '');
    
    $stmt = $conn->prepare("UPDATE payments SET status = ?, admin_notes = ? WHERE id = ?");
    $stmt->bind_param("ssi", $action, $admin_notes, $payment_id);
    
    if ($stmt->execute()) {
        $success = "Payment {$action} successfully!";
    }
}

// Get pending payments
$payments = $conn->query("SELECT p.*, u.fname, u.lname, u.contact, u.email FROM payments p JOIN users u ON p.boarder_id = u.boarder_id ORDER BY p.payment_date DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Payments | eBMS Admin</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <?php include 'ad-header.php'; ?>

  <div class="main-container">
    <?php include 'ad-sidebar.php'; ?>

    <main class="main-content" id="mainContent">
      <div class="content-header">
        <h1>Manage Payments</h1>
        <p>Review and verify payment submissions</p>
      </div>

      <?php if (isset($success)): ?>
        <div style="background: #e8f5e8; color: #2e7d32; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
          <?php echo $success; ?>
        </div>
      <?php endif; ?>

      <div class="table-container">
        <?php if ($payments->num_rows > 0): ?>
          <table class="data-table">
            <thead>
              <tr>
                <th>Boarder</th>
                <th>Amount</th>
                <th>Month Covered</th>
                <th>Payment Method</th>
                <th>Reference</th>
                <th>Date</th>
                <th>Receipt</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($payment = $payments->fetch_assoc()): ?>
                <tr>
                  <td>
                    <strong><?php echo $payment['fname'] . ' ' . $payment['lname']; ?></strong><br>
                    <small><?php echo $payment['contact']; ?></small>
                  </td>
                  <td>₱<?php echo number_format($payment['amount'], 2); ?></td>
                  <td><?php echo $payment['month_covered']; ?></td>
                  <td><?php echo $payment['mode_of_payment']; ?></td>
                  <td><?php echo $payment['reference_number']; ?></td>
                  <td><?php echo date('M j, Y', strtotime($payment['payment_date'])); ?></td>
                  <td>
                    <?php if ($payment['receipt_image']): ?>
                      <a href="uploads/receipts/<?php echo $payment['receipt_image']; ?>" target="_blank" class="view-btn" style="padding: 5px 10px; font-size: 0.8em;">View</a>
                    <?php else: ?>
                      N/A
                    <?php endif; ?>
                  </td>
                  <td>
                    <span class="status-<?php echo $payment['status']; ?>">
                      <?php echo ucfirst($payment['status']); ?>
                    </span>
                  </td>
                  <td>
                    <?php if ($payment['status'] == 'pending'): ?>
                      <form method="POST" style="display: flex; gap: 5px; flex-direction: column;">
                        <input type="hidden" name="payment_id" value="<?php echo $payment['id']; ?>">
                        <textarea name="admin_notes" placeholder="Notes (optional)" rows="2" style="padding: 5px; font-size: 0.8em; border: 1px solid #ddd; border-radius: 3px;"></textarea>
                        <div style="display: flex; gap: 5px;">
                          <button type="submit" name="action" value="approved" class="view-btn" style="background: #28a745; padding: 5px 8px; font-size: 0.8em;">Approve</button>
                          <button type="submit" name="action" value="rejected" class="view-btn" style="background: #dc3545; padding: 5px 8px; font-size: 0.8em;">Reject</button>
                        </div>
                      </form>
                    <?php else: ?>
                      <em>Processed</em>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        <?php else: ?>
          <div style="text-align: center; padding: 40px; color: #666;">
            <i class="fa-solid fa-receipt" style="font-size: 48px; color: #ccc; margin-bottom: 15px;"></i>
            <p>No payment records found.</p>
          </div>
        <?php endif; ?>
      </div>
    </main>
  </div>

  <?php include 'ad-footer.php'; ?>
</body>
</html>