<?php 
session_start();
include 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About eBMS | eBoard Management System</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <?php include 'index-header.php'; ?>

  <div class="main-content" id="mainContent">
    <div class="content-header">
      <h1>About eBMS</h1>
      <p>Learn more about our eBoard Management System</p>
    </div>

    <div class="about-content" style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); max-width: 800px; margin: 0 auto;">
      <h2 style="color: #4263eb; margin-bottom: 20px;">What is eBMS?</h2>
      <p style="line-height: 1.6; margin-bottom: 20px;">
        eBMS (eBoard Management System) is a comprehensive digital platform designed to streamline 
        the management of boarding houses and dormitories. Our system provides an efficient, secure, 
        and user-friendly solution for both boarders and administrators.
      </p>

      <h3 style="color: #5e7599; margin: 25px 0 15px 0;">Features for Boarders:</h3>
      <ul style="line-height: 1.8; margin-bottom: 25px;">
        <li>Easy room reservation and booking</li>
        <li>Online payment processing with receipt upload</li>
        <li>Payment history tracking</li>
        <li>Maintenance request submission</li>
        <li>Real-time announcement viewing</li>
        <li>Profile management</li>
      </ul>

      <h3 style="color: #5e7599; margin: 25px 0 15px 0;">Features for Administrators:</h3>
      <ul style="line-height: 1.8; margin-bottom: 25px;">
        <li>Reservation approval system</li>
        <li>Payment verification and management</li>
        <li>Boarder account management</li>
        <li>Maintenance request tracking</li>
        <li>Announcement broadcasting</li>
        <li>Automated SMS/Email notifications</li>
      </ul>

      <div style="background: #e8f4ff; padding: 20px; border-radius: 10px; margin-top: 30px;">
        <h4 style="color: #4263eb; margin-bottom: 15px;">Contact Information</h4>
        <p><strong>Address:</strong> Gonzaga, Cagayan</p>
        <p><strong>Email:</strong> info@ebms.com</p>
        <p><strong>Phone:</strong> +63 XXX-XXX-XXXX</p>
      </div>
    </div>
  </div>

  <?php include 'index-footer.php'; ?>
</body>
</html>