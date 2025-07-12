<?php
// exportar_excel.php
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=pagos_filtrados.xls");

require 'conexion.php';

$where = [];
$params = [];
$types = "";

if (!empty($_POST['persona'])) {
    $where[] = "p.persona_id = ?";
    $params[] = $_POST['persona'];
    $types .= "i";
}

if (!empty($_POST['concepto'])) {
    $where[] = "p.concepto_id = ?";
    $params[] = $_POST['concepto'];
    $types .= "i";
}

if (!empty($_POST['fecha'])) {
    $where[] = "DATE(p.fecha_pago) = ?";
    $params[] = $_POST['fecha'];
    $types .= "s";
}

$sql = "
SELECT per.paterno, per.materno, per.nombre, c.nombre AS concepto, p.monto, p.modo_pago, p.fecha_pago
FROM pagos p
JOIN personas per ON p.persona_id = per.id
JOIN conceptos_pago c ON p.concepto_id = c.id
";

if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$stmt = $conn->prepare($sql);
if ($where) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

// Tabla HTML compatible con Excel
echo "<table border='1'>
<tr>
  <th>Persona</th>
  <th>Concepto</th>
  <th>Monto</th>
  <th>Modo de Pago</th>
  <th>Fecha</th>
</tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>
      <td>{$row['paterno']} {$row['materno']}, {$row['nombre']}</td>
      <td>{$row['concepto']}</td>
      <td>{$row['monto']}</td>
      <td>{$row['modo_pago']}</td>
      <td>{$row['fecha_pago']}</td>
    </tr>";
}
echo "</table>";
?>
