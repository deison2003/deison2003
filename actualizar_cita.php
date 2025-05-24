<?php
include 'includes/db.php';

$id = $_POST['id_cita'];
$id_paciente = $_POST['id_paciente'];
$id_medico = $_POST['id_medico'];
$fecha_hora = $_POST['fecha_hora'];
$motivo = $_POST['motivo'];
$estado = $_POST['estado'];

$sql = "UPDATE citas SET 
        id_paciente = ?, 
        id_medico = ?, 
        fecha_hora = ?, 
        motivo = ?, 
        estado = ?
        WHERE id_cita = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iisssi", $id_paciente, $id_medico, $fecha_hora, $motivo, $estado, $id);

if($stmt->execute()) {
    header("Location: citas.php?success=3");
} else {
    header("Location: citas.php?error=3");
}

$stmt->close();
$conn->close();
?>