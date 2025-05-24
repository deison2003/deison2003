<?php
// Conexión a la base de datos
$mysqli = new mysqli("localhost", "root", "", "hospital");
if ($mysqli->connect_errno) {
    die("Fallo la conexión: " . $mysqli->connect_error);
}

// Procesar inserción
if (isset($_POST['accion']) && $_POST['accion'] === 'agregar') {
    $id_paciente = $_POST['id_paciente'];
    $id_medico = $_POST['id_medico'];
    $fecha_hora = $_POST['fecha_hora'];
    $estado = $_POST['estado'];
    $motivo = $_POST['motivo'];
    $stmt = $mysqli->prepare("INSERT INTO citas (id_paciente, id_medico, fecha_hora, estado, motivo) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $id_paciente, $id_medico, $fecha_hora, $estado, $motivo);
    $stmt->execute();
    $stmt->close();
    header("Location: citas.php");
    exit;
}

// Procesar actualización
if (isset($_POST['accion']) && $_POST['accion'] === 'editar') {
    $id_cita = $_POST['id_cita'];
    $id_paciente = $_POST['id_paciente'];
    $id_medico = $_POST['id_medico'];
    $fecha_hora = $_POST['fecha_hora'];
    $estado = $_POST['estado'];
    $motivo = $_POST['motivo'];
    $stmt = $mysqli->prepare("UPDATE citas SET id_paciente=?, id_medico=?, fecha_hora=?, estado=?, motivo=? WHERE id_cita=?");
    $stmt->bind_param("iisssi", $id_paciente, $id_medico, $fecha_hora, $estado, $motivo, $id_cita);
    $stmt->execute();
    $stmt->close();
    header("Location: citas.php");
    exit;
}

// Procesar eliminación
if (isset($_GET['eliminar'])) {
    $id_cita = intval($_GET['eliminar']);
    $mysqli->query("DELETE FROM citas WHERE id_cita=$id_cita");
    header("Location: citas.php");
    exit;
}

// Obtener datos para edición si corresponde
$editar = false;
if (isset($_GET['editar'])) {
    $editar = true;
    $id_cita = intval($_GET['editar']);
    $res = $mysqli->query("SELECT c.*, p.nombre as paciente_nombre, p.apellido as paciente_apellido, 
                          m.nombre as medico_nombre, m.apellido as medico_apellido 
                          FROM citas c
                          LEFT JOIN pacientes p ON c.id_paciente = p.id_paciente
                          LEFT JOIN medicos m ON c.id_medico = m.id_medico
                          WHERE c.id_cita=$id_cita");
    $cita_editar = $res->fetch_assoc();
}

// Obtener todas las citas con información de pacientes y médicos
$citas_result = $mysqli->query("SELECT c.*, p.nombre as paciente_nombre, p.apellido as paciente_apellido, 
                         m.nombre as medico_nombre, m.apellido as medico_apellido,
                         e.nombre as especialidad
                         FROM citas c
                         LEFT JOIN pacientes p ON c.id_paciente = p.id_paciente
                         LEFT JOIN medicos m ON c.id_medico = m.id_medico
                         LEFT JOIN especialidades e ON m.id_especialidad = e.id_especialidad
                         ORDER BY c.fecha_hora DESC");
$citas = [];
while($row = $citas_result->fetch_assoc()) {
    $citas[] = $row;
}

// Obtener pacientes y médicos para selects
$pacientes = $mysqli->query("SELECT id_paciente, nombre, apellido FROM pacientes ORDER BY apellido, nombre");
$medicos = $mysqli->query("SELECT m.id_medico, m.nombre, m.apellido, e.nombre as especialidad 
                          FROM medicos m 
                          LEFT JOIN especialidades e ON m.id_especialidad = e.id_especialidad
                          ORDER BY m.apellido, m.nombre");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Citas | Hospital XYZ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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
            --hospital-pending: #ffd166;
            --hospital-confirmed: #06d6a0;
            --hospital-cancelled: #ef476f;
            --hospital-completed: #118ab2;
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
        
        .badge-status {
            padding: 0.35em 0.65em;
            border-radius: 50px;
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.75rem;
        }
        
        .badge-pending {
            background-color: rgba(255, 209, 102, 0.1);
            color: var(--hospital-warning);
        }
        
        .badge-confirmed {
            background-color: rgba(6, 214, 160, 0.1);
            color: var(--hospital-confirmed);
        }
        
        .badge-cancelled {
            background-color: rgba(239, 71, 111, 0.1);
            color: var(--hospital-cancelled);
        }
        
        .badge-completed {
            background-color: rgba(17, 138, 178, 0.1);
            color: var(--hospital-completed);
        }
        
        .action-btns .btn {
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            font-size: 0.85rem;
        }
        
        .doctor-info {
            display: flex;
            align-items: center;
        }
        
        .doctor-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--hospital-accent);
            color: var(--hospital-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-weight: bold;
        }
        
        .patient-info {
            display: flex;
            align-items: center;
        }
        
        .patient-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #e2e8f0;
            color: #4a5568;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-weight: bold;
        }
        
        .info-text {
            display: flex;
            flex-direction: column;
        }
        
        .info-text strong {
            font-weight: 600;
        }
        
        .info-text small {
            color: #718096;
            font-size: 0.8rem;
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
                <i class="bi bi-calendar-check"></i> Gestión de Citas Médicas
            </h1>
            <button class="btn btn-hospital-primary" data-bs-toggle="modal" data-bs-target="#modalAgregar">
                <i class="bi bi-plus-circle me-2"></i> Nueva Cita
            </button>
        </div>
        
        <div class="form-section">
            <h4 class="mb-4"><i class="bi bi-search me-2"></i> Buscar Citas</h4>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Fecha</label>
                    <input type="text" class="form-control datepicker" placeholder="Seleccionar fecha">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Estado</label>
                    <select class="form-select">
                        <option value="">Todos los estados</option>
                        <option value="Pendiente">Pendiente</option>
                        <option value="Confirmada">Confirmada</option>
                        <option value="Cancelada">Cancelada</option>
                        <option value="Completada">Completada</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Médico</label>
                    <select class="form-select">
                        <option value="">Todos los médicos</option>
                        <?php while($med = $medicos->fetch_assoc()): ?>
                        <option value="<?= $med['id_medico'] ?>">Dr. <?= htmlspecialchars($med['nombre']) ?> <?= htmlspecialchars($med['apellido']) ?> (<?= htmlspecialchars($med['especialidad']) ?>)</option>
                        <?php endwhile; ?>
                        <?php $medicos->data_seek(0); ?>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Paciente</th>
                        <th>Médico</th>
                        <th>Fecha y Hora</th>
                        <th>Estado</th>
                        <th>Motivo</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($citas as $row): ?>
                    <tr>
                        <td class="fw-bold">#<?= $row['id_cita'] ?></td>
                        <td>
                            <div class="patient-info">
                                <div class="patient-avatar">
                                    <?= substr($row['paciente_nombre'], 0, 1) ?>
                                </div>
                                <div class="info-text">
                                    <strong><?= htmlspecialchars($row['paciente_nombre']) ?> <?= htmlspecialchars($row['paciente_apellido']) ?></strong>
                                    <small>Paciente ID: <?= $row['id_paciente'] ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="doctor-info">
                                <div class="doctor-avatar">
                                    <?= substr($row['medico_nombre'], 0, 1) ?>
                                </div>
                                <div class="info-text">
                                    <strong>Dr. <?= htmlspecialchars($row['medico_nombre']) ?> <?= htmlspecialchars($row['medico_apellido']) ?></strong>
                                    <small><?= htmlspecialchars($row['especialidad']) ?></small>
                                </div>
                            </div>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($row['fecha_hora'])) ?></td>
                        <td>
                            <?php 
                            $badgeClass = '';
                            switch($row['estado']) {
                                case 'Pendiente': $badgeClass = 'badge-pending'; break;
                                case 'Confirmada': $badgeClass = 'badge-confirmed'; break;
                                case 'Cancelada': $badgeClass = 'badge-cancelled'; break;
                                case 'Completada': $badgeClass = 'badge-completed'; break;
                            }
                            ?>
                            <span class="badge badge-status <?= $badgeClass ?>"><?= $row['estado'] ?></span>
                        </td>
                        <td><?= htmlspecialchars($row['motivo']) ?></td>
                        <td class="text-center action-btns">
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalEditar<?= $row['id_cita'] ?>" title="Editar">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button class="btn btn-sm btn-danger delete-btn" data-id="<?= $row['id_cita'] ?>" title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modales de edición fuera de la tabla -->
    <?php foreach($citas as $row): ?>
    <div class="modal fade" id="modalEditar<?= $row['id_cita'] ?>" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <form method="post" class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i> Editar Cita</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="accion" value="editar">
            <input type="hidden" name="id_cita" value="<?= $row['id_cita'] ?>">
            <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Paciente</label>
                  <select name="id_paciente" class="form-select" required>
                    <?php
                    // Consulta independiente para cada modal
                    $pacientes_modal = $mysqli->query("SELECT id_paciente, nombre, apellido FROM pacientes ORDER BY apellido, nombre");
                    while($pac = $pacientes_modal->fetch_assoc()): ?>
                    <option value="<?= $pac['id_paciente'] ?>" <?= $pac['id_paciente'] == $row['id_paciente'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($pac['nombre']) ?> <?= htmlspecialchars($pac['apellido']) ?> (ID: <?= $pac['id_paciente'] ?>)
                    </option>
                    <?php endwhile; ?>
                  </select>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Médico</label>
                  <select name="id_medico" class="form-select" required>
                    <?php
                    // Consulta independiente para cada modal
                    $medicos_modal = $mysqli->query("SELECT m.id_medico, m.nombre, m.apellido, e.nombre as especialidad 
                          FROM medicos m 
                          LEFT JOIN especialidades e ON m.id_especialidad = e.id_especialidad
                          ORDER BY m.apellido, m.nombre");
                    while($med = $medicos_modal->fetch_assoc()): ?>
                    <option value="<?= $med['id_medico'] ?>" <?= $med['id_medico'] == $row['id_medico'] ? 'selected' : '' ?>>
                        Dr. <?= htmlspecialchars($med['nombre']) ?> <?= htmlspecialchars($med['apellido']) ?> (<?= htmlspecialchars($med['especialidad']) ?>)
                    </option>
                    <?php endwhile; ?>
                  </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Fecha y Hora</label>
                  <input type="datetime-local" name="fecha_hora" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($row['fecha_hora'])) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Estado</label>
                  <select name="estado" class="form-select" required>
                    <?php
                    $estados = ['Pendiente'=>'Pendiente','Confirmada'=>'Confirmada','Cancelada'=>'Cancelada','Completada'=>'Completada'];
                    foreach ($estados as $key => $value) {
                        $sel = ($row['estado'] == $key) ? 'selected' : '';
                        echo "<option value='$key' $sel>$value</option>";
                    }
                    ?>
                  </select>
                </div>
            </div>
            <div class="mb-3">
              <label class="form-label">Motivo</label>
              <textarea name="motivo" class="form-control" rows="3"><?= htmlspecialchars($row['motivo']) ?></textarea>
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
    <?php endforeach; ?>

    <!-- Modal Agregar -->
    <div class="modal fade" id="modalAgregar" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <form method="post" class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title"><i class="bi bi-calendar-plus me-2"></i> Nueva Cita Médica</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="accion" value="agregar">
            <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Paciente</label>
                  <select name="id_paciente" class="form-select" required>
                    <option value="" selected disabled>Seleccionar paciente</option>
                    <?php while($pac = $pacientes->fetch_assoc()): ?>
                    <option value="<?= $pac['id_paciente'] ?>">
                        <?= htmlspecialchars($pac['nombre']) ?> <?= htmlspecialchars($pac['apellido']) ?> (ID: <?= $pac['id_paciente'] ?>)
                    </option>
                    <?php endwhile; ?>
                    <?php $pacientes->data_seek(0); ?>
                  </select>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Médico</label>
                  <select name="id_medico" class="form-select" required>
                    <option value="" selected disabled>Seleccionar médico</option>
                    <?php while($med = $medicos->fetch_assoc()): ?>
                    <option value="<?= $med['id_medico'] ?>">
                        Dr. <?= htmlspecialchars($med['nombre']) ?> <?= htmlspecialchars($med['apellido']) ?> (<?= htmlspecialchars($med['especialidad']) ?>)
                    </option>
                    <?php endwhile; ?>
                    <?php $medicos->data_seek(0); ?>
                  </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Fecha y Hora</label>
                  <input type="datetime-local" name="fecha_hora" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Estado</label>
                  <select name="estado" class="form-select" required>
                    <option value="Pendiente" selected>Pendiente</option>
                    <option value="Confirmada">Confirmada</option>
                    <option value="Cancelada">Cancelada</option>
                    <option value="Completada">Completada</option>
                  </select>
                </div>
            </div>
            <div class="mb-3">
              <label class="form-label">Motivo</label>
              <textarea name="motivo" class="form-control" rows="3" placeholder="Describa el motivo de la cita"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-success">
                <i class="bi bi-plus-circle me-2"></i> Agregar Cita
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
            <p>¿Está seguro que desea eliminar esta cita?</p>
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
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
    <script>
    // Confirmación de eliminación con modal
    document.addEventListener('DOMContentLoaded', function() {
        const deleteButtons = document.querySelectorAll('.delete-btn');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        const confirmDeleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
        
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const citaId = this.getAttribute('data-id');
                confirmDeleteBtn.href = `citas.php?eliminar=${citaId}`;
                confirmDeleteModal.show();
            });
        });
        
        // Inicializar tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Inicializar datepicker
        flatpickr(".datepicker", {
            locale: "es",
            dateFormat: "d/m/Y",
            allowInput: true
        });
    });
    </script>
</body>
</html>