<?php
$mysqli = new mysqli("localhost", "root", "", "hospital");
if ($mysqli->connect_errno) {
    die("Fallo la conexión: " . $mysqli->connect_error);
}

// Insertar
if (isset($_POST['accion']) && $_POST['accion'] === 'agregar') {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $stock = intval($_POST['stock']);
    $stmt = $mysqli->prepare("INSERT INTO medicamentos (nombre, descripcion, stock) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $nombre, $descripcion, $stock);
    $stmt->execute();
    $stmt->close();
    header("Location: medicamentos.php");
    exit;
}

// Editar
if (isset($_POST['accion']) && $_POST['accion'] === 'editar') {
    $id_medicamento = $_POST['id_medicamento'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $stock = intval($_POST['stock']);
    $stmt = $mysqli->prepare("UPDATE medicamentos SET nombre=?, descripcion=?, stock=? WHERE id_medicamento=?");
    $stmt->bind_param("ssii", $nombre, $descripcion, $stock, $id_medicamento);
    $stmt->execute();
    $stmt->close();
    header("Location: medicamentos.php");
    exit;
}

// Eliminar
if (isset($_GET['eliminar'])) {
    $id_medicamento = intval($_GET['eliminar']);
    $mysqli->query("DELETE FROM medicamentos WHERE id_medicamento=$id_medicamento");
    header("Location: medicamentos.php");
    exit;
}

// Obtener medicamentos
$medicamentos = $mysqli->query("SELECT * FROM medicamentos ORDER BY id_medicamento DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Medicamentos | Hospital XYZ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --hospital-primary: #005f73;
            --hospital-secondary: #0a9396;
            --hospital-accent: #94d2bd;
            --hospital-light: #e9d8a6;
            --hospital-dark: #001219;
            --hospital-danger: #bb3e03;
            --hospital-warning: #ee9b00;
            --hospital-success: #4c956c;
            --stock-low: #ef476f;
            --stock-medium: #ffd166;
            --stock-high: #06d6a0;
        }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .hospital-container {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--hospital-accent);
        }
        
        .page-title {
            color: var(--hospital-dark);
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
        }
        
        .page-title i {
            color: var(--hospital-primary);
            margin-right: 12px;
            font-size: 1.8rem;
        }
        
        .btn-hospital-primary {
            background-color: var(--hospital-primary);
            color: white;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-hospital-primary:hover {
            background-color: var(--hospital-secondary);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .form-section {
            background: #f8fafc;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border: 1px solid #e2e8f0;
        }
        
        .form-label {
            font-weight: 500;
            color: var(--hospital-dark);
            margin-bottom: 0.5rem;
        }
        
        .form-control, .form-select {
            border-radius: 8px;
            padding: 0.5rem 1rem;
            border: 1px solid #dee2e6;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--hospital-accent);
            box-shadow: 0 0 0 0.25rem rgba(10, 147, 150, 0.25);
        }
        
        .medicamentos-table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        
        .medicamentos-table .table {
            margin-bottom: 0;
        }
        
        .medicamentos-table .table th {
            background: linear-gradient(135deg, var(--hospital-primary) 0%, var(--hospital-secondary) 100%);
            color: white;
            font-weight: 600;
            border-bottom: none;
        }
        
        .medicamentos-table .table td {
            vertical-align: middle;
            border-top: 1px solid #e2e8f0;
        }
        
        .stock-indicator {
            display: flex;
            align-items: center;
        }
        
        .stock-bar {
            width: 60px;
            height: 6px;
            background: #e9ecef;
            border-radius: 3px;
            overflow: hidden;
            margin-right: 10px;
        }
        
        .stock-level {
            height: 100%;
        }
        
        .stock-low {
            background-color: var(--stock-low);
        }
        
        .stock-medium {
            background-color: var(--stock-medium);
        }
        
        .stock-high {
            background-color: var(--stock-high);
        }
        
        .stock-value {
            font-weight: 500;
        }
        
        .stock-value.low {
            color: var(--stock-low);
        }
        
        .stock-value.medium {
            color: var(--stock-medium);
        }
        
        .stock-value.high {
            color: var(--stock-high);
        }
        
        .action-btns .btn {
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            font-size: 0.85rem;
        }
        
        .modal-header {
            background: linear-gradient(135deg, var(--hospital-primary) 0%, var(--hospital-secondary) 100%);
            color: white;
        }
        
        .btn-close-white {
            filter: invert(1);
        }
        
        @media (max-width: 768px) {
            .hospital-container {
                padding: 1.5rem;
                margin: 1rem;
            }
            
            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .page-title {
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="hospital-container">
        <div class="page-header">
            <h1 class="page-title">
                <i class="bi bi-capsule"></i> Gestión de Medicamentos
            </h1>
            <button class="btn btn-hospital-primary" data-bs-toggle="modal" data-bs-target="#modalAgregar">
                <i class="bi bi-plus-circle me-2"></i> Nuevo Medicamento
            </button>
        </div>
        
        <div class="form-section">
            <div class="row g-3">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" placeholder="Buscar medicamentos...">
                        <button class="btn btn-outline-secondary" type="button">Buscar</button>
                    </div>
                </div>
                <div class="col-md-4">
                    <select class="form-select">
                        <option value="">Todos los estados</option>
                        <option value="low">Stock bajo (<10)</option>
                        <option value="medium">Stock medio (10-50)</option>
                        <option value="high">Stock alto (>50)</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="medicamentos-table">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Stock</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while($row = $medicamentos->fetch_assoc()): 
                        $stockClass = '';
                        $stockLevelClass = '';
                        if ($row['stock'] < 10) {
                            $stockClass = 'low';
                            $stockLevelClass = 'stock-low';
                        } elseif ($row['stock'] <= 50) {
                            $stockClass = 'medium';
                            $stockLevelClass = 'stock-medium';
                        } else {
                            $stockClass = 'high';
                            $stockLevelClass = 'stock-high';
                        }
                    ?>
                        <tr>
                            <td class="fw-bold">#<?= $row['id_medicamento'] ?></td>
                            <td><?= htmlspecialchars($row['nombre']) ?></td>
                            <td><?= htmlspecialchars($row['descripcion']) ?></td>
                            <td>
                                <div class="stock-indicator">
                                    <div class="stock-bar">
                                        <div class="stock-level <?= $stockLevelClass ?>" style="width: <?= min(100, $row['stock']) ?>%"></div>
                                    </div>
                                    <span class="stock-value <?= $stockClass ?>"><?= $row['stock'] ?></span>
                                </div>
                            </td>
                            <td class="text-center action-btns">
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalEditar<?= $row['id_medicamento'] ?>" title="Editar">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button class="btn btn-sm btn-danger delete-btn" data-id="<?= $row['id_medicamento'] ?>" title="Eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        
                        <!-- Modal Editar -->
                        <div class="modal fade" id="modalEditar<?= $row['id_medicamento'] ?>" tabindex="-1" aria-hidden="true">
                          <div class="modal-dialog">
                            <form method="post" class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i> Editar Medicamento</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <div class="modal-body">
                                <input type="hidden" name="accion" value="editar">
                                <input type="hidden" name="id_medicamento" value="<?= $row['id_medicamento'] ?>">
                                <div class="mb-3">
                                  <label class="form-label">Nombre</label>
                                  <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($row['nombre']) ?>" required>
                                </div>
                                <div class="mb-3">
                                  <label class="form-label">Descripción</label>
                                  <textarea name="descripcion" class="form-control" rows="3"><?= htmlspecialchars($row['descripcion']) ?></textarea>
                                </div>
                                <div class="mb-3">
                                  <label class="form-label">Stock</label>
                                  <input type="number" name="stock" class="form-control" value="<?= $row['stock'] ?>" min="0" required>
                                </div>
                              </div>
                              <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-2"></i> Guardar Cambios
                                </button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                              </div>
                            </form>
                          </div>
                        </div>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Agregar -->
    <div class="modal fade" id="modalAgregar" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <form method="post" class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i> Nuevo Medicamento</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="accion" value="agregar">
            <div class="mb-3">
              <label class="form-label">Nombre</label>
              <input type="text" name="nombre" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Descripción</label>
              <textarea name="descripcion" class="form-control" rows="3"></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label">Stock</label>
              <input type="number" name="stock" class="form-control" min="0" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-success">
                <i class="bi bi-plus-circle me-2"></i> Agregar
            </button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal de Confirmación -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header bg-danger text-white">
            <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i> Confirmar Eliminación</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p>¿Está seguro que desea eliminar este medicamento?</p>
            <p class="fw-bold">Esta acción no se puede deshacer.</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <a href="#" id="confirmDeleteBtn" class="btn btn-danger">
                <i class="bi bi-trash me-2"></i> Eliminar
            </a>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Confirmación de eliminación con modal
    document.addEventListener('DOMContentLoaded', function() {
        const deleteButtons = document.querySelectorAll('.delete-btn');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        const confirmDeleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
        
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const medId = this.getAttribute('data-id');
                confirmDeleteBtn.href = `medicamentos.php?eliminar=${medId}`;
                confirmDeleteModal.show();
            });
        });
        
        // Inicializar tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
    </script>
</body>
</html>