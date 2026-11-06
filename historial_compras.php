
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
require_once 'conexion.php';

$sql = "SELECT c.id AS numero_factura, c.fecha, p.nombre_empresa AS proveedor,
u.nombre_completo AS cajero, c.total
FROM compras c
INNER JOIN proveedores p ON c.proveedor_id = p.id
INNER JOIN usuarios u ON c.usuario_id = u.id
ORDER BY c.id DESC";
// Actualizar consulta SQL
$sql = "SELECT c.id AS numero_factura, c.fecha, c.total, c.estado, p.nombre_empresa AS
proveedor, u.nombre_completo AS usuario
FROM compras c
INNER JOIN proveedores p ON c.proveedor_id = p.id
INNER JOIN usuarios u ON c.usuario_id = u.id
ORDER BY c.fecha DESC";

$resultado = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Historial de Compras</title>
<!-- Formulario de Filtros Multicriterio con Persistencia de Datos -->
<div class="card mb-4 shadow-sm">
<div class="card-header bg-dark text-white font-weight-bold">
🔍 Filtros de Búsqueda Avanzada
</div>
<div class="card-body">
<form method="GET" action="historial_compras.php" class="row">
<div class="col-md-3 mb-2">
<label>Desde Fecha:</label>
<input type="date" name="fecha_inicio" class="form-control"
value="<?php echo htmlspecialchars($fecha_inicio); ?>">
</div>
<div class="col-md-3 mb-2">
<label>Hasta Fecha:</label>
<input type="date" name="fecha_fin" class="form-control"
value="<?php echo htmlspecialchars($fecha_fin); ?>">
</div>
<div class="col-md-3 mb-2">
<label>Proveedor:</label>
<select name="id_proveedor" class="form-control">
<option value="0">-- Todos los Proveedores --</option>
<?php while ($p = $res_prov->fetch_assoc()): ?>
<option value="<?php echo $p['id']; ?>"
<?php echo ($id_proveedor == $p['id']) ? 'selected' : ''; ?>>
<?php echo htmlspecialchars($p['nombre_empresa']); ?>

</option>
<?php endwhile; ?>
</select>
</div>
<div class="col-md-3 mb-2">
<label>Estado de Factura:</label>
<select name="estado" class="form-control">
<option value="">-- Todos --</option>
<option value="COMPLETADA" <?php echo ($estado === 'COMPLETADA') ?
'selected' : ''; ?>>Completada</option>
<option value="ANULADA" <?php echo ($estado === 'ANULADA') ? 'selected' :
''; ?>>Anulada</option>
</select>
</div>
<div class="col-md-12 text-right mt-2">
<button type="submit" class="btn btn-primary font-weight-bold">
⚡ Aplicar Filtros
</button>
<a href="historial_compras.php" class="btn btn-secondary font-weight-bold">
🔄 Limpiar Filtros
</a>
</div>
</form>
</div>
</div>
<!-- Panel de Métricas / Totalizadores Financieros Dinámicos -->
<div class="row mb-4">
<div class="col-md-4">
<div class="card bg-success text-white shadow-sm">
<div class="card-body">
<h6 class="card-title text-uppercase mb-1">Monto Total Invertido</h6>
<h2 class="font-weight-bold mb-0">$<?php echo number_format($total_gastado,
2); ?></h2>
<small>Capital neto en compras válidas</small>
</div>
</div>
</div>
<div class="col-md-4">
<div class="card bg-primary text-white shadow-sm">
<div class="card-body">
<h6 class="card-title text-uppercase mb-1">Compras Completadas</h6>
<h2 class="font-weight-bold mb-0"><?php echo $conteo_completadas; ?></h2>
<small>Facturas vigentes en inventario</small>
</div>
</div>
</div>
<div class="col-md-4">
<div class="card bg-danger text-white shadow-sm">
<div class="card-body">
<h6 class="card-title text-uppercase mb-1">Facturas Anuladas</h6>
<h2 class="font-weight-bold mb-0"><?php echo $conteo_anuladas; ?></h2>
<small>Operaciones revertidas sin costo</small>
</div>
</div>
</div>
</div>
<style>
body { font-family: Arial, sans-serif; background-color: #f4f6f9; margin: 20px; }
.contenedor { background: #ffffff; padding: 25px; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
.encabezado { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }

.btn-regresar { background: #64748b; color: #ffffff; padding: 8px 12px; text-decoration: none; border-radius: 4px; }

.btn-detalle { background: #2563eb; color: white; padding: 6px 12px; text-decoration: none; border-radius: 4px; font-size: 13px; font-weight: bold; }

table { width: 100%; border-collapse: collapse; }
th, td { padding: 10px; border-bottom: 1px solid #cbd5e1; text-align: left; }
th { background-color: #1e293b; color: #ffffff; }
.monto { color: #059669; font-weight: bold; }
</style>
</head>
<body>
<div class="contenedor">
<div class="encabezado">
<h2>Historial de Ingresos de Mercaderia</h2>
<a href="dashboard.php" class="btn-regresar">Regresar al Panel</a>
</div>

<table>
<thead>
<tr>
<th>Estado</th>
<th>Acciones</th>
<th>Numero</th>
<th>Fecha y Hora</th>
<th>Proveedor</th>
<th>Usuario Responsable</th>
<th>Total Invertido</th>
<th>Acciones</th>
</tr>
</thead>
<tbody>
<?php
if ($resultado->num_rows > 0) {
    while($fila = $resultado->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $fila['numero_factura'] . "</td>";
        echo "<td>" . $fila['fecha'] . "</td>";
        echo "<td>" . $fila['proveedor'] . "</td>";
        echo "<td>" . $fila['cajero'] . "</td>";
        echo "<td class='monto'>$" . number_format($fila['total'], 2) . "</td>";
        echo "<td><a href='detalle_compra.php?id=" . $fila['numero_factura'] . "' class='btn-detalle'>Ver Detalle</a></td>";
        echo "</tr>";
        
<!-- En <tbody> dentro del while -->
<td>
<?php if ($fila['estado'] === 'COMPLETADA'): ?>

<span style="background: #dcfce7; color: #15803d; padding: 4px 8px; border-
radius: 12px; font-size: 11px; font-weight: bold;">ACTIVA</span>

<?php else: ?>

<span style="background: #fee2e2; color: #b91c1c; padding: 4px 8px; border-
radius: 12px; font-size: 11px; font-weight: bold;">ANULADA</span>

<?php endif; ?>
</td>
        <td>
<a href="detalle_compra.php?id=<?php echo $fila['numero_factura']; ?>" style="background:
#2563eb; color: white; padding: 6px 12px; text-decoration: none; border-radius: 4px; font-size:
13px; font-weight: bold;">Ver Detalle</a>
</td>
    }
} else {
    echo "<tr><td colspan='6'>Sin registros de compras disponibles.</td></tr>";
}
?>
</tbody>
</table>
</div>
</body>
</html>
