<?php
session_start();
include 'db.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Handle room updates
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_room'])) {
    $room_number = $_POST['room_number'];
    $monthly_rent = floatval($_POST['monthly_rent']);
    $status = $_POST['status'];
    
    $stmt = $conn->prepare("UPDATE rooms SET monthly_rent = ?, status = ? WHERE room_number = ?");
    $stmt->bind_param("dss", $monthly_rent, $status, $room_number);
    
    if ($stmt->execute()) {
        $success = "Room updated successfully!";
    } else {
        $error = "Error updating room: " . $conn->error;
    }
}

// Handle new room creation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_room'])) {
    $room_number = $_POST['room_number'];
    $bed_type = $_POST['bed_type'];
    $bathroom_type = $_POST['bathroom_type'];
    $monthly_rent = floatval($_POST['monthly_rent']);
    $cooling_type = $_POST['cooling_type'];
    $wifi_access = $_POST['wifi_access'];
    $kitchen_access = $_POST['kitchen_access'];
    $laundry_access = $_POST['laundry_access'];
    $status = 'Available';
    
    $stmt = $conn->prepare("INSERT INTO rooms (room_number, bed_type, bathroom_type, monthly_rent, cooling_type, wifi_access, kitchen_access, laundry_access, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssdsssss", $room_number, $bed_type, $bathroom_type, $monthly_rent, $cooling_type, $wifi_access, $kitchen_access, $laundry_access, $status);
    
    if ($stmt->execute()) {
        $success = "Room added successfully!";
    } else {
        $error = "Error adding room: " . $conn->error;
    }
}

// Get all rooms
$rooms = $conn->query("SELECT * FROM rooms ORDER BY room_number");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Rooms | eBMS Admin</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <?php include 'ad-header.php'; ?>

  <div class="main-container">
    <?php include 'ad-sidebar.php'; ?>

    <main class="main-content" id="mainContent">
      <div class="content-header">
        <h1>Manage Rooms</h1>
        <p>Update room information and status</p>
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

      <div class="table-container">
        <?php if ($rooms->num_rows > 0): ?>
          <table class="data-table">
            <thead>
              <tr>
                <th>Room Number</th>
                <th>Bed Type</th>
                <th>Bathroom</th>
                <th>Monthly Rent</th>
                <th>Cooling</th>
                <th>Wi-Fi</th>
                <th>Kitchen</th>
                <th>Laundry</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($room = $rooms->fetch_assoc()): ?>
                <tr>
                  <td><?php echo $room['room_number']; ?></td>
                  <td><?php echo $room['bed_type']; ?></td>
                  <td><?php echo $room['bathroom_type']; ?></td>
                  <td>
                    <form method="POST" style="display: inline;">
                      <input type="hidden" name="room_number" value="<?php echo $room['room_number']; ?>">
                      <input type="number" name="monthly_rent" value="<?php echo $room['monthly_rent']; ?>" step="0.01" min="0" style="width: 100px; padding: 8px;">
                  </td>
                  <td><?php echo $room['cooling_type']; ?></td>
                  <td><?php echo $room['wifi_access']; ?></td>
                  <td><?php echo $room['kitchen_access']; ?></td>
                  <td><?php echo $room['laundry_access']; ?></td>
                  <td>
                    <select name="status" style="padding: 8px;">
                      <option value="Available" <?php echo $room['status'] == 'Available' ? 'selected' : ''; ?>>Available</option>
                      <option value="Occupied" <?php echo $room['status'] == 'Occupied' ? 'selected' : ''; ?>>Occupied</option>
                      <option value="Reserved" <?php echo $room['status'] == 'Reserved' ? 'selected' : ''; ?>>Reserved</option>
                      <option value="Maintenance" <?php echo $room['status'] == 'Maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                    </select>
                  </td>
                  <td>
                    <button type="submit" name="update_room" class="update-btn">Update</button>
                    </form>
                  </td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        <?php else: ?>
          <div class="empty-state">
            <i class="fa-solid fa-bed"></i>
            <p>No rooms found in the system.</p>
          </div>
        <?php endif; ?>
      </div>

      <!-- Add New Room Form -->
      <div class="form-section">
        <h3>Add New Room</h3>
        <form method="POST">
          <div class="form-grid">
            <div class="form-group">
              <label class="required">Room Number:</label>
              <input type="text" name="room_number" required>
            </div>
            <div class="form-group">
              <label class="required">Bed Type:</label>
              <select name="bed_type" required>
                <option value="Single Bed">Single Bed</option>
                <option value="Bunk Bed">Bunk Bed</option>
                <option value="Double Bed">Double Bed</option>
              </select>
            </div>
            <div class="form-group">
              <label class="required">Bathroom:</label>
              <select name="bathroom_type" required>
                <option value="Private">Private</option>
                <option value="Shared">Shared</option>
              </select>
            </div>
            <div class="form-group">
              <label class="required">Monthly Rent:</label>
              <input type="number" name="monthly_rent" step="0.01" min="0" required>
            </div>
            <div class="form-group">
              <label class="required">Cooling Type:</label>
              <select name="cooling_type" required>
                <option value="A/C">A/C</option>
                <option value="Fan">Fan</option>
              </select>
            </div>
            <div class="form-group">
              <label class="required">Wi-Fi Access:</label>
              <select name="wifi_access" required>
                <option value="Available">Available</option>
                <option value="None">None</option>
              </select>
            </div>
            <div class="form-group">
              <label class="required">Kitchen Access:</label>
              <select name="kitchen_access" required>
                <option value="Private">Private</option>
                <option value="Shared">Shared</option>
              </select>
            </div>
            <div class="form-group">
              <label class="required">Laundry Access:</label>
              <select name="laundry_access" required>
                <option value="Private">Private</option>
                <option value="Shared">Shared</option>
              </select>
            </div>
            <div class="form-group full-width">
              <button type="submit" name="add_room" class="btn">Add Room</button>
            </div>
          </div>
        </form>
      </div>
    </main>
  </div>

  <?php include 'ad-footer.php'; ?>
</body>
</html>