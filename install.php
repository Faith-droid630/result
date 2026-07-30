<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'medibook';

$mysqli = new mysqli($host, $user, $pass);
if ($mysqli->connect_error) {
  die('Connection failed: ' . $mysqli->connect_error);
}
$mysqli->set_charset('utf8mb4');

$schemaFile = __DIR__ . '/schema.sql';
if (!file_exists($schemaFile)) {
  die('Missing schema.sql file.');
}

$sql = file_get_contents($schemaFile);
if ($sql === false) {
  die('Could not read schema.sql.');
}

if (!$mysqli->multi_query($sql)) {
  die('Schema import failed: ' . $mysqli->error);
}

while ($mysqli->more_results() && $mysqli->next_result()) {
  if ($result = $mysqli->store_result()) {
    $result->free();
  }
}

$mysqli->close();

echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Install Complete</title></head><body>';
echo '<h1>Database installed</h1>';
echo '<p>The <strong>medibook</strong> database and sample records were created successfully.</p>';
echo '<p>Remove or secure <code>install.php</code> after use.</p>';
echo '</body></html>';
