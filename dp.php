<?php
$hostname = "localhost";
$username = "root";
$password = "";
$database = "student_grading";

$conn = new mysqli($hostname, $username, "", $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}