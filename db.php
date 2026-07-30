<?php
// Database connection settings for XAMPP defaults
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "medibook";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
