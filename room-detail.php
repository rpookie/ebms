<?php 
session_start();
include 'db.php';

// Get room number from URL parameter
$roomNumber = isset($_GET['room']) ? $_GET['room'] : '101';

// Fetch room data from database
$room_query = $conn->prepare("SELECT * FROM rooms WHERE room_number = ?");
$room_query->bind_param("s", $roomNumber);
$room_query->execute();
$room_result = $room_query->get_result();
$room = $room_result->fetch_assoc();

if (!$room) {
    header("Location: rooms.php");
    exit();
}

// Convert images string to array
$room_images = explode(',', $room['images']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Room <?php echo $room['room_number']; ?> Details | eBMS</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <?php include 'index-header.php'; ?>

  <div class="main-content" id="mainContent">
    <section class="room-detail">
      <div class="left">
        <img src="<?php echo $room['main_img']; ?>" alt="Main Room Picture" class="main-img" id="mainImage">
        <div class="image-thumbnails">
          <?php foreach ($room_images as $image): ?>
            <img src="<?php echo trim($image); ?>" alt="Room Picture" class="thumbnail" onclick="changeMainImage('<?php echo trim($image); ?>')">
          <?php endforeach; ?>
        </div>
      </div>

      <div class="details">
        <h2>ROOM <?php echo $room['room_number']; ?></h2>
        <p><span>Bed Type:</span> <?php echo $room['bed_type']; ?></p>
        <p><span>Bathroom:</span> <?php echo $room['bathroom_type']; ?></p>
        <p><span>Status:</span> <?php echo $room['status']; ?></p>
        <p><span>Monthly Rent:</span> ₱<?php echo $room['monthly_rent']; ?></p>
        <p><span>Cooling Type:</span> <?php echo $room['cooling_type']; ?></p>
        <p><span>Wi-Fi Access:</span> <?php echo $room['wifi_access']; ?></p>
        <p><span>Kitchen Access:</span> <?php echo $room['kitchen_access']; ?></p>
        <p><span>Laundry Access:</span> <?php echo $room['laundry_access']; ?></p>
        <br>
        <a href="reserve-room.php?room=<?php echo $roomNumber; ?>" class="reserve-btn">Reserve Room</a>
      </div>
    </section>
  </div>

  <?php include 'index-footer.php'; ?>

  <script>
    function changeMainImage(src) {
      document.getElementById('mainImage').src = src;
    }
  </script>
  
  <style>
    .room-detail {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      align-items: flex-start;
      background: #fff;
      color: #000;
      border-radius: 20px;
      padding: 30px;
      margin: 20px auto;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
      max-width: 900px;
      gap: 30px;
    }

    .left {
      display: flex;
      flex-direction: column;
      gap: 10px;
      align-items: center;
    }

    .main-img {
      width: 300px;
      height: 300px;
      object-fit: cover;
      border-radius: 15px;
      border: 2px solid #416cec;
    }

    .image-thumbnails {
      display: flex;
      gap: 10px;
    }

    .thumbnail {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 10px;
      border: 2px solid #416cec;
      cursor: pointer;
      transition: transform 0.3s ease;
    }

    .thumbnail:hover {
      transform: scale(1.1);
    }

    .details {
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      max-width: 400px;
    }

    .details h2 {
      font-family: 'Titan One', cursive;
      font-size: 1.6em;
      color: #000;
      margin-bottom: 15px;
    }

    .details p {
      margin: 5px 0;
      font-size: 1em;
      line-height: 1.4em;
    }

    .details span {
      font-weight: bold;
      color: #000;
    }

    @media (max-width: 768px) {
      .room-detail {
        flex-direction: column;
        align-items: center;
      }
      .details {
        align-items: center;
        text-align: center;
      }
    }
  </style>
</body>
</html>