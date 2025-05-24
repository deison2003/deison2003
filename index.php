<?php
require_once 'includes/auth.php';
include 'includes/header.php'; ?>

<div class="hospital-hero">
    <div class="hero-overlay"></div>
    <div class="container">
        <div class="hero-content text-center">
            <h1 class="display-4 fw-bold mb-3">Sistema de Gestión Hospitalaria</h1>
            <p class="lead mb-4">Plataforma integral para la administración médica y atención al paciente</p>
            <div class="hero-stats">
                <div class="stat-item">
                    <div class="stat-number" id="pacientes-counter">0</div>
                    <div class="stat-label">Pacientes</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" id="medicos-counter">0</div>
                    <div class="stat-label">Médicos</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" id="citas-counter">0</div>
                    <div class="stat-label">Citas Hoy</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container mt-5">
    <div class="section-header mb-5">
        <h2 class="section-title">Módulos Principales</h2>
        <p class="section-subtitle">Accede a las diferentes áreas del sistema</p>
    </div>

    <div class="row g-4">
        <!-- Pacientes -->
        <div class="col-lg-4 col-md-6">
            <div class="card module-card hover-effect">
                <div class="card-icon bg-soft-blue">
                    <i class="fas fa-user-injured"></i>
                </div>
                <div class="card-body">
                    <h5 class="card-title">Pacientes</h5>
                    <p class="card-text">Gestión completa de historiales médicos, registros y seguimiento de pacientes.</p>
                    <a href="pacientes.php" class="btn btn-primary btn-module">
                        Acceder <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Solo para admin: Médicos -->
        <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
        <div class="col-lg-4 col-md-6">
            <div class="card module-card hover-effect">
                <div class="card-icon bg-soft-green">
                    <i class="fas fa-user-md"></i>
                </div>
                <div class="card-body">
                    <h5 class="card-title">Médicos</h5>
                    <p class="card-text">Administración del personal médico, especialidades y horarios de atención.</p>
                    <a href="medicos.php" class="btn btn-primary btn-module">
                        Acceder <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Citas -->
        <div class="col-lg-4 col-md-6">
            <div class="card module-card hover-effect">
                <div class="card-icon bg-soft-purple">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="card-body">
                    <h5 class="card-title">Citas Médicas</h5>
                    <p class="card-text">Programación, seguimiento y gestión de consultas médicas.</p>
                    <a href="citas.php" class="btn btn-primary btn-module">
                        Acceder <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Solo para admin: Habitaciones -->
        <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
        <div class="col-lg-4 col-md-6">
            <div class="card module-card hover-effect">
                <div class="card-icon bg-soft-orange">
                    <i class="fas fa-procedures"></i>
                </div>
                <div class="card-body">
                    <h5 class="card-title">Habitaciones</h5>
                    <p class="card-text">Control de camas, hospitalizaciones y estados de ocupación.</p>
                    <a href="habitaciones.php" class="btn btn-primary btn-module">
                        Acceder <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Medicamentos -->
        <div class="col-lg-4 col-md-6">
            <div class="card module-card hover-effect">
                <div class="card-icon bg-soft-red">
                    <i class="fas fa-pills"></i>
                </div>
                <div class="card-body">
                    <h5 class="card-title">Medicamentos</h5>
                    <p class="card-text">Inventario, recetas y control de fármacos del hospital.</p>
                    <a href="medicamentos.php" class="btn btn-primary btn-module">
                        Acceder <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Reportes -->
        <div class="col-lg-4 col-md-6">
            <div class="card module-card hover-effect">
                <div class="card-icon bg-soft-teal">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="card-body">
                    <h5 class="card-title">Reportes</h5>
                    <p class="card-text">Estadísticas, gráficos y análisis del funcionamiento hospitalario.</p>
                    <a href="reportes.php" class="btn btn-primary btn-module">
                        Acceder <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container mt-6 mb-5">
    <div class="quick-actions">
        <h3 class="mb-4">Acciones Rápidas</h3>
        <div class="row g-3">
            <div class="col-md-3 col-6">
                <a href="#" class="quick-action-btn" data-bs-toggle="modal" data-bs-target="#nuevaCitaModal">
                    <i class="fas fa-plus-circle"></i>
                    <span>Nueva Cita</span>
                </a>
            </div>
            <div class="col-md-3 col-6">
                <a href="#" class="quick-action-btn">
                    <i class="fas fa-search"></i>
                    <span>Buscar Paciente</span>
                </a>
            </div>
            <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
            <div class="col-md-3 col-6">
                <a href="#" class="quick-action-btn">
                    <i class="fas fa-file-prescription"></i>
                    <span>Generar Receta</span>
                </a>
            </div>
            <div class="col-md-3 col-6">
                <a href="#" class="quick-action-btn">
                    <i class="fas fa-bell"></i>
                    <span>Alertas</span>
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Nueva Cita -->
<div class="modal fade" id="nuevaCitaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Programar Nueva Cita</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label class="form-label">Paciente</label>
                        <select class="form-select">
                            <option selected>Seleccionar paciente</option>
                            <option>Juan Pérez</option>
                            <option>María García</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Médico</label>
                        <select class="form-select">
                            <option selected>Seleccionar médico</option>
                            <option>Dr. Roberto Sánchez</option>
                            <option>Dra. Ana Martínez</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha y Hora</label>
                        <input type="datetime-local" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Motivo</label>
                        <textarea class="form-control" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary">Guardar Cita</button>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<style>
:root {
    --hospital-blue: #1a73e8;
    --hospital-green: #34a853;
    --hospital-red: #ea4335;
    --hospital-yellow: #fbbc05;
    --hospital-dark: #2d3748;
    --hospital-light: #f8f9fa;
}

.hospital-hero {
    background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('assets/images/hospital-bg.jpg') center/cover no-repeat;
    color: white;
    padding: 6rem 0;
    position: relative;
    margin-bottom: 3rem;
}

.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, var(--hospital-blue) 0%, var(--hospital-green) 100%);
    opacity: 0.9;
    z-index: 0;
}

.hero-content {
    position: relative;
    z-index: 1;
}

.hero-stats {
    display: flex;
    justify-content: center;
    gap: 2rem;
    margin-top: 2rem;
}

.stat-item {
    text-align: center;
    padding: 1rem 2rem;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    backdrop-filter: blur(5px);
}

.stat-number {
    font-size: 2.5rem;
    font-weight: bold;
    color: white;
}

.stat-label {
    font-size: 0.9rem;
    opacity: 0.9;
}

.section-header {
    text-align: center;
}

.section-title {
    font-size: 2rem;
    font-weight: 700;
    color: var(--hospital-dark);
    position: relative;
    display: inline-block;
    margin-bottom: 1rem;
}

.section-title:after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 3px;
    background: var(--hospital-blue);
    border-radius: 3px;
}

.section-subtitle {
    color: #6c757d;
    max-width: 600px;
    margin: 0 auto;
}

.module-card {
    border: none;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    height: 100%;
    overflow: hidden;
    position: relative;
    background: white;
}

.module-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.card-icon {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    color: white;
    position: absolute;
    top: -20px;
    right: 20px;
}

.bg-soft-blue {
    background-color: rgba(26, 115, 232, 0.1);
    color: var(--hospital-blue);
}

.bg-soft-green {
    background-color: rgba(52, 168, 83, 0.1);
    color: var(--hospital-green);
}

.bg-soft-purple {
    background-color: rgba(121, 40, 202, 0.1);
    color: #7928ca;
}

.bg-soft-orange {
    background-color: rgba(255, 152, 0, 0.1);
    color: #ff9800;
}

.bg-soft-red {
    background-color: rgba(234, 67, 53, 0.1);
    color: var(--hospital-red);
}

.bg-soft-teal {
    background-color: rgba(0, 150, 136, 0.1);
    color: #009688;
}

.card-body {
    padding: 2rem;
    padding-top: 3rem;
}

.card-title {
    font-size: 1.3rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: var(--hospital-dark);
}

.card-text {
    color: #6c757d;
    margin-bottom: 1.5rem;
}

.btn-module {
    border-radius: 50px;
    padding: 0.5rem 1.5rem;
    font-weight: 500;
    border: none;
    background: var(--hospital-blue);
}

.quick-actions {
    background: white;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
}

.quick-action-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 1.5rem 0.5rem;
    background: white;
    border-radius: 10px;
    text-align: center;
    color: var(--hospital-dark);
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
    height: 100%;
}

.quick-action-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    color: var(--hospital-blue);
}

.quick-action-btn i {
    font-size: 1.8rem;
    margin-bottom: 0.5rem;
    color: var(--hospital-blue);
}

.quick-action-btn span {
    font-weight: 500;
}

.hover-effect {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.hover-effect:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

@media (max-width: 768px) {
    .hero-stats {
        flex-direction: column;
        gap: 1rem;
    }
    
    .stat-item {
        padding: 0.8rem 1rem;
    }
    
    .stat-number {
        font-size: 2rem;
    }
}
</style>

<script>
// Contadores animados
document.addEventListener('DOMContentLoaded', function() {
    const animateCounter = (elementId, target) => {
        const element = document.getElementById(elementId);
        const duration = 2000;
        const step = target / (duration / 16);
        let current = 0;
        
        const timer = setInterval(() => {
            current += step;
            if (current >= target) {
                clearInterval(timer);
                current = target;
            }
            element.textContent = Math.floor(current);
        }, 16);
    };
    
    animateCounter('pacientes-counter', 1243);
    animateCounter('medicos-counter', 87);
    animateCounter('citas-counter', 56);
});
</script>