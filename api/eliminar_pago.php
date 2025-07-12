<?php
// eliminar_pago.php
require 'conexion.php';

if (!$conn) {
    die("Conexion fallida: " . mysqli_connect_error());
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    $stmt = $conn->prepare("DELETE FROM pagos WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "Pago eliminado correctamente.";
    } else {
        echo "Error al eliminar el pago: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "ID inválido.";
}

$conn->close();
?>
