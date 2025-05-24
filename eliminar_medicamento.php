<?php
include 'includes/db.php';

$id = $_GET['id'];

$sql = "DELETE FROM medicamentos WHERE id_medicamento = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if($stmt->execute()) {
    header("Location: medicamentos.php?success=3");
} else {
    header("Location: medicamentos.php?error=3");
}

$stmt->close();
$conn->close();
?>