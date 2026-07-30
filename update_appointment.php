<?php
header('Content-Type: application/json');
include "db.php";

function respond(array $data, int $status = 200) {
  http_response_code($status);
  echo json_encode($data);
  exit;
}

$id = intval($_POST['id'] ?? 0);
$action = $_POST['action'] ?? '';

$status_map = [
  "approved" => "Confirmed",
  "rejected" => "Rejected",
  "complete" => "Complete"
];

if ($id <= 0 || !isset($status_map[$action])) {
  respond(["success" => false, "message" => "Invalid appointment ID or action."], 400);
}

$status = $status_map[$action];

$doctor_id = 1;
$stmt = $conn->prepare("SELECT appt_date, status, queue_number FROM appointment WHERE appointment_id = ? AND doctor_id = ?");
$stmt->bind_param("ii", $id, $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
$appointment = $result->fetch_assoc();
$stmt->close();

if (!$appointment) {
  respond(["success" => false, "message" => "Appointment not found."], 404);
}

$new_queue = $appointment['queue_number'];
$update_sql = "UPDATE appointment SET status = ?";
$params = [$status];
$param_types = 's';

if ($action === 'approved' && $appointment['appt_date'] === date('Y-m-d') && empty($appointment['queue_number'])) {
  $queue_data = $conn->query(
    "SELECT COALESCE(MAX(queue_number), 0) + 1 AS next_queue FROM appointment WHERE doctor_id = $doctor_id AND appt_date = CURDATE()"
  )->fetch_assoc();
  $new_queue = (int) $queue_data['next_queue'];
  $update_sql .= ", queue_number = ?";
  $params[] = $new_queue;
  $param_types .= 'i';
}

$update_sql .= " WHERE appointment_id = ? AND doctor_id = ?";
$params[] = $id;
$params[] = $doctor_id;
$param_types .= 'ii';

$stmt = $conn->prepare($update_sql);
if ($stmt === false) {
  respond(["success" => false, "message" => "Database error preparing appointment update."], 500);
}
$stmt->bind_param($param_types, ...$params);
$stmt->execute();
$affected_rows = $stmt->affected_rows;
$stmt->close();

if ($affected_rows === 0 && $appointment['status'] === $status) {
  respond([
    "success" => true,
    "appointment_id" => $id,
    "new_status" => $status,
    "queue_number" => $new_queue,
    "message" => "No change required."
  ]);
}

respond([
  "success" => true,
  "appointment_id" => $id,
  "new_status" => $status,
  "queue_number" => $new_queue,
  "message" => "Appointment updated."
]);
