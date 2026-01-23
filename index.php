<?php
session_start();

if (isset($_SESSION['usuario_id'])) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once 'config/connection.php';
    
    $usuario = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($usuario) || empty($password)) {
        $error = "Usuario y contraseña son obligatorios";
    } else {
        $stmt = $conn->prepare("SELECT id, nombre, usuario, password, rol, activo FROM usuarios WHERE usuario = ?");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            
            if ($user['activo'] == 0) {
                $error = "Tu cuenta ha sido desactivada";
            } elseif (password_verify($password, $user['password'])) {
                $_SESSION['usuario_id'] = $user['id'];
                $_SESSION['nombre'] = $user['nombre'];
                $_SESSION['usuario'] = $user['usuario'];
                $_SESSION['rol'] = $user['rol'];
                
                header('Location: dashboard.php');
                exit;
            } else {
                $error = "Usuario o contraseña incorrectos";
            }
        } else {
            $error = "Usuario o contraseña incorrectos";
        }
        
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IQGYM - Inicio de Sesión</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-900 via-gray-900 to-orange-600 min-h-screen flex items-center justify-center p-4">
    
    <div class="w-full max-w-md">
        
        <!-- Logo y título -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-2xl shadow-2xl mb-4 transform hover:scale-110 transition-transform">
                <i class="fa-solid fa-dumbbell text-blue-600 text-3xl"></i>
            </div>
            <h1 class="text-5xl font-bold text-white mb-2 drop-shadow-lg">IQGYM</h1>
            <p class="text-gray-200 text-lg">Sistema de Gestión de Gimnasio</p>
        </div>

        <!-- Formulario -->
        <div class="bg-gray-800/90 backdrop-blur-md rounded-2xl shadow-2xl p-8 border border-gray-700">
            
            <h2 class="text-2xl font-semibold text-white mb-6">Iniciar Sesión</h2>
            
            <!-- Alertas -->
            <?php if (isset($error)): ?>
                <div class="bg-red-500/20 border border-red-500 text-red-200 p-3 mb-4 text-sm rounded-lg">
                    <i class="fa-solid fa-circle-exclamation mr-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['msg'])): ?>
                <div class="bg-green-500/20 border border-green-500 text-green-200 p-3 mb-4 text-sm rounded-lg">
                    <i class="fa-solid fa-circle-check mr-2"></i>
                    <?php echo htmlspecialchars($_GET['msg']); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="dashboard.php" autocomplete="off">
                
                <!-- Usuario -->
                <div class="mb-4">
                    <label for="usuario" class="block text-sm font-medium text-gray-200 mb-2">
                        Usuario
                    </label>
                    <input 
                        type="text" 
                        id="usuario" 
                        name="usuario" 
                        autocomplete="off"
                        class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        required
                        autofocus
                    >
                </div>

                <!-- Contraseña -->
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-200 mb-2">
                        Contraseña
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            autocomplete="off"
                            class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            required
                        >
                        <button 
                            type="button" 
                            onclick="togglePassword()" 
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white transition"
                        >
                            <i id="toggleIcon" class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Botón -->
                <button 
                    type="submit"
                    class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-3 rounded-lg transition-all transform hover:scale-105 shadow-lg"
                >
                    Ingresar
                </button>
            </form>
        </div>

        <!-- Credenciales de prueba -->
        <div class="mt-6 bg-gray-800/80 backdrop-blur-md rounded-2xl shadow-xl p-6 border border-gray-700">
            <p class="text-sm font-semibold text-white mb-4 flex items-center">
                <i class="fa-solid fa-key text-orange-500 mr-2"></i>
                Credenciales de prueba
            </p>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between items-center p-3 bg-gray-700/50 rounded-lg hover:bg-gray-700 transition cursor-pointer" onclick="fillCredentials('admin', 'admin123')">
                    <div>
                        <p class="text-white font-medium">Administrador</p>
                        <p class="text-gray-400 text-xs mt-0.5">admin / admin123</p>
                    </div>
                    <i class="fa-solid fa-arrow-right text-orange-500"></i>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-700/50 rounded-lg hover:bg-gray-700 transition cursor-pointer" onclick="fillCredentials('recepcion', 'recepcion123')">
                    <div>
                        <p class="text-white font-medium">Recepcionista</p>
                        <p class="text-gray-400 text-xs mt-0.5">recepcion / recepcion123</p>
                    </div>
                    <i class="fa-solid fa-arrow-right text-orange-500"></i>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-700/50 rounded-lg hover:bg-gray-700 transition cursor-pointer" onclick="fillCredentials('entrenador', 'entrenador123')">
                    <div>
                        <p class="text-white font-medium">Entrenador</p>
                        <p class="text-gray-400 text-xs mt-0.5">entrenador / entrenador123</p>
                    </div>
                    <i class="fa-solid fa-arrow-right text-orange-500"></i>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="text-center mt-8 text-sm text-gray-300">
            <p>&copy; <?php echo date('Y'); ?> IQGYM. Proyecto Universitario.</p>
        </footer>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        function fillCredentials(usuario, password) {
            document.getElementById('usuario').value = usuario;
            document.getElementById('password').value = password;
        }
    </script>

</body>
</html>