<?php
include 'includes/db.php';

$id = $_POST['id_habitacion'];
$numero = $_POST['numero'];
$tipo = $_POST['tipo'];
$estado = $_POST['estado'];

$sql = "UPDATE habitaciones SET 
        numero = ?, 
        tipo = ?, 
        estado = ?
        WHERE id_habitacion = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssi", $numero, $tipo, $estado, $id);

if($stmt->execute()) {
    header("Location: habitaciones.php?success=1");
} else {
    header("Location: habitaciones.php?error=1");
}

$stmt->close();
$conn->close();
?>