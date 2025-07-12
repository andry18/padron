<?php
require 'conexion.php';

$result = $conn->query("SELECT id, nombre, monto FROM conceptos_pago");
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}
echo json_encode($data);
?>
