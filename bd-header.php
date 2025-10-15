<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Boarder Dashboard - eBMS</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <!-- Header -->
  <div class="header">
    <button class="menu-toggle" onclick="toggleSidebar()">☰</button>
    <div class="logo">eBMS</div>
    <div class="user-menu">
      <button class="user-btn" onclick="toggleDropdown()">
        <?php echo $_SESSION['fname'] . ' ' . $_SESSION['lname']; ?> <i class="fa-solid fa-user"></i> <i class="fa-solid fa-caret-down"></i>
      </button>
      <div class="dropdown">
        <a href="profile.php"><i class="fa-solid fa-user"></i> Profile</a>
        <a href="settings.php"><i class="fa-solid fa-cog"></i> Settings</a>
        <a href="logout.php"><i class="fa-solid fa-sign-out-alt"></i> Logout</a>
      </div>
    </div>
  </div>

  	<script>
// Enhanced sidebar toggle for dashboard pages
	function toggleSidebar() {
	    const sidebar = document.getElementById("sidebar");
	    const mainContent = document.getElementById("mainContent");
	    
	    if (sidebar && mainContent) {
	        if (window.innerWidth <= 768) {
	            // Mobile behavior
	            sidebar.classList.toggle('mobile-active');
	            mainContent.classList.toggle('sidebar-open');
	        } else {
	            // Desktop behavior - toggle hidden class
	            sidebar.classList.toggle('hidden');
	            if (sidebar.classList.contains('hidden')) {
	                mainContent.style.marginLeft = "0";
	            } else {
	                mainContent.style.marginLeft = "220px";
	            }
	        }
	    }
	}

	// Initialize sidebar to closed state on page load
	document.addEventListener('DOMContentLoaded', function() {
	    const sidebar = document.getElementById("sidebar");
	    const mainContent = document.getElementById("mainContent");
	    
	    if (sidebar && mainContent) {
	        // Ensure sidebar starts closed
	        sidebar.classList.add('hidden');
	        mainContent.style.marginLeft = "0";
	        mainContent.classList.remove('sidebar-open');
	    }
	});
	</script>