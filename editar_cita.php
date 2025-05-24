<?php
include 'includes/header.php';
include 'includes/db.php';

$id = $_GET['id'];

// Obtener datos de la cita
$sql = "SELECT * FROM citas WHERE id_cita = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$cita = $result->fetch_assoc();

// Obtener pacientes y médicos para los select
$pacientes = $conn->query("SELECT id_paciente, nombre, apellido FROM pacientes");
$medicos = $conn->query("SELECT id_medico, nombre, apellido FROM medicos");
?>

<h2>Editar Cita</h2>

<form action="actualizar_cita.php" method="post">
    <input type="hidden" name="id_cita" value="<?= $cita['id_cita'] ?>">
    
    <div class="mb-3">
        <label class="form-label">Paciente</label>
        <select class="form-select" name="id_paciente" required>
            <?php while($pac = $pacientes->fetch_assoc()): ?>
            <option value="<?= $pac['id_paciente'] ?>" <?= $pac['id_paciente'] == $cita['id_paciente'] ? 'selected' : '' ?>>
                <?= $pac['nombre'] . ' ' . $pac['apellido'] ?>
            </option>
            <?php endwhile; ?>
        </select>
    </div>
    
    <div class="mb-3">
        <label class="form-label">Médico</label>
        <select class="form-select" name="id_medico" required>
            <?php while($med = $medicos->fetch_assoc()): ?>
            <option value="<?= $med['id_medico'] ?>" <?= $med['id_medico'] == $cita['id_medico'] ? 'selected' : '' ?>>
                <?= $med['nombre'] . ' ' . $med['apellido'] ?>
            </option>
            <?php endwhile; ?>
        </select>
    </div>
    
    <div class="mb-3">
        <label class="form-label">Fecha y Hora</label>
        <input type="datetime-local" class="form-control" name="fecha_hora" 
               value="<?= date('Y-m-d\TH:i', strtotime($cita['fecha_hora'])) ?>" required>
    </div>
    
    <div class="mb-3">
        <label class="form-label">Motivo</label>
        <textarea class="form-control" name="motivo" rows="3" required><?= $cita['motivo'] ?></textarea>
    </div>
    
    <div class="mb-3">
        <label class="form-label">Estado</label>
        <select class="form-select" name="estado" required>
            <option value="Pendiente" <?= $cita['estado'] == 'Pendiente' ? 'selected' : '' ?>>Pendiente</option>
            <option value="Confirmada" <?= $cita['estado'] == 'Confirmada' ? 'selected' : '' ?>>Confirmada</option>
            <option value="Cancelada" <?= $cita['estado'] == 'Cancelada' ? 'selected' : '' ?>>Cancelada</option>
            <option value="Completada" <?= $cita['estado'] == 'Completada' ? 'selected' : '' ?>>Completada</option>
        </select>
    </div>
    
    <button type="submit" class="btn btn-primary">Actualizar</button>
    <a href="citas.php" class="btn btn-secondary">Cancelar</a>
</form>

<?php include 'includes/footer.php'; ?>