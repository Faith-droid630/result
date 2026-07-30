<?php
include "db.php";
$doctor_id = 1;

$appointments = $conn->query("
  SELECT a.*, p.name AS patient_name, p.initials
  FROM appointment a JOIN patient p ON a.patient_id = p.patient_id
  WHERE a.doctor_id = $doctor_id
  ORDER BY a.appt_date DESC, a.appt_time DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Appointments</title>
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
      <a href="appointments.php" class="active">Appointments</a>
      <a href="schedule.php">Schedule & availability</a>
      <a href="patients.php">My Patients</a>
      <a href="reviews.php">Reviews</a>
      <a href="profile.php">Profile & settings</a>
    </nav>
  </aside>

  <main class="main">
    <div class="greeting">
      <div>
        <h1>Appointments</h1>
        <p>Every appointment on record, most recent first.</p>
      </div>
    </div>

    <div class="card">
      <table class="queue">
        <thead>
          <tr><th>Patient</th><th>Date</th><th>Time</th><th>Type</th><th>Priority</th><th>Status</th></tr>
        </thead>
        <tbody>
          <?php while ($row = $appointments->fetch_assoc()): ?>
          <tr>
            <td>
              <div class="queue-patient">
                <div class="avatar-chip"><?= $row['initials'] ?></div>
                <div><p class="name"><?= htmlspecialchars($row['patient_name']) ?></p></div>
              </div>
            </td>
            <td><?= date("M j, Y", strtotime($row['appt_date'])) ?></td>
            <td><?= date("g:i A", strtotime($row['appt_time'])) ?></td>
            <td><?= $row['visit_type'] ?></td>
            <td><span class="pill <?= $row['priority'] == 'Urgent' ? 'pill-urgent' : 'pill-normal' ?>"><?= $row['priority'] ?></span></td>
            <td><span class="pill pill-confirmed"><?= $row['status'] ?></span></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </main>
</div>
</body>
</html>
