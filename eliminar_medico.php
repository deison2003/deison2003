<?php
include 'includes/db.php';

$id = $_GET['id'];

$sql = "DELETE FROM medicos WHERE id_medico = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if($stmt->execute()) {
    header("Location: medicos.php?success=3");
} else {
    header("Location: medicos.php?error=3");
}

$stmt->close();
$conn->close();
?>