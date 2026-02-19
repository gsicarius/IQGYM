<?php
session_start();
require_once __DIR__ . '/connection.php';
// ── Conexión ──────────────────────────────────────────────────────────────────

$mensaje = '';

// ── Procesar formulario POST ──────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id       = (int)($_POST['idActual']    ?? 0);
    $nombre   = trim($_POST['txtnombre']    ?? '');
    $apellido = trim($_POST['txtapellido']  ?? '');
    $email    = trim($_POST['txtemail']     ?? '');
    $telefono = trim($_POST['txttelefono']  ?? '');
    $usuario  = trim($_POST['txtusuario']   ?? '');
    $password = trim($_POST['txtpassword']  ?? '');
    $id_rol   = (int)($_POST['txtrol']      ?? 0);

    // ── Eliminar ──────────────────────────────────────────────────────────────
    if ($_POST['accion'] === 'eliminar') {
        if ($id) {
            $stmt = mysqli_prepare($conn, "DELETE FROM usuarios WHERE id_usuario = ?");
            mysqli_stmt_bind_param($stmt, 'i', $id);
            mysqli_stmt_execute($stmt);
            $mensaje = '✓ Usuario eliminado correctamente';
        } else {
            $mensaje = '✗ Selecciona un usuario para eliminar';
        }

    // ── Guardar (insertar o actualizar) ───────────────────────────────────────
    } else {
        if (!$nombre || !$apellido || !$email || !$telefono || !$usuario || !$password || !$id_rol) {
            $mensaje = '✗ Todos los campos son obligatorios';
        } else {
            // Verificar duplicado
            $stmt = mysqli_prepare($conn, "SELECT id_usuario FROM usuarios WHERE (email=? OR usuario=?) AND id_usuario!=?");
            mysqli_stmt_bind_param($stmt, 'ssi', $email, $usuario, $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);

            if (mysqli_stmt_num_rows($stmt) > 0) {
                $mensaje = '✗ El email o usuario ya existe';
            } elseif ($id) {
                $stmt = mysqli_prepare($conn, "UPDATE usuarios SET nombre=?,apellido=?,email=?,telefono=?,usuario=?,password=MD5(?),id_rol=?,ultima_modificacion=NOW() WHERE id_usuario=?");
                mysqli_stmt_bind_param($stmt, 'ssssssii', $nombre, $apellido, $email, $telefono, $usuario, $password, $id_rol, $id);
                mysqli_stmt_execute($stmt);
                $mensaje = '✓ Usuario actualizado correctamente';
            } else {
                $stmt = mysqli_prepare($conn, "INSERT INTO usuarios(nombre,apellido,email,telefono,usuario,password,id_rol,activo,fecha_creacion) VALUES(?,?,?,?,?,MD5(?),?,1,NOW())");
                mysqli_stmt_bind_param($stmt, 'ssssssi', $nombre, $apellido, $email, $telefono, $usuario, $password, $id_rol);
                mysqli_stmt_execute($stmt);
                $id = mysqli_insert_id($conn);
                $mensaje = '✓ Usuario creado correctamente';
            }
        }
    }
}

// ── Cargar datos para la vista ────────────────────────────────────────────────
$usuarios = [];
$res = mysqli_query($conn, "
    SELECT u.id_usuario, u.nombre, u.apellido, u.email, u.telefono, u.usuario,
           r.id_rol, r.nombre_rol
    FROM usuarios u
    JOIN roles r ON u.id_rol = r.id_rol
    WHERE r.nombre_rol IN ('recepcionista','entrenador')
    ORDER BY u.nombre");
while ($row = mysqli_fetch_assoc($res)) $usuarios[] = $row;

$roles = [];
$res = mysqli_query($conn, "SELECT id_rol, nombre_rol FROM roles WHERE nombre_rol IN ('recepcionista','entrenador')");
while ($row = mysqli_fetch_assoc($res)) $roles[] = $row;

$seleccionado = (int)($_POST['idActual'] ?? 0);
if ($mensaje && str_contains($mensaje, 'creado')) $seleccionado = $id ?? 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuracion</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>

<body class="bg-gray-900">
    <?php include '../includes/sidebar.php'; ?>

    <div class="lg:ml-64">
        <header>
            <h1 class="font-semibold text-white text-3xl ml-3 text-center">
                Configuración
            </h1>
        </header>

        <main>

                  <section class="flex flex-wrap">
                <div class="flex items-center justify-between w-full px-6 py-3 bg-gray-800 mt-3">
                    <h2 class="font-semibold text-white text-xl">
                        <i class="fa-solid fa-users mr-2"></i> Gestion de usuarios
                    </h2>
                    <button onclick="toggleContent()" class="text-white text-xl hover:text-blue-500 transition-all">
                        <i class="text-white fa-solid fa-chevron-right transition-transform duration-300" id="arrow"></i>
                    </button>
                </div>

                <div id="content" class="w-full flex justify-center">
                    <div class="w-full max-w-4xl px-6">
                        <hr class="border-t-2 border-gray-700 w-full my-4">
                        <div class="text-white pb-4">

                            <?php if ($mensaje): ?>
                                <div class="mb-4 px-4 py-3 rounded-lg text-sm font-semibold
                                    <?= str_starts_with($mensaje,'✓') ? 'bg-green-700 text-green-100' : 'bg-red-700 text-red-100' ?>">
                                    <?= htmlspecialchars($mensaje) ?>
                                </div>
                            <?php endif; ?>

                            <h2 class="mb-2">Usuarios Registrados</h2>
                            <select id="cmbUsuarios" onchange="autocompletar(this)"
                                class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg 
                                text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 
                                focus:border-transparent transition mb-4">
                                <option value="">— Nuevo usuario —</option>
                                <?php foreach ($usuarios as $u): ?>
                                    <option value="<?= $u['id_usuario'] ?>"
                                        data-json="<?= htmlspecialchars(json_encode($u), ENT_QUOTES) ?>"
                                        <?= $seleccionado == $u['id_usuario'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($u['nombre'].' '.$u['apellido'].' ('.$u['nombre_rol'].')') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <form method="POST" action="configuracion.php">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="mb-4">
                                        <img src="../assets/IMAGES/logo.png" alt="FotoUsuario" class="w-40 h-40 object-contain drop-shadow-2xl">
                                    </div>

                                    <div class="flex gap-4 w-full max-w-2xl mb-4">
                                        <div class="flex-1">
                                            <h3 class="mb-2">Nombre:</h3>
                                            <input type="text" name="txtnombre" id="txtnombre"
                                                value="<?= htmlspecialchars($_POST['txtnombre'] ?? '') ?>"
                                                class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg 
                                                text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 
                                                focus:border-transparent transition mb-4">
                                            <h3 class="mb-2">Telefono:</h3>
                                            <input type="text" name="txttelefono" id="txttelefono"
                                                value="<?= htmlspecialchars($_POST['txttelefono'] ?? '') ?>"
                                                class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg 
                                                text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 
                                                focus:border-transparent transition">
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="mb-2">Apellido:</h3>
                                            <input type="text" name="txtapellido" id="txtapellido"
                                                value="<?= htmlspecialchars($_POST['txtapellido'] ?? '') ?>"
                                                class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg 
                                                text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 
                                                focus:border-transparent transition mb-4">
                                            <h3 class="mb-2">Email:</h3>
                                            <input type="text" name="txtemail" id="txtemail"
                                                value="<?= htmlspecialchars($_POST['txtemail'] ?? '') ?>"
                                                class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg 
                                                text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 
                                                focus:border-transparent transition">
                                        </div>
                                    </div>

                                    <div class="flex gap-4 w-full max-w-2xl mb-4">
                                        <div class="flex-1">
                                            <h3 class="mb-2">Usuario:</h3>
                                            <input type="text" name="txtusuario" id="txtusuario"
                                                value="<?= htmlspecialchars($_POST['txtusuario'] ?? '') ?>"
                                                class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg 
                                                text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 
                                                focus:border-transparent transition">
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="mb-2">Contraseña:</h3>
                                            <input type="password" name="txtpassword" id="txtpassword"
                                                class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg 
                                                text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 
                                                focus:border-transparent transition">
                                        </div>
                                    </div>

                                    <div class="w-full max-w-2xl mb-4">
                                        <h3 class="mb-2">Rol:</h3>
                                        <select name="txtrol" id="txtrol"
                                            class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg 
                                            text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 
                                            focus:border-transparent transition">
                                            <option value="">— Selecciona un rol —</option>
                                            <?php foreach ($roles as $r): ?>
                                                <option value="<?= $r['id_rol'] ?>"
                                                    <?= (($_POST['txtrol'] ?? '') == $r['id_rol']) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($r['nombre_rol']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <input type="hidden" name="idActual" id="idActual" value="<?= $seleccionado ?>">

                                    <div class="flex gap-7">
                                        <button type="submit" name="accion" value="guardar"
                                            class="w-full p-9 max-w-2xl bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 
                                            text-white font-semibold py-3 rounded-lg transition-all transform hover:scale-105 shadow-lg">
                                            Ingresar
                                        </button>
                                        <button type="submit" name="accion" value="eliminar"
                                            onclick="return confirm('¿Eliminar este usuario?')"
                                            class="w-full max-w-2xl bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 
                                            text-white font-semibold py-3 p-9 rounded-lg transition-all transform hover:scale-105 shadow-lg">
                                            Eliminar
                                        </button>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </section>
        </main>

    </div>

    <!-- Overlay para móviles -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-30 lg:hidden hidden"></div>

    <script src="../assets/js/sidebar.js"></script>

    <script>
      const usuarios = <?= json_encode($usuarios) ?>;

        function toggleContent() {
            document.getElementById('content').classList.toggle('hidden');
            document.getElementById('arrow').classList.toggle('rotate-90');
        }

        function autocompletar(cmb) {
            const u = usuarios.find(x => x.id_usuario == cmb.value);
            document.getElementById('idActual').value    = u?.id_usuario ?? '';
            document.getElementById('txtnombre').value   = u?.nombre     ?? '';
            document.getElementById('txtapellido').value = u?.apellido   ?? '';
            document.getElementById('txtemail').value    = u?.email      ?? '';
            document.getElementById('txttelefono').value = u?.telefono   ?? '';
            document.getElementById('txtusuario').value  = u?.usuario    ?? '';
            document.getElementById('txtpassword').value = '';
            document.getElementById('txtrol').value      = u?.id_rol     ?? '';
        }

        window.addEventListener('DOMContentLoaded', () => {
            const cmb = document.getElementById('cmbUsuarios');
            if (cmb.value) autocompletar(cmb);
        });
    </script>


</body>

</html>