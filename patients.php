<?php
include "db.php";
$doctor_id = 1;

$patients = $conn->query("
  SELECT p.patient_id, p.name, p.initials, p.contact_info,
    COUNT(a.appointment_id) AS visit_count,
    MAX(a.appt_date) AS last_visit
  FROM patient p
  JOIN appointment a ON a.patient_id = p.patient_id
  WHERE a.doctor_id = $doctor_id
  GROUP BY p.patient_id
  ORDER BY last_visit DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>My Patients</title>
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
      <a href="patients.php" class="active">My Patients</a>
      <a href="reviews.php">Reviews</a>
      <a href="profile.php">Profile & settings</a>
    </nav>
  </aside>

  <main class="main">
    <div class="greeting">
      <div>
        <h1>My Patients</h1>
        <p>Everyone you've seen, most recent visit first.</p>
      </div>
    </div>

    <div class="card">
      <?php while ($row = $patients->fetch_assoc()): ?>
      <div class="patient-item" style="margin-bottom:16px;">
        <div class="avatar-chip"><?= $row['initials'] ?></div>
        <div>
          <p class="name"><?= htmlspecialchars($row['name']) ?></p>
          <p class="meta"><?= $row['visit_count'] ?> visit<?= $row['visit_count'] > 1 ? 's' : '' ?> · Last <?= date("M j", strtotime($row['last_visit'])) ?></p>
        </div>
      </div>
      <?php endwhile; ?>
    </div>
  </main>
</div>
</body>
</html>
