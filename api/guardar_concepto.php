<?php
require 'conexion.php';

$nombre = $_POST['concepto'];
$monto = $_POST['monto'];

$stmt = $conn->prepare("INSERT INTO conceptos_pago (nombre, monto) VALUES (?, ?)");
$stmt->bind_param("sd", $nombre, $monto);

if ($stmt->execute()) {
    echo "Concepto guardado correctamente.";
} else {
    echo "Error al guardar concepto.";
}
?>
