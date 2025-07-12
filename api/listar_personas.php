<?php
require 'conexion.php';

$result = $conn->query("SELECT id, paterno, materno, nombre FROM personas");
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}
echo json_encode($data);
?>
