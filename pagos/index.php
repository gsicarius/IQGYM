<?php
session_start();

require_once '../config/connection.php';

$mensaje = '';
$mostrar_formulario = false;
$cliente_seleccionado = null;

// ── Procesar pago POST ───────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'procesar_pago') {
    $id_cliente = (int)($_POST['idActual'] ?? 0);
    $plan_id = (int)($_POST['plan'] ?? 0);
    $metodo_pago = trim($_POST['formato_pago'] ?? '');
    $total_ingreso = isset($_POST['TotalIngreso']) ? (float)$_POST['TotalIngreso'] : 0;

    if (!$id_cliente || !$plan_id) {
        $mensaje = '✗ Debes seleccionar cliente y plan para procesar el pago';
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id_plan, nombre_plan, precio, duracion_dias FROM planes WHERE id_plan = ? AND activo = 1");
        mysqli_stmt_bind_param($stmt, 'i', $plan_id);
        mysqli_stmt_execute($stmt);
        $res_plan = mysqli_stmt_get_result($stmt);
        $plan = mysqli_fetch_assoc($res_plan);

        if (!$plan) {
            $mensaje = '✗ Plan seleccionado no válido';
        } elseif ($metodo_pago === 'efectivo' && $total_ingreso < (float)$plan['precio']) {
            $mensaje = '✗ El ingreso en efectivo debe ser igual o mayor al total a pagar';
        } else {
            $duracion = (int)$plan['duracion_dias'];
            $nueva_vencimiento = date('Y-m-d', strtotime("+{$duracion} days"));

            // Actualizar cliente con plan y vencimiento
            $stmt2 = mysqli_prepare($conn, "UPDATE clientes SET id_plan_actual = ?, fecha_vencimiento = ?, estatus = ? WHERE id_cliente = ?");
            $estatus = ($metodo_pago === 'efectivo') ? 'activo' : 'inactivo';
            mysqli_stmt_bind_param($stmt2, 'issi', $plan_id, $nueva_vencimiento, $estatus, $id_cliente);

            if (mysqli_stmt_execute($stmt2)) {
                if ($metodo_pago === 'efectivo') {
                    $mensaje = '✓ Pago en efectivo procesado: estatus activo, plan y vencimiento actualizados';
                } else {
                    $mensaje = '✓ Pago procesado. Para activación automática se requiere efectivo';
                }
            } else {
                $mensaje = '✗ Error al actualizar al cliente';
            }
        }
    }

    // recargar datos del cliente para mostrar en el formulario (si se procesó con éxito o no)
    if ($id_cliente) {
        $stmt = mysqli_prepare($conn, "SELECT c.*, p.nombre_plan, p.precio, p.duracion_dias FROM clientes c LEFT JOIN planes p ON c.id_plan_actual = p.id_plan WHERE c.id_cliente = ?");
        mysqli_stmt_bind_param($stmt, 'i', $id_cliente);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $cliente_seleccionado = mysqli_fetch_assoc($res);
        $mostrar_formulario = true;
    }
}

// ── Actualizar estatus automáticamente según fecha de vencimiento ─────────────
mysqli_query($conn, "
    UPDATE clientes 
    SET estatus='inactivo' 
    WHERE fecha_vencimiento < CURDATE() 
    AND estatus='activo'
");

// ── Cargar planes desde la DB ─────────────────────────────────────────────────
$planes = [];
$res_planes = mysqli_query($conn, "SELECT id_plan, nombre_plan, precio, duracion_dias FROM planes WHERE activo = 1 ORDER BY nombre_plan");
while ($row = mysqli_fetch_assoc($res_planes))
    $planes[] = $row;

// ── Cargar cliente si viene ID en GET ─────────────────────────────────────────
if (isset($_GET['id']) && $_GET['id'] !== 'nuevo') {
    $id = (int) $_GET['id'];
    $stmt = mysqli_prepare($conn, "
        SELECT c.*, p.nombre_plan, p.precio, p.duracion_dias 
        FROM clientes c
        LEFT JOIN planes p ON c.id_plan_actual = p.id_plan
        WHERE c.id_cliente = ?
    ");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $cliente_seleccionado = mysqli_fetch_assoc($res);
    $mostrar_formulario = true;
} elseif (isset($_GET['id']) && $_GET['id'] === 'nuevo') {
    $mostrar_formulario = true;
}

// ── Cargar todos los clientes con búsqueda ────────────────────────────────────
$busqueda = trim($_GET['buscar'] ?? '');
$clientes = [];

if ($busqueda) {
    $stmt = mysqli_prepare($conn, "
        SELECT id_cliente, nombre, apellido, email, telefono, estatus, fecha_vencimiento 
        FROM clientes 
        WHERE nombre LIKE ? OR apellido LIKE ? OR email LIKE ? OR telefono LIKE ?
        ORDER BY nombre
    ");
    $busqueda_param = "%$busqueda%";
    mysqli_stmt_bind_param($stmt, 'ssss', $busqueda_param, $busqueda_param, $busqueda_param, $busqueda_param);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
} else {
    $res = mysqli_query($conn, "
        SELECT id_cliente, nombre, apellido, email, telefono, estatus, fecha_vencimiento 
        FROM clientes 
        ORDER BY nombre
    ");
}

while ($row = mysqli_fetch_assoc($res))
    $clientes[] = $row;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <style>
        /* Chrome, Safari, Edge */
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>
</head>

<body class="bg-gray-900">
    <?php include '../includes/sidebar.php'; ?>

    <div class="lg:ml-64">
        <header>
            <h1 class="font-semibold text-white text-3xl ml-3 text-center py-4">
                Pagos
            </h1>
        </header>

        <main class="px-6">

            <?php if ($mensaje): ?>
                <div class="mb-4 px-4 py-3 rounded-lg text-sm font-semibold max-w-6xl mx-auto
                    <?= str_starts_with($mensaje, '✓') ? 'bg-green-700 text-green-100' : 'bg-red-700 text-red-100' ?>">
                    <?= htmlspecialchars($mensaje) ?>
                </div>
            <?php endif; ?>

            <!-- ═══════════════════════════════════════════════════════════════════ -->
            <!-- TABLA DE CLIENTES -->
            <!-- ═══════════════════════════════════════════════════════════════════ -->
            <?php if (!$mostrar_formulario): ?>
                <section class="bg-gray-800 rounded-lg p-6 max-w-6xl mx-auto">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                        <h2 class="text-white text-xl font-semibold">
                            <i class="fa-solid fa-users mr-2"></i> Clientes Registrados
                        </h2>

                        <!-- Barra de búsqueda -->
                        <div class="flex gap-3 w-full md:w-auto">
                            <form method="GET" class="flex gap-2 flex-1 md:flex-initial">
                                <div class="relative flex-1 md:w-64">
                                    <input type="text" name="buscar" value="<?= htmlspecialchars($busqueda) ?>"
                                        placeholder="Buscar por nombre, teléfono o email..."
                                        class="w-full px-4 py-2 pl-10 bg-gray-700 border border-gray-600 rounded-lg 
                                        text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <i class="fa-solid fa-search absolute left-3 top-3 text-gray-400"></i>
                                </div>
                                <button type="submit"
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                                    Buscar
                                </button>
                                <?php if ($busqueda): ?>
                                    <a href="index.php"
                                        class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                                        <i class="fa-solid fa-times"></i>
                                    </a>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>

                    <?php if ($busqueda): ?>
                        <div class="mb-4 text-gray-400 text-sm">
                            <i class="fa-solid fa-info-circle mr-1"></i>
                            Mostrando <?= count($clientes) ?> resultado(s) para "<?= htmlspecialchars($busqueda) ?>"
                        </div>
                    <?php endif; ?>

                    <div class="overflow-x-auto">
                        <table class="w-full text-white">
                            <thead class="bg-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-left">ID</th>
                                    <th class="px-4 py-3 text-left">Nombre completo</th>
                                    <th class="px-4 py-3 text-left">Teléfono</th>
                                    <th class="px-4 py-3 text-left">Email</th>
                                    <th class="px-4 py-3 text-left">Vencimiento</th>
                                    <th class="px-4 py-3 text-left">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($clientes) > 0): ?>
                                    <?php foreach ($clientes as $c): ?>
                                        <tr class="border-b border-gray-700 hover:bg-gray-750 transition">
                                            <td class="px-4 py-3"><?= $c['id_cliente'] ?></td>
                                            <td class="px-4 py-3">
                                                <a href="?id=<?= $c['id_cliente'] ?>"
                                                    class="text-blue-400 hover:text-blue-300 hover:underline">
                                                    <?= htmlspecialchars($c['nombre'] . ' ' . $c['apellido']) ?>
                                                </a>
                                            </td>
                                            <td class="px-4 py-3"><?= htmlspecialchars($c['telefono']) ?></td>
                                            <td class="px-4 py-3"><?= htmlspecialchars($c['email']) ?></td>
                                            <td class="px-4 py-3">
                                                <?php if ($c['fecha_vencimiento']): ?>
                                                    <?php
                                                    $dias_restantes = floor((strtotime($c['fecha_vencimiento']) - time()) / 86400);
                                                    $color = $dias_restantes < 0 ? 'text-red-400' : ($dias_restantes <= 7 ? 'text-yellow-400' : 'text-gray-300');
                                                    ?>
                                                    <span class="<?= $color ?>">
                                                        <?= date('d/m/Y', strtotime($c['fecha_vencimiento'])) ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-gray-500">Sin plan</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-4 py-3">
                                                <?php
                                                $bg_color = $c['estatus'] === 'activo' ? 'bg-green-600' : 'bg-red-600';
                                                ?>
                                                <span class="px-2 py-1 <?= $bg_color ?> text-white text-xs rounded uppercase">
                                                    <?= htmlspecialchars($c['estatus']) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                                            <?= $busqueda ? 'No se encontraron resultados' : 'No hay clientes registrados aún' ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- ═══════════════════════════════════════════════════════════════════ -->
                <!-- FORMULARIO DE PAGO -->
                <!-- ═══════════════════════════════════════════════════════════════════ -->
            <?php else: ?>
                <?php include 'form.php'; ?>
            <?php endif; ?>
        </main>
    </div>

    <div id="sidebarOverlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-30 lg:hidden hidden"></div>
    <script src="../assets/js/sidebar.js"></script>

    <?php include '../includes/footer.php'; ?>
</body>

</html>