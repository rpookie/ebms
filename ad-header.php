<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - eBMS</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <!-- Header -->
  <div class="header">
    <button class="menu-toggle" onclick="toggleSidebar()">☰</button>
    <div class="logo">eBMS Admin</div>
    <div class="user-menu">
      <button class="user-btn" onclick="toggleDropdown()">
        Administrator <i class="fa-solid fa-user-shield"></i> <i class="fa-solid fa-caret-down"></i>
      </button>
      <div class="dropdown" id="userDropdown">
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
          document.body.classList.toggle('sidebar-open');
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

    // Toggle dropdown menu
    function toggleDropdown() {
      const dropdown = document.getElementById('userDropdown');
      dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
      const dropdown = document.getElementById('userDropdown');
      const userBtn = document.querySelector('.user-btn');
      
      if (!userBtn.contains(event.target) && dropdown && !dropdown.contains(event.target)) {
        dropdown.style.display = 'none';
      }
      
      // Close sidebar when clicking outside on mobile
      const sidebar = document.getElementById("sidebar");
      const menuToggle = document.querySelector('.menu-toggle');
      
      if (window.innerWidth <= 768 && sidebar && !sidebar.contains(event.target) && 
          event.target !== menuToggle && !menuToggle.contains(event.target)) {
        sidebar.classList.remove('mobile-active');
        document.body.classList.remove('sidebar-open');
      }
    });

    // Initialize sidebar to closed state on page load
    document.addEventListener('DOMContentLoaded', function() {
      const sidebar = document.getElementById("sidebar");
      const mainContent = document.getElementById("mainContent");
      const dropdown = document.getElementById('userDropdown');
      
      if (sidebar && mainContent) {
        // Ensure sidebar starts closed
        sidebar.classList.add('hidden');
        mainContent.style.marginLeft = "0";
        document.body.classList.remove('sidebar-open');
      }
      
      if (dropdown) {
        dropdown.style.display = 'none';
      }
    });

    // Handle window resize
    window.addEventListener('resize', function() {
      const sidebar = document.getElementById("sidebar");
      const mainContent = document.getElementById("mainContent");
      
      if (window.innerWidth > 768) {
        // Desktop - remove mobile active class
        sidebar.classList.remove('mobile-active');
        document.body.classList.remove('sidebar-open');
      } else {
        // Mobile - ensure sidebar is hidden
        if (!sidebar.classList.contains('mobile-active')) {
          sidebar.classList.add('hidden');
          mainContent.style.marginLeft = "0";
        }
      }
    });
  </script>