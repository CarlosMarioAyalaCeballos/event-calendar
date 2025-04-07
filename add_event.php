<?php
require 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $_POST["titulo"];
    $descripcion = $_POST["descripcion"];
    $fecha = $_POST["fecha"];

    $sql = "INSERT INTO eventos (titulo, descripcion, fecha) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $titulo, $descripcion, $fecha);
    $stmt->execute();

    header("Location: index.php");
    exit();
}
?>