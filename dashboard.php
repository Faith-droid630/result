<?php
include "db.php";

// For now we assume doctor_id = 1 (Dr. Fotso). Later this will come from login.
$doctor_id = 1;

$doctor = $conn->query("SELECT * FROM doctor WHERE doctor_id = $doctor_id")->fetch_assoc();

$pending = $conn->query("
  SELECT a.*, p.name AS patient_name, p.initials
  FROM appointment a JOIN patient p ON a.patient_id = p.patient_id
  WHERE a.doctor_id = $doctor_id AND a.status = 'Pending'
  ORDER BY a.appt_date, a.appt_time
");

$queue = $conn->query("
  SELECT a.*, p.name AS patient_name, p.initials
  FROM appointment a JOIN patient p ON a.patient_id = p.patient_id
  WHERE a.doctor_id = $doctor_id AND a.appt_date = CURDATE() AND a.status != 'Pending'
  ORDER BY a.appt_time
");

$reviews = $conn->query("
  SELECT r.*, p.name AS patient_name
  FROM review r JOIN patient p ON r.patient_id = p.patient_id
  WHERE r.doctor_id = $doctor_id
  ORDER BY r.review_date DESC LIMIT 3
");

$today_count = $conn->query("SELECT COUNT(*) c FROM appointment WHERE doctor_id = $doctor_id AND appt_date = CURDATE()")->fetch_assoc()['c'];
$pending_count = $conn->query("SELECT COUNT(*) c FROM appointment WHERE doctor_id = $doctor_id AND status = 'Pending'")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Doctor dashboard</title>
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
      <a href="dashboard.php" class="active">Overview</a>
      <a href="appointments.php">Appointments</a>
      <a href="schedule.php">Schedule & availability</a>
      <a href="patients.php">My Patients</a>
      <a href="reviews.php">Reviews</a>
      <a href="profile.php">Profile & settings</a>
    </nav>
  </aside>

  <main class="main">
    <div class="greeting">
      <div>
        <h1>Good morning, <?= htmlspecialchars($doctor['name']) ?></h1>
        <p data-pending-intro>You have <?= $today_count ?> appointments today · <?= $pending_count ?> awaiting your approval.</p>
      </div>
      <div class="top-right">
        <div class="notification" title="Pending approvals">
          <span class="notification-icon">🔔</span>
          <span class="notification-badge" data-notification-badge><?= $pending_count ?></span>
        </div>
      </div>
    </div>

    <div class="stat-grid">
      <div class="card stat-card">
        <p class="label">Today's appointments</p>
        <p class="value"><?= $today_count ?></p>
      </div>
      <div class="card stat-card">
        <p class="label">Pending approval</p>
        <p class="value"><span data-pending-count><?= $pending_count ?></span></p>
      </div>
      <div class="card stat-card">
        <p class="label">Average rating</p>
        <p class="value"><?= $doctor['avg_rating'] ?> ★</p>
        <p class="delta muted"><?= $doctor['review_count'] ?> reviews</p>
      </div>
    </div>

    <div class="dash-grid">
      <div class="dash-col">

        <div class="card">
          <div class="card-head">
            <h2>Pending requests</h2>
            <span class="pill pill-amber" data-pending-header><?= $pending_count ?> waiting</span>
          </div>

          <?php while ($row = $pending->fetch_assoc()): ?>
          <div class="request-row" data-request data-id="<?= $row['appointment_id'] ?>">
            <div class="person">
              <div class="avatar-chip"><?= $row['initials'] ?></div>
              <div>
                <p class="name"><?= htmlspecialchars($row['patient_name']) ?></p>
                <p class="meta">Requested <?= date("g:i A, M j", strtotime($row['appt_date']." ".$row['appt_time'])) ?> · <?= $row['priority'] ?></p>
              </div>
            </div>
            <div class="row-actions">
              <button class="btn btn-primary btn-approve">Approve</button>
              <button class="btn btn-reject">Reject</button>
            </div>
          </div>
          <?php endwhile; ?>
        </div>

        <div class="card">
          <div class="card-head">
            <h2>Today's queue</h2>
          </div>
          <table class="queue">
            <thead>
              <tr><th>#</th><th>Patient</th><th>Time</th><th>Priority</th><th>Status</th><th></th></tr>
            </thead>
            <tbody>
              <?php while ($row = $queue->fetch_assoc()): ?>
              <tr data-id="<?= $row['appointment_id'] ?>">
                <td><?= str_pad($row['queue_number'], 3, '0', STR_PAD_LEFT) ?></td>
                <td>
                  <div class="queue-patient">
                    <div class="avatar-chip"><?= $row['initials'] ?></div>
                    <div><p class="name"><?= htmlspecialchars($row['patient_name']) ?></p><p class="type"><?= $row['visit_type'] ?></p></div>
                  </div>
                </td>
                <td><?= date("g:i A", strtotime($row['appt_time'])) ?></td>
                <td><span class="pill <?= $row['priority'] == 'Urgent' ? 'pill-urgent' : 'pill-normal' ?>"><?= $row['priority'] ?></span></td>
                <td><span class="pill pill-confirmed"><?= $row['status'] ?></span></td>
                <td><button class="complete-btn" data-prev-status="<?= $row['status'] ?>">Complete</button></td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="dash-col">
        <div class="card">
          <h2 style="font-size:14px; margin:0 0 12px;">Recent reviews</h2>
          <?php while ($row = $reviews->fetch_assoc()): ?>
          <div class="review-item">
            <div class="row1"><span class="name"><?= htmlspecialchars($row['patient_name']) ?></span> <span class="mini-star">★★★★★</span></div>
            <p class="quote">"<?= htmlspecialchars($row['comment']) ?>"</p>
          </div>
          <?php endwhile; ?>
        </div>
      </div>
    </div>
  </main>
</div>

<script src="app.js"></script>
</body>
</html>
