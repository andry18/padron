<?php
// Conexión a la base de datos
require 'api/conexion.php';

// Procesar formulario de guardado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar'])) {
    $motivo = $conn->real_escape_string($_POST['motivo']);
    $monto = floatval($_POST['monto']);
    $fecha = $conn->real_escape_string($_POST['fecha']);
    $conn->query("INSERT INTO rendiciones (motivo, monto, fecha) VALUES ('$motivo', $monto, '$fecha')");
    header("Location: rendicion.php");
    exit;
}

// Procesar edición
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar'])) {
    $id = intval($_POST['id']);
    $motivo = $conn->real_escape_string($_POST['motivo']);
    $monto = floatval($_POST['monto']);
    $fecha = $conn->real_escape_string($_POST['fecha']);
    $conn->query("UPDATE rendiciones SET motivo='$motivo', monto=$monto, fecha='$fecha' WHERE id=$id");
    header("Location: rendicion.php");
    exit;
}

// Procesar eliminación
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $conn->query("DELETE FROM rendiciones WHERE id=$id");
    header("Location: rendicion.php");
    exit;
}

// Obtener datos para edición
$editData = null;
if (isset($_GET['editar'])) {
    $id = intval($_GET['editar']);
    $res = $conn->query("SELECT * FROM rendiciones WHERE id=$id");
    $editData = $res->fetch_assoc();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Rendición</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/estilo.css">
    <style>
        /* Puedes ajustar estos estilos según pagos.php */
        .container { max-width: 800px; margin: 30px auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px #ccc; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input[type="text"], input[type="number"], input[type="date"] { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
        button, .btn { padding: 8px 16px; border: none; border-radius: 4px; background: #007bff; color: #fff; cursor: pointer; }
        .btn-danger { background: #dc3545; }
        .btn-secondary { background: #6c757d; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background: #f8f9fa; }
        .acciones a { margin-right: 8px; }
    </style>
</head>
<body>
    <div class="container-fluid mt-4">
  <?php include_once('util/menu.php'); ?>
  <h2 class="text-center mb-4">Rendicion de gastos</h2>


        <h2><?php echo $editData ? "Editar Rendición" : "Nueva Rendición"; ?></h2>
        <form method="post">
            <div class="form-group">
                <input type="hidden" name="id" value="<?php echo $editData['id'] ?? ''; ?>">
                <label>Motivo:</label>
                <input type="text" name="motivo" required value="<?php echo $editData['motivo'] ?? ''; ?>">
            </div>
            <div class="form-group">
                <label>Monto:</label>
                <input type="number" step="0.01" name="monto" required value="<?php echo $editData['monto'] ?? ''; ?>">
            </div>
            <div class="form-group">
                <label>Fecha:</label>
                <input type="date" name="fecha" required value="<?php echo $editData['fecha'] ?? ''; ?>">
            </div>
            <button type="submit" name="<?php echo $editData ? 'editar' : 'guardar'; ?>">
                <?php echo $editData ? 'Actualizar' : 'Guardar'; ?>
            </button>
            <?php if ($editData): ?>
                <a href="rendicion.php" class="btn btn-secondary">Cancelar</a>
            <?php endif; ?>
        </form>

        <h2 class="mt-5">Listado de Rendiciones</h2>
        <div class="mb-3">
            <a href="export_rendiciones.php" class="btn btn-success">Exportar a Excel</a>
        </div>
        <table>
            <tr>
                <th>ID</th>
                <th>Motivo</th>
                <th>Monto</th>
                <th>Fecha</th>
                <th>Acciones</th>
            </tr>
            <?php
            $res = $conn->query("SELECT * FROM rendiciones ORDER BY fecha DESC");
            while ($row = $res->fetch_assoc()):
            ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['motivo']); ?></td>
                <td><?php echo number_format($row['monto'], 2); ?></td>
                <td><?php echo $row['fecha']; ?></td>
                <td class="acciones">
                    <a href="rendicion.php?editar=<?php echo $row['id']; ?>" class="btn btn-secondary">Editar</a>
                    <a href="rendicion.php?eliminar=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('¿Eliminar este registro?');">Eliminar</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>
