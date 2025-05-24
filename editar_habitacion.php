<?php
include 'includes/header.php';
include 'includes/db.php';

$id = $_GET['id'];

$sql = "SELECT * FROM habitaciones WHERE id_habitacion = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$habitacion = $result->fetch_assoc();
?>

<h2>Editar Habitación</h2>

<form action="actualizar_habitacion.php" method="post">
    <input type="hidden" name="id_habitacion" value="<?= $habitacion['id_habitacion'] ?>">
    
    <div class="mb-3">
        <label class="form-label">Número</label>
        <input type="text" class="form-control" name="numero" value="<?= $habitacion['numero'] ?>" required>
    </div>
    
    <div class="mb-3">
        <label class="form-label">Tipo</label>
        <select class="form-select" name="tipo" required>
            <option value="Individual" <?= $habitacion['tipo'] == 'Individual' ? 'selected' : '' ?>>Individual</option>
            <option value="Doble" <?= $habitacion['tipo'] == 'Doble' ? 'selected' : '' ?>>Doble</option>
            <option value="Suite" <?= $habitacion['tipo'] == 'Suite' ? 'selected' : '' ?>>Suite</option>
        </select>
    </div>
    
    <div class="mb-3">
        <label class="form-label">Estado</label>
        <select class="form-select" name="estado" required>
            <option value="Disponible" <?= $habitacion['estado'] == 'Disponible' ? 'selected' : '' ?>>Disponible</option>
            <option value="Ocupada" <?= $habitacion['estado'] == 'Ocupada' ? 'selected' : '' ?>>Ocupada</option>
            <option value="Mantenimiento" <?= $habitacion['estado'] == 'Mantenimiento' ? 'selected' : '' ?>>Mantenimiento</option>
        </select>
    </div>
    
    <button type="submit" class="btn btn-primary">Actualizar</button>
    <a href="habitaciones.php" class="btn btn-secondary">Cancelar</a>
</form>

<?php include 'includes/footer.php'; ?>