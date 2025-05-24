<?php
include 'includes/db.php';

// Validación básica
if (
    empty($_POST['id_medico']) ||
    empty($_POST['nombre']) ||
    empty($_POST['apellido']) ||
    empty($_POST['id_especialidad'])
) {
    header("Location: medicos.php?error=Datos incompletos");
    exit;
}

$id = $_POST['id_medico'];
$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$id_especialidad = $_POST['id_especialidad'];
$telefono = $_POST['telefono'] ?? null;
$correo_electronico = $_POST['correo_electronico'] ?? null;

$sql = "UPDATE medicos SET 
        nombre = ?, 
        apellido = ?, 
        id_especialidad = ?, 
        telefono = ?, 
        correo_electronico = ?
        WHERE id_medico = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssissi", $nombre, $apellido, $id_especialidad, $telefono, $correo_electronico, $id);

if($stmt->execute()) {
    header("Location: medicos.php?success=2");
} else {
    header("Location: medicos.php?error=No se pudo actualizar el médico");
}

$stmt->close();
$conn->close();
?>