<?php
session_start();
include 'db.php';

// Load PHPMailer at the TOP of the file
require 'PHPMailer.php';
require 'SMTP.php';
require 'Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Function to send approval email
function sendApprovalEmail($user_info) {
    $mail = new PHPMailer(true);
    
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'jjrlsimp@gmail.com'; // Your email
        $mail->Password = 'voqwlldmgmwwpnec'; // Your app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        $mail->setFrom('jjrlsimp@gmail.com', 'eBMS System');
        $mail->addAddress($user_info['email']);
        
        $mail->isHTML(true);
        $mail->Subject = 'Reservation Approved - Welcome to eBMS!';
        $mail->Body = "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #4CAF50; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 20px; }
                .footer { background: #ddd; padding: 15px; text-align: center; font-size: 12px; border-radius: 0 0 10px 10px; }
                .info-box { background: white; padding: 15px; margin: 15px 0; border-left: 4px solid #4CAF50; border-radius: 5px; }
                .credentials { background: #e7f3ff; padding: 15px; border-radius: 5px; margin: 15px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>🎉 Reservation Approved! 🎉</h2>
                </div>
                <div class='content'>
                    <p>Dear <strong>{$user_info['fname']} {$user_info['lname']}</strong>,</p>
                    
                    <p>We are pleased to inform you that your room reservation has been <strong>APPROVED</strong>!</p>
                    
                    <div class='info-box'>
                        <h3>Approval Details:</h3>
                        <p><strong>Room Number:</strong> {$user_info['room_number']}</p>
                        <p><strong>Boarder ID:</strong> {$user_info['boarder_id']}</p>
                        <p><strong>Move-in Date:</strong> " . date('F j, Y', strtotime('+3 days')) . "</p>
                        <p><strong>Status:</strong> Approved ✅</p>
                    </div>
                    
                    <div class='credentials'>
                        <h3>Your Login Credentials:</h3>
                        <p><strong>Boarder ID / Username:</strong> {$user_info['boarder_id']}</p>
                        <p><strong>Temporary Password:</strong> welcome123</p>
                        <p><strong>Login URL:</strong> http://localhost/ebms/login.php</p>
                    </div>
                    
                    <h4>Next Steps:</h4>
                    <ol>
                        <li><strong>Login to your account</strong> using the credentials above</li>
                        <li><strong>Change your password</strong> after first login for security</li>
                        <li><strong>Complete your payment</strong> within 3 days</li>
                        <li><strong>Bring valid ID</strong> during move-in</li>
                        <li><strong>Move-in date:</strong> " . date('F j, Y', strtotime('+3 days')) . "</li>
                    </ol>
                    
                    <p style='color: #d32f2f;'><strong>Important Security Notice:</strong> Please change your password immediately after first login.</p>
                    
                    <p>Welcome to eBMS! We look forward to having you with us.</p>
                </div>
                <div class='footer'>
                    <p>eBMS - Electronic Boarding Management System<br>
                    Gonzaga, Cagayan</p>
                </div>
            </div>
        </body>
        </html>";
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Approval email failed: " . $mail->ErrorInfo);
        return false;
    }
}

// Function to send rejection email
function sendRejectionEmail($user_info) {
    $mail = new PHPMailer(true);
    
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'jjrlsimp@gmail.com'; // Your email
        $mail->Password = 'voqwlldmgmwwpnec'; // Your app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        $mail->setFrom('jjrlsimp@gmail.com', 'eBMS System');
        $mail->addAddress($user_info['email']);
        
        $mail->isHTML(true);
        $mail->Subject = 'Reservation Status Update - eBMS';
        $mail->Body = "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #f44336; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 20px; }
                .footer { background: #ddd; padding: 15px; text-align: center; font-size: 12px; border-radius: 0 0 10px 10px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Reservation Status Update</h2>
                </div>
                <div class='content'>
                    <p>Dear <strong>{$user_info['fname']} {$user_info['lname']}</strong>,</p>
                    
                    <p>After careful review, we regret to inform you that your room reservation for <strong>Room {$user_info['room_number']}</strong> could not be approved at this time.</p>
                    
                    <p>Possible reasons may include:</p>
                    <ul>
                        <li>Room availability constraints</li>
                        <li>Documentation requirements</li>
                        <li>Administrative considerations</li>
                    </ul>
                    
                    <p>We appreciate your interest in eBMS and encourage you to apply again in the future when circumstances may change.</p>
                    
                    <p>If you have any questions about this decision, please feel free to contact us.</p>
                    
                    <p>Thank you for considering eBMS for your accommodation needs.</p>
                </div>
                <div class='footer'>
                    <p>eBMS - Electronic Boarding Management System<br>
                    Gonzaga, Cagayan</p>
                </div>
            </div>
        </body>
        </html>";
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Rejection email failed: " . $mail->ErrorInfo);
        return false;
    }
}

// Handle reservation approval/rejection
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $user_id = intval($_POST['user_id']);
    $action = $_POST['action'];
    
    if ($action == 'approve') {
        // Generate permanent password
        $permanent_password = password_hash('welcome123', PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("UPDATE users SET status = 'approved', password = ? WHERE id = ?");
        $stmt->bind_param("si", $permanent_password, $user_id);
        
        if ($stmt->execute()) {
            // Get user info for notification
            $user_info = $conn->query("SELECT * FROM users WHERE id = $user_id")->fetch_assoc();
            
            // Send approval email
            $email_sent = sendApprovalEmail($user_info);
            
            $success = "Reservation approved successfully! " . ($email_sent ? "Notification email sent to user." : "Email notification failed.");
        }
    } elseif ($action == 'reject') {
        $stmt = $conn->prepare("UPDATE users SET status = 'rejected' WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        
        // Free up the room
        $user_info = $conn->query("SELECT * FROM users WHERE id = $user_id")->fetch_assoc();
        $conn->query("UPDATE rooms SET status = 'Available' WHERE room_number = '{$user_info['room_number']}'");
        
        // Send rejection email
        $email_sent = sendRejectionEmail($user_info);
        
        $success = "Reservation rejected successfully! " . ($email_sent ? "Notification email sent to user." : "Email notification failed.");
    }
}

// Get pending reservations
$reservations = $conn->query("SELECT * FROM users WHERE status = 'pending' ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Reservations | eBMS Admin</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <?php include 'ad-header.php'; ?>

  <div class="main-container">
    <?php include 'ad-sidebar.php'; ?>

    <main class="main-content" id="mainContent">
      <div class="content-header">
        <h1>Manage Reservations</h1>
        <p>Review and approve/reject room reservation requests</p>
      </div>

      <?php if (isset($success)): ?>
        <div style="background: #e8f5e8; color: #2e7d32; padding: 15px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #4CAF50;">
          <i class="fas fa-check-circle"></i> <?php echo $success; ?>
        </div>
      <?php endif; ?>

      <div class="table-container">
        <?php if ($reservations->num_rows > 0): ?>
          <table class="data-table">
            <thead>
              <tr>
                <th>Name</th>
                <th>Contact</th>
                <th>Email</th>
                <th>Room</th>
                <th>Boarder ID</th>
                <th>Age</th>
                <th>Guardian</th>
                <th>Date Applied</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($reservation = $reservations->fetch_assoc()): ?>
                <tr>
                  <td><strong><?php echo $reservation['fname'] . ' ' . $reservation['lname']; ?></strong></td>
                  <td><?php echo $reservation['contact']; ?></td>
                  <td><?php echo $reservation['email']; ?></td>
                  <td><span style="background: #4c56a1; color: white; padding: 4px 8px; border-radius: 4px; font-weight: bold;"><?php echo $reservation['room_number']; ?></span></td>
                  <td><code style="background: #f1f1f1; padding: 4px 8px; border-radius: 4px;"><?php echo $reservation['boarder_id']; ?></code></td>
                  <td><?php echo $reservation['age']; ?></td>
                  <td><?php echo $reservation['guardian_fullname'] . ' (' . $reservation['guardian_relationship'] . ')'; ?></td>
                  <td><?php echo date('M j, Y g:i A', strtotime($reservation['created_at'])); ?></td>
                  <td>
                    <form method="POST" style="display: inline;" onsubmit="return confirmAction('<?php echo $action; ?>', '<?php echo $reservation['fname']; ?>')">
                      <input type="hidden" name="user_id" value="<?php echo $reservation['id']; ?>">
                      <button type="submit" name="action" value="approve" class="btn-approve">
                        <i class="fas fa-check"></i> Approve
                      </button>
                      <button type="submit" name="action" value="reject" class="btn-reject">
                        <i class="fas fa-times"></i> Reject
                      </button>
                    </form>
                  </td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        <?php else: ?>
          <div style="text-align: center; padding: 40px; color: #666;">
            <i class="fa-solid fa-check-circle" style="font-size: 48px; color: #ccc; margin-bottom: 15px;"></i>
            <h3>No Pending Reservations</h3>
            <p>All reservation requests have been processed.</p>
          </div>
        <?php endif; ?>
      </div>
    </main>
  </div>

  <?php include 'ad-footer.php'; ?>

  <script>
  function confirmAction(action, name) {
      if (action === 'approve') {
          return confirm(`Are you sure you want to APPROVE ${name}'s reservation?\n\nThis will:\n• Create their boarder account\n• Send login credentials via email\n• Assign them to the room`);
      } else {
          return confirm(`Are you sure you want to REJECT ${name}'s reservation?\n\nThis will:\n• Reject their application\n• Free up the room for others\n• Send rejection notification`);
      }
  }
  </script>

  <style>
  .btn-approve {
      background: #28a745;
      color: white;
      padding: 8px 12px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 14px;
      margin-right: 5px;
      transition: background 0.3s;
  }

  .btn-approve:hover {
      background: #218838;
  }

  .btn-reject {
      background: #dc3545;
      color: white;
      padding: 8px 12px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 14px;
      transition: background 0.3s;
  }

  .btn-reject:hover {
      background: #c82333;
  }

  .data-table td {
      vertical-align: middle;
  }
  </style>
</body>
</html>