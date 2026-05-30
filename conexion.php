<?php

$host = "localhost";
$db_name = "sistema_inventario";
$username = "root";
$password = "";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {

    $conn = new mysqli(
        $host,
        $username,
        $password,
        $db_name
    );

    $conn->set_charset("utf8");

} catch (mysqli_sql_exception $e) {

    die(
        "Error crítico: No se pudo establecer la conexión segura con el servidor de datos."
    );

}

?>
