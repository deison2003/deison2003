<?php
$mysqli = new mysqli("localhost", "root", "", "hospital");
if ($mysqli->connect_errno) {
    die("Fallo la conexión: " . $mysqli->connect_error);
}

// Insertar
if (isset($_POST['accion']) && $_POST['accion'] === 'agregar') {
    $numero = $_POST['numero'];
    $tipo = $_POST['tipo'];
    $estado = $_POST['estado'];
    $stmt = $mysqli->prepare("INSERT INTO habitaciones (numero, tipo, estado) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $numero, $tipo, $estado);
    $stmt->execute();
    $stmt->close();
    header("Location: habitaciones_b.php");
    exit;
}

// Editar
if (isset($_POST['accion']) && $_POST['accion'] === 'editar') {
    $id_habitacion = $_POST['id_habitacion'];
    $numero = $_POST['numero'];
    $tipo = $_POST['tipo'];
    $estado = $_POST['estado'];
    $stmt = $mysqli->prepare("UPDATE habitaciones SET numero=?, tipo=?, estado=? WHERE id_habitacion=?");
    $stmt->bind_param("sssi", $numero, $tipo, $estado, $id_habitacion);
    $stmt->execute();
    $stmt->close();
    header("Location: habitaciones_b.php");
    exit;
}

// Eliminar
if (isset($_GET['eliminar'])) {
    $id_habitacion = intval($_GET['eliminar']);
    $mysqli->query("DELETE FROM habitaciones WHERE id_habitacion=$id_habitacion");
    header("Location: habitaciones_b.php");
    exit;
}

// Obtener habitaciones
$habitaciones = $mysqli->query("SELECT * FROM habitaciones ORDER BY id_habitacion DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Habitaciones | Hospital XYZ</title>
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
            --room-available: #06d6a0;
            --room-occupied: #ef476f;
            --room-maintenance: #ffd166;
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
        
        .rooms-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }
        
        .room-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            border: 1px solid #e2e8f0;
        }
        
        .room-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }
        
        .room-header {
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .room-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--hospital-primary);
        }
        
        .room-type {
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 500;
            background-color: rgba(10, 147, 150, 0.1);
            color: var(--hospital-secondary);
        }
        
        .room-body {
            padding: 1rem;
        }
        
        .room-status {
            display: inline-block;
            padding: 0.35em 0.65em;
            border-radius: 50px;
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.75rem;
        }
        
        .status-available {
            background-color: rgba(6, 214, 160, 0.1);
            color: var(--room-available);
        }
        
        .status-occupied {
            background-color: rgba(239, 71, 111, 0.1);
            color: var(--room-occupied);
        }
        
        .status-maintenance {
            background-color: rgba(255, 209, 102, 0.1);
            color: var(--room-maintenance);
        }
        
        .room-actions {
            padding: 1rem;
            border-top: 1px solid #e2e8f0;
            display: flex;
            gap: 0.5rem;
        }
        
        .action-btn {
            flex: 1;
            padding: 0.5rem;
            border-radius: 6px;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
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
            
            .rooms-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="hospital-container">
        <div class="page-header">
            <h1 class="page-title">
                <i class="bi bi-door-open"></i> Gestión de Habitaciones
            </h1>
            <button class="btn btn-hospital-primary" data-bs-toggle="modal" data-bs-target="#modalAgregar">
                <i class="bi bi-plus-circle me-2"></i> Nueva Habitación
            </button>
        </div>
        
        <div class="form-section">
            <h4 class="mb-4"><i class="bi bi-funnel me-2"></i> Filtros</h4>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Tipo</label>
                    <select class="form-select">
                        <option value="">Todos los tipos</option>
                        <option value="Individual">Individual</option>
                        <option value="Doble">Doble</option>
                        <option value="Suite">Suite</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Estado</label>
                    <select class="form-select">
                        <option value="">Todos los estados</option>
                        <option value="Disponible">Disponible</option>
                        <option value="Ocupada">Ocupada</option>
                        <option value="Mantenimiento">Mantenimiento</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Número</label>
                    <input type="text" class="form-control" placeholder="Buscar por número">
                </div>
            </div>
        </div>
        
        <div class="rooms-grid">
            <?php while($row = $habitaciones->fetch_assoc()): 
                $statusClass = '';
                switch($row['estado']) {
                    case 'Disponible': $statusClass = 'status-available'; break;
                    case 'Ocupada': $statusClass = 'status-occupied'; break;
                    case 'Mantenimiento': $statusClass = 'status-maintenance'; break;
                }
            ?>
            <div class="room-card">
                <div class="room-header">
                    <span class="room-number">#<?= htmlspecialchars($row['numero']) ?></span>
                    <span class="room-type"><?= $row['tipo'] ?></span>
                </div>
                <div class="room-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">Estado</h6>
                            <span class="room-status <?= $statusClass ?>"><?= $row['estado'] ?></span>
                        </div>
                        <div>
                            <h6 class="mb-1">ID</h6>
                            <span class="text-muted">#<?= $row['id_habitacion'] ?></span>
                        </div>
                    </div>
                </div>
                <div class="room-actions">
                    <button class="btn btn-sm btn-primary action-btn" data-bs-toggle="modal" data-bs-target="#modalEditar<?= $row['id_habitacion'] ?>">
                        <i class="bi bi-pencil"></i> Editar
                    </button>
                    <button class="btn btn-sm btn-danger action-btn delete-btn" data-id="<?= $row['id_habitacion'] ?>">
                        <i class="bi bi-trash"></i> Eliminar
                    </button>
                </div>
            </div>
            
            <!-- Modal Editar -->
            <div class="modal fade" id="modalEditar<?= $row['id_habitacion'] ?>" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog">
                <form method="post" class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i> Editar Habitación #<?= htmlspecialchars($row['numero']) ?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <input type="hidden" name="accion" value="editar">
                    <input type="hidden" name="id_habitacion" value="<?= $row['id_habitacion'] ?>">
                    <div class="mb-3">
                      <label class="form-label">Número</label>
                      <input type="text" name="numero" class="form-control" value="<?= htmlspecialchars($row['numero']) ?>" required>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Tipo</label>
                      <select name="tipo" class="form-select" required>
                        <option value="Individual" <?= $row['tipo']=='Individual'?'selected':'' ?>>Individual</option>
                        <option value="Doble" <?= $row['tipo']=='Doble'?'selected':'' ?>>Doble</option>
                        <option value="Suite" <?= $row['tipo']=='Suite'?'selected':'' ?>>Suite</option>
                      </select>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Estado</label>
                      <select name="estado" class="form-select" required>
                        <option value="Disponible" <?= $row['estado']=='Disponible'?'selected':'' ?>>Disponible</option>
                        <option value="Ocupada" <?= $row['estado']=='Ocupada'?'selected':'' ?>>Ocupada</option>
                        <option value="Mantenimiento" <?= $row['estado']=='Mantenimiento'?'selected':'' ?>>Mantenimiento</option>
                      </select>
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
        </div>
    </div>

    <!-- Modal Agregar -->
    <div class="modal fade" id="modalAgregar" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <form method="post" class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i> Nueva Habitación</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="accion" value="agregar">
            <div class="mb-3">
              <label class="form-label">Número</label>
              <input type="text" name="numero" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Tipo</label>
              <select name="tipo" class="form-select" required>
                <option value="Individual">Individual</option>
                <option value="Doble">Doble</option>
                <option value="Suite">Suite</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Estado</label>
              <select name="estado" class="form-select" required>
                <option value="Disponible">Disponible</option>
                <option value="Ocupada">Ocupada</option>
                <option value="Mantenimiento">Mantenimiento</option>
              </select>
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
            <p>¿Está seguro que desea eliminar esta habitación?</p>
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
                const roomId = this.getAttribute('data-id');
                confirmDeleteBtn.href = `habitaciones_b.php?eliminar=${roomId}`;
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