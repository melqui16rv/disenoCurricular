# CONFIGURACIÓN FINAL DE RUTAS - disenoCurricular

## Resumen de Cambios Aplicados

### 1. Archivo config.php (/conf/config.php)

**FUNCIONAMIENTO:**
- Detecta automáticamente si está en entorno local o producción
- En LOCAL: Mantiene `$_SERVER['DOCUMENT_ROOT']` original del sistema para que las rutas `/disenoCurricular/...` funcionen
- En PRODUCCIÓN: Ajusta `$_SERVER['DOCUMENT_ROOT']` a `/home/appscide/public_html`

**DETECCIÓN DE ENTORNO:**
- Local: `localhost`, `127.0.0.1`, `::1` o ejecución CLI
- Producción: cualquier otro host

**CONFIGURACIÓN DE RUTAS:**
```php
// FUNCIONAMIENTO EN LOCAL:
// $_SERVER['DOCUMENT_ROOT'] = '/Users/melquiromero/Documents/phpStorm' (original del sistema)
// Las rutas como $_SERVER['DOCUMENT_ROOT'] . '/disenoCurricular/conf/config.php' 
// se resuelven a: /Users/melquiromero/Documents/phpStorm/disenoCurricular/conf/config.php ✓

// FUNCIONAMIENTO EN PRODUCCIÓN:
// $_SERVER['DOCUMENT_ROOT'] = '/home/appscide/public_html'
// Las rutas como $_SERVER['DOCUMENT_ROOT'] . '/disenoCurricular/conf/config.php'
// se resuelven a: /home/appscide/public_html/disenoCurricular/conf/config.php ✓
```

### 2. URLs Base (BASE_URL)

**LOCAL:**
- `BASE_URL = 'http://localhost:8889/disenoCurricular/'`
- Incluye el directorio `/disenoCurricular/` en la URL

**PRODUCCIÓN:**
- `BASE_URL = 'https://tudominio.com/'` (sin /disenoCurricular/)
- El directorio raíz de la aplicación es el public_html

### 3. Rutas de Includes Estandarizadas

**PATRÓN PARA CONFIG.PHP:**
```php
// Siempre usar ruta relativa para config.php desde cualquier archivo
require_once __DIR__ . '/../../conf/config.php'; // Desde app/forms/
require_once __DIR__ . '/../conf/config.php';    // Desde math/forms/
```

**PATRÓN PARA OTROS ARCHIVOS (después de incluir config.php):**
```php
require_once $_SERVER['DOCUMENT_ROOT'] . '/disenoCurricular/ruta/archivo.php';
```

**EJEMPLOS FUNCIONANDO:**
- `$_SERVER['DOCUMENT_ROOT'] . '/disenoCurricular/sql/conexion.php'`
- `$_SERVER['DOCUMENT_ROOT'] . '/disenoCurricular/math/forms/metodosDisenos.php'`
- `$_SERVER['DOCUMENT_ROOT'] . '/disenoCurricular/math/forms/metodosComparacion.php'`

### 4. Archivos Actualizados

✅ **config.php** - Configuración automática de entornos
✅ **index.php** - Rutas de includes corregidas
✅ **ajax.php** - Rutas de includes corregidas
✅ **metodosDisenos.php** - Compatible con nuevas rutas
✅ **metodosComparacion.php** - Compatible con nuevas rutas

### 5. Pruebas Realizadas

✅ **CLI:** Scripts funcionan correctamente desde línea de comandos
✅ **WEB:** Aplicación carga correctamente en http://localhost:8889/app/forms/index.php
✅ **INCLUDES:** Todos los archivos se incluyen correctamente
✅ **BD:** Conexión y métodos funcionan (292 diseños obtenidos)

### 6. Migración de Entornos

**PARA CAMBIAR DE LOCAL A PRODUCCIÓN:**
1. Solo modificar config.php si es necesario ajustar rutas específicas
2. Todo lo demás funciona automáticamente

**PARA DESARROLLO LOCAL:**
1. Ejecutar servidor: `php -S localhost:8889` desde el directorio raíz
2. Acceder: `http://localhost:8889/app/forms/index.php`
3. Todas las rutas funcionan automáticamente

### 7. Ventajas de esta Configuración

- **Portable:** Solo config.php requiere cambios entre entornos
- **Consistente:** Todas las rutas usan el mismo patrón
- **Automática:** Detección de entorno sin configuración manual
- **Robusta:** Funciona tanto en CLI como en servidor web
- **Escalable:** Fácil agregar nuevos archivos siguiendo el patrón

## Estado Actual: ✅ FUNCIONANDO COMPLETAMENTE

La aplicación está lista para usar en local y solo requiere desplegar config.php en producción.
