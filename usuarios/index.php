<?php
session_start();
require_once '../config/connection.php';

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
    <title>Usuarios</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>

<body class="bg-gray-900">
    <?php include '../includes/sidebar.php'; ?>

    <div class="lg:ml-64">
        <header>
            <h1 class="font-semibold text-white text-3xl ml-3 text-center">
                Usuarios
            </h1>
        </header>

        <main>

            <?php include 'form.php'; ?>
        </main>

    </div>

    <!-- Overlay para móviles -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-30 lg:hidden hidden"></div>

    <script src="../assets/js/sidebar.js"></script>

    <?php include '../includes/footer.php'; ?>

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