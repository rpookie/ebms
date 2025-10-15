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

// Handle payment submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $amount = floatval($_POST['amount']);
    $month_covered = $conn->real_escape_string($_POST['month_covered']);
    $mode_of_payment = $conn->real_escape_string($_POST['mode_of_payment']);
    $reference_number = $conn->real_escape_string($_POST['reference_number']);
    
    // Handle receipt upload
    $receipt_image = null;
    if (isset($_FILES['receipt_image']) && $_FILES['receipt_image']['error'] == 0) {
        $target_dir = "uploads/receipts/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES["receipt_image"]["name"], PATHINFO_EXTENSION));
        $new_filename = "receipt_" . $user['boarder_id'] . "_" . time() . "." . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES["receipt_image"]["tmp_name"], $target_file)) {
            $receipt_image = $new_filename;
        }
    }
    
    // Insert payment into database
    $stmt = $conn->prepare("INSERT INTO payments (boarder_id, amount, month_covered, mode_of_payment, reference_number, receipt_image, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
    $stmt->bind_param("sdssss", $user['boarder_id'], $amount, $month_covered, $mode_of_payment, $reference_number, $receipt_image);
    
    if ($stmt->execute()) {
        $success = "Payment submitted successfully! Waiting for admin approval.";
    } else {
        $error = "Error submitting payment. Please try again.";
    }
}

// Get room rent amount
$room_query = $conn->prepare("SELECT monthly_rent FROM rooms WHERE room_number = ?");
$room_query->bind_param("s", $user['room_number']);
$room_query->execute();
$room_result = $room_query->get_result();
$room = $room_result->fetch_assoc();
$monthly_rent = $room ? $room['monthly_rent'] : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Make Payment | eBMS</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <?php include 'bd-header.php'; ?>

  <div class="main-container">
    <?php include 'bd-sidebar.php'; ?>

    <main class="main-content" id="mainContent">
      <div class="content-header">
        <h1>Make Payment</h1>
        <p>Submit your room rental payment</p>
      </div>

      <div class="form-container">
        <?php if (isset($success)): ?>
          <div class="success-message" style="background: #e8f5e8; color: #2e7d32; padding: 15px; border-radius: 5px; margin-bottom: 20px; text-align: center;">
            <?php echo $success; ?>
          </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
          <div class="error-message" style="background: #ffebee; color: #c62828; padding: 15px; border-radius: 5px; margin-bottom: 20px; text-align: center;">
            <?php echo $error; ?>
          </div>
        <?php endif; ?>

        <form method="POST" action="payment.php" enctype="multipart/form-data" onsubmit="return validateForm('paymentForm')">
          <div class="form-grid">
            <div class="form-group">
              <label class="required">Amount:</label>
              <input type="number" name="amount" step="0.01" min="1" value="<?php echo $monthly_rent; ?>" required>
              <div class="error" id="amountError"></div>
            </div>

            <div class="form-group">
              <label class="required">Month Covered:</label>
              <select name="month_covered" required>
                <option value="">-- Select Month --</option>
                <?php
                $months = [
                    'January', 'February', 'March', 'April', 'May', 'June',
                    'July', 'August', 'September', 'October', 'November', 'December'
                ];
                $current_year = date('Y');
                foreach ($months as $month) {
                    echo "<option value='$month $current_year'>$month $current_year</option>";
                }
                ?>
              </select>
              <div class="error" id="month_coveredError"></div>
            </div>

            <div class="form-group">
              <label class="required">Mode of Payment:</label>
              <select name="mode_of_payment" required>
                <option value="">-- Select Payment Method --</option>
                <option value="GCash">GCash</option>
                <option value="Bank Transfer">Bank Transfer</option>
                <option value="Cash">Cash</option>
                <option value="PayPal">PayPal</option>
              </select>
              <div class="error" id="mode_of_paymentError"></div>
            </div>

            <div class="form-group">
              <label class="required">Reference Number:</label>
              <input type="text" name="reference_number" required placeholder="Transaction ID/Reference">
              <div class="error" id="reference_numberError"></div>
            </div>

            <div class="form-group full-width">
              <label class="required">Upload Receipt:</label>
              <input type="file" name="receipt_image" accept="image/*" required>
              <small style="color: #666;">Upload a clear image of your payment receipt (JPG, PNG, GIF)</small>
              <div class="error" id="receipt_imageError"></div>
            </div>

            <div class="form-group full-width" style="text-align: center; margin-top: 20px;">
              <button type="submit" class="btn">Submit Payment</button>
            </div>
          </div>
        </form>

        <!-- Payment Information -->
        <div style="background: #e8f4ff; padding: 20px; border-radius: 10px; margin-top: 30px;">
          <h3 style="color: #4263eb; margin-bottom: 15px;">Payment Instructions</h3>
          <p><strong>GCash:</strong> Send payment to 0917-XXX-XXXX</p>
          <p><strong>Bank Transfer:</strong> BPI Account: XXXX-XXXX-XXXX</p>
          <p><strong>Note:</strong> Always include your Boarder ID (<?php echo $user['boarder_id']; ?>) in the transaction notes.</p>
        </div>
      </div>
    </main>
  </div>

  <?php include 'bd-footer.php'; ?>
  <script src="script.js"></script>
</body>
</html>