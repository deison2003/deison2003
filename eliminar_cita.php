<?php
include 'includes/db.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: citas.php?error=ID no especificado");
    exit;
}

// Usar el procedimiento almacenado para eliminar
$sql = "CALL EliminarCita(?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if($stmt->execute()) {
    header("Location: citas.php?success=4");
} else {
    header("Location: citas.php?error=No se pudo eliminar la cita");
}

$stmt->close();
$conn->close();
?>