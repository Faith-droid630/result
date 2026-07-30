<?php
include "db.php";
$doctor_id = 1;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $bio = trim($_POST['bio'] ?? '');
  $room = trim($_POST['room_location'] ?? '');
  $languages = trim($_POST['languages'] ?? '');

  if (strlen($room) > 100 || strlen($languages) > 100) {
    $error = 'Room location and languages must be 100 characters or less.';
  } else {
    $stmt = $conn->prepare("UPDATE doctor SET bio = ?, room_location = ?, languages = ? WHERE doctor_id = ?");
    $stmt->bind_param("sssi", $bio, $room, $languages, $doctor_id);

    if ($stmt->execute()) {
      header("Location: profile.php?saved=1");
      exit;
    }

    $error = 'Unable to save changes. Please try again.';
  }
}

$doctor = $conn->query("SELECT * FROM doctor WHERE doctor_id = $doctor_id")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Profile & settings</title>
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
      <a href="reviews.php">Reviews</a>
      <a href="profile.php" class="active">Profile & settings</a>
    </nav>
  </aside>

  <main class="main">
    <div class="greeting">
      <div>
        <h1>Profile & settings</h1>
        <p><?= htmlspecialchars($doctor['name']) ?></p>
        <?php if (!empty($error)): ?>
          <div class="pill pill-urgent" style="margin-bottom:14px; display:inline-block; padding:8px 14px;">
            <?= htmlspecialchars($error) ?>
          </div>
        <?php elseif (isset($_GET['saved'])): ?>
          <div class="pill pill-confirmed" style="margin-bottom:14px; display:inline-block; padding:8px 14px;">
            Changes saved ✓
          </div>
        <?php endif; ?>
      </div>
    </div>


    <div class="card">
      <form method="POST" style="display:flex; flex-direction:column; gap:14px; max-width:500px;">
        <div>
          <label style="font-size:12px; color:#64748b;">Bio</label><br>
          <textarea name="bio" rows="4" style="width:100%; padding:8px; border:1px solid #e2e8f0; border-radius:8px;"><?= htmlspecialchars($doctor['bio']) ?></textarea>
        </div>
        <div>
          <label style="font-size:12px; color:#64748b;">Room location</label><br>
          <input type="text" name="room_location" value="<?= htmlspecialchars($doctor['room_location']) ?>" style="width:100%; padding:8px; border:1px solid #e2e8f0; border-radius:8px;">
        </div>
        <div>
          <label style="font-size:12px; color:#64748b;">Languages</label><br>
          <input type="text" name="languages" value="<?= htmlspecialchars($doctor['languages']) ?>" style="width:100%; padding:8px; border:1px solid #e2e8f0; border-radius:8px;">
        </div>
        <button type="submit" class="btn btn-primary" style="width:fit-content;">Save changes</button>
      </form>
    </div>
  </main>
</div>
</body>
</html>