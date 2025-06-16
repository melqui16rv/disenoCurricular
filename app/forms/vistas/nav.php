<?php
// Navegación específica para el sistema de diseños curriculares
?>
<nav style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); padding: 1rem 0; margin-bottom: 1rem; border-radius: 10px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);">
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <img src="<?php echo BASE_URL; ?>assets/img/sena-logo.png" alt="SENA" style="height: 40px;" onerror="this.style.display='none'">
                <div>
                    <h3 style="margin: 0; color: #2c3e50; font-size: 1.2rem;">Sistema de Diseños Curriculares</h3>
                    <p style="margin: 0; color: #7f8c8d; font-size: 0.9rem;">SENA - Servicio Nacional de Aprendizaje</p>
                </div>
            </div>
            
            <div style="display: flex; gap: 1rem; align-items: center;">
                <a href="?accion=listar" class="nav-link <?php echo ($accion === 'listar' || !isset($accion)) ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i> Inicio
                </a>
                
                <?php if ($accion === 'ver_competencias' && isset($diseño_actual)): ?>
                    <span class="nav-link active">
                        <i class="fas fa-tasks"></i> Competencias
                    </span>
                <?php endif; ?>
                
                <?php if ($accion === 'ver_raps' && isset($competencia_actual)): ?>
                    <span class="nav-link active">
                        <i class="fas fa-list-ul"></i> RAPs
                    </span>
                <?php endif; ?>
                
                <div style="font-size: 0.9rem; color: #6c757d;">
                    <i class="fas fa-calendar-alt"></i> 
                    <?php echo date('d/m/Y H:i'); ?>
                </div>
            </div>
        </div>
    </div>
</nav>

<style>
.nav-link {
    padding: 8px 16px;
    color: #2c3e50;
    text-decoration: none;
    border-radius: 6px;
    transition: all 0.3s ease;
    font-weight: 500;
}

.nav-link:hover {
    background: rgba(102, 126, 234, 0.1);
    color: #667eea;
}

.nav-link.active {
    background: linear-gradient(45deg, #667eea, #764ba2);
    color: white;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

@media (max-width: 768px) {
    nav > div > div {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    nav .container > div > div:last-child {
        flex-direction: row;
        flex-wrap: wrap;
        justify-content: center;
    }
}
</style>
