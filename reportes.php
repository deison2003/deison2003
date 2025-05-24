<?php
$mysqli = new mysqli("localhost", "root", "", "hospital");
if ($mysqli->connect_errno) {
    die("Fallo la conexión: " . $mysqli->connect_error);
}

// Estadísticas generales
$total_pacientes = $mysqli->query("SELECT COUNT(*) FROM pacientes")->fetch_row()[0];
$total_medicos = $mysqli->query("SELECT COUNT(*) FROM medicos")->fetch_row()[0];
$total_citas = $mysqli->query("SELECT COUNT(*) FROM citas")->fetch_row()[0];
$total_habitaciones = $mysqli->query("SELECT COUNT(*) FROM habitaciones")->fetch_row()[0];
$total_medicamentos = $mysqli->query("SELECT COUNT(*) FROM medicamentos")->fetch_row()[0];

// Citas por estado
$citas_estado = [];
$res = $mysqli->query("SELECT estado, COUNT(*) as total FROM citas GROUP BY estado");
while($row = $res->fetch_assoc()) $citas_estado[$row['estado']] = $row['total'];

// Habitaciones por estado
$habitaciones_estado = [];
$res = $mysqli->query("SELECT estado, COUNT(*) as total FROM habitaciones GROUP BY estado");
while($row = $res->fetch_assoc()) $habitaciones_estado[$row['estado']] = $row['total'];

// Medicamentos con bajo stock
$medicamentos_bajo_stock = $mysqli->query("SELECT nombre, stock FROM medicamentos WHERE stock < 10 ORDER BY stock ASC");

// Pacientes por género
$pacientes_genero = [];
$res = $mysqli->query("SELECT genero, COUNT(*) as total FROM pacientes GROUP BY genero");
while($row = $res->fetch_assoc()) $pacientes_genero[$row['genero']] = $row['total'];

// Citas por especialidad
$citas_especialidad = [];
$res = $mysqli->query("SELECT e.nombre as especialidad, COUNT(*) as total FROM citas c JOIN medicos m ON c.id_medico = m.id_medico JOIN especialidades e ON m.id_especialidad = e.id_especialidad GROUP BY e.nombre");
while($row = $res->fetch_assoc()) $citas_especialidad[$row['especialidad']] = $row['total'];

// Últimas 5 citas
$ultimas_citas = $mysqli->query("SELECT c.id_cita, p.nombre as paciente, m.nombre as medico, c.fecha_hora, c.estado 
                                FROM citas c 
                                JOIN pacientes p ON c.id_paciente = p.id_paciente 
                                JOIN medicos m ON c.id_medico = m.id_medico 
                                ORDER BY c.fecha_hora DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Hospitalario | Hospital XYZ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
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
            max-width: 1800px;
            margin: 2rem auto;
            padding: 2rem;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
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
        
        .stat-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            height: 100%;
            overflow: hidden;
            position: relative;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }
        
        .stat-card .card-body {
            padding: 1.5rem;
            position: relative;
            z-index: 1;
        }
        
        .stat-card .card-icon {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 3rem;
            opacity: 0.2;
            z-index: 0;
        }
        
        .stat-card .card-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: #6c757d;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .stat-card .card-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--hospital-dark);
            margin-bottom: 0.5rem;
        }
        
        .stat-card .card-change {
            font-size: 0.8rem;
            display: flex;
            align-items: center;
        }
        
        .stat-card .card-change.up {
            color: var(--hospital-success);
        }
        
        .stat-card .card-change.down {
            color: var(--hospital-danger);
        }
        
        .chart-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            height: 100%;
        }
        
        .chart-card .card-header {
            background: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            font-weight: 600;
            padding: 1rem 1.5rem;
        }
        
        .chart-card .card-body {
            padding: 1.5rem;
        }
        
        .recent-activity {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
            height: 100%;
        }
        
        .recent-activity .activity-item {
            display: flex;
            padding: 0.75rem 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .recent-activity .activity-item:last-child {
            border-bottom: none;
        }
        
        .recent-activity .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(10, 147, 150, 0.1);
            color: var(--hospital-secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            flex-shrink: 0;
        }
        
        .recent-activity .activity-content {
            flex-grow: 1;
        }
        
        .recent-activity .activity-title {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .recent-activity .activity-time {
            font-size: 0.8rem;
            color: #6c757d;
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
        
        .low-stock-table {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
        }
        
        .low-stock-table .table {
            margin-bottom: 0;
        }
        
        .low-stock-table .table th {
            border-top: none;
            font-weight: 600;
            color: #6c757d;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 1px;
        }
        
        .low-stock-table .table td {
            vertical-align: middle;
        }
        
        .stock-indicator {
            width: 100%;
            height: 6px;
            background: #e9ecef;
            border-radius: 3px;
            overflow: hidden;
        }
        
        .stock-indicator .stock-level {
            height: 100%;
            background: var(--hospital-danger);
        }
        
        @media (max-width: 768px) {
            .hospital-container {
                padding: 1rem;
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
                <i class="bi bi-speedometer2"></i> Dashboard Hospitalario
            </h1>
            <div class="d-flex">
                <div class="dropdown me-2">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="reportRangeDropdown" data-bs-toggle="dropdown">
                        <i class="bi bi-calendar-week me-2"></i> Últimos 30 días
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="reportRangeDropdown">
                        <li><a class="dropdown-item" href="#">Hoy</a></li>
                        <li><a class="dropdown-item" href="#">Últimos 7 días</a></li>
                        <li><a class="dropdown-item" href="#">Últimos 30 días</a></li>
                        <li><a class="dropdown-item" href="#">Este mes</a></li>
                        <li><a class="dropdown-item" href="#">Este año</a></li>
                    </ul>
                </div>
                <button class="btn btn-primary">
                    <i class="bi bi-download me-2"></i> Exportar
                </button>
            </div>
        </div>
        
        <div class="row g-4 mb-4">
            <div class="col-xl-2 col-lg-4 col-md-6">
                <div class="stat-card bg-white">
                    <div class="card-body">
                        <i class="bi bi-people card-icon"></i>
                        <h6 class="card-title">Pacientes</h6>
                        <div class="card-value"><?= number_format($total_pacientes) ?></div>
                        <div class="card-change up">
                            <i class="bi bi-arrow-up-circle-fill me-1"></i> 12% vs mes anterior
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-4 col-md-6">
                <div class="stat-card bg-white">
                    <div class="card-body">
                        <i class="bi bi-person-badge card-icon"></i>
                        <h6 class="card-title">Médicos</h6>
                        <div class="card-value"><?= number_format($total_medicos) ?></div>
                        <div class="card-change up">
                            <i class="bi bi-arrow-up-circle-fill me-1"></i> 5% vs mes anterior
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-4 col-md-6">
                <div class="stat-card bg-white">
                    <div class="card-body">
                        <i class="bi bi-calendar-check card-icon"></i>
                        <h6 class="card-title">Citas</h6>
                        <div class="card-value"><?= number_format($total_citas) ?></div>
                        <div class="card-change up">
                            <i class="bi bi-arrow-up-circle-fill me-1"></i> 18% vs mes anterior
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-4 col-md-6">
                <div class="stat-card bg-white">
                    <div class="card-body">
                        <i class="bi bi-door-closed card-icon"></i>
                        <h6 class="card-title">Habitaciones</h6>
                        <div class="card-value"><?= number_format($total_habitaciones) ?></div>
                        <div class="card-change down">
                            <i class="bi bi-arrow-down-circle-fill me-1"></i> 3% ocupadas
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-4 col-md-6">
                <div class="stat-card bg-white">
                    <div class="card-body">
                        <i class="bi bi-capsule card-icon"></i>
                        <h6 class="card-title">Medicamentos</h6>
                        <div class="card-value"><?= number_format($total_medicamentos) ?></div>
                        <div class="card-change">
                            <i class="bi bi-dash-circle-fill me-1"></i> Sin cambios
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-4 col-md-6">
                <div class="stat-card bg-white">
                    <div class="card-body">
                        <i class="bi bi-exclamation-triangle card-icon text-warning"></i>
                        <h6 class="card-title">Bajo Stock</h6>
                        <div class="card-value"><?= $medicamentos_bajo_stock->num_rows ?></div>
                        <div class="card-change down">
                            <i class="bi bi-arrow-down-circle-fill me-1"></i> Revisar inventario
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row g-4 mb-4">
            <div class="col-xl-6 col-lg-12">
                <div class="chart-card card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-calendar-check me-2"></i> Citas por Estado</span>
                        <select class="form-select form-select-sm w-auto">
                            <option>Últimos 30 días</option>
                            <option>Este mes</option>
                            <option>Este año</option>
                        </select>
                    </div>
                    <div class="card-body">
                        <canvas id="citasEstadoChart" height="300"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-12">
                <div class="chart-card card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-door-closed me-2"></i> Habitaciones por Estado</span>
                        <select class="form-select form-select-sm w-auto">
                            <option>Estado actual</option>
                            <option>Histórico</option>
                        </select>
                    </div>
                    <div class="card-body">
                        <canvas id="habitacionesEstadoChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row g-4 mb-4">
            <div class="col-xl-4 col-lg-6">
                <div class="chart-card card h-100">
                    <div class="card-header">
                        <i class="bi bi-gender-ambiguous me-2"></i> Pacientes por Género
                    </div>
                    <div class="card-body">
                        <canvas id="pacientesGeneroChart" height="250"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-6">
                <div class="chart-card card h-100">
                    <div class="card-header">
                        <i class="bi bi-clipboard2-pulse me-2"></i> Citas por Especialidad
                    </div>
                    <div class="card-body">
                        <canvas id="citasEspecialidadChart" height="250"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-12">
                <div class="recent-activity">
                    <h5 class="mb-4"><i class="bi bi-clock-history me-2"></i> Últimas Citas</h5>
                    <?php while($row = $ultimas_citas->fetch_assoc()): 
                        $badgeClass = '';
                        switch($row['estado']) {
                            case 'Pendiente': $badgeClass = 'badge-pending'; break;
                            case 'Confirmada': $badgeClass = 'badge-confirmed'; break;
                            case 'Cancelada': $badgeClass = 'badge-cancelled'; break;
                            case 'Completada': $badgeClass = 'badge-completed'; break;
                        }
                    ?>
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <div class="activity-content">
                            <div class="d-flex justify-content-between">
                                <h6 class="activity-title"><?= htmlspecialchars($row['paciente']) ?></h6>
                                <span class="badge-status <?= $badgeClass ?>"><?= $row['estado'] ?></span>
                            </div>
                            <p class="mb-1">Dr. <?= htmlspecialchars($row['medico']) ?></p>
                            <div class="d-flex justify-content-between">
                                <small class="activity-time"><?= date('d/m/Y H:i', strtotime($row['fecha_hora'])) ?></small>
                                <small>ID: <?= $row['id_cita'] ?></small>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
        
        <div class="low-stock-table mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5><i class="bi bi-exclamation-triangle-fill text-danger me-2"></i> Medicamentos con Bajo Stock (&lt; 10 unidades)</h5>
                <a href="medicamentos.php" class="btn btn-sm btn-outline-primary">Ver todos</a>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Medicamento</th>
                            <th>Stock</th>
                            <th>Nivel</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $medicamentos_bajo_stock->data_seek(0); ?>
                        <?php while($row = $medicamentos_bajo_stock->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['nombre']) ?></td>
                            <td><?= $row['stock'] ?></td>
                            <td>
                                <div class="stock-indicator">
                                    <div class="stock-level" style="width: <?= ($row['stock']/10)*100 ?>%"></div>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Citas por estado
        new Chart(document.getElementById('citasEstadoChart'), {
            type: 'doughnut',
            data: {
                labels: <?= json_encode(array_keys($citas_estado)) ?>,
                datasets: [{
                    data: <?= json_encode(array_values($citas_estado)) ?>,
                    backgroundColor: [
                        'rgba(255, 209, 102, 0.8)',
                        'rgba(6, 214, 160, 0.8)',
                        'rgba(239, 71, 111, 0.8)',
                        'rgba(17, 138, 178, 0.8)'
                    ],
                    borderColor: [
                        'rgba(255, 209, 102, 1)',
                        'rgba(6, 214, 160, 1)',
                        'rgba(239, 71, 111, 1)',
                        'rgba(17, 138, 178, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                plugins: {
                    legend: {
                        position: 'right',
                    }
                },
                cutout: '70%',
                maintainAspectRatio: false
            }
        });

        // Habitaciones por estado
        new Chart(document.getElementById('habitacionesEstadoChart'), {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_keys($habitaciones_estado)) ?>,
                datasets: [{
                    label: 'Habitaciones',
                    data: <?= json_encode(array_values($habitaciones_estado)) ?>,
                    backgroundColor: [
                        'rgba(6, 214, 160, 0.8)',
                        'rgba(239, 71, 111, 0.8)',
                        'rgba(255, 209, 102, 0.8)'
                    ],
                    borderColor: [
                        'rgba(6, 214, 160, 1)',
                        'rgba(239, 71, 111, 1)',
                        'rgba(255, 209, 102, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                },
                maintainAspectRatio: false
            }
        });

        // Pacientes por género
        new Chart(document.getElementById('pacientesGeneroChart'), {
            type: 'pie',
            data: {
                labels: <?= json_encode(array_keys($pacientes_genero)) ?>,
                datasets: [{
                    data: <?= json_encode(array_values($pacientes_genero)) ?>,
                    backgroundColor: [
                        'rgba(17, 138, 178, 0.8)',
                        'rgba(239, 71, 111, 0.8)',
                        'rgba(255, 209, 102, 0.8)'
                    ],
                    borderColor: [
                        'rgba(17, 138, 178, 1)',
                        'rgba(239, 71, 111, 1)',
                        'rgba(255, 209, 102, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                plugins: {
                    legend: {
                        position: 'right',
                    }
                },
                maintainAspectRatio: false
            }
        });

        // Citas por especialidad
        new Chart(document.getElementById('citasEspecialidadChart'), {
            type: 'polarArea',
            data: {
                labels: <?= json_encode(array_keys($citas_especialidad)) ?>,
                datasets: [{
                    data: <?= json_encode(array_values($citas_especialidad)) ?>,
                    backgroundColor: [
                        'rgba(10, 147, 150, 0.8)',
                        'rgba(148, 210, 189, 0.8)',
                        'rgba(233, 216, 166, 0.8)',
                        'rgba(0, 95, 115, 0.8)',
                        'rgba(0, 18, 25, 0.8)'
                    ],
                    borderColor: [
                        'rgba(10, 147, 150, 1)',
                        'rgba(148, 210, 189, 1)',
                        'rgba(233, 216, 166, 1)',
                        'rgba(0, 95, 115, 1)',
                        'rgba(0, 18, 25, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                plugins: {
                    legend: {
                        position: 'right',
                    }
                },
                maintainAspectRatio: false
            }
        });
    });
    </script>
</body>
</html>