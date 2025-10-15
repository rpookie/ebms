<?php
session_start();
include 'db.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Handle boarder deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_boarder'])) {
    $boarder_id = $_POST['boarder_id'];
    
    // Free up the room first
    $user_info = $conn->query("SELECT room_number FROM users WHERE boarder_id = '$boarder_id'")->fetch_assoc();
    if ($user_info && $user_info['room_number']) {
        $conn->query("UPDATE rooms SET status = 'Available' WHERE room_number = '{$user_info['room_number']}'");
    }
    
    // Delete the boarder and related records
    $conn->query("DELETE FROM payments WHERE boarder_id = '$boarder_id'");
    $conn->query("DELETE FROM maintenance WHERE boarder_id = '$boarder_id'");
    $conn->query("DELETE FROM users WHERE boarder_id = '$boarder_id'");
    
    $success = "Boarder deleted successfully!";
}

// Get all boarders
$boarders = $conn->query("SELECT * FROM users WHERE role = 'boarder' AND status = 'approved' ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Boarders | eBMS Admin</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <?php include 'ad-header.php'; ?>

  <div class="main-container">
    <?php include 'ad-sidebar.php'; ?>

    <main class="main-content" id="mainContent">
      <div class="content-header">
        <h1>Manage Boarders</h1>
        <p>View and manage all registered boarders</p>
      </div>

      <?php if (isset($success)): ?>
        <div class="success-message">
          <?php echo $success; ?>
        </div>
      <?php endif; ?>

      <div class="table-container">
        <?php if ($boarders->num_rows > 0): ?>
          <table class="data-table">
            <thead>
              <tr>
                <th>Boarder ID</th>
                <th>Name</th>
                <th>Contact</th>
                <th>Email</th>
                <th>Room</th>
                <th>Age</th>
                <th>Guardian</th>
                <th>Date Joined</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($boarder = $boarders->fetch_assoc()): ?>
                <tr>
                  <td><?php echo $boarder['boarder_id']; ?></td>
                  <td><?php echo $boarder['fname'] . ' ' . $boarder['lname']; ?></td>
                  <td><?php echo $boarder['contact']; ?></td>
                  <td><?php echo $boarder['email']; ?></td>
                  <td><?php echo $boarder['room_number'] ?: 'Not assigned'; ?></td>
                  <td><?php echo $boarder['age']; ?></td>
                  <td><?php echo $boarder['guardian_fullname'] . ' (' . $boarder['guardian_relationship'] . ')'; ?></td>
                  <td><?php echo date('M j, Y', strtotime($boarder['created_at'])); ?></td>
                  <td>
                    <form method="POST" style="display: inline;">
                      <input type="hidden" name="boarder_id" value="<?php echo $boarder['boarder_id']; ?>">
                      <button type="submit" name="delete_boarder" class="delete-btn" onclick="return confirm('Are you sure you want to delete this boarder? This action cannot be undone.')">Delete</button>
                    </form>
                  </td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        <?php else: ?>
          <div class="empty-state">
            <i class="fa-solid fa-users"></i>
            <p>No boarders registered yet.</p>
          </div>
        <?php endif; ?>
      </div>
    </main>
  </div>

  <?php include 'ad-footer.php'; ?>
</body>
</html>