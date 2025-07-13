<?php
require 'api/conexion.php';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=rendiciones.xls");
echo "<table border='1'>";
echo "<tr>
        <th>ID</th>
        <th>Motivo</th>
        <th>Monto</th>
        <th>Fecha</th>
      </tr>";

$res = $conn->query("SELECT * FROM rendiciones ORDER BY fecha DESC");
while ($row = $res->fetch_assoc()) {
    echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['motivo']}</td>
            <td>{$row['monto']}</td>
            <td>{$row['fecha']}</td>
          </tr>";
}
echo "</table>";
exit;
