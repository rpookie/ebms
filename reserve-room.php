<?php
session_start();
include 'db.php';

// Get room number from URL parameter
$selectedRoom = isset($_GET['room']) ? $_GET['room'] : '';

// Check if room exists and is available
if ($selectedRoom) {
    $room_check = $conn->prepare("SELECT * FROM rooms WHERE room_number = ? AND status = 'Available'");
    $room_check->bind_param("s", $selectedRoom);
    $room_check->execute();
    $room_result = $room_check->get_result();
    
    if ($room_result->num_rows == 0) {
        header("Location: rooms.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reserve Room | eBMS</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <?php include 'index-header.php'; ?>

  <div class="main-content" id="mainContent">
    <div class="form-container">
      <h2>Reserve Room</h2>
      <form id="reservationForm" action="process-reserve.php" method="POST" onsubmit="return validateForm('reservationForm')">
        <div class="form-grid">
          <!-- Personal Information -->
          <div class="form-group">
            <label class="required">First Name:</label>
            <input type="text" name="fname" required>
            <div class="error" id="fnameError"></div>
          </div>
          
          <div class="form-group">
            <label>Middle Name:</label>
            <input type="text" name="mname">
          </div>

          <div class="form-group">
            <label class="required">Last Name:</label>
            <input type="text" name="lname" required>
            <div class="error" id="lnameError"></div>
          </div>
          
          <div class="form-group">
            <label class="required">Email:</label>
            <input type="email" name="email" required>
            <div class="error" id="emailError"></div>
          </div>

          <div class="form-group">
            <label class="required">Contact No.:</label>
            <input type="text" name="contact" required pattern="[0-9+\-\s()]{10,}">
            <div class="error" id="contactError"></div>
          </div>

          <div class="form-group">
            <label class="required">Age:</label>
            <input type="number" name="age" min="18" max="120" required>
            <div class="error" id="ageError"></div>
          </div>
          
          <div class="form-group full-width">
            <label class="required">Address:</label>
            <textarea name="address" rows="3" required></textarea>
            <div class="error" id="addressError"></div>
          </div>

          <!-- Guardian Information -->
          <div class="form-group full-width">
            <label style="color: #4263eb; font-size: 16px; margin: 15px 0 10px 0; display: block;">Guardian Information</label>
          </div>

          <div class="form-group">
            <label class="required">Guardian Full Name:</label>
            <input type="text" name="guardian_fullname" required>
            <div class="error" id="guardian_fullnameError"></div>
          </div>
          
          <div class="form-group">
            <label class="required">Relationship:</label>
            <select name="guardian_relationship" required>
              <option value="">-- Select Relationship --</option>
              <option value="Parent">Parent</option>
              <option value="Sibling">Sibling</option>
              <option value="Spouse">Spouse</option>
              <option value="Relative">Relative</option>
              <option value="Friend">Friend</option>
              <option value="Other">Other</option>
            </select>
            <div class="error" id="guardian_relationshipError"></div>
          </div>

          <div class="form-group">
            <label class="required">Guardian Contact No.:</label>
            <input type="text" name="guardian_contact" required pattern="[0-9+\-\s()]{10,}">
            <div class="error" id="guardian_contactError"></div>
          </div>

          <div class="form-group">
            <label>Guardian Email (Optional):</label>
            <input type="email" name="guardian_email">
            <div class="error" id="guardian_emailError"></div>
          </div>

          <!-- Room Selection -->
          <div class="form-group full-width">
            <label style="color: #4263eb; font-size: 16px; margin: 15px 0 10px 0; display: block;">Room Selection</label>
          </div>

          <div class="form-group full-width">
            <label class="required">Select Room:</label>
            <select name="room" required>
              <option value="">-- Select Room --</option>
              <?php
              $available_rooms = $conn->query("SELECT * FROM rooms WHERE status = 'Available'");
              while ($room = $available_rooms->fetch_assoc()) {
                  $selected = ($selectedRoom == $room['room_number']) ? 'selected' : '';
                  echo "<option value='{$room['room_number']}' $selected>Room {$room['room_number']} - ₱{$room['monthly_rent']}/month</option>";
              }
              ?>
            </select>
            <div class="error" id="roomError"></div>
          </div>

          <div class="form-group full-width" style="text-align: center; margin-top: 20px;">
            <button type="submit" class="btn">Reserve Room</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <?php include 'index-footer.php'; ?>
  <script src="script.js"></script>
</body>
</html>