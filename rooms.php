<?php 
session_start();
include 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Rooms | eBoard Management System</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="style.css">
</head>
<body class="rooms-page">
  <?php include 'index-header.php'; ?>

  <div class="main-content" id="mainContent">
    <div class="page-header">
      <h1>Available Rooms</h1>
      <p>Choose your perfect room for a comfortable stay</p>
    </div>

    <div class="rooms-container">
      <?php
      $rooms_result = $conn->query("SELECT * FROM rooms WHERE status='Available'");
      if ($rooms_result->num_rows > 0) {
        while ($room = $rooms_result->fetch_assoc()) {
      ?>
      <div class="room-card">
        <div class="room-image">
          <i class="fas fa-bed"></i> Room <?php echo $room['room_number']; ?>
        </div>
        <span class="room-title">Room <?php echo $room['room_number']; ?></span>
        <div class="room-price">₱<?php echo number_format($room['monthly_rent'], 2); ?>/month</div>
        <button class="view-btn" onclick="viewRoomDetails('<?php echo $room['room_number']; ?>')">View Room Details</button>
      </div>
      <?php 
        }
      } else {
        echo '<div class="no-rooms">
                <i class="fas fa-bed"></i>
                <h3>No rooms available at the moment</h3>
                <p>Please check back later for available rooms.</p>
              </div>';
      }
      ?>
    </div>
  </div>

  <?php include 'index-footer.php'; ?>

  <script>
    function viewRoomDetails(roomNumber) {
      window.location.href = 'room-detail.php?room=' + roomNumber;
    }
  </script>

  <style>
    .rooms-page .main-content {
      padding: 80px 40px 40px 40px !important;
      min-height: calc(100vh - 120px) !important;
      background: linear-gradient(180deg, #f8f9ff 0%, #ffffff 100%) !important;
    }

    .page-header {
      text-align: center;
      margin-bottom: 50px;
    }

    .page-header h1 {
      font-family: 'Titan One', cursive;
      font-size: 2.8em;
      color: #4c56a1;
      margin-bottom: 15px;
      background: linear-gradient(135deg, #4c56a1, #7a5af5);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .page-header p {
      font-size: 1.2em;
      color: #5e7599;
    }

    .rooms-container {
      display: flex;
      justify-content: center;
      gap: 30px;
      flex-wrap: wrap;
    }

    .room-card {
      background: white;
      border: 2px solid #416cec;
      border-radius: 15px;
      padding: 25px;
      text-align: center;
      width: 320px;
      box-shadow: 0 6px 15px rgba(0,0,0,0.1);
      transition: all 0.3s ease;
    }

    .room-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 12px 25px rgba(0,0,0,0.15);
    }

    .room-image {
      width: 100%;
      height: 180px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border-radius: 10px;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 1.2em;
    }

    .room-title {
      display: block;
      font-size: 1.4em;
      color: #2c3e50;
      margin: 10px 0;
      font-weight: bold;
    }

    .room-price {
      font-size: 1.2em;
      color: #416cec;
      font-weight: bold;
      margin: 15px 0;
    }

    .view-btn {
      background: linear-gradient(90deg, #416cec, #345dd8);
      color: white;
      border: none;
      border-radius: 10px;
      padding: 12px 25px;
      font-size: 1em;
      font-family: 'Tomorrow', sans-serif;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 4px 10px rgba(65, 108, 236, 0.3);
      text-decoration: none;
      display: inline-block;
      font-weight: 600;
    }

    .view-btn:hover {
      background: linear-gradient(90deg, #345dd8, #416cec);
      transform: translateY(-3px);
      box-shadow: 0 6px 15px rgba(65, 108, 236, 0.4);
    }

    .no-rooms {
      text-align: center;
      width: 100%;
      padding: 60px;
      color: #666;
    }

    .no-rooms i {
      font-size: 4em;
      margin-bottom: 20px;
      color: #ccc;
    }

    @media (max-width: 768px) {
      .rooms-page .main-content {
        padding: 20px !important;
      }
      
      .page-header h1 {
        font-size: 2.2em;
      }
      
      .page-header p {
        font-size: 1.1em;
      }
      
      .room-card {
        width: 100%;
        max-width: 350px;
      }
    }
  </style>
</body>
</html>