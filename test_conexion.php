<?php

require_once 'conexion.php';

try {

    $query =
    "SELECT p.nombre_producto,
            p.precio,
            p.stock
     FROM productos p";

    $result =
    $conn->query($query);

    echo "<h1>Enlace Exitoso: Conexión y Consulta Verificadas</h1>";

    echo "<ul>";

    while ($prod =
    $result->fetch_assoc()) {

        echo "<li><strong>"
        . $prod['nombre_producto']
        . "</strong> - Precio: $"
        . $prod['precio']
        . " | Stock: "
        . $prod['stock']
        . " unidades.</li>";

    }

    echo "</ul>";

    $result->free();

} catch (mysqli_sql_exception $e) {

    echo "Error al consultar los datos: "
    . $e->getMessage();

}

?>
