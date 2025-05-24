<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Hospitalario</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --hospital-primary: #005f73;
            --hospital-secondary: #0a9396;
            --hospital-accent: #94d2bd;
            --hospital-light: #e9d8a6;
            --hospital-dark: #001219;
            --hospital-alert: #ee9b00;
            --hospital-danger: #bb3e03;
        }
        
        .navbar-hospital {
            background: linear-gradient(135deg, var(--hospital-primary) 0%, var(--hospital-secondary) 100%) !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 0.8rem 1rem;
            transition: all 0.3s ease;
        }
        
        .navbar-hospital.scrolled {
            padding: 0.5rem 1rem;
            background: var(--hospital-primary) !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.8rem;
            color: white !important;
            display: flex;
            align-items: center;
        }
        
        .navbar-brand i {
            margin-right: 10px;
            font-size: 1.5rem;
            color: var(--hospital-accent);
        }
        
        .navbar-brand span {
            color: var(--hospital-accent);
        }
        
        .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            margin: 0 0.2rem;
            border-radius: 50px;
            transition: all 0.3s ease;
            position: relative;
            display: flex;
            align-items: center;
        }
        
        .nav-link i {
            margin-right: 8px;
            font-size: 0.9rem;
        }
        
        .nav-link:hover, .nav-link:focus {
            color: white !important;
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
        }
        
        .nav-link.active {
            color: white !important;
            background: rgba(255, 255, 255, 0.2);
            font-weight: 600;
        }
        
        .nav-link.active:after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 50%;
            height: 3px;
            background: var(--hospital-accent);
            border-radius: 3px;
        }
        
        .navbar-toggler {
            border: none;
            padding: 0.5rem;
        }
        
        .navbar-toggler:focus {
            box-shadow: none;
        }
        
        .navbar-toggler-icon {
            background-image: none;
            position: relative;
            width: 24px;
            height: 2px;
            background: white;
            display: block;
            transition: all 0.3s ease;
        }
        
        .navbar-toggler-icon:before, .navbar-toggler-icon:after {
            content: '';
            position: absolute;
            width: 24px;
            height: 2px;
            background: white;
            left: 0;
            transition: all 0.3s ease;
        }
        
        .navbar-toggler-icon:before {
            top: -8px;
        }
        
        .navbar-toggler-icon:after {
            top: 8px;
        }
        
        .navbar-toggler[aria-expanded="true"] .navbar-toggler-icon {
            background: transparent;
        }
        
        .navbar-toggler[aria-expanded="true"] .navbar-toggler-icon:before {
            transform: rotate(45deg);
            top: 0;
        }
        
        .navbar-toggler[aria-expanded="true"] .navbar-toggler-icon:after {
            transform: rotate(-45deg);
            top: 0;
        }
        
        .dropdown-menu {
            background-color: white;
            border: none;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-top: 8px;
            padding: 0.5rem 0;
        }
        
        .dropdown-item {
            padding: 0.5rem 1.5rem;
            color: var(--hospital-dark);
            transition: all 0.2s ease;
        }
        
        .dropdown-item:hover {
            background: var(--hospital-accent);
            color: white;
        }
        
        .dropdown-divider {
            margin: 0.3rem 0;
            border-color: rgba(0, 0, 0, 0.05);
        }
        
        .user-profile {
            display: flex;
            align-items: center;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            transition: all 0.3s ease;
        }
        
        .user-profile:hover {
            background: rgba(255, 255, 255, 0.15);
            cursor: pointer;
        }
        
        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--hospital-accent);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-weight: bold;
            color: var(--hospital-primary);
        }
        
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--hospital-danger);
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        @media (max-width: 992px) {
            .navbar-collapse {
                background: var(--hospital-primary);
                padding: 1rem;
                border-radius: 0 0 10px 10px;
                margin-top: 10px;
            }
            
            .nav-link {
                margin: 0.3rem 0;
                padding: 0.8rem 1rem !important;
            }
            
            .nav-link.active:after {
                display: none;
            }
            
            .dropdown-menu {
                margin-left: 1rem;
                width: calc(100% - 2rem);
            }
            
            .user-profile {
                margin-top: 1rem;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-hospital fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-hospital"></i>
                HOSPITAL<span>XYZ</span>
            </a>
            
            <div class="d-flex align-items-center order-lg-3">
                <!-- Botón de cerrar sesión -->
                <a href="login.php" class="nav-link position-relative" style="color: var(--hospital-accent); font-size: 1.3rem;" title="Cerrar sesión">
                    <i class="fas fa-door-open"></i>
                </a>
                
                <!-- Perfil de usuario -->
                <div class="dropdown">
                    <div class="user-profile dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown">
                        <div class="user-avatar">AD</div>
                        <span class="d-none d-lg-inline">Admin</span>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-user-circle me-2"></i> Mi perfil
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-cog me-2"></i> Configuración
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="#">
                                <i class="fas fa-sign-out-alt me-2"></i> Cerrar sesión
                            </a>
                        </li>
                    </ul>
                </div>
                
                <button class="navbar-toggler ms-3" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
            
            <div class="collapse navbar-collapse order-lg-2" id="navbarContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php" id="nav-inicio">
                            <i class="fas fa-home"></i> Inicio
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pacientes.php" id="nav-pacientes">
                            <i class="fas fa-user-injured"></i> Pacientes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="medicos.php" id="nav-medicos">
                            <i class="fas fa-user-md"></i> Médicos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="citas.php" id="nav-citas">
                            <i class="fas fa-calendar-check"></i> Citas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="habitaciones_b.php" id="nav-habitaciones">
                            <i class="fas fa-procedures"></i> Habitaciones
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="medicamentos.php" id="nav-medicamentos">
                            <i class="fas fa-pills"></i> Medicamentos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="reportes.php" id="nav-reportes">
                            <i class="fas fa-chart-bar"></i> Reportes
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5 pt-5">
        <!-- Contenido de la página -->
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Efecto de cambio al hacer scroll
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar-hospital');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
        
        // Función para resaltar el elemento de navegación activo
        function setActiveNavItem() {
            // Obtener la ruta actual
            const currentPath = window.location.pathname.split('/').pop();
            
            // Mapeo de páginas a sus IDs de navegación
            const pageMap = {
                'index.php': 'nav-inicio',
                'pacientes.php': 'nav-pacientes',
                'medicos.php': 'nav-medicos',
                'citas.php': 'nav-citas',
                'citas-calendario.php': 'nav-citas',
                'citas-nueva.php': 'nav-citas',
                'habitaciones_b.php': 'nav-habitaciones',
                'medicamentos.php': 'nav-medicamentos',
                'reportes.php': 'nav-reportes'
            };
            
            // Encontrar el ID del elemento activo
            const activeId = pageMap[currentPath];
            
            if (activeId) {
                // Remover clase active de todos los elementos
                document.querySelectorAll('.nav-link').forEach(link => {
                    link.classList.remove('active');
                });
                
                // Agregar clase active al elemento correspondiente
                const activeElement = document.getElementById(activeId);
                if (activeElement) {
                    activeElement.classList.add('active');
                }
            }
        }
        
        // Ejecutar cuando el DOM esté listo
        document.addEventListener('DOMContentLoaded', setActiveNavItem);
    </script>
</body>
</html>