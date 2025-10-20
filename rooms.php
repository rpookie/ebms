<?php 
session_start();
include 'db.php';

// Determine access type based on session and direct access
$is_logged_in = isset($_SESSION['user_id']);
$is_admin = $is_logged_in && $_SESSION['role'] == 'admin';
$is_boarder = $is_logged_in && $_SESSION['role'] == 'boarder';
$is_public_access = !$is_logged_in;

// Redirect if trying to access protected pages without login
if (!$is_public_access && !$is_logged_in) {
    header("Location: login.php");
    exit();
}

// Handle room deletion (Admin only)
if ($is_admin && isset($_GET['delete_room'])) {
    $room_number = $_GET['delete_room'];
    $stmt = $conn->prepare("DELETE FROM rooms WHERE room_number = ?");
    $stmt->bind_param("s", $room_number);
    if ($stmt->execute()) {
        $success = "Room deleted successfully!";
    } else {
        $error = "Error deleting room: " . $conn->error;
    }
}

// Handle image upload and room addition (Admin only)
if ($is_admin && isset($_POST['add_room'])) {
    $room_number = $_POST['room_number'];
    $monthly_rent = $_POST['monthly_rent'];
    $bed_type = $_POST['bed_type'];
    $bathroom_type = $_POST['bathroom_type'];
    $cooling_type = $_POST['cooling_type'];
    $wifi_access = $_POST['wifi_access'];
    $description = $_POST['description'];
    
    // Handle image upload
    $main_img = '';
    if (isset($_FILES['room_image']) && $_FILES['room_image']['error'] == 0) {
        $upload_dir = 'uploads/rooms/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['room_image']['name'], PATHINFO_EXTENSION);
        $filename = 'room_' . $room_number . '_' . time() . '.' . $file_extension;
        $upload_path = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['room_image']['tmp_name'], $upload_path)) {
            $main_img = $upload_path;
        }
    }
    
    $stmt = $conn->prepare("INSERT INTO rooms (room_number, monthly_rent, bed_type, bathroom_type, cooling_type, wifi_access, description, main_img, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Available')");
    $stmt->bind_param("sdssssss", $room_number, $monthly_rent, $bed_type, $bathroom_type, $cooling_type, $wifi_access, $description, $main_img);
    
    if ($stmt->execute()) {
        $success = "Room added successfully!";
    } else {
        $error = "Error adding room: " . $conn->error;
    }
}

// Get rooms based on access type
if ($is_admin) {
    // Admin sees all rooms regardless of status
    $rooms_result = $conn->query("SELECT * FROM rooms ORDER BY room_number");
    // Get boarder reservations for admin view
    $reservations_result = $conn->query("
        SELECT r.*, u.username, u.full_name 
        FROM reservations r 
        JOIN users u ON r.user_id = u.id 
        WHERE r.status IN ('pending', 'approved')
        ORDER BY r.created_at DESC
    ");
} else if ($is_boarder) {
    $user_id = $_SESSION['user_id'];
    // Get boarder's reserved room
    $reserved_room_result = $conn->query("
        SELECT r.*, res.status as reservation_status, res.created_at as reserved_date 
        FROM reservations res 
        JOIN rooms r ON res.room_number = r.room_number 
        WHERE res.user_id = $user_id AND res.status IN ('pending', 'approved')
        ORDER BY res.created_at DESC LIMIT 1
    ");
    // Also get available rooms for boarder to see
    $rooms_result = $conn->query("SELECT * FROM rooms WHERE status='Available' ORDER BY room_number");
} else {
    // Public access - only available rooms
    $rooms_result = $conn->query("SELECT * FROM rooms WHERE status='Available' ORDER BY room_number");
}

// Handle room reservation
if (($is_public_access || $is_boarder) && isset($_GET['reserve_room'])) {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['redirect_to'] = 'rooms.php?reserve_room=' . $_GET['reserve_room'];
        header("Location: login.php");
        exit();
    }
    
    $room_number = $_GET['reserve_room'];
    $user_id = $_SESSION['user_id'];
    
    // Check if room is available
    $check_room = $conn->query("SELECT * FROM rooms WHERE room_number = '$room_number' AND status = 'Available'");
    if ($check_room->num_rows > 0) {
        // Check if user already has a pending or approved reservation
        $check_reservation = $conn->query("SELECT * FROM reservations WHERE user_id = $user_id AND status IN ('pending', 'approved')");
        if ($check_reservation->num_rows == 0) {
            $stmt = $conn->prepare("INSERT INTO reservations (user_id, room_number, status) VALUES (?, ?, 'pending')");
            $stmt->bind_param("is", $user_id, $room_number);
            if ($stmt->execute()) {
                // Update room status to reserved
                $conn->query("UPDATE rooms SET status = 'Reserved' WHERE room_number = '$room_number'");
                $success = "Room reserved successfully! Waiting for admin approval.";
            } else {
                $error = "Error reserving room: " . $conn->error;
            }
        } else {
            $error = "You already have a pending or approved reservation.";
        }
    } else {
        $error = "Room is no longer available.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>
    <?php 
    if ($is_admin) echo 'Manage Rooms | eBMS';
    elseif ($is_boarder) echo 'My Room & Available Rooms | eBMS';
    else echo 'Available Rooms | eBMS';
    ?>
  </title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Titan+One&family=Tomorrow:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
  <?php 

  // Include header and sidebar only for logged-in users (admin/boarder)
  if ($is_logged_in) {
      include 'header.php';
      include 'sidebar.php';
  }else echo '<header class="custom-header">
    <a href="index.php" class="nav-link">
          <i class="bi bi-house"></i>Home
    </a>
    <div class="logo">eBMS</div>
    <div class="nav-links" id="navLinks">
        <a href="rooms.php" class="nav-link">
            <i class="fas fa-bed"></i> Rooms
        </a>
        <a href="about.php" class="nav-link">
            <i class="fas fa-info-circle"></i> About Us
        </a>
        <a href="login.php" class="nav-link">
            <i class="fas fa-sign-in-alt"></i> Login
        </a>
    </div>
  </header>'
  ?>
  
  <div class="main-container">
    <main class="main-content" id="mainContent" style="margin-top: <?php echo $is_public_access ? '0' : '70px'; ?>;">
      <?php if (!$is_public_access): ?>
        <div class="content-header">
          <h1>
            <?php 
            if ($is_admin) echo 'Manage Rooms';
            elseif ($is_boarder) echo 'My Room & Available Rooms';
            else echo 'Available Rooms';
            ?>
          </h1>
          <p>
            <?php 
            if ($is_admin) echo 'Add, edit, and manage all rooms';
            elseif ($is_boarder) echo 'View your reserved room and other available rooms';
            else echo 'Choose your perfect room for a comfortable stay';
            ?>
          </p>
        </div>
      <?php endif; ?>

      <?php if (isset($error)): ?>
        <div class="error-message">
          <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
        </div>
      <?php endif; ?>

      <?php if (isset($success)): ?>
        <div class="success-message">
          <i class="fas fa-check-circle"></i> <?php echo $success; ?>
        </div>
      <?php endif; ?>

      <!-- ADMIN: Add Room Form and Controls -->
      <?php if ($is_admin): ?>
        <div class="admin-controls">
          <div>
            <h3 style="margin: 0; color: #2c3e50;">Room Management</h3>
            <p style="margin: 5px 0 0 0; color: #666;">Add new rooms or manage existing ones</p>
          </div>
        </div>

        <div class="add-room-form">
          <h3 style="margin-bottom: 20px; color: #2c3e50;"><i class="fas fa-plus-circle"></i> Add New Room</h3>
          <form method="POST" enctype="multipart/form-data">
            <div class="form-row">
              <div class="form-group">
                <label for="room_number">Room Number *</label>
                <input type="text" class="form-control" id="room_number" name="room_number" required>
              </div>
              <div class="form-group">
                <label for="monthly_rent">Monthly Rent (₱) *</label>
                <input type="number" class="form-control" id="monthly_rent" name="monthly_rent" step="0.01" required>
              </div>
            </div>
            
            <div class="form-row">
              <div class="form-group">
                <label for="bed_type">Bed Type *</label>
                <select class="form-control" id="bed_type" name="bed_type" required>
                  <option value="">Select Bed Type</option>
                  <option value="Single Bed">Single Bed</option>
                  <option value="Double Bed">Double Bed</option>
                  <option value="Bunk Bed">Bunk Bed</option>
                </select>
              </div>
              <div class="form-group">
                <label for="bathroom_type">Bathroom Type *</label>
                <select class="form-control" id="bathroom_type" name="bathroom_type" required>
                  <option value="">Select Bathroom Type</option>
                  <option value="Private">Private</option>
                  <option value="Shared">Shared</option>
                </select>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="cooling_type">Cooling Type *</label>
                <select class="form-control" id="cooling_type" name="cooling_type" required>
                  <option value="">Select Cooling Type</option>
                  <option value="Aircon">Aircon</option>
                  <option value="Electric Fan">Electric Fan</option>
                  <option value="None">None</option>
                </select>
              </div>
              <div class="form-group">
                <label for="wifi_access">Wi-Fi Access *</label>
                <select class="form-control" id="wifi_access" name="wifi_access" required>
                  <option value="">Select Wi-Fi Access</option>
                  <option value="Available">Available</option>
                  <option value="Not Available">Not Available</option>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label for="room_image">Room Image</label>
              <input type="file" class="form-control" id="room_image" name="room_image" accept="image/*">
            </div>

            <div class="form-group">
              <label for="description">Description</label>
              <textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter room description..."></textarea>
            </div>

            <button type="submit" name="add_room" class="btn btn-success">
              <i class="fas fa-plus"></i> Add Room
            </button>
          </form>
        </div>
      <?php endif; ?>

      <!-- BOARDER: Reserved Room Section -->
      <?php if ($is_boarder && isset($reserved_room_result) && $reserved_room_result->num_rows > 0): ?>
        <?php $reserved_room = $reserved_room_result->fetch_assoc(); ?>
        <div class="reserved-room-section">
          <div class="reserved-room-header">
            <i class="fas fa-home fa-2x"></i>
            <h2 style="margin: 0;">My Reserved Room</h2>
          </div>
          
          <div class="room-card" style="border-color: #28a745; margin: 0;">
            <div class="room-image">
              <?php if (!empty($reserved_room['main_img'])): ?>
                <img src="<?php echo $reserved_room['main_img']; ?>" alt="Room <?php echo $reserved_room['room_number']; ?>">
              <?php else: ?>
                <i class="fas fa-bed"></i> Room <?php echo $reserved_room['room_number']; ?>
              <?php endif; ?>
              <span class="room-status status-<?php echo strtolower($reserved_room['reservation_status']); ?>">
                <?php echo ucfirst($reserved_room['reservation_status']); ?>
              </span>
            </div>
            
            <span class="room-title">Room <?php echo $reserved_room['room_number']; ?></span>
            
            <div class="room-price">₱<?php echo number_format($reserved_room['monthly_rent'], 2); ?>/month</div>
            
            <div class="room-features">
              <span class="feature-tag"><?php echo $reserved_room['bed_type']; ?></span>
              <span class="feature-tag"><?php echo $reserved_room['bathroom_type']; ?></span>
              <span class="feature-tag"><?php echo $reserved_room['cooling_type']; ?></span>
              <?php if ($reserved_room['wifi_access'] == 'Available'): ?>
                <span class="feature-tag">Wi-Fi</span>
              <?php endif; ?>
            </div>

            <div class="room-actions">
              <a href="room-detail.php?room=<?php echo $reserved_room['room_number']; ?>" class="btn">
                <i class="fas fa-eye"></i> View Details
              </a>
              <span class="btn btn-warning" style="cursor: default;">
                <i class="fas fa-calendar-check"></i> Reserved on <?php echo date('M d, Y', strtotime($reserved_room['reserved_date'])); ?>
              </span>
            </div>
          </div>
        </div>

        <div class="content-header">
          <h3>Other Available Rooms</h3>
          <p>Explore other available rooms</p>
        </div>
      <?php endif; ?>

      <!-- Rooms Grid for All Users -->
      <div class="rooms-container">
        <?php if (isset($rooms_result) && $rooms_result->num_rows > 0): ?>
          <?php while ($room = $rooms_result->fetch_assoc()): ?>
            <div class="room-card">
              <div class="room-image">
                <?php if (!empty($room['main_img'])): ?>
                  <img src="<?php echo $room['main_img']; ?>" alt="Room <?php echo $room['room_number']; ?>">
                <?php else: ?>
                  <i class="fas fa-bed"></i> Room <?php echo $room['room_number']; ?>
                <?php endif; ?>
                <span class="room-status status-<?php echo strtolower($room['status']); ?>">
                  <?php echo $room['status']; ?>
                </span>
              </div>
              
              <span class="room-title">Room <?php echo $room['room_number']; ?></span>
              
              <div class="room-price">₱<?php echo number_format($room['monthly_rent'], 2); ?>/month</div>
              
              <div class="room-features">
                <span class="feature-tag"><?php echo $room['bed_type']; ?></span>
                <span class="feature-tag"><?php echo $room['bathroom_type']; ?></span>
                <span class="feature-tag"><?php echo $room['cooling_type']; ?></span>
                <?php if ($room['wifi_access'] == 'Available'): ?>
                  <span class="feature-tag">Wi-Fi</span>
                <?php endif; ?>
              </div>

              <div class="room-actions">
                <a href="room-detail.php?room=<?php echo $room['room_number']; ?>" class="btn">
                  <i class="fas fa-eye"></i> View Details
                </a>
                
                <?php if ($is_admin): ?>
                  <button class="btn btn-danger" onclick="deleteRoom('<?php echo $room['room_number']; ?>')">
                    <i class="fas fa-trash"></i> Delete
                  </button>
                <?php elseif ($room['status'] == 'Available' && ($is_public_access || $is_boarder)): ?>
                  <?php if (!($is_boarder && isset($reserved_room_result) && $reserved_room_result->num_rows > 0)): ?>
                    <a href="rooms.php?reserve_room=<?php echo $room['room_number']; ?>" class="btn btn-success">
                      <i class="fas fa-calendar-check"></i> Reserve
                    </a>
                  <?php endif; ?>
                <?php endif; ?>
              </div>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <div class="empty-state">
            <i class="fas fa-bed"></i>
            <h3>No rooms available</h3>
            <p>
              <?php 
              if ($is_admin) echo 'Add your first room to get started';
              elseif ($is_boarder) echo 'No other rooms available at the moment';
              else echo 'Please check back later for available rooms';
              ?>
            </p>
          </div>
        <?php endif; ?>
      </div>

      <!-- ADMIN: Reservations Table -->
      <?php if ($is_admin && isset($reservations_result) && $reservations_result->num_rows > 0): ?>
        <div class="reservations-table">
          <h3 style="margin-bottom: 20px; color: #2c3e50;"><i class="fas fa-list"></i> Current Reservations</h3>
          <table class="table">
            <thead>
              <tr>
                <th>Room Number</th>
                <th>Boarder Name</th>
                <th>Username</th>
                <th>Status</th>
                <th>Reserved Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($reservation = $reservations_result->fetch_assoc()): ?>
                <tr>
                  <td><?php echo $reservation['room_number']; ?></td>
                  <td><?php echo $reservation['full_name']; ?></td>
                  <td><?php echo $reservation['username']; ?></td>
                  <td>
                    <span class="feature-tag status-<?php echo strtolower($reservation['status']); ?>">
                      <?php echo ucfirst($reservation['status']); ?>
                    </span>
                  </td>
                  <td><?php echo date('M d, Y', strtotime($reservation['created_at'])); ?></td>
                  <td>
                    <a href="ad-reservations.php" class="btn" style="padding: 8px 15px; font-size: 0.9em;">
                      <i class="fas fa-cog"></i> Manage
                    </a>
                  </td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>

      <?php if ($is_public_access): ?>
        <div style="text-align: center; margin-top: 40px;">
          <a href="index.php" class="btn">
            <i class="fas fa-arrow-left"></i> Back to Home
          </a>
        </div>
      <?php endif; ?>
    </main>
  </div>

  <script>
    function deleteRoom(roomNumber) {
      if (confirm('Are you sure you want to delete Room ' + roomNumber + '? This action cannot be undone.')) {
        window.location.href = 'rooms.php?delete_room=' + roomNumber;
      }
    }
  </script>
  
  <?php 
  // Include footer for all users
  include 'footer.php'; 
  ?>
</body>
</html>
