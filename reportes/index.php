<?php
session_start();
require_once '../config/connection.php';

// ── KPIs ──────────────────────────────────────────────────────────────────────
$r = mysqli_query($conn, "SELECT COUNT(*) as total, SUM(estatus='activo') as activos, SUM(estatus='inactivo') as inactivos, SUM(estatus='suspendido') as suspendidos FROM clientes");
$kpi_clientes = mysqli_fetch_assoc($r);

$r = mysqli_query($conn, "SELECT COALESCE(SUM(monto),0) as total FROM pagos WHERE estatus='completado' AND MONTH(fecha_pago)=MONTH(NOW()) AND YEAR(fecha_pago)=YEAR(NOW())");
$kpi_ingresos = mysqli_fetch_assoc($r)['total'];

$r = mysqli_query($conn, "SELECT COUNT(*) as total FROM clientes WHERE fecha_vencimiento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY) AND estatus='activo'");
$kpi_por_vencer = mysqli_fetch_assoc($r)['total'];

$r = mysqli_query($conn, "SELECT COUNT(*) as total FROM clases WHERE activa=1");
$kpi_clases = mysqli_fetch_assoc($r)['total'];

$r = mysqli_query($conn, "SELECT COUNT(*) as total, SUM(estatus='completado') as completados, SUM(estatus='pendiente') as pendientes FROM pagos WHERE MONTH(fecha_pago)=MONTH(NOW()) AND YEAR(fecha_pago)=YEAR(NOW())");
$kpi_pagos = mysqli_fetch_assoc($r);

// ── GRÁFICA: Ingresos últimos 6 meses ────────────────────────────────────────
$r = mysqli_query($conn, "
    SELECT DATE_FORMAT(fecha_pago, '%b %Y') as mes, COALESCE(SUM(monto),0) as total
    FROM pagos
    WHERE estatus='completado' AND fecha_pago >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY YEAR(fecha_pago), MONTH(fecha_pago)
    ORDER BY YEAR(fecha_pago), MONTH(fecha_pago)
");
$ingresos_labels = [];
$ingresos_data = [];
while ($row = mysqli_fetch_assoc($r)) {
    $ingresos_labels[] = $row['mes'];
    $ingresos_data[] = (float) $row['total'];
}

// ── GRÁFICA: Dona clientes ────────────────────────────────────────────────────
$dona_data = [(int) $kpi_clientes['activos'], (int) $kpi_clientes['inactivos'], (int) $kpi_clientes['suspendidos']];

// ── TABLA: Últimos pagos del mes ─────────────────────────────────────────────
$r = mysqli_query($conn, "SELECT * FROM v_pagos_mes_actual ORDER BY fecha_pago DESC LIMIT 8");
$ultimos_pagos = [];
while ($row = mysqli_fetch_assoc($r))
    $ultimos_pagos[] = $row;

// ── TABLA: Vencimientos próximos ─────────────────────────────────────────────
$r = mysqli_query($conn, "
    SELECT CONCAT(nombre,' ',apellido) as nombre_completo, fecha_vencimiento,
           DATEDIFF(fecha_vencimiento, CURDATE()) as dias
    FROM clientes
    WHERE fecha_vencimiento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 14 DAY)
      AND estatus='activo'
    ORDER BY fecha_vencimiento ASC
    LIMIT 6
");
$por_vencer = [];
while ($row = mysqli_fetch_assoc($r))
    $por_vencer[] = $row;
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — IQGYM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>

<body class="bg-gray-900">
    <?php include '../includes/sidebar.php'; ?>

    <div class="lg:ml-64">
        <header>
            <h1 class="font-semibold text-white text-3xl ml-3 text-center py-4">
                Dashboard
            </h1>
        </header>

        <main class="px-6 pb-8">

            <!-- ── KPIs ───────────────────────────────────────────────────── -->
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">

                <div class="bg-gray-800 rounded-lg p-4 border border-gray-700">
                    <div class="flex items-center gap-2 mb-3">
                        <i class="fa-solid fa-users text-blue-400 text-sm"></i>
                        <span class="text-gray-400 text-xs uppercase tracking-wider font-semibold">Miembros</span>
                    </div>
                    <p class="text-white text-2xl font-bold mb-1"><?= $kpi_clientes['total'] ?></p>
                    <p class="text-xs text-gray-500">
                        <span class="text-green-400"><?= $kpi_clientes['activos'] ?> activos</span>
                        &nbsp;·&nbsp;
                        <span class="text-red-400"><?= $kpi_clientes['inactivos'] ?> inactivos</span>
                    </p>
                </div>

                <div class="bg-gray-800 rounded-lg p-4 border border-gray-700">
                    <div class="flex items-center gap-2 mb-3">
                        <i class="fa-solid fa-dollar-sign text-green-400 text-sm"></i>
                        <span class="text-gray-400 text-xs uppercase tracking-wider font-semibold">Ingresos</span>
                    </div>
                    <p class="text-white text-2xl font-bold mb-1">$<?= number_format($kpi_ingresos, 0) ?></p>
                    <p class="text-xs text-gray-500"><?= $kpi_pagos['completados'] ?> pagos este mes</p>
                </div>

                <div class="bg-gray-800 rounded-lg p-4 border border-gray-700">
                    <div class="flex items-center gap-2 mb-3">
                        <i class="fa-solid fa-clock text-yellow-400 text-sm"></i>
                        <span class="text-gray-400 text-xs uppercase tracking-wider font-semibold">Por vencer</span>
                    </div>
                    <p class="text-white text-2xl font-bold mb-1"><?= $kpi_por_vencer ?></p>
                    <p class="text-xs text-gray-500">próximos 7 días</p>
                </div>

                <div class="bg-gray-800 rounded-lg p-4 border border-gray-700">
                    <div class="flex items-center gap-2 mb-3">
                        <i class="fa-solid fa-dumbbell text-purple-400 text-sm"></i>
                        <span class="text-gray-400 text-xs uppercase tracking-wider font-semibold">Clases</span>
                    </div>
                    <p class="text-white text-2xl font-bold mb-1"><?= $kpi_clases ?></p>
                    <p class="text-xs text-gray-500">activas en horario</p>
                </div>

                <div class="bg-gray-800 rounded-lg p-4 border border-gray-700">
                    <div class="flex items-center gap-2 mb-3">
                        <i class="fa-solid fa-hourglass-half text-orange-400 text-sm"></i>
                        <span class="text-gray-400 text-xs uppercase tracking-wider font-semibold">Pendientes</span>
                    </div>
                    <p class="text-white text-2xl font-bold mb-1"><?= $kpi_pagos['pendientes'] ?></p>
                    <p class="text-xs text-gray-500">pagos pendientes</p>
                </div>

            </div>

            <!-- ── GRÁFICAS ────────────────────────────────────────────────── -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">

                <div class="bg-gray-800 rounded-lg p-6 border border-gray-700 lg:col-span-2">
                    <h2 class="text-white font-semibold mb-4">
                        <i class="fa-solid fa-chart-line mr-2 text-gray-400"></i>Ingresos últimos 6 meses
                    </h2>
                    <canvas id="chartIngresos" height="110"></canvas>
                </div>

                <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
                    <h2 class="text-white font-semibold mb-4">
                        <i class="fa-solid fa-chart-pie mr-2 text-gray-400"></i>Estado de miembros
                    </h2>
                    <canvas id="chartDona" height="160"></canvas>
                    <div class="flex justify-center gap-4 mt-4">
                        <span class="flex items-center gap-1 text-xs text-gray-500">
                            <span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span>Activos
                        </span>
                        <span class="flex items-center gap-1 text-xs text-gray-500">
                            <span class="w-2 h-2 rounded-full bg-red-500 inline-block"></span>Inactivos
                        </span>
                        <span class="flex items-center gap-1 text-xs text-gray-500">
                            <span class="w-2 h-2 rounded-full bg-yellow-500 inline-block"></span>Suspendidos
                        </span>
                    </div>
                </div>

            </div>

            <!-- ── TABLAS ──────────────────────────────────────────────────── -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

                <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
                    <h2 class="text-white font-semibold mb-4">
                        <i class="fa-solid fa-receipt mr-2 text-gray-400"></i>Últimos pagos del mes
                    </h2>
                    <?php if (count($ultimos_pagos) > 0): ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-white">
                                <thead class="bg-gray-700">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">
                                            Cliente</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Plan
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Monto
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">
                                            Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($ultimos_pagos as $p): ?>
                                        <?php
                                        $badge = match ($p['estatus']) {
                                            'completado' => 'bg-green-600',
                                            'pendiente' => 'bg-yellow-600',
                                            'cancelado' => 'bg-red-600',
                                            default => 'bg-gray-600'
                                        };
                                        ?>
                                        <tr class="border-b border-gray-700 hover:bg-gray-700 transition">
                                            <td class="px-4 py-3 text-sm"><?= htmlspecialchars($p['cliente']) ?></td>
                                            <td class="px-4 py-3 text-sm text-gray-400">
                                                <?= htmlspecialchars($p['nombre_plan']) ?></td>
                                            <td class="px-4 py-3 text-sm text-green-400 font-semibold">
                                                $<?= number_format($p['monto'], 2) ?></td>
                                            <td class="px-4 py-3">
                                                <span class="px-2 py-1 <?= $badge ?> text-white text-xs rounded uppercase">
                                                    <?= $p['estatus'] ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-center text-gray-400 py-8 text-sm">Sin pagos este mes</p>
                    <?php endif; ?>
                </div>

                <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
                    <h2 class="text-white font-semibold mb-4">
                        <i class="fa-solid fa-calendar-xmark mr-2 text-gray-400"></i>Vencimientos próximos (14 días)
                    </h2>
                    <?php if (count($por_vencer) > 0): ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-white">
                                <thead class="bg-gray-700">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">
                                            Cliente</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Vence
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Días
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($por_vencer as $v): ?>
                                        <?php
                                        $dias = (int) $v['dias'];
                                        $color = $dias <= 3 ? 'text-red-400' : ($dias <= 7 ? 'text-yellow-400' : 'text-gray-300');
                                        ?>
                                        <tr class="border-b border-gray-700 hover:bg-gray-700 transition">
                                            <td class="px-4 py-3 text-sm"><?= htmlspecialchars($v['nombre_completo']) ?></td>
                                            <td class="px-4 py-3 text-sm text-gray-400">
                                                <?= date('d/m/Y', strtotime($v['fecha_vencimiento'])) ?></td>
                                            <td class="px-4 py-3 text-sm font-semibold <?= $color ?>">
                                                <?= $dias === 0 ? 'Hoy' : $dias . ' días' ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-center text-gray-400 py-8 text-sm">Sin vencimientos próximos</p>
                    <?php endif; ?>
                </div>

            </div>

        </main>
    </div>

    <div id="sidebarOverlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-30 lg:hidden hidden"></div>
    <script src="../assets/js/sidebar.js"></script>

    <script>
        const gridColor = 'rgba(255,255,255,0.05)';
        const tickColor = '#6b7280';
        const tooltipDefaults = {
            backgroundColor: '#1f2937',
            borderColor: '#374151',
            borderWidth: 1,
            titleColor: '#9ca3af',
            bodyColor: '#f9fafb',
            padding: 10
        };

        new Chart(document.getElementById('chartIngresos'), {
            type: 'line',
            data: {
                labels: <?= json_encode($ingresos_labels) ?>,
                datasets: [{
                    label: 'Ingresos',
                    data: <?= json_encode($ingresos_data) ?>,
                    borderColor: '#22c55e',
                    backgroundColor: 'rgba(34,197,94,0.08)',
                    borderWidth: 2,
                    pointBackgroundColor: '#22c55e',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        ...tooltipDefaults,
                        callbacks: { label: ctx => ' $' + ctx.parsed.y.toLocaleString() }
                    }
                },
                scales: {
                    x: { grid: { color: gridColor }, ticks: { color: tickColor, font: { size: 11 } } },
                    y: { grid: { color: gridColor }, ticks: { color: tickColor, font: { size: 11 }, callback: v => '$' + v.toLocaleString() } }
                }
            }
        });

        new Chart(document.getElementById('chartDona'), {
            type: 'doughnut',
            data: {
                labels: ['Activos', 'Inactivos', 'Suspendidos'],
                datasets: [{
                    data: <?= json_encode($dona_data) ?>,
                    backgroundColor: ['#22c55e', '#ef4444', '#eab308'],
                    borderColor: '#1f2937',
                    borderWidth: 3,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                cutout: '68%',
                plugins: {
                    legend: { display: false },
                    tooltip: { ...tooltipDefaults }
                }
            }
        });
    </script>

    <?php include '../includes/footer.php'; ?>
</body>

</html>