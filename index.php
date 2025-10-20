<?php 
session_start();
include 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Welcome to eBMS</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Titan+One&family=Tomorrow:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    /* Reset and base styles */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Tomorrow', sans-serif;
        background: #f8f9ff;
        margin: 0;
        padding: 0;
        min-height: 100vh;
    }

    /* Custom Header Styles */
    .custom-header {
        background: linear-gradient(135deg, #2c3e50, #34495e);
        color: white;
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1000;
        height: 70px;
    }

    .custom-header .logo {
        font-family: 'Titan One', cursive;
        font-size: 1.8em;
        font-weight: bold;
    }

    .custom-header .nav-links {
        display: flex;
        gap: 20px;
        align-items: center;
    }

    .custom-header .nav-link {
        color: white;
        text-decoration: none;
        padding: 8px 15px;
        border-radius: 5px;
        background: rgba(255,255,255,0.2);
        transition: background 0.3s;
        font-family: 'Tomorrow', sans-serif;
        font-weight: 500;
    }

    .custom-header .nav-link:hover {
        background: rgba(255,255,255,0.3);
        transform: translateY(-2px);
    }

    .custom-header .nav-link i {
        margin-right: 5px;
    }

    .menu-toggle {
        background: none;
        border: none;
        color: white;
        font-size: 1.5em;
        cursor: pointer;
        padding: 5px 10px;
        border-radius: 5px;
        transition: background 0.3s;
        display: none;
    }

    .menu-toggle:hover {
        background: rgba(255,255,255,0.1);
    }

    /* Main content area */
    .main-content {
        padding: 100px 20px 60px 20px;
        min-height: 100vh;
        background: linear-gradient(135deg, #2c3e50, #34495e);
        transition: margin-left 0.3s ease;
        width: 100%;
    }

    /* Welcome section */
    .welcome-section {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 15px;
        margin-bottom: 40px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        border: 2px solid #7a5af5;
        max-width: 1200px;
        margin-left: auto;
        margin-right: auto;
    }

    .welcome-section h1 {
        font-family: 'Titan One', cursive;
        font-size: 3em;
        margin-bottom: 20px;
        color: #4c56a1;
        background: linear-gradient(135deg, #4c56a1, #7a5af5);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .tagline {
        font-size: 1.3em;
        max-width: 700px;
        margin: 0 auto;
        line-height: 1.6;
        color: #5e7599;
        font-weight: 500;
    }

    /* Rooms container */
    .rooms-container {
        display: flex;
        justify-content: center;
        gap: 30px;
        flex-wrap: wrap;
        margin: 40px 0;
        max-width: 1200px;
        margin-left: auto;
        margin-right: auto;
    }

    /* Room cards */
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
        width: 100%;
    }

    .view-btn:hover {
        background: linear-gradient(90deg, #345dd8, #416cec);
        transform: translateY(-3px);
        box-shadow: 0 6px 15px rgba(65, 108, 236, 0.4);
    }

    /* Note box */
    .note-box {
        text-align: center;
        background: #e8f4ff;
        border: 2px solid #416cec;
        border-radius: 12px;
        padding: 25px;
        margin: 50px auto;
        max-width: 700px;
        color: #2c3e50;
        font-size: 1.1em;
        line-height: 1.6;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    /* No rooms state */
    .no-rooms {
        text-align: center;
        width: 100%;
        padding: 40px;
        color: #666;
    }

    .no-rooms i {
        font-size: 3em;
        margin-bottom: 20px;
        color: #ccc;
    }

    /* Mobile responsive */
    @media (max-width: 768px) {
        .custom-header {
            padding: 10px 15px;
            height: 60px;
        }
        
        .custom-header .nav-links {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: linear-gradient(135deg, #2c3e50, #34495e);
            flex-direction: column;
            padding: 20px;
            gap: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .custom-header .nav-links.active {
            display: flex;
        }
        
        .menu-toggle {
            display: block;
        }
        
        .custom-header .logo {
            font-size: 1.5em;
        }
        
        .main-content {
            padding: 80px 15px 50px 15px;
        }
        
        .welcome-section {
            padding: 40px 20px;
        }
        
        .welcome-section h1 {
            font-size: 2.2em;
        }
        
        .tagline {
            font-size: 1.1em;
        }
        
        .room-card {
            width: 100%;
            max-width: 350px;
            padding: 20px;
        }
        
        .rooms-container {
            gap: 20px;
        }
    }
  </style>
</head>
<body>
  <!-- Custom Header for Index Page -->
  <header class="custom-header">
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
  </header>


  <div class="main-content" id="mainContent">
    <section class="welcome-section">
      <h1>eBMS</h1>
      <p class="tagline">Your home away from home — safe, cozy, and affordable living for professionals and students.</p>
    </section>

    <div class="rooms-container">
      <?php
      $rooms_result = $conn->query("SELECT * FROM rooms WHERE status='Available' LIMIT 3");
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

    <div class="note-box">
      💡 Choose a room first before registering an account. You'll be notified once your account is approved and given a move-in date.
    </div>
  </div>

  <script>
    function viewRoomDetails(roomNumber) {
      window.location.href = 'room-detail.php?room=' + roomNumber;
    }

    // Mobile menu functionality
    function toggleMobileMenu() {
      const navLinks = document.getElementById("navLinks");
      navLinks.classList.toggle("active");
    }

    // Close mobile menu when clicking outside
    document.addEventListener('click', function(event) {
      const navLinks = document.getElementById("navLinks");
      const menuToggle = document.querySelector('.menu-toggle');
      
      if (window.innerWidth <= 768 && navLinks && menuToggle) {
        if (!menuToggle.contains(event.target) && !navLinks.contains(event.target)) {
          navLinks.classList.remove('active');
        }
      }
    });

    // Close mobile menu on window resize
    window.addEventListener('resize', function() {
      const navLinks = document.getElementById("navLinks");
      if (window.innerWidth > 768 && navLinks) {
        navLinks.classList.remove('active');
      }
    });

    // Sidebar functionality (if you still want to keep it)
    function toggleSidebar() {
      const sidebar = document.getElementById("sidebar");
      const overlay = document.getElementById("sidebarOverlay");
      
      if (sidebar) {
        sidebar.classList.toggle("active");
        if (overlay) overlay.classList.toggle("active");
        
        // Adjust main content margin
        if (window.innerWidth > 768) {
          if (sidebar.classList.contains("active")) {
            document.getElementById("mainContent").style.marginLeft = "250px";
          } else {
            document.getElementById("mainContent").style.marginLeft = "0";
          }
        }
      }
    }

    // Adjust layout on window resize
    window.addEventListener('resize', function() {
      const sidebar = document.getElementById("sidebar");
      const mainContent = document.getElementById("mainContent");
      
      if (window.innerWidth <= 768) {
        mainContent.style.marginLeft = "0";
      } else if (sidebar && sidebar.classList.contains("active")) {
        mainContent.style.marginLeft = "250px";
      }
    });

    // Initial adjustment
    document.addEventListener('DOMContentLoaded', function() {
      const sidebar = document.getElementById("sidebar");
      const mainContent = document.getElementById("mainContent");
      
      if (window.innerWidth > 768 && sidebar && sidebar.classList.contains("active")) {
        mainContent.style.marginLeft = "250px";
      }
    });
  </script>
  <?php include 'footer.php'; ?> 
</body>
</html>
