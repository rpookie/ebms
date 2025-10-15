<style>
  .sidebar {
    width: 220px;
    background: linear-gradient(180deg, #4c56a1 0%, #c6c0c0 100%);
    border: 2px solid #7a5af5;
    padding: 20px 10px;
    box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    gap: 10px;
    transition: all 0.3s ease;
    position: fixed;
    height: calc(100vh - 60px);
    top: 60px;
    left: 0;
    overflow-y: auto;
    z-index: 999;
    transform: translateX(-100%);
  }

  .sidebar:not(.hidden) {
    transform: translateX(0);
  }

  .sidebar.hidden {
    transform: translateX(-100%);
  }

  .sidebar.mobile-active {
    transform: translateX(0);
  }

  .sidebar a {
    text-decoration: none;
    text-align: left;
    display: flex;
    align-items: center;
    padding: 12px 15px;
    border: 1.5px solid #5a78ff;
    border-radius: 8px;
    color: #b2bed3;
    font-weight: 600;
    transition: all 0.3s ease;
  }

  .sidebar a i {
    margin-right: 10px;
    width: 20px;
    text-align: center;
  }

  .sidebar a:hover {
    background: #5a78ff;
    color: #fff;
    transform: translateY(-2px);
  }

  .sidebar a.active {
    background: #5a78ff;
    color: #fff;
  }

  @media (max-width: 768px) {
    .sidebar {
      width: 280px;
    }
    
    body.sidebar-open {
      overflow: hidden;
    }
    
    .sidebar.mobile-active {
      transform: translateX(0);
      z-index: 1000;
    }
  }
</style>

<!-- Sidebar -->
<div class="sidebar dashboard-sidebar hidden" id="sidebar">
  <a href="ad-dashboard.php" class="active">
    <i class="fas fa-home"></i> Dashboard
  </a>
  <a href="ad-reservations.php">
    <i class="fas fa-user-check"></i> Reservations
  </a>
  <a href="ad-payments.php">
    <i class="fas fa-credit-card"></i> Payments
  </a>
  <a href="ad-boarders.php">
    <i class="fas fa-users"></i> Boarders
  </a>
  <a href="ad-rooms.php">
    <i class="fas fa-bed"></i> Rooms
  </a>
  <a href="announcements.php">
    <i class="fas fa-bullhorn"></i> Announcements
  </a>
  <a href="maintenance.php">
    <i class="fas fa-tools"></i> Maintenance
  </a>
  <a href="logout.php">
    <i class="fas fa-sign-out-alt"></i> Logout
  </a>
</div>