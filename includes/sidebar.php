<?php
// Calcular ruta base de la aplicación (p.ej. /IQGYM-app)
$script_name = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
$segments = explode('/', trim($script_name, '/'));
$base = '';
if (count($segments) > 0 && strpos($segments[0], '.php') === false) {
    $base = '/' . $segments[0];
}

// Verificar que hay sesión activa
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ' . $base . '/index.php');
    exit;
}

// Obtener la página actual
$pagina_actual = basename($_SERVER['PHP_SELF']);
$ruta_actual = $_SERVER['PHP_SELF'];
?>

<!-- Sidebar -->
<aside id="sidebar" class="fixed top-0 left-0 w-64 h-screen bg-gradient-to-b from-gray-900 to-gray-950 text-white flex flex-col shadow-2xl z-50 transition-transform duration-300 -translate-x-full lg:translate-x-0">
    
    <!-- Header del sidebar -->
    <div class="p-12 border-b border-gray-800">
<!-- Logo -->
    <div class="flex items-center px-4 py-4 border-b border-gray-800">
    <img src="<?php echo $base; ?>/assets/IMAGES/logo.png" alt="IQGYM" class="w-18 h-18 object-contain">
</div>
        
        <!-- Usuario -->
<div class="mt-auto border-t border-gray-800">
    <div class="flex items-center gap-3 px-4 py-4">
        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-orange-500 rounded-full flex items-center justify-center text-white font-bold shadow-lg flex-shrink-0">
            <?php echo strtoupper(substr($_SESSION['nombre'], 0, 1)); ?>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-white truncate"><?php echo htmlspecialchars($_SESSION['nombre']); ?></p>
            <p class="text-xs text-gray-400"><?php echo ucfirst($_SESSION['rol']); ?></p>
        </div>
    </div>
</div>
    <!-- Navegación -->
    <nav class="flex-1 overflow-y-auto py-4">
        
        <!-- Dashboard - TODOS -->
        <a href="<?php echo $base; ?>/dashboard.php" class="flex items-center gap-3 px-6 py-3 text-gray-300 hover:bg-gray-800 hover:text-white transition-colors <?php echo $pagina_actual == 'dashboard.php' ? 'bg-blue-600/20 border-l-4 border-blue-500' : ''; ?>">
            <i class="fa-solid fa-chart-line w-5 text-center"></i>
            <span class="text-sm font-medium">Dashboard</span>
        </a>

        <!-- Usuarios - SOLO ADMIN -->
        <?php if ($_SESSION['rol'] == 'admin'): ?>
        <a href="<?php echo $base; ?>/usuarios/index.php" class="flex items-center gap-3 px-6 py-3 text-gray-300 hover:bg-gray-800 hover:text-white transition-colors <?php echo strpos($ruta_actual, '/usuarios/') !== false ? 'bg-blue-600/20 border-l-4 border-blue-500' : ''; ?>">
            <i class="fa-solid fa-users-gear w-5 text-center"></i>
            <span class="text-sm font-medium">Usuarios</span>
        </a>
        <?php endif; ?>

        <!-- Clientes - TODOS -->
        <a href="<?php echo $base; ?>/client/index.php" class="flex items-center gap-3 px-6 py-3 text-gray-300 hover:bg-gray-800 hover:text-white transition-colors <?php echo strpos($ruta_actual, '/client/') !== false ? 'bg-blue-600/20 border-l-4 border-blue-500' : ''; ?>">
            <i class="fa-solid fa-users w-5 text-center"></i>
            <span class="text-sm font-medium">Clientes</span>
        </a>

        <!-- Pagos - Admin y Recepcionista -->
        <?php if (in_array($_SESSION['rol'], ['admin', 'recepcionista'])): ?>
        <a href="<?php echo $base; ?>/pagos/index.php" class="flex items-center gap-3 px-6 py-3 text-gray-300 hover:bg-gray-800 hover:text-white transition-colors <?php echo strpos($ruta_actual, '/pagos/') !== false ? 'bg-blue-600/20 border-l-4 border-blue-500' : ''; ?>">
            <i class="fa-solid fa-credit-card w-5 text-center"></i>
            <span class="text-sm font-medium">Pagos</span>
        </a>
        <?php endif; ?>

        <!-- Planes - Admin y Recepcionista -->
        <?php if (in_array($_SESSION['rol'], ['admin', 'recepcionista'])): ?>
        <a href="<?php echo $base; ?>/planes/index.php" class="flex items-center gap-3 px-6 py-3 text-gray-300 hover:bg-gray-800 hover:text-white transition-colors <?php echo strpos($ruta_actual, '/planes/') !== false ? 'bg-blue-600/20 border-l-4 border-blue-500' : ''; ?>">
            <i class="fa-solid fa-tags w-5 text-center"></i>
            <span class="text-sm font-medium">Planes</span>
        </a>
        <?php endif; ?>

        <!-- Agenda - TODOS -->
        <a href="<?php echo $base; ?>/agenda/index.php" class="flex items-center gap-3 px-6 py-3 text-gray-300 hover:bg-gray-800 hover:text-white transition-colors <?php echo strpos($ruta_actual, '/agenda/') !== false ? 'bg-blue-600/20 border-l-4 border-blue-500' : ''; ?>">
            <i class="fa-solid fa-calendar-days w-5 text-center"></i>
            <span class="text-sm font-medium">Agenda</span>
        </a>

        <!-- Reportes - SOLO ADMIN -->
        <?php if ($_SESSION['rol'] == 'admin'): ?>
        <a href="<?php echo $base; ?>/reportes/index.php" class="flex items-center gap-3 px-6 py-3 text-gray-300 hover:bg-gray-800 hover:text-white transition-colors <?php echo strpos($ruta_actual, '/reportes/') !== false ? 'bg-blue-600/20 border-l-4 border-blue-500' : ''; ?>">
            <i class="fa-solid fa-chart-pie w-5 text-center"></i>
            <span class="text-sm font-medium">Reportes</span>
        </a>
        <?php endif; ?>


        <!-- Cerrar Sesión -->
        <a href="<?php echo $base; ?>/logout.php" class="flex items-center gap-3 px-6 py-3 text-red-400 hover:bg-red-900/20 hover:text-red-300 transition-colors mt-4 border-t border-gray-800">
            <i class="fa-solid fa-right-from-bracket w-5 text-center"></i>
            <span class="text-sm font-medium">Cerrar Sesión</span>
        </a>
    </nav>
</aside>

<!-- Overlay para móviles -->
<div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden"></div>

<!-- Botón toggle para móviles -->
<button id="sidebarToggle" class="fixed top-4 left-4 z-50 lg:hidden bg-gray-700 text-white p-3 rounded-lg shadow-lg hover:bg-gray-800 transition-colors">
    <i class="fa-solid fa-bars text-xl"></i>
</button>