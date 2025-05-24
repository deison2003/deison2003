<?php 
include 'includes/header.php';
include 'includes/db.php';

// Obtener lista de pacientes con paginación
$records_per_page = 10;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

$sql = "SELECT * FROM pacientes ORDER BY apellido, nombre LIMIT $offset, $records_per_page";
$result = $conn->query($sql);

// Contar total de pacientes para paginación
$total_pages_sql = "SELECT COUNT(*) FROM pacientes";
$total_pages_result = $conn->query($total_pages_sql);
$total_rows = $total_pages_result->fetch_row()[0];
$total_pages = ceil($total_rows / $records_per_page);
?>

<div class="hospital-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="page-title"><i class="fas fa-user-injured me-2"></i>Gestión de Pacientes</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Pacientes</li>
                </ol>
            </nav>
        </div>
        <button class="btn btn-hospital-primary" data-bs-toggle="modal" data-bs-target="#nuevoPacienteModal">
            <i class="fas fa-plus-circle me-2"></i>Nuevo Paciente
        </button>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="search-box">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" id="searchInput" placeholder="Buscar pacientes...">
                        <button class="btn btn-outline-secondary" type="button" id="clearSearch">Limpiar</button>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="dropdown me-2">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown">
                            <i class="fas fa-filter me-1"></i>Filtrar
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                            <li><a class="dropdown-item" href="#" data-filter="all">Todos</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#" data-filter="Masculino">Masculino</a></li>
                            <li><a class="dropdown-item" href="#" data-filter="Femenino">Femenino</a></li>
                            <li><a class="dropdown-item" href="#" data-filter="Otro">Otro</a></li>
                        </ul>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown">
                            <i class="fas fa-download me-1"></i>Exportar
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-file-excel me-2"></i>Excel</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-file-pdf me-2"></i>PDF</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-file-csv me-2"></i>CSV</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle" id="pacientesTable">
                    <thead class="table-light">
                        <tr>
                            <th width="80px">ID <button class="sort-btn" data-sort="id"><i class="fas fa-sort"></i></button></th>
                            <th>Nombre <button class="sort-btn" data-sort="nombre"><i class="fas fa-sort"></i></button></th>
                            <th>Apellido <button class="sort-btn" data-sort="apellido"><i class="fas fa-sort"></i></button></th>
                            <th>Edad <button class="sort-btn" data-sort="edad"><i class="fas fa-sort"></i></button></th>
                            <th>Género</th>
                            <th>Contacto</th>
                            <th>Documento</th>
                            <th width="150px" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): 
                            $fecha_nac = new DateTime($row['fecha_nacimiento']);
                            $hoy = new DateTime();
                            $edad = $hoy->diff($fecha_nac)->y;
                        ?>
                        <tr data-gender="<?php echo $row['genero']; ?>">
                            <td class="fw-bold"><?php echo $row['id_paciente']; ?></td>
                            <td><?php echo $row['nombre']; ?></td>
                            <td><?php echo $row['apellido']; ?></td>
                            <td><?php echo $edad; ?> años</td>
                            <td>
                                <span class="badge gender-badge <?php echo strtolower($row['genero']); ?>">
                                    <?php echo $row['genero']; ?>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <small><i class="fas fa-phone me-2"></i><?php echo $row['telefono']; ?></small>
                                    <small><i class="fas fa-envelope me-2"></i><?php echo $row['correo_electronico']; ?></small>
                                </div>
                            </td>
                            <td>
                                <small><?php echo $row['tipo_documento']; ?>: <?php echo $row['numero_documento']; ?></small>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="editar_paciente.php?id=<?php echo $row['id_paciente']; ?>" class="btn btn-sm btn-hospital-edit" data-bs-toggle="tooltip" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="historial_paciente.php?id=<?php echo $row['id_paciente']; ?>" class="btn btn-sm btn-hospital-info" data-bs-toggle="tooltip" title="Historial">
                                        <i class="fas fa-file-medical"></i>
                                    </a>
                                    <button class="btn btn-sm btn-hospital-delete delete-patient" data-id="<?php echo $row['id_paciente']; ?>" data-bs-toggle="tooltip" title="Eliminar">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <?php if($total_pages > 1): ?>
            <nav aria-label="Page navigation" class="mt-4">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $page-1; ?>">Anterior</a>
                    </li>
                    <?php for($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                    <?php endfor; ?>
                    <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $page+1; ?>">Siguiente</a>
                    </li>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal para nuevo paciente -->
<div class="modal fade" id="nuevoPacienteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-hospital-primary text-white">
                <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Nuevo Paciente</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="patientForm" action="guardar_paciente.php" method="post">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre(s) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nombre" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Apellido(s) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="apellido" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha de Nacimiento <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="fecha_nacimiento" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Género <span class="text-danger">*</span></label>
                            <select class="form-select" name="genero" required>
                                <option value="" selected disabled>Seleccionar...</option>
                                <option value="Masculino">Masculino</option>
                                <option value="Femenino">Femenino</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Dirección</label>
                        <input type="text" class="form-control" name="direccion">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" name="telefono">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" name="correo_electronico">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tipo de Documento <span class="text-danger">*</span></label>
                            <select class="form-select" name="tipo_documento" required>
                                <option value="" selected disabled>Seleccionar...</option>
                                <option value="CC">Cédula de Ciudadanía</option>
                                <option value="TI">Tarjeta de Identidad</option>
                                <option value="CE">Cédula de Extranjería</option>
                                <option value="Pasaporte">Pasaporte</option>
                                <option value="RC">Registro Civil</option>
                            </select>
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Número de Documento <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="numero_documento" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Grupo Sanguíneo</label>
                            <select class="form-select" name="grupo_sanguineo">
                                <option value="" selected disabled>Seleccionar...</option>
                                <option value="A+">A+</option>
                                <option value="A-">A-</option>
                                <option value="B+">B+</option>
                                <option value="B-">B-</option>
                                <option value="AB+">AB+</option>
                                <option value="AB-">AB-</option>
                                <option value="O+">O+</option>
                                <option value="O-">O-</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Alergias Conocidas</label>
                            <input type="text" class="form-control" name="alergias" placeholder="Ej: Penicilina, mariscos...">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Observaciones</label>
                        <textarea class="form-control" name="observaciones" rows="2"></textarea>
                    </div>
                    
                    <div class="d-flex justify-content-end mt-4">
                        <button type="button" class="btn btn-outline-secondary me-3" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-hospital-primary">
                            <i class="fas fa-save me-2"></i>Guardar Paciente
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación para eliminar -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-hospital-danger text-white">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>Confirmar Eliminación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro que desea eliminar este paciente? Esta acción no se puede deshacer.</p>
                <p class="fw-bold">Todos los registros asociados (citas, historial) también serán eliminados.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a href="#" id="confirmDeleteBtn" class="btn btn-hospital-danger">
                    <i class="fas fa-trash-alt me-2"></i>Eliminar
                </a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<style>
/* Estilos personalizados para el módulo de pacientes */
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

.hospital-container {
    padding: 2rem;
    max-width: 1400px;
    margin: 0 auto;
}

.page-title {
    color: var(--hospital-dark);
    font-weight: 700;
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.breadcrumb {
    background: transparent;
    padding: 0;
    font-size: 0.9rem;
}

.btn-hospital-primary {
    background-color: var(--hospital-primary);
    color: white;
    border: none;
    padding: 0.5rem 1.5rem;
    border-radius: 50px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-hospital-primary:hover {
    background-color: var(--hospital-secondary);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.btn-hospital-edit {
    background-color: var(--hospital-warning);
    color: white;
}

.btn-hospital-info {
    background-color: var(--hospital-secondary);
    color: white;
}

.btn-hospital-delete {
    background-color: var(--hospital-danger);
    color: white;
}

.btn-hospital-danger {
    background-color: var(--hospital-danger);
    color: white;
    border: none;
}

.card {
    border: none;
    border-radius: 10px;
    overflow: hidden;
}

.search-box .input-group {
    width: 300px;
    border-radius: 50px;
    overflow: hidden;
}

.search-box .input-group-text {
    border-right: none;
}

.search-box .form-control {
    border-left: none;
}

.table {
    margin-bottom: 0;
}

.table th {
    font-weight: 600;
    color: var(--hospital-dark);
    border-bottom: 2px solid var(--hospital-accent);
    vertical-align: middle;
}

.table td {
    vertical-align: middle;
}

.sort-btn {
    background: none;
    border: none;
    padding: 0;
    margin-left: 5px;
    color: var(--hospital-secondary);
    cursor: pointer;
}

.badge.gender-badge {
    padding: 0.35em 0.65em;
    font-weight: 500;
    border-radius: 50px;
    text-transform: capitalize;
}

.badge.masculino {
    background-color: rgba(0, 95, 115, 0.1);
    color: var(--hospital-primary);
}

.badge.femenino {
    background-color: rgba(234, 67, 53, 0.1);
    color: #ea4335;
}

.badge.otro {
    background-color: rgba(238, 155, 0, 0.1);
    color: var(--hospital-warning);
}

.pagination .page-item.active .page-link {
    background-color: var(--hospital-primary);
    border-color: var(--hospital-primary);
}

.pagination .page-link {
    color: var(--hospital-primary);
}

.modal-header {
    padding: 1rem 1.5rem;
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

@media (max-width: 768px) {
    .hospital-container {
        padding: 1rem;
    }
    
    .search-box .input-group {
        width: 100%;
        margin-bottom: 1rem;
    }
    
    .d-flex.justify-content-between {
        flex-direction: column;
        align-items: flex-start !important;
    }
    
    .btn-group {
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .btn-group .btn {
        margin-bottom: 5px;
    }
}
</style>

<script>
$(document).ready(function() {
    // Inicializar tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    // Filtrado por búsqueda
    $('#searchInput').keyup(function() {
        const value = $(this).val().toLowerCase();
        $('#pacientesTable tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
    
    // Limpiar búsqueda
    $('#clearSearch').click(function() {
        $('#searchInput').val('');
        $('#pacientesTable tbody tr').show();
    });
    
    // Filtrado por género
    $('[data-filter]').click(function(e) {
        e.preventDefault();
        const gender = $(this).data('filter');
        
        if(gender === 'all') {
            $('#pacientesTable tbody tr').show();
        } else {
            $('#pacientesTable tbody tr').hide();
            $(`#pacientesTable tbody tr[data-gender="${gender}"]`).show();
        }
    });
    
    // Confirmación para eliminar
    $('.delete-patient').click(function() {
        const patientId = $(this).data('id');
        $('#confirmDeleteBtn').attr('href', 'eliminar_paciente.php?id=' + patientId);
        $('#confirmDeleteModal').modal('show');
    });
    
    // Ordenar tabla
    $('.sort-btn').click(function() {
        const column = $(this).data('sort');
        const table = $('#pacientesTable');
        const rows = table.find('tbody > tr').get();
        
        rows.sort(function(a, b) {
            const A = $(a).find('td').eq(getIndex(column)).text().toUpperCase();
            const B = $(b).find('td').eq(getIndex(column)).text().toUpperCase();
            
            if(A < B) return -1;
            if(A > B) return 1;
            return 0;
        });
        
        function getIndex(column) {
            const headers = table.find('th');
            for(let i = 0; i < headers.length; i++) {
                if($(headers[i]).text().includes(column)) return i;
            }
            return 0;
        }
        
        $.each(rows, function(index, row) {
            table.children('tbody').append(row);
        });
    });
});
</script>