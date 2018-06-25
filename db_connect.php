<?php
$servername = "trackback";
$username = "enikiosk-RO";
$password = "enikiosk-RO";
$dbname = "tracker";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
