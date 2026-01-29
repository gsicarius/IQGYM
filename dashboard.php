<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

require_once 'config/connection.php';

// Obtener estadísticas según el rol
$rol = $_SESSION['rol'];

if ($rol == 'admin') {
    // Total clientes activos
    $result = $conn->query("SELECT COUNT(*) as total FROM clientes WHERE estatus = 'activo'");
    $total_clientes = $result->fetch_assoc()['total'];
    
    // Ingresos del mes (columna correcta: fecha_pago)
    $result = $conn->query("SELECT SUM(monto) as total FROM pagos WHERE MONTH(fecha_pago) = MONTH(CURRENT_DATE()) AND YEAR(fecha_pago) = YEAR(CURRENT_DATE()) AND estatus = 'completado'");
    $ingresos_mes = $result->fetch_assoc()['total'] ?? 0;
    
    // Vencimientos próximos (7 días) - usando fecha_fin_vigencia de pagos
    $result = $conn->query("
        SELECT COUNT(DISTINCT c.id_cliente) as total 
        FROM clientes c
        INNER JOIN pagos p ON c.id_cliente = p.id_cliente
        WHERE p.fecha_fin_vigencia BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
        AND c.estatus = 'activo'
        AND p.id_pago IN (
            SELECT MAX(id_pago) 
            FROM pagos 
            GROUP BY id_cliente
        )
    ");
    $vencimientos = $result->fetch_assoc()['total'];
    
    // Total planes activos
    $result = $conn->query("SELECT COUNT(*) as total FROM planes WHERE activo = 1");
    $total_planes = $result->fetch_assoc()['total'];
    
} elseif ($rol == 'recepcionista') {
    // Clientes activos
    $result = $conn->query("SELECT COUNT(*) as total FROM clientes WHERE estatus = 'activo'");
    $total_clientes = $result->fetch_assoc()['total'];
    
    // Vencimientos hoy - usando fecha_fin_vigencia de pagos
    $result = $conn->query("
        SELECT COUNT(DISTINCT c.id_cliente) as total 
        FROM clientes c
        INNER JOIN pagos p ON c.id_cliente = p.id_cliente
        WHERE p.fecha_fin_vigencia = CURDATE()
        AND c.estatus = 'activo'
        AND p.id_pago IN (
            SELECT MAX(id_pago) 
            FROM pagos 
            GROUP BY id_cliente
        )
    ");
    $vencimientos_hoy = $result->fetch_assoc()['total'];
    
} elseif ($rol == 'entrenador') {
    // Clientes asignados a este entrenador
    $result = $conn->query("SELECT COUNT(*) as total FROM clientes WHERE estatus = 'activo' AND id_entrenador_asignado = {$_SESSION['usuario_id']}");
    $total_clientes = $result->fetch_assoc()['total'];
    
    // Clases hoy - usando la tabla clases con citas_clases
    $dia_semana = date('N'); // 1 (lunes) a 7 (domingo)
    $result = $conn->query("
        SELECT COUNT(DISTINCT cl.id_clase) as total 
        FROM clases cl
        LEFT JOIN citas_clases cc ON cl.id_clase = cc.id_clase AND cc.fecha_clase = CURDATE()
        WHERE cl.id_entrenador = {$_SESSION['usuario_id']}
        AND cl.dia_semana = {$dia_semana}
        AND cl.activa = 1
    ");
    $clases_hoy = $result->fetch_assoc()['total'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - IQGYM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body class="bg-gray-900">
    
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="lg:ml-64">
        
        <!-- Header con información de usuario -->
        <header class="bg-gray-800 border-b border-gray-700 sticky top-0 z-10">
            <div class="flex items-center justify-between px-6 py-4">
                
                <!-- Botón menú móvil -->
                <button id="sidebarToggle" class="lg:hidden text-gray-400 hover:text-white transition">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>

                <!-- Logo móvil (solo visible en pantallas pequeñas) -->
                <div class="lg:hidden">
                    <img src="assets/images/logo.png" alt="IQGYM" class="w-10 h-10 object-contain">
                </div>

                <!-- Información de usuario -->
                <div class="ml-auto flex items-center gap-4">
                    <div class="hidden sm:block text-right">
                        <p class="text-sm font-medium text-white"><?php echo htmlspecialchars($_SESSION['nombre'] . ' ' . ($_SESSION['apellido'] ?? '')); ?></p>
                        <p class="text-xs text-gray-400"><?php echo ucfirst($_SESSION['rol']); ?></p>
                    </div>
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-orange-500 rounded-full flex items-center justify-center text-white font-bold shadow-lg">
                        <?php echo strtoupper(substr($_SESSION['nombre'], 0, 1)); ?>
                    </div>
                </div>
            </div>
        </header>
                
        <main class="p-6">
            
            <!-- Bienvenida -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-white mb-2">
                    ¡Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?>!
                </h1>
                <p class="text-gray-400">
                    <i class="fa-solid fa-circle text-green-500 text-xs mr-2"></i>
                    Panel de <span class="text-blue-400 font-medium"><?php echo ucfirst($_SESSION['rol']); ?></span>
                </p>
            </div>

            <!-- Estadísticas según rol -->
            <?php if ($rol == 'admin'): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                
                <!-- Card 1 -->
                <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl p-6 shadow-xl hover:shadow-2xl transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-white/20 rounded-lg p-3">
                            <i class="fa-solid fa-users text-white text-2xl"></i>
                        </div>
                        <span class="text-blue-200 text-sm font-medium">Activos</span>
                    </div>
                    <h3 class="text-4xl font-bold text-white mb-1"><?php echo $total_clientes; ?></h3>
                    <p class="text-blue-200 text-sm">Clientes</p>
                </div>

                <!-- Card 2 -->
                <div class="bg-gradient-to-br from-green-600 to-green-700 rounded-xl p-6 shadow-xl hover:shadow-2xl transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-white/20 rounded-lg p-3">
                            <i class="fa-solid fa-dollar-sign text-white text-2xl"></i>
                        </div>
                        <span class="text-green-200 text-sm font-medium">Este mes</span>
                    </div>
                    <h3 class="text-4xl font-bold text-white mb-1">$<?php echo number_format($ingresos_mes, 0); ?></h3>
                    <p class="text-green-200 text-sm">Ingresos</p>
                </div>

                <!-- Card 3 -->
                <div class="bg-gradient-to-br from-orange-600 to-orange-700 rounded-xl p-6 shadow-xl hover:shadow-2xl transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-white/20 rounded-lg p-3">
                            <i class="fa-solid fa-clock text-white text-2xl"></i>
                        </div>
                        <span class="text-orange-200 text-sm font-medium">Próximos 7 días</span>
                    </div>
                    <h3 class="text-4xl font-bold text-white mb-1"><?php echo $vencimientos; ?></h3>
                    <p class="text-orange-200 text-sm">Vencimientos</p>
                </div>

                <!-- Card 4 -->
                <div class="bg-gradient-to-br from-purple-600 to-purple-700 rounded-xl p-6 shadow-xl hover:shadow-2xl transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-white/20 rounded-lg p-3">
                            <i class="fa-solid fa-tags text-white text-2xl"></i>
                        </div>
                        <span class="text-purple-200 text-sm font-medium">Disponibles</span>
                    </div>
                    <h3 class="text-4xl font-bold text-white mb-1"><?php echo $total_planes; ?></h3>
                    <p class="text-purple-200 text-sm">Planes</p>
                </div>

            </div>
            <?php elseif ($rol == 'recepcionista'): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                
                <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl p-6 shadow-xl hover:shadow-2xl transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-white/20 rounded-lg p-3">
                            <i class="fa-solid fa-users text-white text-2xl"></i>
                        </div>
                    </div>
                    <h3 class="text-4xl font-bold text-white mb-1"><?php echo $total_clientes; ?></h3>
                    <p class="text-blue-200 text-sm">Clientes Activos</p>
                </div>

                <div class="bg-gradient-to-br from-orange-600 to-orange-700 rounded-xl p-6 shadow-xl hover:shadow-2xl transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-white/20 rounded-lg p-3">
                            <i class="fa-solid fa-exclamation-triangle text-white text-2xl"></i>
                        </div>
                    </div>
                    <h3 class="text-4xl font-bold text-white mb-1"><?php echo $vencimientos_hoy; ?></h3>
                    <p class="text-orange-200 text-sm">Vencimientos Hoy</p>
                </div>

            </div>
            <?php elseif ($rol == 'entrenador'): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                
                <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl p-6 shadow-xl hover:shadow-2xl transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-white/20 rounded-lg p-3">
                            <i class="fa-solid fa-users text-white text-2xl"></i>
                        </div>
                    </div>
                    <h3 class="text-4xl font-bold text-white mb-1"><?php echo $total_clientes; ?></h3>
                    <p class="text-blue-200 text-sm">Mis Clientes</p>
                </div>

                <div class="bg-gradient-to-br from-purple-600 to-purple-700 rounded-xl p-6 shadow-xl hover:shadow-2xl transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-white/20 rounded-lg p-3">
                            <i class="fa-solid fa-calendar-day text-white text-2xl"></i>
                        </div>
                    </div>
                    <h3 class="text-4xl font-bold text-white mb-1"><?php echo $clases_hoy; ?></h3>
                    <p class="text-purple-200 text-sm">Clases Hoy</p>
                </div>

            </div>
            <?php endif; ?>

            <!-- Acceso rápido -->
            <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                <h2 class="text-xl font-semibold text-white mb-6 flex items-center gap-2">
                    <i class="fa-solid fa-rocket text-orange-500"></i>
                    Acceso Rápido
                </h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    
                    <a href="client/index.php" class="flex flex-col items-center justify-center p-6 bg-gray-700/50 rounded-lg hover:bg-gray-700 hover:scale-105 transition-all">
                        <i class="fa-solid fa-users text-blue-400 text-3xl mb-3"></i>
                        <span class="text-white text-sm font-medium">Clientes</span>
                    </a>

                    <?php if (in_array($rol, ['admin', 'recepcionista'])): ?>
                    <a href="pagos/index.php" class="flex flex-col items-center justify-center p-6 bg-gray-700/50 rounded-lg hover:bg-gray-700 hover:scale-105 transition-all">
                        <i class="fa-solid fa-credit-card text-green-400 text-3xl mb-3"></i>
                        <span class="text-white text-sm font-medium">Pagos</span>
                    </a>
                    <?php endif; ?>

                    <a href="agenda/index.php" class="flex flex-col items-center justify-center p-6 bg-gray-700/50 rounded-lg hover:bg-gray-700 hover:scale-105 transition-all">
                        <i class="fa-solid fa-calendar-days text-purple-400 text-3xl mb-3"></i>
                        <span class="text-white text-sm font-medium">Agenda</span>
                    </a>

                    <?php if ($rol == 'admin'): ?>
                    <a href="reportes/index.php" class="flex flex-col items-center justify-center p-6 bg-gray-700/50 rounded-lg hover:bg-gray-700 hover:scale-105 transition-all">
                        <i class="fa-solid fa-chart-pie text-orange-400 text-3xl mb-3"></i>
                        <span class="text-white text-sm font-medium">Reportes</span>
                    </a>
                    <?php endif; ?>

                </div>
            </div>

        </main>
    </div>

    <!-- Overlay para móviles -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-30 lg:hidden hidden"></div>

    <script src="assets/js/sidebar.js"></script>

</body>
</html>