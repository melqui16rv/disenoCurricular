<?php
// Navegación específica para el sistema de diseños curriculares
?>
<nav style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(15px); padding: 1.2rem 0; margin-bottom: 1rem; border-radius: 12px; box-shadow: 0 2px 15px rgba(0, 0, 0, 0.06); border: 1px solid rgba(0, 0, 0, 0.03);">
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <img src="<?php echo BASE_URL; ?>assets/img/sena-logo.png" alt="SENA" style="height: 40px;" onerror="this.style.display='none'">
                <div>
                    <h3 style="margin: 0; color: #2c5530; font-size: 1.3rem; font-weight: 500;">Sistema de Diseños Curriculares</h3>
                    <p style="margin: 0; color: #5a7c5a; font-size: 0.9rem; font-weight: 400;">SENA - Servicio Nacional de Aprendizaje</p>
                </div>
            </div>
            
            <div style="display: flex; gap: 1rem; align-items: center;">
                <a href="?accion=listar" class="nav-link <?php echo ($accion === 'listar' || !isset($accion)) ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i> Inicio
                </a>
                
                <a href="?accion=completar_informacion" class="nav-link <?php echo ($accion === 'completar_informacion') ? 'active' : ''; ?>">
                    <i class="fas fa-clipboard-check"></i> Completar Información
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
                
                <div style="font-size: 0.9rem; color: #5a7c5a; font-weight: 400;">
                    <i class="fas fa-calendar-alt"></i> 
                    <?php echo date('d/m/Y H:i'); ?>
                </div>
            </div>
        </div>
    </div>
</nav>

<style>
.nav-link {
    padding: 10px 18px;
    color: #2c5530;
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.3s ease;
    font-weight: 400;
    border: 1px solid transparent;
}

.nav-link:hover {
    background: rgba(232, 245, 232, 0.5);
    color: #2c5530;
    border-color: rgba(232, 245, 232, 0.8);
    transform: translateY(-1px);
}

.nav-link.active {
    background: linear-gradient(45deg, #e8f5e8, #f0f8f0);
    color: #2c5530;
    font-weight: 500;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
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
