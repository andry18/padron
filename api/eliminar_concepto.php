<?php
require 'conexion.php';
$id = $_GET['id'];

// Verificar si hay pagos asociados
$res = $conn->query("SELECT COUNT(*) AS total FROM pagos WHERE concepto_id = $id");
$row = $res->fetch_assoc();

if ($row['total'] > 0) {
    echo "No se puede eliminar el concepto porque tiene pagos asociados.";
} else {
    if ($conn->query("DELETE FROM conceptos_pago WHERE id = $id")) {
        echo "Concepto eliminado correctamente.";
    } else {
        echo "Error al eliminar concepto: " . $conn->error;
    }
}
?>
