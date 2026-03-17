<?php
session_start();
require_once '../config/connection.php';

$mensaje = '';
$mostrar_formulario = false;
$plan_seleccionado = null;


// ── Procesar formulario POST ──────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = (int) ($_POST['idActual'] ?? 0);
    $nombre = trim($_POST['txtnombre'] ?? '');
    $descripcion = trim($_POST['txtdescripcion'] ?? '');
    $precio = (float) ($_POST['precio'] ?? 0);
    $duracion = (int) ($_POST['txtduracion'] ?? 0);
    $activo = (int) ($_POST['chkactivo'] ?? 0);
    // ── Eliminar ──────────────────────────────────────────────────────────────
    if (isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
        if ($id) {
            $stmt = mysqli_prepare($conn, "DELETE FROM planes WHERE id_plan = ?");
            mysqli_stmt_bind_param($stmt, 'i', $id);
            mysqli_stmt_execute($stmt);
            $mensaje = '✓ plan eliminado correctamente';
        } else {
            $mensaje = '✗ Selecciona un plan para eliminar';
        }

        // ── Guardar (insertar o actualizar) ───────────────────────────────────────
    } else {
        if (!$nombre || !$descripcion || !$precio || !$duracion) {
            $mensaje = '✗ Todos los campos son obligatorios';
            $mostrar_formulario = true;
        } else {
            if ($id) {
                $stmt = mysqli_prepare($conn, "UPDATE planes SET nombre_plan=?, descripcion=?, precio=?, duracion_dias=?, activo=?, ultima_modificacion=NOW() WHERE id_plan=?");
                mysqli_stmt_bind_param($stmt, 'ssdiii', $nombre, $descripcion, $precio, $duracion, $activo, $id);
                mysqli_stmt_execute($stmt);
                $mensaje = '✓ Plan actualizado correctamente';
            } else {
                $stmt = mysqli_prepare($conn, "INSERT INTO planes (nombre_plan, descripcion, precio, duracion_dias, activo, fecha_creacion, ultima_modificacion) VALUES (?, ?, ?, ?, 0, NOW(), NOW())");
                mysqli_stmt_bind_param($stmt, 'ssdi', $nombre, $descripcion, $precio, $duracion);
                mysqli_stmt_execute($stmt);
                $mensaje = '✓ Plan creado correctamente';
            }
        }
    }
}

// ── Cargar plan si viene ID en GET ─────────────────────────────────────────
if (isset($_GET['id']) && $_GET['id'] !== 'nuevo') {
    $id = (int) $_GET['id'];
    $stmt = mysqli_prepare($conn, "SELECT * FROM planes WHERE id_plan = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $plan_seleccionado = mysqli_fetch_assoc($res);
    $mostrar_formulario = true;
} elseif (isset($_GET['id']) && $_GET['id'] === 'nuevo') {
    $mostrar_formulario = true;
}

// ── Cargar todos los planes para la tabla ─────────────────────────────────
$planes = [];
$res = mysqli_query($conn, "SELECT * FROM planes ORDER BY id_plan DESC");
while ($row = mysqli_fetch_assoc($res)) {
    $planes[] = $row;
}
?>

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>planes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>

<body class="bg-gray-900">
    <?php include '../includes/sidebar.php'; ?>

    <div class="lg:ml-64">
        <header>
            <h1 class="font-semibold text-white text-3xl ml-3 text-center py-4">
                planes
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
            <!-- TABLA DE planes -->
            <!-- ═══════════════════════════════════════════════════════════════════ -->
            <?php if (!$mostrar_formulario): ?>
                <section class="bg-gray-800 rounded-lg p-6 max-w-6xl mx-auto">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                        <h2 class="text-white text-xl font-semibold">
                            <i class="fa-solid fa-users mr-2"></i> planes Registrados
                        </h2>

                        <a href="?id=nuevo"
                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition flex items-center gap-2 whitespace-nowrap">
                            <i class="fa-solid fa-plus"></i> Agregar
                        </a>

                        <div class="overflow-x-auto">
                            <?php if (count($planes) > 0): ?>
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <?php foreach ($planes as $c): ?>
                                        <?php
                                        $bg_color = $c['activo'] == 1 ? 'bg-green-600' : 'bg-red-600';
                                        $label = $c['activo'] == 1 ? 'activo' : 'inactivo';
                                        ?>
                                        <div
                                            class="bg-gray-700 border border-gray-600 rounded-xl p-5 hover:border-gray-400 transition">
                                            <div class="flex items-center justify-between mb-3">
                                                <span class="text-xs text-gray-400">#<?= $c['id_plan'] ?></span>
                                                <span class="px-2 py-1 <?= $bg_color ?> text-white text-xs rounded uppercase">

                                                </span>
                                            </div>
                                            <a href="?id=<?= $c['id_plan'] ?>"
                                                class="block text-blue-400 hover:text-blue-300 hover:underline font-semibold text-4xl mb-1">
                                                <?= htmlspecialchars($c['nombre_plan']) ?>
                                            </a>
                                            <p class="text-gray-400 text-sm mb-4"><?= htmlspecialchars($c['descripcion']) ?></p>
                                            <div class="space-y-2 text-sm text-gray-300">
                                                <div class="flex justify-between">
                                                    <span class="text-gray-500">Duración</span>
                                                    <span><?= $c['duracion_dias'] ?> días</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-gray-500">Precio</span>
                                                    <span>$<?= number_format($c['precio'], 2) ?></span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-gray-500">Creacion</span>
                                                    <span><?= date('d/m/Y H:i', strtotime($c['fecha_creacion'])) ?></span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-gray-500">Modificado</span>
                                                    <span><?= date('d/m/Y H:i', strtotime($c['ultima_modificacion'])) ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="py-8 text-center text-gray-400">
                                    No hay planes registrados aún
                                </div>
                            <?php endif; ?>
                        </div>
                </section>

                <!-- ═══════════════════════════════════════════════════════════════════ -->
                <!-- FORMULARIO DE EDICIÓN/CREACIÓN -->
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