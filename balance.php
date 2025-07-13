<?php
require 'api/conexion.php';

// Consulta ingresos agrupados por tipo de pago (JOIN conceptos_pago)
$resPagos = $conn->query("
    SELECT cp.nombre AS tipo_pago, SUM(p.monto) AS total
    FROM pagos p
    INNER JOIN conceptos_pago cp ON p.concepto_id = cp.id
    GROUP BY cp.id, cp.nombre
");
$totalPagos = $conn->query("SELECT SUM(monto) AS total FROM pagos")->fetch_assoc()['total'] ?? 0;

// Consulta rendiciones (egresos)
$resRendiciones = $conn->query("SELECT motivo, monto, fecha FROM rendiciones ORDER BY fecha DESC");
$totalRendiciones = $conn->query("SELECT SUM(monto) AS total FROM rendiciones")->fetch_assoc()['total'] ?? 0;

// Balance
$montoRestante = $totalPagos - $totalRendiciones;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Balance</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/estilo.css">
    <style>
        .cuadro { background: #fff; border-radius: 8px; box-shadow: 0 2px 8px #ccc; padding: 20px; margin-bottom: 30px; }
        .cuadro h4 { margin-bottom: 20px; }
        .table th, .table td { vertical-align: middle; }
        .resumen { font-weight: bold; }
    </style>
</head>
<body>
    <div class="container-fluid mt-4">
        <?php include_once('util/menu.php'); ?>
        <h2 class="text-center mb-4">Balance General</h2>

        <div class="cuadro">
            <h4>Total de Ingresos por Tipo de Pago</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tipo de Pago</th>
                        <th>Monto Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $resPagos->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['tipo_pago']); ?></td>
                        <td><?php echo number_format($row['total'], 2); ?></td>
                    </tr>
                    <?php endwhile; ?>
                    <tr class="table-success resumen">
                        <td>Total de Ingresos</td>
                        <td><?php echo number_format($totalPagos, 2); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="cuadro">
            <h4>Total de Egresos (Rendiciones)</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Motivo</th>
                        <th>Monto</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $resRendiciones->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['motivo']); ?></td>
                        <td><?php echo number_format($row['monto'], 2); ?></td>
                        <td><?php echo $row['fecha']; ?></td>
                    </tr>
                    <?php endwhile; ?>
                    <tr class="table-danger resumen">
                        <td colspan="2">Total de Egresos</td>
                        <td><?php echo number_format($totalRendiciones, 2); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="cuadro">
            <h4>Resumen de Caja</h4>
            <table class="table table-bordered">
                <tr>
                    <th>Total de Ingresos</th>
                    <td><?php echo number_format($totalPagos, 2); ?></td>
                </tr>
                <tr>
                    <th>Total de Egresos</th>
                    <td><?php echo number_format($totalRendiciones, 2); ?></td>
                </tr>
                <tr class="table-info resumen">
                    <th>Monto restante en caja</th>
                    <td><?php echo number_format($montoRestante, 2); ?></td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
