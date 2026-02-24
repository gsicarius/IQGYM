<?php
session_start();

require_once '../config/connection.php';

$mensaje = '';
$mostrar_formulario = false;
$cliente_seleccionado = null;

// ── Actualizar estatus automáticamente según fecha de vencimiento ─────────────
mysqli_query($conn, "
    UPDATE clientes 
    SET estatus='inactivo' 
    WHERE fecha_vencimiento < CURDATE() 
    AND estatus='activo'
");

// ── Cargar cliente si viene ID en GET ─────────────────────────────────────────
if (isset($_GET['id']) && $_GET['id'] !== 'nuevo') {
    $id = (int) $_GET['id'];
    $stmt = mysqli_prepare($conn, "SELECT * FROM clientes WHERE id_cliente = ?");
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
                <!-- FORMULARIO DE PAGO-->
                <!-- ═══════════════════════════════════════════════════════════════════ -->

            <?php else: ?>
                <section class="bg-gray-800 rounded-lg p-6 max-w-4xl mx-auto">
                    <div class="mb-4">
                        <a href="index.php" class="text-blue-400 hover:text-blue-300 flex items-center gap-2 w-fit">
                            <i class="fa-solid fa-arrow-left"></i> Regresar a la lista
                        </a>
                    </div>

                    <h2 class="text-white text-2xl font-semibold text-center mb-6">
                        <?= $cliente_seleccionado
                            ? 'Formulario de Pago <br><br> ' . htmlspecialchars($cliente_seleccionado['nombre'] . ' ' . $cliente_seleccionado['apellido'])
                            : 'Registrar Pago' ?>
                    </h2>

                    

                    <form method="POST" action="index.php">
                        <div class="flex  gap-6 items-center">

                        <div class="flex flex-col items-center justify-center min-w-[160px]">
                        <img src="../assets/IMAGES/logo.png" alt="FotoCliente"
                            class="w-40 h-40 object-contain drop-shadow-2xl">
                        </div>
                            <div class="flex gap-4 w-full max-w-2xl mb-4">
                                <div class="flex-1">
                                    <h3 class="mb-2 text-white">Nombre:</h3>
                                    <input type="text" name="txtnombre" id="txtnombre"
                                        value="<?= htmlspecialchars($cliente_seleccionado['nombre'] ?? '') ?>" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg 
                                        text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 
                                        focus:border-transparent transition mb-4" required>

                                    <h3 class="mb-2 text-white">Telefono:</h3>
                                    <input type="text" name="txttelefono" id="txttelefono"
                                        value="<?= htmlspecialchars($cliente_seleccionado['telefono'] ?? '') ?>" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg 
                                        text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 
                                        focus:border-transparent transition" required>
                                </div>
                                <div class="flex-1">
                                    <h3 class="mb-2 text-white">Apellido:</h3>
                                    <input type="text" name="txtapellido" id="txtapellido"
                                        value="<?= htmlspecialchars($cliente_seleccionado['apellido'] ?? '') ?>" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg 
                                        text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 
                                        focus:border-transparent transition mb-4" required>

                                    <h3 class="mb-2 text-white">Email:</h3>
                                    <input type="email" name="txtemail" id="txtemail"
                                        value="<?= htmlspecialchars($cliente_seleccionado['email'] ?? '') ?>" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg 
                                        text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 
                                        focus:border-transparent transition" required>
                                </div>

                                <div class="flex-1 mt-2">
                                    <h3 class="mb-2 text-white">fecha_vencimiento:</h3>
                                    
                                    <?php if ($cliente_seleccionado && $cliente_seleccionado['fecha_vencimiento']): ?>
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
                                </div>
                            </div>
                            <input type="hidden" name="idActual" value="<?= $cliente_seleccionado['id_cliente'] ?? '' ?>">
                        </div>
                        <div>
                            <select name="plan" id="plan" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg 
                                        text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 
                                        focus:border-transparent transition mb-3">
                                <option value="">Seleccionar Plan</option>
                                <?php foreach ($planes as $plan): ?>
                                    <option value="<?= $plan['id_plan'] ?>" <?= ($cliente_seleccionado['id_plan'] == $plan['id_plan']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($plan['nombre']) ?> - $<?= number_format($plan['precio'], 2) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- FORMATO PARA PAGOS -->
                        <div>
                            <select name="formato_pago" id="formato_pago" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg 
                                        text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 
                                        focus:border-transparent transition">
                                        <option value="opciones">Metodo de pago</option>
                                    <option value="tarjetaC">Tarjeta de Crédito</option>
                                    <option value="tarjetaD">Tarjeta de Débito</option>
                                    <option value="efectivo">Efectivo</option>
                            </select>
                            
                        </div>
                    </form>
                </section>
            <?php endif; ?>
        </main>
    </div>

    <div id="sidebarOverlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-30 lg:hidden hidden"></div>
    <script src="../assets/js/sidebar.js"></script>
</body>

</html>