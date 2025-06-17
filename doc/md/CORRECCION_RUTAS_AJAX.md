# 🔧 CORRECCIÓN DE RUTAS - Funcionalidad de Comparación de RAPs

## ⚠️ PROBLEMA IDENTIFICADO: Rutas incorrectas

**Fecha:** 17 de junio de 2025  
**Problema:** Las rutas AJAX usaban ruta absoluta `/app/forms/control/ajax.php` que no funcionará correctamente con la configuración del proyecto.

---

## ✅ SOLUCIÓN IMPLEMENTADA

### Problema Original:
```javascript
// ❌ INCORRECTO - Ruta absoluta
fetch('/app/forms/control/ajax.php', {
```

### Solución Aplicada:
```javascript
// ✅ CORRECTO - Ruta relativa
fetch('../control/ajax.php', {
```

---

## 🔧 ARCHIVOS CORREGIDOS

### 1. `/app/forms/vistas/crear_raps.php`
**Línea 364:**
- ❌ Antes: `fetch('/app/forms/control/ajax.php', {`
- ✅ Ahora: `fetch('../control/ajax.php', {`

### 2. `/app/forms/vistas/editar_raps.php`
**Función cargarComparacion():**
- ❌ Antes: `fetch('/app/forms/control/ajax.php', {`
- ✅ Ahora: `fetch('../control/ajax.php', {`

---

## 🗂️ EXPLICACIÓN DE RUTAS

### Estructura de directorios:
```
app/forms/
├── vistas/
│   ├── crear_raps.php      <- Desde aquí
│   └── editar_raps.php     <- Desde aquí
└── control/
    └── ajax.php            <- Hacia aquí
```

### Ruta relativa correcta:
- **Desde:** `/app/forms/vistas/`
- **Hacia:** `/app/forms/control/`
- **Ruta relativa:** `../control/ajax.php`

---

## 📋 CONFIGURACIÓN DEL PROYECTO

El archivo `config.php` define:
```php
if (isset($_SERVER['HTTP_HOST'])) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $path = str_replace('/app/forms/index.php', '', $_SERVER['SCRIPT_NAME']);
    define('BASE_URL', $protocol . '://' . $host . $path . '/');
} else {
    define('BASE_URL', '/disenoCurricular/');
}
```

**Beneficios de usar ruta relativa:**
- ✅ Funciona en cualquier entorno (local, staging, producción)
- ✅ No depende de la configuración de BASE_URL
- ✅ Más sencillo y directo
- ✅ Mantiene la estructura relativa del proyecto

---

## 🚀 ESTADO ACTUAL

### ✅ Correcciones Completadas:
1. **Rutas AJAX corregidas** en ambos archivos
2. **Documentación actualizada**
3. **Funcionalidad lista para deploy**

### 📋 Verificación:
```javascript
// En crear_raps.php y editar_raps.php:
fetch('../control/ajax.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: `accion=obtener_comparacion_raps&codigoCompetencia=${codigoCompetenciaReal}&disenoActual=${disenoActual}`
})
```

---

## 🎯 SIGUIENTE PASO

**LISTO PARA DEPLOY FINAL**

Los archivos ahora usan rutas relativas correctas que funcionarán tanto en el entorno local como en el hosting, independientemente de la configuración de BASE_URL.

### Archivos a subir al hosting:
1. `app/forms/vistas/crear_raps.php` - ✅ Ruta corregida
2. `app/forms/vistas/editar_raps.php` - ✅ Ruta corregida  
3. `app/forms/control/ajax.php` - ✅ Ya corregido anteriormente

**La funcionalidad de comparación de RAPs ahora debería funcionar correctamente.**
