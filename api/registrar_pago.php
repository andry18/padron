<?php
require 'conexion.php';

$persona = $_POST['persona'];
$concepto = $_POST['conceptoPago'];
$monto = $_POST['montoPagado'];
$modo = $_POST['modoPago'];
$fecha = $_POST['fechaPago'];

$stmt = $conn->prepare("INSERT INTO pagos (persona_id, concepto_id, monto, modo_pago, fecha_pago) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("iisss", $persona, $concepto, $monto, $modo, $fecha);

if ($stmt->execute()) {
    echo "Pago registrado correctamente.";
} else {
    echo "Error al registrar el pago: " . $stmt->error;
}
?>