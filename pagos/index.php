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
                        <div class="flex flex-col md:flex-row gap-6 items-center">

                            <div class="flex flex-col items-center justify-center min-w-[160px]">
                                <img src="../assets/IMAGES/logo.png" alt="FotoCliente"
                                    class="w-40 h-40 object-contain drop-shadow-2xl">
                            </div>

                            <div class="flex flex-col md:flex-row gap-4 w-full max-w-2xl mb-4">
                                <div class="flex-1">
                                    <h3 class="mb-2 text-white">Nombre:</h3>
                                    <input type="text" name="txtnombre" id="txtnombre"
                                        value="<?= htmlspecialchars($cliente_seleccionado['nombre'] ?? '') ?>" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg 
                                        text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 
                                        focus:border-transparent transition mb-4" required>

                                    <h3 class="mb-2 text-white">Teléfono:</h3>
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
                                    <h3 class="mb-2 text-white">Vencimiento:</h3>
                                    <?php if ($cliente_seleccionado && $cliente_seleccionado['fecha_vencimiento']): ?>
                                        <?php
                                        $dias_restantes = floor((strtotime($cliente_seleccionado['fecha_vencimiento']) - time()) / 86400);
                                        $color = $dias_restantes < 0 ? 'text-red-400' : ($dias_restantes <= 7 ? 'text-yellow-400' : 'text-gray-300');
                                        ?>
                                        <span class="<?= $color ?>">
                                            <?= date('d/m/Y', strtotime($cliente_seleccionado['fecha_vencimiento'])) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-gray-500">Sin plan</span>
                                    <?php endif; ?>

                                    <?php if ($cliente_seleccionado && $cliente_seleccionado['nombre_plan']): ?>
                                        <h3 class="mb-2 mt-4 text-white">Plan actual:</h3>
                                        <span
                                            class="text-blue-400"><?= htmlspecialchars($cliente_seleccionado['nombre_plan']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <input type="hidden" name="idActual" value="<?= $cliente_seleccionado['id_cliente'] ?? '' ?>">
                        </div>

                        <!-- SELECT DE PLANES -->
                        <div>
                            <select name="plan" id="plan" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg 
                                        text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 
                                        focus:border-transparent transition mb-3">
                                <option value="">Seleccionar Plan</option>
                                <?php foreach ($planes as $p): ?>
                                    <option value="<?= $p['id_plan'] ?>" data-precio="<?= $p['precio'] ?>"
                                        data-duracion="<?= $p['duracion_dias'] ?>"
                                        <?= ($cliente_seleccionado['id_plan_actual'] == $p['id_plan']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($p['nombre_plan']) ?> - $<?= number_format($p['precio'], 2) ?>
                                        (<?= $p['duracion_dias'] ?> días)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- FORMATO PARA PAGOS -->
                        <div>
                            <h2 class="text-center text-white font-semibold text-xl mb-3">
                                Total a Pagar: $<span id="total_pagar">
                                    <?= $cliente_seleccionado['precio'] ? number_format($cliente_seleccionado['precio'], 2) : '0.00' ?>
                                </span>
                            </h2>

                            <select name="formato_pago" id="formato_pago" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg 
                                        text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 
                                        focus:border-transparent transition">
                                <option value="opciones">Método de pago</option>
                                <option value="tarjetaC">Tarjeta de Crédito</option>
                                <option value="tarjetaD">Tarjeta de Débito</option>
                                <option value="efectivo">Efectivo</option>
                            </select>

                            <div id="credito" class="mt-3">
                                <input type="number" name="NumeroTarjeta" id="NumeroTarjeta" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg 
                                        text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 
                                        focus:border-transparent transition" placeholder="Número de la tarjeta">
                                <div class="flex mt-3">
                                    <input type="number" name="CVV" id="CVV" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg 
                                        text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 
                                        focus:border-transparent transition mr-2" placeholder="CVV">
                                    <input type="date" name="FechaVencimiento" id="FechaVencimiento" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg 
                                        text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 
                                        focus:border-transparent transition">
                                </div>
                            </div>

                            <div id="efectivo">
                                <label class="text-semibold text-white">Ingreso:</label>
                                <input type="number" name="TotalIngreso" id="TotalIngreso" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg 
                                        text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 
                                        focus:border-transparent transition" placeholder="Total Ingresado">
                                <p class="text-white text-semibold mt-2">Cambio: $<span id="cambio">0.00</span></p>
                            </div>

                            <div class="flex justify-center mt-4" id="btn_pago">
                                <button type="submit" name="accion" value="procesar_pago"
                                    class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 
                                        text-white font-semibold py-3 px-8 rounded-lg transition-all transform hover:scale-105 shadow-lg">
                                    <i class="fa-solid fa-save mr-2"></i>
                                    Procesar Pago
                                </button>
                            </div>
                        </div>

                    </form>
                </section>
            <?php endif; ?>
        </main>
    </div>

    <div id="sidebarOverlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-30 lg:hidden hidden"></div>
    <script src="../assets/js/sidebar.js"></script>

    <script>
        const credito = document.getElementById('credito')
        const efectivo = document.getElementById('efectivo')
        const metodos_pago = document.getElementById('formato_pago')
        const btn_pago = document.getElementById('btn_pago')
        const select_plan = document.getElementById('plan')
        const total_pagar = document.getElementById('total_pagar')
        const input_ingreso = document.getElementById('TotalIngreso')
        const span_cambio = document.getElementById('cambio')

        input_ingreso.addEventListener('input', function () {
            const total = parseFloat(total_pagar.textContent) || 0
            const ingreso = parseFloat(this.value) || 0
            const cambio = ingreso - total
            span_cambio.textContent = cambio >= 0 ? cambio.toFixed(2) : '0.00'
        })

        // ── Estado inicial: ocultar todo ──────────────────────────────────────
        window.addEventListener('DOMContentLoaded', () => {
            credito.classList.add('hidden')
            efectivo.classList.add('hidden')
            btn_pago.classList.add('hidden')
        })

        // ── Actualizar total cuando cambia el plan ────────────────────────────
        if (select_plan) {
            select_plan.addEventListener('change', function () {
                const opcion = this.options[this.selectedIndex]
                const precio = opcion.getAttribute('data-precio') ?? '0.00'
                total_pagar.textContent = parseFloat(precio).toFixed(2)
            })
        }

        // ── Mostrar/ocultar sección según método de pago ──────────────────────
        function mostrarFormularioPago(opc) {
            credito.classList.add('hidden')
            efectivo.classList.add('hidden')

            if (opc === 'opciones') {
                btn_pago.classList.add('hidden')
            } else if (opc === 'tarjetaC' || opc === 'tarjetaD') {
                credito.classList.remove('hidden')
                btn_pago.classList.remove('hidden')
            } else if (opc === 'efectivo') {
                efectivo.classList.remove('hidden')
                btn_pago.classList.remove('hidden')
            }
        }

        if (metodos_pago) {
            metodos_pago.addEventListener('change', function () {
                mostrarFormularioPago(metodos_pago.value)
            })
        }
    </script>
</body>

</html>