<footer class="hospital-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="footer-brand d-flex align-items-center mb-3">
                    <i class="fas fa-hospital me-3"></i>
                    <h3>Hospital XYZ</h3>
                </div>
                <p class="footer-about">Sistema integral de gestión hospitalaria diseñado para optimizar los procesos médicos y administrativos.</p>
                <div class="social-links mt-4">
                    <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
            
            <div class="col-lg-2 col-md-4">
                <h4 class="footer-title">Enlaces Rápidos</h4>
                <ul class="footer-links">
                    <li><a href="index.php"><i class="fas fa-chevron-right me-2"></i> Inicio</a></li>
                    <li><a href="pacientes.php"><i class="fas fa-chevron-right me-2"></i> Pacientes</a></li>
                    <li><a href="medicos.php"><i class="fas fa-chevron-right me-2"></i> Médicos</a></li>
                    <li><a href="citas.php"><i class="fas fa-chevron-right me-2"></i> Citas</a></li>
                </ul>
            </div>
            
            <div class="col-lg-2 col-md-4">
                <h4 class="footer-title">Servicios</h4>
                <ul class="footer-links">
                    <li><a href="#"><i class="fas fa-chevron-right me-2"></i> Urgencias</a></li>
                    <li><a href="#"><i class="fas fa-chevron-right me-2"></i> Consultas</a></li>
                    <li><a href="#"><i class="fas fa-chevron-right me-2"></i> Hospitalización</a></li>
                    <li><a href="#"><i class="fas fa-chevron-right me-2"></i> Laboratorio</a></li>
                </ul>
            </div>
            
            <div class="col-lg-4 col-md-4">
                <h4 class="footer-title">Contacto</h4>
                <ul class="footer-contact">
                    <li><i class="fas fa-map-marker-alt me-3"></i> Av. Principal #123, Ciudad</li>
                    <li><i class="fas fa-phone-alt me-3"></i> (123) 456-7890</li>
                    <li><i class="fas fa-envelope me-3"></i> contacto@hospitalxyz.com</li>
                    <li><i class="fas fa-clock me-3"></i> 24/7 Servicio de Emergencias</li>
                </ul>
            </div>
        </div>
        
        <hr class="footer-divider">
        
        <div class="footer-bottom">
            <div class="row align-items-center">
                <div class="col-md-6 text-md-start text-center mb-3 mb-md-0">
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> Sistema Hospitalario. Todos los derechos reservados.</p>
                </div>
                <div class="col-md-6 text-md-end text-center">
                    <div class="footer-legal">
                        <a href="#">Política de Privacidad</a>
                        <a href="#">Términos de Servicio</a>
                        <a href="#">Mapa del Sitio</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
.hospital-footer {
    background: linear-gradient(135deg, var(--hospital-primary) 0%, var(--hospital-dark) 100%);
    color: white;
    padding: 4rem 0 1.5rem;
    margin-top: 5rem;
    position: relative;
}

.hospital-footer::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 10px;
    background: linear-gradient(90deg, var(--hospital-accent) 0%, var(--hospital-secondary) 100%);
}

.footer-brand {
    font-weight: 700;
    color: white;
}

.footer-brand i {
    font-size: 2rem;
    color: var(--hospital-accent);
}

.footer-about {
    opacity: 0.8;
    line-height: 1.6;
}

.footer-title {
    font-size: 1.2rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    position: relative;
    padding-bottom: 0.5rem;
}

.footer-title::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 50px;
    height: 2px;
    background: var(--hospital-accent);
}

.footer-links {
    list-style: none;
    padding: 0;
}

.footer-links li {
    margin-bottom: 0.8rem;
}

.footer-links a {
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    transition: all 0.3s ease;
    display: block;
}

.footer-links a:hover {
    color: white;
    transform: translateX(5px);
}

.footer-links i {
    font-size: 0.7rem;
    color: var(--hospital-accent);
}

.footer-contact {
    list-style: none;
    padding: 0;
}

.footer-contact li {
    margin-bottom: 1rem;
    display: flex;
    align-items: flex-start;
}

.footer-contact i {
    margin-top: 3px;
    color: var(--hospital-accent);
}

.social-links {
    display: flex;
    gap: 1rem;
}

.social-icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.social-icon:hover {
    background: var(--hospital-accent);
    transform: translateY(-3px);
}

.footer-divider {
    border-color: rgba(255, 255, 255, 0.1);
    margin: 2rem 0;
}

.footer-bottom {
    padding-top: 1rem;
}

.footer-legal a {
    color: rgba(255, 255, 255, 0.6);
    text-decoration: none;
    margin-left: 1.5rem;
    transition: all 0.3s ease;
    font-size: 0.9rem;
}

.footer-legal a:hover {
    color: var(--hospital-accent);
}

.footer-legal a:first-child {
    margin-left: 0;
}

@media (max-width: 768px) {
    .hospital-footer {
        text-align: center;
        padding: 3rem 0 1rem;
    }
    
    .footer-title::after {
        left: 50%;
        transform: translateX(-50%);
    }
    
    .footer-links a {
        justify-content: center;
    }
    
    .footer-contact li {
        justify-content: center;
    }
    
    .footer-legal a {
        margin: 0 0.5rem;
    }
}
</style>