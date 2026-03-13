<?php
// Este archivo se incluye desde usuarios/index.php para mostrar el selector y el formulario de usuarios.
// Variables esperadas: $usuarios, $roles, $seleccionado
?>

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

                <form method="POST" action="index.php">
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
