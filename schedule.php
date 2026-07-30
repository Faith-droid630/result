<?php
include "db.php";
$doctor_id = 1;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $date = trim($_POST['slot_date'] ?? '');
  $time = trim($_POST['slot_time'] ?? '');

  if ($date === '' || $time === '') {
    $error = 'Date and time are required.';
  } else {
    $date_obj = DateTime::createFromFormat('Y-m-d', $date);
    $time_obj = DateTime::createFromFormat('H:i', $time);

    if (!$date_obj || $date_obj->format('Y-m-d') !== $date || !$time_obj || $time_obj->format('H:i') !== $time) {
      $error = 'Invalid date or time.';
    } else {
      $stmt = $conn->prepare("INSERT INTO availability_slot (doctor_id, slot_date, slot_time, is_booked) VALUES (?, ?, ?, 0)");
      $stmt->bind_param("iss", $doctor_id, $date, $time);
      if ($stmt->execute()) {
        header("Location: schedule.php?saved=1");
        exit;
      }
      $error = 'Could not save the slot. Please try again.';
    }
  }
}

$slots = $conn->query("
  SELECT * FROM availability_slot
  WHERE doctor_id = $doctor_id
  ORDER BY slot_date, slot_time
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Schedule & availability</title>
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
      <a href="schedule.php" class="active">Schedule & availability</a>
      <a href="patients.php">My Patients</a>
      <a href="reviews.php">Reviews</a>
      <a href="profile.php">Profile & settings</a>
    </nav>
  </aside>

  <main class="main">
    <div class="greeting">
      <div>
        <h1>Schedule & availability</h1>
        <p>Open time slots patients can book.</p>
      </div>
    </div>

    <div class="card" style="margin-bottom:18px;">
      <div class="card-head"><h2>Add a new slot</h2></div>
      <?php if (!empty($error)): ?>
        <div class="pill pill-urgent" style="margin-bottom:12px; display:inline-block;"><?= htmlspecialchars($error) ?></div>
      <?php elseif (isset($_GET['saved'])): ?>
        <div class="pill pill-confirmed" style="margin-bottom:12px; display:inline-block;">Slot added successfully.</div>
      <?php endif; ?>
      <form method="POST" style="display:flex; gap:10px; align-items:end;">
        <div>
          <label style="font-size:12px; color:#64748b;">Date</label><br>
          <input type="date" name="slot_date" required style="padding:8px; border:1px solid #e2e8f0; border-radius:8px;">
        </div>
        <div>
          <label style="font-size:12px; color:#64748b;">Time</label><br>
          <input type="time" name="slot_time" required style="padding:8px; border:1px solid #e2e8f0; border-radius:8px;">
        </div>
        <button type="submit" class="btn btn-primary">Add slot</button>
      </form>
    </div>

    <div class="card">
      <table class="queue">
        <thead>
          <tr><th>Date</th><th>Time</th><th>Status</th></tr>
        </thead>
        <tbody>
          <?php while ($row = $slots->fetch_assoc()): ?>
          <tr>
            <td><?= date("M j, Y", strtotime($row['slot_date'])) ?></td>
            <td><?= date("g:i A", strtotime($row['slot_time'])) ?></td>
            <td><span class="pill <?= $row['is_booked'] ? 'pill-urgent' : 'pill-confirmed' ?>"><?= $row['is_booked'] ? 'Booked' : 'Open' ?></span></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </main>
</div>
</body>
</html>
