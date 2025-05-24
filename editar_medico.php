<?php
include 'includes/header.php';
include 'includes/db.php';

$id = $_GET['id'];

$sql = "SELECT * FROM medicos WHERE id_medico = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$medico = $result->fetch_assoc();

$especialidades = $conn->query("SELECT * FROM especialidades");
?>

<h2>Editar Médico</h2>

<form action="actualizar_medico.php" method="post">
    <input type="hidden" name="id_medico" value="<?= $medico['id_medico'] ?>">
    
    <div class="mb-3">
        <label class="form-label">Nombre</label>
        <input type="text" class="form-control" name="nombre" value="<?= $medico['nombre'] ?>" required>
    </div>
    
    <div class="mb-3">
        <label class="form-label">Apellido</label>
        <input type="text" class="form-control" name="apellido" value="<?= $medico['apellido'] ?>" required>
    </div>
    
    <div class="mb-3">
        <label class="form-label">Especialidad</label>
        <select class="form-select" name="id_especialidad" required>
            <?php while($esp = $especialidades->fetch_assoc()): ?>
            <option value="<?= $esp['id_especialidad'] ?>" <?= $esp['id_especialidad'] == $medico['id_especialidad'] ? 'selected' : '' ?>>
                <?= $esp['nombre'] ?>
            </option>
            <?php endwhile; ?>
        </select>
    </div>
    
    <div class="mb-3">
        <label class="form-label">Teléfono</label>
        <input type="tel" class="form-control" name="telefono" value="<?= $medico['telefono'] ?>">
    </div>
    
    <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" class="form-control" name="correo_electronico" value="<?= $medico['correo_electronico'] ?>">
    </div>
    
    <button type="submit" class="btn btn-primary">Actualizar</button>
    <a href="medicos.php" class="btn btn-secondary">Cancelar</a>
</form>

<?php include 'includes/footer.php'; ?>