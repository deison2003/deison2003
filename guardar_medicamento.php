<?php
include 'includes/db.php';

$nombre = $_POST['nombre'];
$descripcion = $_POST['descripcion'] ?? null;
$stock = $_POST['stock'];

$sql = "INSERT INTO medicamentos (nombre, descripcion, stock) VALUES (?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssi", $nombre, $descripcion, $stock);

if($stmt->execute()) {
    header("Location: medicamentos.php?success=1");
} else {
    header("Location: medicamentos.php?error=1");
}

$stmt->close();
$conn->close();
?>