<?php
$servername = "127.0.0.1"; // o "localhost", como prefieras
$username = "root";
$password = "TuClaveSegura123";
$database = "calendario";
$port = 3306; // Puerto estándar

$conn = new mysqli($servername, $username, $password, $database, $port);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>