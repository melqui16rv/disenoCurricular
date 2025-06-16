# Sistema de Diseños Curriculares - SENA

## 📋 Descripción

Sistema web desarrollado en PHP para la gestión integral de diseños curriculares del SENA (Servicio Nacional de Aprendizaje). Permite crear, editar, visualizar y eliminar programas formativos, competencias y resultados de aprendizaje (RAPs).

## 🚀 Características

### Gestión de Diseños Curriculares
- ✅ Crear nuevos diseños curriculares
- ✅ Editar información de programas existentes
- ✅ Eliminar diseños y sus dependencias
- ✅ Visualización completa con estadísticas

### Gestión de Competencias
- ✅ Agregar competencias a diseños
- ✅ Editar información de competencias
- ✅ Eliminar competencias y sus RAPs asociados
- ✅ Visualización de cobertura de horas

### Gestión de RAPs (Resultados de Aprendizaje)
- ✅ Crear RAPs asociados a competencias
- ✅ Editar resultados de aprendizaje
- ✅ Eliminar RAPs individuales
- ✅ Control de horas por competencia

### Características Técnicas
- 🎨 Interfaz moderna y responsive
- 🔍 Búsqueda en tiempo real en tablas
- 📊 Estadísticas automáticas
- 💾 Autoguardado de formularios
- 🌍 Detección automática de entorno (local/producción)
- 🔒 Validación en tiempo real con AJAX
- 📱 Compatible con dispositivos móviles
- ✨ Validaciones AJAX en tiempo real
- 📱 Compatible con dispositivos móviles

## 🏗️ Estructura del Proyecto

```
disenoCurricular/
├── app/forms/                      # Aplicación principal
│   ├── index.php                   # Controlador principal
│   ├── control/
│   │   └── ajax.php               # Controlador AJAX
│   └── vistas/                    # Vistas de la aplicación
│       ├── nav.php                # Navegación
│       ├── listar_diseños.php     # Lista de diseños
│       ├── crear_diseños.php      # Formulario crear diseño
│       ├── editar_diseños.php     # Formulario editar diseño
│       ├── listar_competencias.php # Lista de competencias
│       ├── crear_competencias.php  # Formulario crear competencia
│       ├── editar_competencias.php # Formulario editar competencia
│       ├── listar_raps.php        # Lista de RAPs
│       ├── crear_raps.php         # Formulario crear RAP
│       └── editar_raps.php        # Formulario editar RAP
├── assets/                        # Recursos estáticos
│   ├── css/forms/
│   │   └── estilosPrincipales.css # Estilos principales
│   └── js/forms/
│       └── scripts.js             # JavaScript principal
├── conf/                          # Configuración
│   ├── config.php                 # Configuración general
│   └── auth.php                   # Autenticación (base)
├── math/forms/                    # Lógica de negocio
│   └── metodosDiseños.php         # Clase principal CRUD
├── sql/                           # Base de datos
│   └── conexion.php               # Conexión PDO
└── import/
    └── Base_De_Datos.sql          # Esquema de base de datos
```

## 🛠️ Instalación y Configuración

### Requisitos del Sistema
- PHP 7.4 o superior
- MySQL 5.7 o superior / MariaDB 10.3+
- Servidor web (Apache/Nginx) o entorno local (XAMPP/MAMP/WAMP)
- Navegador moderno con soporte para JavaScript ES6+

### Configuración para Desarrollo Local

#### 1. Configurar Base de Datos Local
```sql
-- Crear base de datos
CREATE DATABASE disenos_curriculares CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Importar estructura y datos
USE disenos_curriculares;
SOURCE import/Base_De_Datos_Local.sql;
```

#### 2. Configuración Automática de Entorno
El sistema detecta automáticamente si está ejecutándose en:
- **Local**: localhost, 127.0.0.1, IPs privadas
- **Producción**: Cualquier otro dominio

**Configuración Local (automática):**
- Host: localhost
- Base de datos: disenos_curriculares
- Usuario: root
- Contraseña: (vacía por defecto)

**Configuración de Producción:**
- Host: localhost
- Base de datos: appscide_disenos_curriculares
- Usuario: appscide_Administrador
- Contraseña: (configurada para hosting)

#### 3. Acceso al Sistema
```
http://localhost/disenoCurricular/app/forms/
```

#### 4. Validación del Sistema
Ejecuta el script de validación para verificar la configuración:
```
http://localhost/disenoCurricular/validacion_sistema.php
```

### Configuración Personalizada
Si necesitas modificar la configuración de base de datos, edita `/sql/conexion.php`:

```php
// Modificar en la función isLocalEnvironment() si es necesario
private function isLocalEnvironment() {
    // Agregar hosts personalizados aquí
    $localHosts = ['localhost', '127.0.0.1', '::1', 'tu-host-local'];
    // ...
}
```

## 🗄️ Base de Datos

### Tabla `diseños`
Almacena la información principal de los programas formativos:
- `codigoDiseño` (PK): Código único concatenado (codigoPrograma-versionPrograma)
- `codigoPrograma`: Código del programa
- `versionPrograma`: Versión del programa
- `nombrePrograma`: Nombre del programa formativo
- `lineaTecnologica`: Línea tecnológica
- `redTecnologica`: Red tecnológica
- `redConocimiento`: Red de conocimiento
- `horasDesarrolloLectiva/Productiva`: Horas por etapa
- `mesesDesarrolloLectiva/Productiva`: Meses por etapa
- `horasDesarrolloDiseño`: Total de horas (calculado)
- `mesesDesarrolloDiseño`: Total de meses (calculado)
- `nivelAcademicoIngreso`: Nivel académico requerido
- `gradoNivelAcademico`: Grado específico
- `formacionTrabajoDesarrolloHumano`: Si/No
- `edadMinima`: Edad mínima de ingreso
- `requisitosAdicionales`: Requisitos adicionales

### Tabla `competencias`
Almacena las competencias asociadas a cada diseño:
- `codigoDiseñoCompetencia` (PK): Código concatenado (codigoDiseño-codigoCompetencia)
- `codigoCompetencia`: Código de la competencia
- `nombreCompetencia`: Nombre de la competencia
- `normaUnidadCompetencia`: Descripción de la norma
- `horasDesarrolloCompetencia`: Horas asignadas
- `requisitosAcademicosInstructor`: Requisitos académicos del instructor
- `experienciaLaboralInstructor`: Experiencia laboral requerida

### Tabla `raps`
Almacena los Resultados de Aprendizaje:
- `codigoDiseñoCompetenciaRap` (PK): Código concatenado (codigoDiseñoCompetencia-codigoRap)
- `codigoRap`: Código del RAP
- `nombreRap`: Descripción del resultado de aprendizaje
- `horasDesarrolloRap`: Horas asignadas al RAP

## 🎯 Uso del Sistema

### Navegación Principal
- **Inicio**: Lista todos los diseños curriculares
- **Crear Diseño**: Formulario para nuevo programa
- **Ver Competencias**: Lista competencias de un diseño
- **Ver RAPs**: Lista RAPs de una competencia

### Flujo de Trabajo
1. **Crear Diseño Curricular**: Definir programa formativo base
2. **Agregar Competencias**: Asociar competencias al diseño
3. **Definir RAPs**: Crear resultados de aprendizaje por competencia
4. **Gestionar**: Editar o eliminar según necesidades

### Códigos del Sistema
- **Diseño**: `codigoPrograma-versionPrograma` (ej: 124101-1)
- **Competencia**: `codigoDiseño-codigoCompetencia` (ej: 124101-1-220201501)
- **RAP**: `codigoDiseñoCompetencia-codigoRAP` (ej: 124101-1-220201501-RA1)

## 🛠️ Características Técnicas

### Validaciones
- ✅ Códigos únicos automáticos
- ✅ Validación AJAX en tiempo real
- ✅ Campos requeridos
- ✅ Tipos de datos correctos
- ✅ Rangos de valores

### Seguridad
- ✅ Prepared statements (PDO)
- ✅ Validación de entrada
- ✅ Escape de salida HTML
- ✅ Manejo de errores

### Experiencia de Usuario
- ✅ Interfaz intuitiva
- ✅ Feedback visual
- ✅ Autoguardado de formularios
- ✅ Búsqueda en tablas
- ✅ Estadísticas en tiempo real
- ✅ Responsive design

## 📊 Estadísticas Disponibles

- Total de diseños curriculares
- Suma total de horas de formación
- Suma total de meses de formación
- Cobertura de competencias por diseño
- Cobertura de RAPs por competencia
- Promedios y distribuciones

## 🔍 Funcionalidades Avanzadas

### Búsqueda y Filtrado
- Búsqueda en tiempo real en todas las tablas
- Filtrado automático por contenido
- Destacado de resultados

### Autoguardado
- Guardado automático de formularios en localStorage
- Recuperación de datos en caso de cierre accidental
- Limpieza automática al envío exitoso

### Validaciones AJAX
- Verificación de códigos únicos en tiempo real
- Feedback inmediato al usuario
- Prevención de duplicados

## 🐛 Solución de Problemas

### Problemas Comunes

1. **Error de conexión a base de datos**
   - Verificar credenciales en `sql/conexion.php`
   - Confirmar que la base de datos existe
   - Verificar permisos del usuario

2. **Recursos no cargan (CSS/JS)**
   - Verificar configuración de `BASE_URL` en `conf/config.php`
   - Confirmar rutas en `.htaccess`
   - Verificar permisos de archivos

3. **Formularios no funcionan**
   - Confirmar que JavaScript está habilitado
   - Verificar errores en consola del navegador
   - Comprobar validaciones PHP

## 🤝 Contribución

Para contribuir al proyecto:
1. Fork del repositorio
2. Crear rama para nueva funcionalidad
3. Realizar cambios con comentarios claros
4. Hacer commit con descripción detallada
5. Enviar pull request

## 📄 Licencia

Este proyecto está desarrollado para el SENA (Servicio Nacional de Aprendizaje) como herramienta interna de gestión curricular.

## 👥 Soporte

Para soporte técnico o reportar problemas:
- Crear issue en el repositorio
- Contactar al equipo de desarrollo
- Revisar documentación técnica

---

**Desarrollado para SENA - Servicio Nacional de Aprendizaje**  
*Sistema de Gestión de Diseños Curriculares v1.0*
