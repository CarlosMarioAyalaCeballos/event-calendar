<?php
$servername = "127.0.0.1";
$username = "root";
$password = "";
$database = "calendario";
$port = 3307;

$conn = new mysqli($servername, $username, $password, $database, $port);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>