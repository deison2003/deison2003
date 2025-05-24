<?php
include 'includes/db.php';

// Validación básica
if (
    empty($_POST['nombre']) ||
    empty($_POST['apellido']) ||
    empty($_POST['id_especialidad'])
) {
    header("Location: medicos.php?error=Datos incompletos");
    exit;
}

$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$id_especialidad = $_POST['id_especialidad'];
$telefono = $_POST['telefono'] ?? null;
$correo_electronico = $_POST['correo_electronico'] ?? null;

// Insertar médico
$sql = "INSERT INTO medicos (nombre, apellido, id_especialidad, telefono, correo_electronico) 
        VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssiss", $nombre, $apellido, $id_especialidad, $telefono, $correo_electronico);

if($stmt->execute()) {
    header("Location: medicos.php?success=1");
} else {
    header("Location: medicos.php?error=No se pudo guardar el médico");
}

$stmt->close();
$conn->close();
?>