<?php
include 'includes/db.php';

$id = $_POST['id_medicamento'];
$nombre = $_POST['nombre'];
$descripcion = $_POST['descripcion'] ?? null;
$stock = $_POST['stock'];

$sql = "UPDATE medicamentos SET 
        nombre = ?, 
        descripcion = ?, 
        stock = ?
        WHERE id_medicamento = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssii", $nombre, $descripcion, $stock, $id);

if($stmt->execute()) {
    header("Location: medicamentos.php?success=2");
} else {
    header("Location: medicamentos.php?error=2");
}

$stmt->close();
$conn->close();
?>