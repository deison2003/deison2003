<?php
include 'includes/db.php';

$id = $_GET['id'];

// Obtener id_habitacion antes de dar el alta
$sql = "SELECT id_habitacion FROM hospitalizaciones WHERE id_hospitalizacion = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$hospitalizacion = $result->fetch_assoc();
$id_habitacion = $hospitalizacion['id_habitacion'];

// Dar de alta al paciente
$sql = "UPDATE hospitalizaciones SET 
        estado = 'Finalizada',
        fecha_salida = NOW()
        WHERE id_hospitalizacion = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if($stmt->execute()) {
    // Liberar la habitación
    $sql = "UPDATE habitaciones SET estado = 'Disponible' WHERE id_habitacion = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_habitacion);
    $stmt->execute();
    
    header("Location: habitaciones.php?success=2");
} else {
    header("Location: habitaciones.php?error=2");
}

$stmt->close();
$conn->close();
?>