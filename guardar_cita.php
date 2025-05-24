<?php
include 'includes/db.php';

// Validación básica de campos requeridos
if (
    empty($_POST['id_paciente']) ||
    empty($_POST['id_medico']) ||
    empty($_POST['fecha_hora']) ||
    empty($_POST['motivo'])
) {
    header("Location: citas.php?error=Datos incompletos");
    exit;
}

$id_paciente = intval($_POST['id_paciente']);
$id_medico = intval($_POST['id_medico']);
$fecha_hora = $_POST['fecha_hora'];
$motivo = trim($_POST['motivo']);

try {
    // Forzar modo de excepciones para errores de triggers
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    $sql = "INSERT INTO citas (id_paciente, id_medico, fecha_hora, motivo) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiss", $id_paciente, $id_medico, $fecha_hora, $motivo);

    $stmt->execute();
    $stmt->close();
    $conn->close();
    header("Location: citas.php?success=1");
    exit;
} catch (mysqli_sql_exception $e) {
    // Captura errores de triggers y restricciones
    $msg = urlencode($e->getMessage());
    header("Location: citas.php?error=$msg");
    exit;
}
?>
