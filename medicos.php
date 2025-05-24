<?php
// Conexión a la base de datos
$host = "localhost";
$user = "root";
$pass = "";
$db = "hospital";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Procesar eliminación
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $conn->query("DELETE FROM medicos WHERE id_medico=$id");
    header("Location: medicos.php");
    exit;
}

// Procesar edición
if (isset($_POST['editar'])) {
    $id = intval($_POST['id_medico']);
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $apellido = $conn->real_escape_string($_POST['apellido']);
    $telefono = $conn->real_escape_string($_POST['telefono']);
    $correo = $conn->real_escape_string($_POST['correo_electronico']);
    $especialidad = intval($_POST['id_especialidad']);
    $conn->query("UPDATE medicos SET nombre='$nombre', apellido='$apellido', telefono='$telefono', correo_electronico='$correo', id_especialidad=$especialidad WHERE id_medico=$id");
    header("Location: medicos.php");
    exit;
}

// Procesar inserción
if (isset($_POST['agregar'])) {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $apellido = $conn->real_escape_string($_POST['apellido']);
    $telefono = $conn->real_escape_string($_POST['telefono']);
    $correo = $conn->real_escape_string($_POST['correo_electronico']);
    $especialidad = intval($_POST['id_especialidad']);
    $conn->query("INSERT INTO medicos (nombre, apellido, telefono, correo_electronico, id_especialidad) VALUES ('$nombre', '$apellido', '$telefono', '$correo', $especialidad)");
    header("Location: medicos.php");
    exit;
}

// Obtener médicos con información de especialidad
$medicos = $conn->query("SELECT m.*, e.nombre as nombre_especialidad FROM medicos m LEFT JOIN especialidades e ON m.id_especialidad = e.id_especialidad ORDER BY m.id_medico ASC");

// Obtener especialidades para selects
$especialidades = $conn->query("SELECT * FROM especialidades ORDER BY nombre ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Médicos | Hospital XYZ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
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
        }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .hospital-container {
            max-width: 1200px;
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
        
        .table thead {
            background: linear-gradient(135deg, var(--hospital-primary) 0%, var(--hospital-secondary) 100%);
            color: white;
        }
        
        .table th {
            font-weight: 600;
            vertical-align: middle;
        }
        
        .table td {
            vertical-align: middle;
        }
        
        .badge-especialidad {
            background-color: rgba(10, 147, 150, 0.1);
            color: var(--hospital-secondary);
            padding: 0.35em 0.65em;
            border-radius: 50px;
            font-weight: 500;
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
        
        .doctor-card {
            border-left: 4px solid var(--hospital-primary);
            transition: all 0.3s ease;
        }
        
        .doctor-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
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
                <i class="bi bi-people-fill"></i> Gestión de Médicos
            </h1>
            <button class="btn btn-hospital-primary" data-bs-toggle="modal" data-bs-target="#modalAgregar">
                <i class="bi bi-plus-circle me-2"></i> Nuevo Médico
            </button>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Contacto</th>
                        <th>Especialidad</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php while($row = $medicos->fetch_assoc()): ?>
                    <tr>
                        <td class="fw-bold">#<?= $row['id_medico'] ?></td>
                        <td><?= htmlspecialchars($row['nombre']) ?></td>
                        <td><?= htmlspecialchars($row['apellido']) ?></td>
                        <td>
                            <div class="d-flex flex-column">
                                <small><i class="bi bi-telephone me-2"></i><?= htmlspecialchars($row['telefono']) ?></small>
                                <small><i class="bi bi-envelope me-2"></i><?= htmlspecialchars($row['correo_electronico']) ?></small>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-especialidad">
                                <?= isset($row['nombre_especialidad']) ? htmlspecialchars($row['nombre_especialidad']) : 'Sin especialidad' ?>
                            </span>
                        </td>
                        <td class="text-center action-btns">
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalEditar<?= $row['id_medico'] ?>" title="Editar">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button class="btn btn-sm btn-danger delete-btn" data-id="<?= $row['id_medico'] ?>" title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                    
                    <!-- Modal Editar -->
                    <div class="modal fade" id="modalEditar<?= $row['id_medico'] ?>" tabindex="-1" aria-hidden="true">
                      <div class="modal-dialog modal-lg">
                        <form method="post" class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i> Editar Médico</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                            <input type="hidden" name="id_medico" value="<?= $row['id_medico'] ?>">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                  <label class="form-label">Nombre</label>
                                  <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($row['nombre']) ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                  <label class="form-label">Apellido</label>
                                  <input type="text" name="apellido" class="form-control" value="<?= htmlspecialchars($row['apellido']) ?>" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                  <label class="form-label">Teléfono</label>
                                  <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($row['telefono']) ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                  <label class="form-label">Correo electrónico</label>
                                  <input type="email" name="correo_electronico" class="form-control" value="<?= htmlspecialchars($row['correo_electronico']) ?>">
                                </div>
                            </div>
                            <div class="mb-3">
                              <label class="form-label">Especialidad</label>
                              <select name="id_especialidad" class="form-select" required>
                                <?php while($esp = $especialidades->fetch_assoc()): ?>
                                <option value="<?= $esp['id_especialidad'] ?>" <?= $esp['id_especialidad'] == $row['id_especialidad'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($esp['nombre']) ?>
                                </option>
                                <?php endwhile; ?>
                                <?php $especialidades->data_seek(0); // Resetear el puntero para usarlo de nuevo ?>
                              </select>
                            </div>
                          </div>
                          <div class="modal-footer">
                            <button type="submit" name="editar" class="btn btn-primary">
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

    <!-- Modal Agregar -->
    <div class="modal fade" id="modalAgregar" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <form method="post" class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i> Nuevo Médico</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Nombre</label>
                  <input type="text" name="nombre" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Apellido</label>
                  <input type="text" name="apellido" class="form-control" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Teléfono</label>
                  <input type="text" name="telefono" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Correo electrónico</label>
                  <input type="email" name="correo_electronico" class="form-control">
                </div>
            </div>
            <div class="mb-3">
              <label class="form-label">Especialidad</label>
              <select name="id_especialidad" class="form-select" required>
                <option value="" selected disabled>Seleccione una especialidad</option>
                <?php while($esp = $especialidades->fetch_assoc()): ?>
                <option value="<?= $esp['id_especialidad'] ?>"><?= htmlspecialchars($esp['nombre']) ?></option>
                <?php endwhile; ?>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" name="agregar" class="btn btn-success">
                <i class="bi bi-plus-circle me-2"></i> Agregar Médico
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
            <p>¿Está seguro que desea eliminar este médico?</p>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Confirmación de eliminación con modal
    document.addEventListener('DOMContentLoaded', function() {
        const deleteButtons = document.querySelectorAll('.delete-btn');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        const confirmDeleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
        
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const doctorId = this.getAttribute('data-id');
                confirmDeleteBtn.href = `medicos.php?eliminar=${doctorId}`;
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