<?php
// Script de debug para verificar el problema de filtros
echo "<h2>DEBUG: Verificación de Variables</h2>";

echo "<h3>Variables GET:</h3>";
echo "<pre>";
print_r($_GET);
echo "</pre>";

echo "<h3>Variables definidas en index.php:</h3>";
echo "Acción: " . ($accion ?? 'No definida') . "<br>";
echo "Tipo: " . ($tipo ?? 'No definida') . "<br>";
echo "Búsqueda: " . ($busqueda ?? 'No definida') . "<br>";
echo "Filtro horas min: " . ($filtro_horas_min ?? 'No definida') . "<br>";
echo "Filtro horas max: " . ($filtro_horas_max ?? 'No definida') . "<br>";

echo "<h3>URL actual:</h3>";
echo $_SERVER['REQUEST_URI'] ?? 'No disponible';

echo "<h3>Filtros array (si está disponible):</h3>";
if (isset($filtros_array)) {
    echo "<pre>";
    print_r($filtros_array);
    echo "</pre>";
}
?>
