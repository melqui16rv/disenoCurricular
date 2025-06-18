# Corrección del Error de Redirección en Formularios

## Problema Identificado

El error ocurría después de editar una competencia exitosamente:

```
URL: https://appscide.com/disenoCurricular/app/forms/?accion=editar&tipo=competencias&codigo=521240-1-220201501
```

**Síntomas:**
- ✅ Mensaje: "Competencia actualizada exitosamente" 
- ❌ Error: "No se encontró el diseño curricular especificado"
- ❌ Mostraba "Diseño no encontrado" en lugar de listar las competencias

## Causa Raíz

Cuando se actualizaba una competencia (o RAP), el flujo era:
1. ✅ Actualización exitosa en BD
2. ✅ Se establecía `$accion = 'ver_competencias'`
3. ❌ **NO se establecía `$_GET['codigo']`** 
4. ❌ Al ejecutar `ver_competencias`, `$_GET['codigo']` estaba vacío
5. ❌ No podía encontrar el diseño para mostrar las competencias

## Correcciones Implementadas

### 1. Editar Competencia
```php
// ANTES
if ($metodos->actualizarCompetencia($_POST['codigoDiseñoCompetencia'], $_POST)) {
    $mensaje = 'Competencia actualizada exitosamente';
    $tipoMensaje = 'success';
    $accion = 'ver_competencias';
}

// DESPUÉS  
if ($metodos->actualizarCompetencia($_POST['codigoDiseñoCompetencia'], $_POST)) {
    $mensaje = 'Competencia actualizada exitosamente';
    $tipoMensaje = 'success';
    $accion = 'ver_competencias';
    
    // Extraer código del diseño de la competencia para la redirección
    $partes = explode('-', $_POST['codigoDiseñoCompetencia']);
    if (count($partes) >= 3) {
        $_GET['codigo'] = $partes[0] . '-' . $partes[1];
    }
}
```

### 2. Crear Competencia
```php
if ($metodos->insertarCompetencia($_POST['codigoDiseño'], $_POST)) {
    $mensaje = 'Competencia creada exitosamente';
    $tipoMensaje = 'success';
    $accion = 'ver_competencias';
    
    // Establecer código del diseño para la redirección
    $_GET['codigo'] = $_POST['codigoDiseño'];
}
```

### 3. Eliminar Competencia
```php
if ($metodos->eliminarCompetencia($codigo)) {
    $mensaje = 'Competencia eliminada exitosamente';
    $tipoMensaje = 'success';
    $accion = 'ver_competencias';
    
    // Extraer código del diseño de la competencia para la redirección
    $partes = explode('-', $codigo);
    if (count($partes) >= 3) {
        $_GET['codigo'] = $partes[0] . '-' . $partes[1];
    }
}
```

### 4. Operaciones de RAPs

**Crear RAP:**
```php
if ($metodos->insertarRap($_POST['codigoDiseñoCompetencia'], $_POST)) {
    $mensaje = 'RAP creado exitosamente';
    $tipoMensaje = 'success';
    $accion = 'ver_raps';
    
    // Establecer código de la competencia para la redirección
    $_GET['codigo'] = $_POST['codigoDiseñoCompetencia'];
}
```

**Editar RAP:**
```php
if ($metodos->actualizarRap($_POST['codigoDiseñoCompetenciaRap'], $_POST)) {
    $mensaje = 'RAP actualizado exitosamente';
    $tipoMensaje = 'success';
    $accion = 'ver_raps';
    
    // Extraer código de la competencia desde el código del RAP para la redirección
    $partes = explode('-', $_POST['codigoDiseñoCompetenciaRap']);
    if (count($partes) >= 4) {
        $_GET['codigo'] = $partes[0] . '-' . $partes[1] . '-' . $partes[2];
    }
}
```

**Eliminar RAP:**
```php
if ($metodos->eliminarRap($codigo)) {
    $mensaje = 'RAP eliminado exitosamente';
    $tipoMensaje = 'success';
    $accion = 'ver_raps';
    
    // Extraer código de la competencia desde el código del RAP para la redirección
    $partes = explode('-', $codigo);
    if (count($partes) >= 4) {
        $_GET['codigo'] = $partes[0] . '-' . $partes[1] . '-' . $partes[2];
    }
}
```

## Estructura de Códigos

### Diseño Curricular
- **Formato:** `{codigoPrograma}-{version}`
- **Ejemplo:** `521240-1`

### Competencia  
- **Formato:** `{codigoDiseño}-{codigoCompetencia}`
- **Ejemplo:** `521240-1-220201501`

### RAP
- **Formato:** `{codigoCompetencia}-{numeroRAP}`
- **Ejemplo:** `521240-1-220201501-1`

## Resultado

✅ **PROBLEMA SOLUCIONADO**

Ahora cuando se edita una competencia:
1. ✅ Se actualiza exitosamente en BD
2. ✅ Se muestra mensaje "Competencia actualizada exitosamente" 
3. ✅ Se extrae código del diseño: `521240-1-220201501` → `521240-1`
4. ✅ Se establece `$_GET['codigo'] = '521240-1'`
5. ✅ Se ejecuta `ver_competencias` con el código correcto
6. ✅ Se cargan y muestran las competencias del diseño

## Archivos Modificados

- ✅ `/app/forms/index.php` - Correcciones de redirección

## Pruebas

- ✅ Crear competencia → Redirige correctamente a lista de competencias
- ✅ Editar competencia → Redirige correctamente a lista de competencias  
- ✅ Eliminar competencia → Redirige correctamente a lista de competencias
- ✅ Crear RAP → Redirige correctamente a lista de RAPs
- ✅ Editar RAP → Redirige correctamente a lista de RAPs
- ✅ Eliminar RAP → Redirige correctamente a lista de RAPs

---
**Fecha:** 18 de junio de 2025  
**Estado:** COMPLETADO ✅
