<?php
session_start();
require_once '../config/connection.php';

$mensaje = '';
$mostrar_formulario = false;
$plan_seleccionado = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id          = (int)   ($_POST['idActual']        ?? 0);
    $nombre      = trim(   ($_POST['txtnombre']        ?? ''));
    $descripcion = trim(   ($_POST['txtdescripcion']   ?? ''));
    $entrenador  = (int)   ($_POST['txtentrenador']    ?? 0);
    $dia_semana  = (int)   ($_POST['txtdia_semana']    ?? 0);
    $hora_inicio =         ($_POST['txthora_inicio']   ?? '');
    $hora_fin    =         ($_POST['txthora_fin']      ?? '');
    $cupo_maximo = (int)   ($_POST['txtcupo_maximo']   ?? 0);
    $activa      = (int)   ($_POST['chkactivo']        ?? 0);
    $fecha_creacion =      ($_POST['txtfecha_creacion'] ?? '');

    if (isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
        if ($id) {
            $stmt = mysqli_prepare($conn, "DELETE FROM clases WHERE id_clase = ?");
            mysqli_stmt_bind_param($stmt, 'i', $id);
            mysqli_stmt_execute($stmt);
            $mensaje = '✓ Clase eliminada correctamente';
        } else {
            $mensaje = '✗ Selecciona una clase para eliminar';
        }
    } else {
        if (!$nombre || !$descripcion || !$entrenador || !$dia_semana || !$hora_inicio || !$hora_fin) {
            $mensaje = '✗ Todos los campos son obligatorios';
            $mostrar_formulario = true;
        } else {
            if ($id) {
                $stmt = mysqli_prepare($conn, "UPDATE clases SET nombre_clase=?, descripcion=?, id_entrenador=?, dia_semana=?, hora_inicio=?, hora_fin=?, cupo_maximo=?, activa=?, fecha_creacion=? WHERE id_clase=?");
                mysqli_stmt_bind_param($stmt, 'siisssiisi', $nombre, $descripcion, $entrenador, $dia_semana, $hora_inicio, $hora_fin, $cupo_maximo, $activa, $fecha_creacion, $id);
                mysqli_stmt_execute($stmt);
                $mensaje = '✓ Clase actualizada correctamente';
            } else {
                $stmt = mysqli_prepare($conn, "INSERT INTO clases (nombre_clase, descripcion, id_entrenador, dia_semana, hora_inicio, hora_fin, cupo_maximo, activa, fecha_creacion) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmt, 'siisssiis', $nombre, $descripcion, $entrenador, $dia_semana, $hora_inicio, $hora_fin, $cupo_maximo, $activa, $fecha_creacion);
                mysqli_stmt_execute($stmt);    
                $mensaje = '✓ Clase creada correctamente';
            }
        }
    }
}

if (isset($_GET['id']) && $_GET['id'] !== 'nuevo') {
    $id = (int) $_GET['id'];
    $stmt = mysqli_prepare($conn, "SELECT * FROM clases WHERE id_clase = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $plan_seleccionado = mysqli_fetch_assoc($res);
    $mostrar_formulario = true;
} elseif (isset($_GET['id']) && $_GET['id'] === 'nuevo') {
    $mostrar_formulario = true;
}

$clases = [];
$res = mysqli_query($conn, "SELECT c.*, CONCAT(u.nombre, ' ', u.apellido) AS nombre_entrenador FROM clases c LEFT JOIN usuarios u ON u.id_usuario = c.id_entrenador ORDER BY c.id_clase DESC");
while ($row = mysqli_fetch_assoc($res)) {
    $clases[] = $row;
}

$entrenadores = [];
$res_ent = mysqli_query($conn, "SELECT id_usuario, nombre, apellido FROM usuarios WHERE id_rol = 3 ORDER BY nombre ASC");
while ($row = mysqli_fetch_assoc($res_ent)) {
    $entrenadores[] = $row;
}

$dias = ['', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clases</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        input[type="date"]::-webkit-calendar-picker-indicator { filter: invert(.5); }
    </style>
</head>

<body class="bg-gray-900 text-white">
    <?php include '../includes/sidebar.php'; ?>

    <div class="lg:ml-64">

        <main class="p-6">

            <?php if ($mensaje): ?>
                <div class="mb-4 px-4 py-3 rounded-lg text-sm font-semibold max-w-6xl mx-auto
                    <?= str_starts_with($mensaje, '✓') ? 'bg-green-700 text-green-100' : 'bg-red-700 text-red-100' ?>">
                    <?= htmlspecialchars($mensaje) ?>
                </div>
            <?php endif; ?>

            <?php if (!$mostrar_formulario): ?>

                <div class="bg-gray-800 rounded-xl p-6 max-w-7xl mx-auto">

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-5">
                        <h2 class="text-white text-xl font-bold flex items-center gap-2">
                            <i class="fa-solid fa-person-running text-blue-400"></i>
                            Clases Registradas
                        </h2>

                        <div class="flex flex-wrap items-center gap-2">
                            <div class="flex items-center bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 gap-2 text-sm">
                                <i class="fa-solid fa-magnifying-glass text-gray-400 text-xs"></i>
                                <input id="q" type="text" placeholder="Buscar por nombre o descripción…"
                                    class="bg-transparent outline-none text-white placeholder-gray-400 w-52">
                            </div>

                            <div class="flex items-center bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 gap-2 text-sm">
                                <i class="fa-regular fa-calendar text-gray-400 text-xs"></i>
                                <input id="fecha_desde" type="date" class="bg-transparent outline-none text-white text-sm" title="Desde">
                            </div>

                            <div class="flex items-center bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 gap-2 text-sm">
                                <i class="fa-regular fa-calendar-check text-gray-400 text-xs"></i>
                                <input id="fecha_hasta" type="date" class="bg-transparent outline-none text-white text-sm" title="Hasta">
                            </div>

                            <select id="filtro_activa"
                                class="bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-sm text-white outline-none">
                                <option value="">Todos</option>
                                <option value="1">Activa</option>
                                <option value="0">Inactiva</option>
                            </select>

                            <button onclick="limpiarFiltros()"
                                class="px-3 py-2 bg-gray-700 border border-gray-600 hover:bg-gray-600 text-gray-300 rounded-lg text-sm transition">
                                <i class="fa-solid fa-xmark"></i>
                            </button>

                            <?php if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], ['admin', 'recepcionista'])): ?>
                                <a href="?id=nuevo"
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold transition flex items-center gap-2 whitespace-nowrap">
                                    <i class="fa-solid fa-plus"></i> Agregar
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <p id="count-strip" class="text-gray-400 text-xs mb-3">
                        Mostrando <?= count($clases) ?> clase<?= count($clases) !== 1 ? 's' : '' ?>
                    </p>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm" id="tabla-clases">
                            <thead>
                                <tr class="border-b border-gray-600">
                                    <th class="text-left py-3 px-4 text-gray-400 font-semibold text-xs uppercase tracking-wide">ID</th>
                                    <th class="text-left py-3 px-4 text-gray-400 font-semibold text-xs uppercase tracking-wide">Nombre</th>
                                    <th class="text-left py-3 px-4 text-gray-400 font-semibold text-xs uppercase tracking-wide">Descripción</th>
                                    <th class="text-left py-3 px-4 text-gray-400 font-semibold text-xs uppercase tracking-wide">Entrenador</th>
                                    <th class="text-left py-3 px-4 text-gray-400 font-semibold text-xs uppercase tracking-wide">Día</th>
                                    <th class="text-left py-3 px-4 text-gray-400 font-semibold text-xs uppercase tracking-wide">Horario</th>
                                    <th class="text-left py-3 px-4 text-gray-400 font-semibold text-xs uppercase tracking-wide">Cupo</th>
                                    <th class="text-left py-3 px-4 text-gray-400 font-semibold text-xs uppercase tracking-wide">Estado</th>
                                    <th class="text-left py-3 px-4 text-gray-400 font-semibold text-xs uppercase tracking-wide">Creación</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($clases as $c): ?>
                                    <tr class="border-b border-gray-700 hover:bg-gray-700 transition"
                                        data-nombre="<?= strtolower(htmlspecialchars($c['nombre_clase'])) ?>"
                                        data-descripcion="<?= strtolower(htmlspecialchars($c['descripcion'])) ?>"
                                        data-activa="<?= $c['activa'] ?>"
                                        data-fecha="<?= $c['fecha_creacion'] ? date('Y-m-d', strtotime($c['fecha_creacion'])) : '' ?>">
                                        <td class="py-3 px-4 text-gray-400 font-mono text-xs"><?= $c['id_clase'] ?></td>
                                        <td class="py-3 px-4 font-semibold">
                                            <a href="?id=<?= $c['id_clase'] ?>" class="text-blue-400 hover:text-blue-300 hover:underline">
                                                <?= htmlspecialchars($c['nombre_clase']) ?>
                                            </a>
                                        </td>
                                        <td class="py-3 px-4 text-gray-400 max-w-xs truncate"><?= htmlspecialchars($c['descripcion']) ?></td>
                                        <td class="py-3 px-4 text-gray-300"><?= htmlspecialchars($c['nombre_entrenador'] ?? '—') ?></td>
                                        <td class="py-3 px-4 text-gray-300"><?= isset($dias[$c['dia_semana']]) ? $dias[$c['dia_semana']] : $c['dia_semana'] ?></td>
                                        <td class="py-3 px-4">
                                            <span class="bg-blue-900 text-blue-300 text-xs font-mono px-2 py-0.5 rounded">
                                                <?= htmlspecialchars(substr($c['hora_inicio'], 0, 5)) ?> – <?= htmlspecialchars(substr($c['hora_fin'], 0, 5)) ?>
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-gray-300 font-mono text-xs"><?= $c['cupo_maximo'] ?></td>
                                        <td class="py-3 px-4">
                                            <span class="px-2 py-1 rounded text-xs font-bold uppercase <?= $c['activa'] ? 'bg-green-600 text-white' : 'bg-red-700 text-white' ?>">
                                                <?= $c['activa'] ? 'Activa' : 'Inactiva' ?>
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-gray-400 text-xs">
                                            <?= $c['fecha_creacion'] ? date('d/m/Y', strtotime($c['fecha_creacion'])) : '—' ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($clases)): ?>
                                    <tr>
                                        <td colspan="9" class="py-10 text-center text-gray-500">No hay clases registradas aún</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        <p id="empty-msg" class="hidden py-10 text-center text-gray-500 text-sm">
                            Sin resultados para ese filtro
                        </p>
                    </div>

                </div>

            <?php else: ?>
                <?php include 'form.php'; ?>
            <?php endif; ?>

        </main>
    </div>

    <div id="sidebarOverlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-30 lg:hidden hidden"></div>
    <script src="../assets/js/sidebar.js"></script>
    <?php include '../includes/footer.php'; ?>

    <script>
        const rows     = document.querySelectorAll('#tabla-clases tbody tr[data-nombre]');
        const strip    = document.getElementById('count-strip');
        const emptyMsg = document.getElementById('empty-msg');

        function filtrar() {
            const q      = document.getElementById('q').value.toLowerCase().trim();
            const desde  = document.getElementById('fecha_desde').value;
            const hasta  = document.getElementById('fecha_hasta').value;
            const activa = document.getElementById('filtro_activa').value;
            let visible  = 0;

            rows.forEach(row => {
                const okQ      = !q      || row.dataset.nombre.includes(q) || row.dataset.descripcion.includes(q);
                const okDesde  = !desde  || row.dataset.fecha >= desde;
                const okHasta  = !hasta  || row.dataset.fecha <= hasta;
                const okActiva = activa === '' || row.dataset.activa === activa;
                const show     = okQ && okDesde && okHasta && okActiva;
                row.style.display = show ? '' : 'none';
                if (show) visible++;
            });

            strip.textContent = `Mostrando ${visible} clase${visible !== 1 ? 's' : ''}`;
            emptyMsg.classList.toggle('hidden', visible > 0);
        }

        ['q', 'fecha_desde', 'fecha_hasta', 'filtro_activa'].forEach(id =>
            document.getElementById(id).addEventListener('input', filtrar)
        );

        function limpiarFiltros() {
            ['q', 'fecha_desde', 'fecha_hasta'].forEach(id => document.getElementById(id).value = '');
            document.getElementById('filtro_activa').value = '';
            filtrar();
        }
    </script>
</body>

</html>
