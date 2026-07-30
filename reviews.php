<?php
include "db.php";
$doctor_id = 1;

$reviews = $conn->query("
  SELECT r.*, p.name AS patient_name
  FROM review r JOIN patient p ON r.patient_id = p.patient_id
  WHERE r.doctor_id = $doctor_id
  ORDER BY r.review_date DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Reviews</title>
<link rel="stylesheet" href="styles.css" />
</head>
<body>
<div class="app">

  <aside class="sidebar">
    <div class="sidebar-brand">
      <img src="medibook-logo.png" alt="MediBook logo" />
      <span>MediBook</span>
    </div>
    <nav class="nav">
      <a href="dashboard.php">Overview</a>
      <a href="appointments.php">Appointments</a>
      <a href="schedule.php">Schedule & availability</a>
      <a href="patients.php">My Patients</a>
      <a href="reviews.php" class="active">Reviews</a>
      <a href="profile.php">Profile & settings</a>
    </nav>
  </aside>

  <main class="main">
    <div class="greeting">
      <div>
        <h1>Reviews</h1>
        <p>Everything patients have said about you.</p>
      </div>
    </div>

    <div class="card">
      <?php while ($row = $reviews->fetch_assoc()): ?>
      <div class="review-item" style="border-bottom:1px solid #f1f5f9; padding-bottom:12px;">
        <div class="row1"><span class="name"><?= htmlspecialchars($row['patient_name']) ?></span> <span class="mini-star">★★★★★</span></div>
        <p class="quote">"<?= htmlspecialchars($row['comment']) ?>"</p>
        <p class="meta" style="font-size:11px; color:#94a3b8; margin-top:4px;"><?= date("M j, Y", strtotime($row['review_date'])) ?></p>
      </div>
      <?php endwhile; ?>
    </div>
  </main>
</div>
</body>
</html>
