<?php
require 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST["id"];
    $titulo = trim($_POST["titulo"]);
    $descripcion = trim($_POST["descripcion"]);
    $fecha = $_POST["fecha"];

    // Validaciones básicas
    if(empty($titulo) || empty($descripcion) || empty($fecha)) {
        http_response_code(400);
        exit("Campos requeridos");
    }

    // Sanitizar entradas
    $titulo = mysqli_real_escape_string($conn, $titulo);
    $descripcion = mysqli_real_escape_string($conn, $descripcion);
    $fecha = mysqli_real_escape_string($conn, $fecha);

    $sql = "UPDATE eventos SET titulo=?, descripcion=?, fecha=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $titulo, $descripcion, $fecha, $id);
    
    if($stmt->execute()) {
        http_response_code(200);
    } else {
        http_response_code(500);
        error_log("Error en la actualización: " . $stmt->error);
    }
    $stmt->close();
    exit();
}

http_response_code(405);
exit("Método no permitido");