<?php
include 'includes/header.php';
include 'includes/db.php';

$id = $_GET['id'];

$sql = "SELECT * FROM medicamentos WHERE id_medicamento = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$medicamento = $result->fetch_assoc();
?>

<h2>Editar Medicamento</h2>

<form action="actualizar_medicamento.php" method="post">
    <input type="hidden" name="id_medicamento" value="<?= $medicamento['id_medicamento'] ?>">
    
    <div class="mb-3">
        <label class="form-label">Nombre</label>
        <input type="text" class="form-control" name="nombre" value="<?= $medicamento['nombre'] ?>" required>
    </div>
    
    <div class="mb-3">
        <label class="form-label">Descripción</label>
        <textarea class="form-control" name="descripcion" rows="3"><?= $medicamento['descripcion'] ?></textarea>
    </div>
    
    <div class="mb-3">
        <label class="form-label">Stock</label>
        <input type="number" class="form-control" name="stock" value="<?= $medicamento['stock'] ?>" min="0" required>
    </div>
    
    <button type="submit" class="btn btn-primary">Actualizar</button>
    <a href="medicamentos.php" class="btn btn-secondary">Cancelar</a>
</form>

<?php include 'includes/footer.php'; ?>